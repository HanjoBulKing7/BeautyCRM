<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class ServicioEmpleadoController extends Controller
{
    /**
     * Sync servicios for an empleado.
     */
    public function sync(Request $request, Empleado $empleado)
    {
        $data = $request->validate([
            'servicios' => 'nullable|array',
            'servicios.*' => 'integer|exists:servicios,id_servicio',
        ]);

        $empleado->servicios()->sync($data['servicios'] ?? []);

        return redirect()->back()->with('success', 'Servicios actualizados correctamente.');
    }
}
