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
    public $fecha_legible;
    public $hora;

    public function __construct(Cita $cita, ?string $googleEventLink = null)
    {
        // Aseguramos relaciones necesarias
        $cita->loadMissing(['servicios', 'cliente', 'empleado']);

        $this->cita = $cita;
        $this->googleEventLink = $googleEventLink;

        $this->fecha_legible = $cita->fecha_cita
            ? Carbon::parse($cita->fecha_cita)->translatedFormat('l d \d\e F Y')
            : 'Fecha no disponible';

        $this->hora = $cita->hora_cita
            ? Carbon::parse($cita->hora_cita)->format('H:i')
            : 'Hora no disponible';
    }

    public function build()
    {
        $serviciosNombres = $this->cita->servicios
            ->pluck('nombre_servicio')
            ->implode(', ');

        return $this->subject(
                'Confirmación de cita - ' .
                ($serviciosNombres ?: 'Servicio') .
                ' - ' .
                $this->fecha_legible
            )
            ->markdown('emails.confirmada');
    }

}
