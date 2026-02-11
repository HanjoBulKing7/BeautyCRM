<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('servicios')
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.empleados.index', compact('empleados'));
    }

    public function create()
    {
        $empleado = new Empleado();

        $servicios = Servicio::query()
            // si manejas estado en servicios, descomenta:
            // ->where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();

        return view('admin.empleados.create', compact('empleado', 'servicios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'nombre'  => ['required', 'string', 'max:120'],
            'apellido'=> ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:255', 'unique:empleados,email'],
            'telefono'=> ['nullable', 'string', 'max:30'],

            // 👇 NO obligatorios (como pediste)
            'informacion_legal' => ['nullable', 'string'],
            'puesto'            => ['nullable', 'string', 'max:120'],
            'departamento'      => ['nullable', 'string', 'max:120'],

            'fecha_contratacion' => ['nullable', 'date'],
            'estatus' => ['nullable', Rule::in(['activo', 'inactivo'])],

            // servicios seleccionados para pivot
            'servicios'   => ['nullable', 'array'],
            'servicios.*' => ['integer', 'exists:servicios,id_servicio'],
        ]);

        $serviciosIds = $data['servicios'] ?? [];
        unset($data['servicios']);

        // valores por defecto si no vienen
        $data['estatus'] = $data['estatus'] ?? 'activo';

        $empleado = Empleado::create($data);

        // ✅ crea registros en servicio_empleado (empleado_id, servicio_id)
        $empleado->servicios()->sync($serviciosIds);

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado creado y servicios asignados.');
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load('servicios');

        $servicios = Servicio::query()
            // ->where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();

        return view('admin.empleados.edit', compact('empleado', 'servicios'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'nombre'  => ['required', 'string', 'max:120'],
            'apellido'=> ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('empleados', 'email')->ignore($empleado->id)],
            'telefono'=> ['nullable', 'string', 'max:30'],

            // 👇 NO obligatorios (como pediste)
            'informacion_legal' => ['nullable', 'string'],
            'puesto'            => ['nullable', 'string', 'max:120'],
            'departamento'      => ['nullable', 'string', 'max:120'],

            'fecha_contratacion' => ['nullable', 'date'],
            'estatus' => ['nullable', Rule::in(['activo', 'inactivo'])],

            // servicios seleccionados para pivot
            'servicios'   => ['nullable', 'array'],
            'servicios.*' => ['integer', 'exists:servicios,id_servicio'],
        ]);

        $serviciosIds = $data['servicios'] ?? [];
        unset($data['servicios']);

        $empleado->update($data);

        // ✅ SIEMPRE sync (si viene vacío, elimina pivots anteriores)
        $empleado->servicios()->sync($serviciosIds);

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado actualizado y servicios sincronizados.');
    }

    public function destroy(Empleado $empleado)
    {
        // limpia pivot antes de borrar (opcional pero recomendable)
        $empleado->servicios()->detach();
        $empleado->delete();

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado eliminado.');
    }
}
