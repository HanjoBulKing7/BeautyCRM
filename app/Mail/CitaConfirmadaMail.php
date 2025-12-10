<?php

namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CitaConfirmadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $googleEventLink;

    /**
     * Create a new message instance.
     */
    public function __construct(Cita $cita, ?string $googleEventLink = null)
    {
        $this->cita = $cita;
        $this->googleEventLink = $googleEventLink;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Aseguramos que aunque venga como string, lo parseamos
        $fecha = $this->cita->fecha_cita
            ? Carbon::parse($this->cita->fecha_cita)->translatedFormat('l d \\de F Y')
            : 'Fecha no disponible';

        $hora = $this->cita->hora_cita
            ? Carbon::parse($this->cita->hora_cita)->format('H:i')
            : 'Hora no disponible';

        return $this->subject('[Confirmación de cita] ' . $this->cita->servicio->nombre_servicio . ' - ' . $fecha)
            ->markdown('emails.citas.confirmada', [
                'fecha_legible' => $fecha,
                'hora'          => $hora,
            ]);
    }
}
