<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
    /**
     * 📋 Listado de empleados
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = User::with('sucursal')->whereIn('rol', ['vendedor', 'gerente', 'admin']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('rol', 'like', "%{$search}%");
            });
        }

        $empleados = $query->orderBy('nombre')->paginate(10);

        return view('empleados.index', compact('empleados'));
    }

    /**
     * 🆕 Mostrar formulario de creación
     */
    public function create()
    {
        $empleado = new User();
        $sucursales = Sucursal::all();

        return view('empleados.create', compact('empleado', 'sucursales'));
    }

    /**
     * 💾 Registrar nuevo empleado (desde panel interno)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255|unique:users,nombre',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'rol'          => 'required|in:admin,vendedor,gerente',
            'sucursal_id'  => 'required|exists:sucursales,id',
            'activo'       => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $data['password'] = Hash::make($data['password']);
            $data['activo'] = $request->has('activo') ? 1 : 0;

            // ✅ Crear el nuevo empleado sin loguearlo
            $empleado = User::create($data);

            DB::commit();

            return redirect()
                ->route('empleados.index')
                ->with('success', 'Empleado registrado correctamente. Ahora puede iniciar sesión.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Error al registrar el empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 👁️ Mostrar detalle de un empleado
     */
    public function show(User $empleado)
    {
        return view('empleados.show', compact('empleado'));
    }

    /**
     * ✏️ Editar empleado
     */
    public function edit(User $empleado)
    {
        $sucursales = Sucursal::all();

        return view('empleados.edit', compact('empleado', 'sucursales'));
    }

    /**
     * 🔄 Actualizar datos del empleado
     */
    public function update(Request $request, User $empleado)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255|unique:users,nombre,' . $empleado->id,
            'email'        => 'required|email|unique:users,email,' . $empleado->id,
            'password'     => 'nullable|string|min:8|confirmed',
            'rol'          => 'required|in:admin,vendedor,gerente',
            'sucursal_id'  => 'required|exists:sucursales,id',
            'activo'       => 'nullable|boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['activo'] = $request->has('activo') ? 1 : 0;

        $empleado->update($data);

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * 🗑️ Eliminar empleado
     */
    public function destroy(User $empleado)
    {
        // Evitar eliminarse a sí mismo
        if (Auth::check() && Auth::id() === $empleado->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $empleado->delete();

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
