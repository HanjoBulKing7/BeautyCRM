<?php

namespace App\Mail;

use App\Mail\Concerns\ConBajaDeCorreos;
use App\Models\Cupon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CumpleanosMail extends Mailable
{
    use Queueable, SerializesModels, ConBajaDeCorreos;

    public string $nombreCliente;
    public string $codigoCupon;
    public string $urlReserva;

    public function __construct(string $nombreCliente, ?int $clienteId = null)
    {
        $this->prepararBaja($clienteId);
        $this->nombreCliente = $nombreCliente;
        $this->codigoCupon   = $this->obtenerOCrearCupon($clienteId);
        $this->urlReserva    = config('app.url') . '/agendar-cita?cupon=' . urlencode($this->codigoCupon);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎂 ¡Feliz Cumpleaños ' . $this->nombreCliente . '! Tu regalo de Beauty Bonita',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cumpleanos',
        );
    }

    private function obtenerOCrearCupon(?int $clienteId): string
    {
        $codigoFallback = 'CUMPLE' . Carbon::now()->format('mY');

        try {
            // Busca un cupón de cumpleaños activo existente
            $cupon = Cupon::where('aplica_cumpleaños', true)
                ->where('estado', 'activo')
                ->whereDate('fecha_fin', '>=', Carbon::now())
                ->first();

            if ($cupon) {
                return $cupon->codigo;
            }

            // Crea un cupón de cumpleaños genérico del mes si no existe
            $existing = Cupon::where('codigo', $codigoFallback)->first();
            if ($existing) {
                return $existing->codigo;
            }

            Cupon::create([
                'codigo'            => $codigoFallback,
                'nombre'            => 'Descuento Cumpleaños ' . Carbon::now()->translatedFormat('F Y'),
                'descripcion'       => 'Regalo de cumpleaños: 15% de descuento en cualquier servicio.',
                'tipo_descuento'    => 'porcentaje',
                'valor_descuento'   => 15,
                'descuento_maximo'  => null,
                'monto_minimo'      => null,
                'fecha_inicio'      => Carbon::now()->startOfMonth(),
                'fecha_fin'         => Carbon::now()->endOfMonth(),
                'cantidad_usos'     => null,
                'cantidad_por_cliente' => 1,
                'usos_actuales'     => 0,
                'aplica_cumpleaños' => true,
                'estado'            => 'activo',
            ]);
        } catch (\Throwable $e) {
            // Silently fall back to the static code; table may not exist locally
        }

        return $codigoFallback;
    }
}
