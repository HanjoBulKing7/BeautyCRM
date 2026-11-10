<?php

namespace App\Services;

use App\Mail\CitaConfirmadaMail;
use App\Models\Cita;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CitaConfirmacion
{
    public function __construct(private GoogleCalendarService $calendar)
    {
    }

    // Nada de esto debe impedir que la cita quede guardada: cada paso falla por separado.
    public function notificar(Cita $cita): void
    {
        $cita->loadMissing(['servicios', 'cliente', 'empleado']);

        if ($cita->empleado_id) {
            try {
                $this->calendar->createEventFromCita($cita);
            } catch (\Throwable $e) {
                Log::error('Google Calendar error', ['id_cita' => $cita->id_cita, 'error' => $e->getMessage()]);
            }
        }

        foreach ([$cita->cliente?->email, $cita->empleado?->email] as $correo) {
            if (!$correo) continue;
            try {
                Mail::to($correo)->send(new CitaConfirmadaMail($cita));
            } catch (\Throwable $e) {
                Log::error('Mail error', ['id_cita' => $cita->id_cita, 'to' => $correo, 'error' => $e->getMessage()]);
            }
        }
    }
}
