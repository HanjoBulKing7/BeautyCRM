<?php

namespace App\Mail\Concerns;

use App\Models\Cliente;
use Illuminate\Mail\Mailables\Headers;

trait ConBajaDeCorreos
{
    public ?string $urlBaja = null;

    protected function prepararBaja(?int $clienteId): void
    {
        $cliente = $clienteId ? Cliente::find($clienteId) : null;
        $this->urlBaja = $cliente?->urlBajaCorreos();
    }

    // Gmail y Yahoo exigen baja de un clic (RFC 8058) en correos de promoción.
    public function headers(): Headers
    {
        if (!$this->urlBaja) {
            return new Headers();
        }

        return new Headers(text: [
            'List-Unsubscribe'      => '<' . $this->urlBaja . '>, <mailto:contacto@beautybonita.com.mx?subject=Baja>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }
}
