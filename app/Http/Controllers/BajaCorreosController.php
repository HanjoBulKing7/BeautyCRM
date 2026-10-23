<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class BajaCorreosController extends Controller
{
    // GET: solo muestra la confirmación; los antivirus de correo abren enlaces y no deben dar de baja a nadie.
    public function show(Cliente $cliente)
    {
        return view('correos.baja', $this->datosVista($cliente));
    }

    // POST: botón de la página o baja de un clic del cliente de correo (List-Unsubscribe-Post).
    public function store(Request $request, Cliente $cliente)
    {
        if (!$cliente->baja_correos_at) {
            $cliente->forceFill(['baja_correos_at' => now()])->save();
        }

        if (!$request->expectsJson() && $request->has('desde_pagina')) {
            return view('correos.baja', $this->datosVista($cliente) + ['recienDadoDeBaja' => true]);
        }

        return response('Listo, ya no recibirás correos de promociones de Beauty Bonita.', 200);
    }

    public function alta(Cliente $cliente)
    {
        $cliente->forceFill(['baja_correos_at' => null])->save();

        return view('correos.baja', $this->datosVista($cliente) + ['recienDadoDeAlta' => true]);
    }

    private function datosVista(Cliente $cliente): array
    {
        return [
            'cliente'  => $cliente,
            'urlBaja'  => $cliente->urlBajaCorreos(),
            'urlAlta'  => URL::signedRoute('correos.alta', ['cliente' => $cliente->id]),
        ];
    }
}
