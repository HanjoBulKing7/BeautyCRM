<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Notifications\EmployeeInvitationNotification;
use Illuminate\Support\Str;

class EmpleadoController extends Controller
{
    /**
     * ✅ INDEX
     */
    public function index(Request $request)
    {
        $empleados = Empleado::latest()->paginate(10);

        // Si viene desde Hub Loader (?modal=1) o AJAX => SOLO contenido
        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.empleados.partials.index-content', compact('empleados'));
        }

        // Vista normal
        return view('admin.empleados.index', compact('empleados'));
    }

    /**
     * ✅ CREATE
     */
    public function create(Request $request)
    {
        $empleado = new Empleado();

        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.empleados.partials.create-content', compact('empleado'));
        }

        return view('admin.empleados.create', compact('empleado'));
    }

    /**
     * ✅ STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'             => 'required|string|max:100',
            'apellido'           => 'required|string|max:100',
            'email'              => 'required|email|max:255|unique:empleados,email',
            'telefono'           => 'required|string|max:20',
            'puesto'             => 'nullable|string|max:100',
            'departamento'       => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus'            => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            $user = User::create([
                'role_id'  => 2, // EMPLEADO
                'name'     => $request->nombre . ' ' . $request->apellido,
                'email'    => $request->email,
                'password' => Hash::make(Str::random(32)),
            ]);

            $empleado = Empleado::create([
                'user_id'            => $user->id,
                'nombre'             => $request->nombre,
                'apellido'           => $request->apellido,
                'email'              => $request->email,
                'telefono'           => $request->telefono,
                'puesto'             => $request->puesto,
                'departamento'       => $request->departamento,
                'fecha_contratacion' => $request->fecha_contratacion,
                'estatus'            => $request->estatus,
                'informacion_legal'  => $request->informacion_legal,
            ]);

            $inviteUrl = URL::temporarySignedRoute(
                'invitation.employee',
                now()->addDays(2),
                ['user' => $user->id]
            );

            $user->notify(new EmployeeInvitationNotification($inviteUrl));
        });

        // ✅ CLAVE: conservar modal=1 si venía del Hub Loader
        return redirect()
            ->route('admin.empleados.index', $this->modalParams($request))
            ->with('success', 'Empleado creado exitosamente.');
    }

    /**
     * ✅ SHOW (si lo usas)
     */
    public function show(Request $request, Empleado $empleado)
    {
        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.empleados.partials.show-content', compact('empleado'));
        }

        return view('admin.empleados.show', compact('empleado'));
    }

    /**
     * ✅ EDIT
     */
    public function edit(Request $request, Empleado $empleado)
    {
        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.empleados.partials.edit-content', compact('empleado'));
        }

        return view('admin.empleados.edit', compact('empleado'));
    }

    /**
     * ✅ UPDATE
     */
    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre'             => 'required|string|max:100',
            'apellido'           => 'required|string|max:100',
            'email'              => 'required|email|max:255|unique:empleados,email,' . $empleado->getKey(),
            'telefono'           => 'required|string|max:20',
            'puesto'             => 'nullable|string|max:100',
            'departamento'       => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'estatus'            => 'required|in:activo,inactivo,vacaciones',
            'informacion_legal'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $empleado) {

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

            // ✅ mantener sincronizado User ligado
            if ($empleado->user_id) {
                User::where('id', $empleado->user_id)->update([
                    'name'  => $request->nombre . ' ' . $request->apellido,
                    'email' => $request->email,
                ]);
            }
        });

        return redirect()
            ->route('admin.empleados.index', $this->modalParams($request))
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * ✅ DESTROY
     */
    public function destroy(Request $request, Empleado $empleado)
    {
        DB::transaction(function () use ($empleado) {
            $userId = $empleado->user_id;
            $empleado->delete();

            // opcional (si quieres borrar el user también)
            if ($userId) {
                User::where('id', $userId)->delete();
            }
        });

        return redirect()
            ->route('admin.empleados.index', $this->modalParams($request))
            ->with('success', 'Empleado eliminado exitosamente.');
    }

    /**
     * Helpers
     */
    private function modalParams(Request $request): array
    {
        return ($request->boolean('modal') || $request->ajax())
            ? ['modal' => 1]
            : [];
    }
}
