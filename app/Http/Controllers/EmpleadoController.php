<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    // Listar empleados
    public function index()
    {
        $empleados = Empleado::latest()->paginate(10); // ← CAMBIAR AQUÍ
        return view('empleados.index', compact('empleados'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $empleado = new Empleado(); // ← Crear instancia vacía
        return view('empleados.create', compact('empleado'));
    }

    // Almacenar nuevo empleado
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'puesto' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus' => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal' => 'nullable|string',
        ]);

        // CREAR CON user_id EXPLÍCITO
        $empleado = Empleado::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'puesto' => $request->puesto,
            'departamento' => $request->departamento,
            'fecha_contratacion' => $request->fecha_contratacion,
            'estatus' => $request->estatus,
            'informacion_legal' => $request->informacion_legal,
            'user_id', // ← ESTA LÍNEA ES CLAVE
        ]);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    // Mostrar formulario de edición
    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    // Actualizar empleado
    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'puesto' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus' => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal' => 'nullable|string',
        ]);

        $empleado->update($request->all());

        return redirect()->route('admin.empleados.index') // ← CAMBIAR
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    // Eliminar empleado
    public function destroy(Empleado $empleado)
    {
        $empleado->delete();

        return redirect()->route('admin.empleados.index') // ← CAMBIAR
            ->with('success', 'Empleado eliminado exitosamente.');
    }

    // Mostrar empleado individual (opcional)
    public function show(Empleado $empleado)
    {
        return view('empleados.show', compact('empleado'));
    }
}