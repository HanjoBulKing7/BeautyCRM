<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Notifications\EmployeeInvitationNotification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;



class EmpleadoController extends Controller
{
    // Listar empleados
    public function index()
    {
        $empleados = Empleado::latest()->paginate(10); // ← CAMBIAR AQUÍ
        return view('admin.empleados.index', compact('empleados'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $empleado = new Empleado(); // ← Crear instancia vacía
        return view('admin.empleados.create', compact('empleado'));
    }

    // Almacenar nuevo empleado
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'telefono' => 'required|string|max:20',
            'puesto' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus' => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal' => 'nullable|string',
        ],[
            'email.unique' => 'Este correo ya está registrado. Usa otro correo.',
        ]
        );



        try {
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

            } catch (QueryException $e) {
                // por si se coló por carrera (1062 duplicate)
                if (($e->errorInfo[1] ?? null) == 1062) {
                    return back()
                        ->withErrors(['email' => 'Ese correo ya está registrado. Usa otro.'])
                        ->withInput();
                }
                throw $e;
            }
        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    // Mostrar formulario de edición
    public function edit(Empleado $empleado)
    {
        return view('admin.empleados.edit', compact('empleado'));
    }

    // Actualizar empleado
    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($empleado->user_id)],
            'telefono' => 'required|string|max:20',
            'puesto' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus' => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal' => 'nullable|string',
        ],[
            'email.unique' => 'Este correo ya está registrado. Usa otro correo.',
        ]);

        // actualiza EMPLEADO
        $empleado->update($request->only([
            'nombre','apellido','email','telefono','puesto','departamento',
            'fecha_contratacion','estatus','informacion_legal',
        ]));

        // actualiza USER relacionado
        \App\Models\User::where('id', $empleado->user_id)->update([
            'name'  => $request->nombre . ' ' . $request->apellido,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.empleados.index')
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