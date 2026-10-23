<?php

namespace App\Mail;

use App\Mail\Concerns\ConBajaDeCorreos;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GraciasVisitaMail extends Mailable
{
    use Queueable, SerializesModels, ConBajaDeCorreos;

    public string $urlResena;
    public string $urlReserva;

    public function __construct(
        public string $nombreCliente,
        public string $serviciosNombres = '',
        ?int $clienteId = null,
    ) {
        $this->prepararBaja($clienteId);
        $this->urlResena  = config('services.google.review_url');
        $this->urlReserva = config('app.url') . '/agendar-cita';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '💛 ¡Gracias por tu visita, ' . $this->nombreCliente . '!',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.gracias-visita');
    }
}
