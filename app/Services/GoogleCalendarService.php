<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\GoogleToken;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;

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
        $this->client->setApprovalPrompt(config('google.approval_prompt'));
            // Agregar esto para desarrollo
        $this->client->setIncludeGrantedScopes(true);
    }

    /**
     * Obtener URL de autenticación de Google
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Manejar el callback de autenticación
     */
    public function handleCallback($code)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                throw new \Exception($token['error_description'] ?? 'Error en la autenticación');
            }

            GoogleToken::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null,
                    'expires_in' => $token['expires_in'],
                    'token_created_at' => now(),
                ]
            );

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Obtener cliente de Google Calendar autenticado
     */
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

    /**
     * Refrescar token de acceso
     */
    protected function refreshToken(GoogleToken $token)
    {
        $this->client->setAccessToken($token->access_token);
        $newToken = $this->client->fetchAccessTokenWithRefreshToken($token->refresh_token);

        $token->update([
            'access_token' => $newToken['access_token'],
            'expires_in' => $newToken['expires_in'],
            'token_created_at' => now(),
        ]);
    }

    // En GoogleCalendarService.php - Modifica el método createEventFromCita:
    public function createEventFromCita(Cita $cita)
    {
        try {
            $calendar = $this->getClient();

            // ✅ Asegurar que las relaciones están cargadas
            $cita->load(['servicio', 'cliente', 'empleado']);

            // ✅ Verificar que startDateTime y endDateTime existen
            if (!$cita->startDateTime || !$cita->endDateTime) {
                \Log::error('Fechas inválidas para Google Calendar', [
                    'cita_id' => $cita->id_cita,
                    'start' => $cita->startDateTime,
                    'end' => $cita->endDateTime,
                    'fecha_cita' => $cita->fecha_cita,
                    'hora_cita' => $cita->hora_cita
                ]);
                throw new \Exception('Fecha y hora de la cita no están configuradas correctamente');
            }

            // ✅ Depuración: Log de las fechas
            \Log::info('Creando evento Google Calendar', [
                'cita_id' => $cita->id_cita,
                'start' => $cita->startDateTime,
                'end' => $cita->endDateTime,
                'summary' => 'Cita: ' . ($cita->servicio->nombre_servicio ?? 'Sin servicio')
            ]);

            $event = new Event([
                'summary' => 'Cita: ' . ($cita->servicio->nombre_servicio ?? 'Sin servicio'),
                'description' => $this->buildEventDescription($cita),
                'start' => new EventDateTime([
                    'dateTime' => $cita->startDateTime,
                    'timeZone' => 'America/Mexico_City',
                ]),
                'end' => new EventDateTime([
                    'dateTime' => $cita->endDateTime,
                    'timeZone' => 'America/Mexico_City',
                ]),
                'location' => 'Salón de Belleza',
            ]);

            $createdEvent = $calendar->events->insert('primary', $event);
            
            \Log::info('✅ Evento creado exitosamente en Google Calendar', [
                'cita_id' => $cita->id_cita,
                'event_id' => $createdEvent->getId()
            ]);
            
            return $createdEvent;
        } catch (\Exception $e) {
            \Log::error('❌ Error al crear evento Google Calendar', [
                'cita_id' => $cita->id_cita,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    /**
     * Actualizar evento en Google Calendar
     */
    public function updateEventFromCita(Cita $cita)
    {
        try {
            if (!$cita->google_event_id) {
                return $this->createEventFromCita($cita);
            }

            $calendar = $this->getClient();

            $event = $calendar->events->get('primary', $cita->google_event_id);
            
            $event->setSummary('Cita: ' . $cita->servicio->nombre_servicio);
            $event->setDescription($this->buildEventDescription($cita));
            $event->setStart(new EventDateTime([
                'dateTime' => $cita->startDateTime,
                'timeZone' => 'America/Mexico_City',
            ]));
            $event->setEnd(new EventDateTime([
                'dateTime' => $cita->endDateTime,
                'timeZone' => 'America/Mexico_City',
            ]));
            $event->setAttendees($this->buildAttendees($cita));

            $updatedEvent = $calendar->events->update('primary', $cita->google_event_id, $event);
            
            return $updatedEvent;
        } catch (\Exception $e) {
            // Si el evento no existe en Google, crear uno nuevo
            if ($e->getCode() == 404) {
                $cita->google_event_id = null;
                $cita->save();
                return $this->createEventFromCita($cita);
            }
            throw $e;
        }
    }

    /**
     * Eliminar evento de Google Calendar
     */
    public function deleteEventFromCita(Cita $cita)
    {
        try {
            if (!$cita->google_event_id) {
                return true;
            }

            $calendar = $this->getClient();
            $calendar->events->delete('primary', $cita->google_event_id);
            
            return true;
        } catch (\Exception $e) {
            // Si el evento no existe, considerar eliminado
            if ($e->getCode() == 404) {
                return true;
            }
            throw $e;
        }
    }

    /**
     * Construir descripción del evento
     */
    protected function buildEventDescription(Cita $cita)
    {
        $description = "\nCita de : {$cita->servicio->nombre_servicio}";
        $description .= "\nPrecio: $" . number_format($cita->servicio->precio, 2);
        // Duración del servicio
        $duracion = $cita->servicio->duracion ?? 60;
        $description .= "\nDuración: {$duracion} minutos";

        if ($cita->observaciones) {
            $description .= "\nObservaciones: {$cita->observaciones}";
        }
        
        $description .= "\nEstado: " . ucfirst($cita->estado_cita);
        
        return $description;
    }

    /**
     * Construir lista de asistentes
     */
    protected function buildAttendees(Cita $cita)
    {
        $attendees = [];
        
        // Cliente como asistente
        if ($cita->cliente && $cita->cliente->email) {
            $attendees[] = ['email' => $cita->cliente->email];
        }
        
        // Empleado como asistente
        if ($cita->empleado && $cita->empleado->email) {
            $attendees[] = ['email' => $cita->empleado->email];
        }
        
        return $attendees;
    }

    /**
     * Sincronizar todas las citas no sincronizadas
     */
    public function syncPendingCitas()
    {
        $pendingCitas = Cita::where('synced_with_google', false)
            ->where('estado_cita', '!=', 'cancelada')
            ->get();

        $results = [
            'success' => 0,
            'errors' => []
        ];

        foreach ($pendingCitas as $cita) {
            try {
                $this->createEventFromCita($cita);
                
                $cita->update([
                    'synced_with_google' => true,
                    'last_sync_at' => now(),
                ]);
                
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Cita ID {$cita->id_cita}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Obtener eventos del calendario
     */
    public function getEvents($maxResults = 10)
    {
        try {
            $calendar = $this->getClient();
            
            $optParams = [
                'maxResults' => $maxResults,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => date('c'),
            ];

            $results = $calendar->events->listEvents('primary', $optParams);
            return $results->getItems();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}