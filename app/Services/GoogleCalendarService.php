<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\GoogleToken;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $calendar;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('BeautyCRM');
        $this->client->setClientId(config('google.client_id'));
        $this->client->setClientSecret(config('google.client_secret'));
        $this->client->setRedirectUri(config('google.redirect_uri'));
        $this->client->setScopes(config('google.scopes'));
        $this->client->setAccessType(config('google.access_type'));
        $this->client->setPrompt(config('google.prompt'));
        $this->client->setIncludeGrantedScopes(true);
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new \Exception('No hay usuario para guardar tokens.');
        }

        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception($token['error_description'] ?? 'Error en la autenticación');
        }

        GoogleToken::updateOrCreate(
            ['user_id' => $userId],
            [
                'access_token'     => $token['access_token'] ?? null,
                'refresh_token'    => $token['refresh_token'] ?? null, // solo viene con offline+consent (a veces)
                'expires_in'       => $token['expires_in'] ?? null,
                'token_created_at' => now(),
            ]
        );

        return true;
    }


    public function getClient()
    {
        $token = GoogleToken::where('user_id', Auth::id())->first();

        if (!$token) {
            throw new \Exception('No hay token de Google configurado');
        }

        if ($token->isExpired() && $token->refresh_token) {
            $this->refreshToken($token);
        }

        $this->client->setAccessToken($token->access_token);
        $this->calendar = new Calendar($this->client);

        return $this->calendar;
    }



    public function getClientForUser(int $userId): Calendar
    {
        $token = GoogleToken::where('user_id', $userId)->first();

        if (!$token) {
            throw new \Exception("El usuario {$userId} no tiene token de Google configurado");
        }

        if ($token->isExpired() && $token->refresh_token) {
            $this->refreshToken($token);
        }

        $this->client->setAccessToken($token->access_token);
        $this->calendar = new Calendar($this->client);

        return $this->calendar;
    }


    protected function refreshToken(GoogleToken $token)
    {
        $this->client->setAccessToken($token->access_token);
        $newToken = $this->client->fetchAccessTokenWithRefreshToken($token->refresh_token);

        $token->update([
            'access_token'     => $newToken['access_token'],
            'expires_in'       => $newToken['expires_in'],
            'token_created_at' => now(),
        ]);
    }

    /**
     * ✅ Construye start/end en hora LOCAL México para evitar desfases por UTC/Z/+00:00
     */
    protected function buildLocalStartEnd(Cita $cita, string $tz = 'America/Mexico_City'): array
    {
        // Asegurar relaciones
        $cita->loadMissing(['servicio', 'cliente', 'empleado']);

        // ✅ Fecha SIEMPRE en Y-m-d (sin 00:00:00)
        $fecha = $cita->fecha_cita instanceof \Carbon\Carbon
            ? $cita->fecha_cita->toDateString()
            : \Carbon\Carbon::parse($cita->fecha_cita)->toDateString();

        // ✅ Hora SIEMPRE como HH:MM:SS
        $hora = is_string($cita->hora_cita) ? trim($cita->hora_cita) : (string) $cita->hora_cita;

        // por si viene "16:00" => "16:00:00"
        if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
            $hora .= ':00';
        }

        // por si viene con basura extra, nos quedamos con HH:MM:SS
        if (preg_match('/(\d{2}:\d{2}:\d{2})/', $hora, $m)) {
            $hora = $m[1];
        }

        $startRaw = "{$fecha} {$hora}";

        // Interpretar el string como hora LOCAL (no UTC)
        $startLocal = \Carbon\Carbon::parse($startRaw, $tz);

        $duracionMin = (int) ($cita->servicio->duracion ?? 60);
        $endLocal = (clone $startLocal)->addMinutes($duracionMin);

        // Importante: enviar sin offset y con timeZone aparte
        return [
            'tz'    => $tz,
            'start' => $startLocal->format('Y-m-d\TH:i:s'),
            'end'   => $endLocal->format('Y-m-d\TH:i:s'),
            'debug' => [
                'start_raw'   => $startRaw,
                'start_local' => $startLocal->toDateTimeString(),
                'end_local'   => $endLocal->toDateTimeString(),
                'start_tz'    => $startLocal->getTimezone()->getName(),
            ],
        ];
    }


    public function createEventFromCita(Cita $cita)
    {
        try {
            if (!$cita->id_empleado) {
                throw new \Exception("La cita {$cita->id_cita} no tiene empleado asignado.");
            }

            $calendar = $this->getClientForUser((int)$cita->id_empleado);

            $cita->loadMissing(['servicio', 'cliente', 'empleado']);

            $dt = $this->buildLocalStartEnd($cita, 'America/Mexico_City');

            $attendees = [];
            if ($cita->cliente && $cita->cliente->email) {
                $attendees[] = ['email' => $cita->cliente->email]; // ✅ correo automático al cliente
            }

            $event = new Event([
                'summary'     => 'Cita: ' . ($cita->servicio->nombre_servicio ?? 'Sin servicio'),
                'description' => $this->buildEventDescription($cita),
                'start'       => new EventDateTime([
                    'dateTime' => $dt['start'],
                    'timeZone' => $dt['tz'],
                ]),
                'end'         => new EventDateTime([
                    'dateTime' => $dt['end'],
                    'timeZone' => $dt['tz'],
                ]),
                'location'    => 'Salón de Belleza',
                'attendees' => $this->buildAttendees($cita),
            ]);

            // ✅ sendUpdates=all manda el correo de Google Calendar al attendee
            $createdEvent = $calendar->events->insert('primary', $event, [
                'sendUpdates' => 'all',
            ]);

            // ✅ Persistir en DB
            $cita->update([
                'google_event_id'    => $createdEvent->getId(),
                'synced_with_google' => true,
                'last_sync_at'       => now(),
            ]);

            Log::info('✅ Evento creado exitosamente en Google Calendar', [
                'cita_id'  => $cita->id_cita,
                'event_id' => $createdEvent->getId()
            ]);

            return $createdEvent;

        } catch (\Exception $e) {
            Log::error('❌ Error al crear evento Google Calendar', [
                'cita_id' => $cita->id_cita,
                'error'   => $e->getMessage(),
            ]);
            throw $e;
        }
    }


    public function updateEventFromCita(Cita $cita)
    {
        try {
            if (!$cita->id_empleado) {
                throw new \Exception("La cita {$cita->id_cita} no tiene empleado asignado.");
            }

            if (!$cita->google_event_id) {
                return $this->createEventFromCita($cita);
            }

            // ✅ IMPORTANTE: editar en el calendario del empleado
            $calendar = $this->getClientForUser((int)$cita->id_empleado);

            $cita->loadMissing(['servicio', 'cliente', 'empleado']);

            $dt = $this->buildLocalStartEnd($cita, 'America/Mexico_City');

            $event = $calendar->events->get('primary', $cita->google_event_id);

            $event->setSummary('Cita: ' . ($cita->servicio->nombre_servicio ?? 'Sin servicio'));
            $event->setDescription($this->buildEventDescription($cita));

            $event->setStart(new EventDateTime([
                'dateTime' => $dt['start'],
                'timeZone' => $dt['tz'],
            ]));

            $event->setEnd(new EventDateTime([
                'dateTime' => $dt['end'],
                'timeZone' => $dt['tz'],
            ]));

            // ✅ decide si quieres solo cliente o también empleado
            $event->setAttendees($this->buildAttendees($cita));

            $updated = $calendar->events->update('primary', $cita->google_event_id, $event, [
                'sendUpdates' => 'all',
            ]);

            $cita->update([
                'synced_with_google' => true,
                'last_sync_at'       => now(),
            ]);

            return $updated;

        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                $cita->update([
                    'google_event_id'    => null,
                    'synced_with_google' => false,
                    'last_sync_at'       => now(),
                ]);
                return $this->createEventFromCita($cita);
            }
            throw $e;
        }
    }


    public function deleteEventFromCita(Cita $cita)
    {
        try {
            if (!$cita->google_event_id) {
                return true;
            }

            if (!$cita->id_empleado) {
                // si no hay empleado, al menos limpia el estado local
                $cita->update([
                    'google_event_id'    => null,
                    'synced_with_google' => false,
                    'last_sync_at'       => now(),
                ]);
                return true;
            }

            $calendar = $this->getClientForUser((int)$cita->id_empleado);
            $calendar->events->delete('primary', $cita->google_event_id);

            $cita->update([
                'google_event_id'    => null,
                'synced_with_google' => false,
                'last_sync_at'       => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                $cita->update([
                    'google_event_id'    => null,
                    'synced_with_google' => false,
                    'last_sync_at'       => now(),
                ]);
                return true;
            }
            throw $e;
        }
    }


    protected function buildEventDescription(Cita $cita)
    {
        $description = "\nCita de : {$cita->servicio->nombre_servicio}";
        $description .= "\nPrecio: $" . number_format($cita->servicio->precio, 2);

        $duracion = $cita->servicio->duracion ?? 60;
        $description .= "\nDuración: {$duracion} minutos";

        if ($cita->observaciones) {
            $description .= "\nObservaciones: {$cita->observaciones}";
        }

        $description .= "\nEstado: " . ucfirst($cita->estado_cita);

        return $description;
    }

    protected function buildAttendees(Cita $cita): array
    {
        $attendees = [];

        if ($cita->cliente && $cita->cliente->email) {
            $attendees[] = ['email' => $cita->cliente->email];
        }

        return $attendees;
    }

    public function syncPendingCitas()
    {
        $pendingCitas = Cita::where('synced_with_google', false)
            ->where('estado_cita', '!=', 'cancelada')
            ->get();

        $results = [
            'success' => 0,
            'errors'  => []
        ];

        foreach ($pendingCitas as $cita) {
            try {
                $this->createEventFromCita($cita);

                $cita->update([
                    'synced_with_google' => true,
                    'last_sync_at'       => now(),
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Cita ID {$cita->id_cita}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function getEvents($maxResults = 10)
    {
        try {
            $calendar = $this->getClient();

            $optParams = [
                'maxResults'   => $maxResults,
                'orderBy'      => 'startTime',
                'singleEvents' => true,
                'timeMin'      => date('c'),
            ];

            $results = $calendar->events->listEvents('primary', $optParams);
            return $results->getItems();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function disconnect(Request $request)
    {
        GoogleToken::where('user_id', Auth::id())->delete();

        return redirect()
            ->back()
            ->with('success', 'Se ha desconectado tu cuenta de Google correctamente. Puedes volver a conectarla cuando quieras.');
    }

    public function reconnect(Request $request)
    {
        GoogleToken::where('user_id', Auth::id())->delete();
        return redirect()->route('google.redirect');
    }
}
