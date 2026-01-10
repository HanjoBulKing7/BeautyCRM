<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Notifications\EmployeeInvitationNotification;
use Illuminate\Support\Str;



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
            'email' => 'required|email|max:255|unique:empleados,email',
            'telefono' => 'required|string|max:20',
            'puesto' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus' => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal' => 'nullable|string',
        ]);

        $user = User::create([
            'role_id' => 2,
            'name' => $request->nombre . ' ' . $request->apellido,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)), // no la usará si solo entra con Google
        ]);

        $empleado = Empleado::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'puesto' => $request->puesto,
            'departamento' => $request->departamento,
            'fecha_contratacion' => $request->fecha_contratacion,
            'estatus' => $request->estatus,
            'informacion_legal' => $request->informacion_legal,
        ]);

        $inviteUrl = URL::temporarySignedRoute(
            'invitation.employee',
            now()->addDays(2),
            ['user' => $user->id] // ✅ ahora sí, USER
        );

        $user->notify(new EmployeeInvitationNotification($inviteUrl));

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

        $empleado->update($request->only([
            'nombre',
            'apellido',
            'email',
            'telefono',
            'puesto',
            'departamento',
            'fecha_contratacion',
            'estatus',
            'informacion_legal',
        ]));


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