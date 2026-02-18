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
        $this->client->setAccessToken([
            'access_token'  => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_in'    => $token->expires_in,
            'created'       => $token->token_created_at->timestamp,
        ]);

        if ($this->client->isAccessTokenExpired()) {

            $newToken = $this->client->fetchAccessTokenWithRefreshToken(
                $token->refresh_token
            );

            if (isset($newToken['access_token'])) {

                $token->update([
                    'access_token'     => $newToken['access_token'],
                    'expires_in'       => $newToken['expires_in'] ?? 3600,
                    'token_created_at' => now(),
                ]);

                $this->client->setAccessToken($newToken);
            }
        }
    }

    /**
     * ✅ Construye start/end en hora LOCAL México para evitar desfases por UTC/Z/+00:00
     */
    protected function buildLocalStartEnd(Cita $cita, string $tz = 'America/Mexico_City'): array
    {
        $cita->loadMissing(['servicios']);

        $fecha = $cita->fecha_cita instanceof \Carbon\Carbon
            ? $cita->fecha_cita->toDateString()
            : \Carbon\Carbon::parse($cita->fecha_cita)->toDateString();

        $hora = trim((string)$cita->hora_cita);

        if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
            $hora .= ':00';
        }

        if (preg_match('/(\d{2}:\d{2}:\d{2})/', $hora, $m)) {
            $hora = $m[1];
        }

        $startRaw = "{$fecha} {$hora}";
        $startLocal = \Carbon\Carbon::parse($startRaw, $tz);

        // 🔥 Duración TOTAL de la cita
        $duracionMin = $cita->duracion_total_minutos
            ?? $cita->servicios->sum('duracion')
            ?? 60;

        $endLocal = (clone $startLocal)->addMinutes($duracionMin);

        return [
            'tz'    => $tz,
            'start' => $startLocal->format('Y-m-d\TH:i:s'),
            'end'   => $endLocal->format('Y-m-d\TH:i:s'),
        ];
    }

    public function createEventFromCita(Cita $cita)
    {
        try {

            $ownerId = config('google.calendar_owner_user_id');

            if (!$ownerId) {
                Log::error('❌ No hay GOOGLE_CALENDAR_OWNER_USER_ID configurado');
                return null;
            }

            $calendar = $this->getClientForUser((int)$ownerId);

            $cita->loadMissing(['servicios', 'cliente', 'empleado']);

            $dt = $this->buildLocalStartEnd($cita, 'America/Mexico_City');

            $serviciosNombres = $cita->servicios
                ->pluck('nombre_servicio')
                ->implode(', ');

            $event = new Event([
                'summary'     => 'Cita: ' . ($serviciosNombres ?: 'Servicio'),
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
                'attendees'   => $this->buildAttendees($cita),
            ]);

            $createdEvent = $calendar->events->insert('primary', $event, [
                'sendUpdates' => 'all',
            ]);

            $cita->update([
                'google_event_id'    => $createdEvent->getId(),
                'synced_with_google' => true,
                'last_sync_at'       => now(),
            ]);

            Log::info('✅ Evento creado en calendario principal', [
                'cita_id'  => $cita->id_cita,
                'event_id' => $createdEvent->getId(),
            ]);

            return $createdEvent;

        } catch (\Throwable $e) {

            Log::error('❌ Error creando evento Google Calendar', [
                'cita_id' => $cita->id_cita ?? null,
                'error'   => $e->getMessage(),
            ]);

            return null; // 🔥 Importante: NO romper webhook
        }
    }


    public function updateEventFromCita(Cita $cita)
    {
        try {
            if (!$cita->empleado_id) {
                throw new \Exception("La cita {$cita->id_cita} no tiene empleado asignado.");
            }

            if (!$cita->google_event_id) {
                return $this->createEventFromCita($cita);
            }

            $calendar = $this->getClient();

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

            if (!$cita->empleado_id) {
                // si no hay empleado, al menos limpia el estado local
                $cita->update([
                    'google_event_id'    => null,
                    'synced_with_google' => false,
                    'last_sync_at'       => now(),
                ]);
                return true;
            }

            $calendar = $this->getClient();
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
        $cita->loadMissing(['servicios']);

        $nombres = $cita->servicios
            ->pluck('nombre_servicio')
            ->implode(', ');

        $precioTotal = $cita->servicios->sum('precio');
        $duracionTotal = $cita->servicios->sum('duracion');

        $description  = "Servicios: {$nombres}";
        $description .= "\nPrecio total: $" . number_format($precioTotal, 2);
        $description .= "\nDuración total: {$duracionTotal} minutos";

        if ($cita->observaciones) {
            $description .= "\nObservaciones: {$cita->observaciones}";
        }

        $description .= "\nEstado: " . ucfirst($cita->estado_cita);

        return $description;
    }

    protected function buildAttendees(Cita $cita): array
    {
        $attendees = [];

        if ($cita->empleado && $cita->empleado->email) {
            $attendees[] = ['email' => $cita->empleado->email];
        }

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
