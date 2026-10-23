<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CumpleanosClienteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fecha_nacimiento' => ['required', 'date', 'before:-10 years', 'after:1920-01-01'],
        ], [
            'fecha_nacimiento.before' => 'Ingresa una fecha de nacimiento válida.',
            'fecha_nacimiento.after'  => 'Ingresa una fecha de nacimiento válida.',
        ]);

        $cliente = Cliente::where('user_id', Auth::id())->first();
        if (!$cliente) {
            return back()->with('cumple_error', 'No se encontró el perfil de cliente asociado a tu cuenta.');
        }

        // Solo se guarda si aún no existe: una vez registrada no se puede cambiar desde la cuenta.
        $actualizadas = Cliente::whereKey($cliente->id)
            ->whereNull('fecha_nacimiento')
            ->update([
                'fecha_nacimiento'               => Carbon::parse($request->input('fecha_nacimiento'))->toDateString(),
                'fecha_nacimiento_registrada_at' => now(),
            ]);

        if ($actualizadas === 0) {
            return back()->with('cumple_error', 'Tu fecha de nacimiento ya está registrada. Si necesitas corregirla, contáctanos.');
        }

        return back()->with('cumple_ok', '¡Listo! Guardamos tu cumpleaños 🎂 Te enviaremos un regalo en tu mes.');
    }
}
