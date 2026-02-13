<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->orderBy('nombre_servicio')
            ->get();

        return view('admin.empleados.create', compact('empleado', 'servicios'));
    }

    public function store(Request $request)
    {
        $this->normalizeServiciosInput($request);

        $data = $this->validateEmpleado($request, null);

        $serviciosIds = $data['servicios'] ?? [];
        unset($data['servicios']);

        $data['estatus'] = $data['estatus'] ?? 'activo';

        return DB::transaction(function () use ($data, $serviciosIds) {
            $empleado = Empleado::create($data);

            // ✅ crea N filas en servicio_empleado
            $empleado->servicios()->sync($serviciosIds);

            return redirect()
                ->route('admin.empleados.index')
                ->with('success', 'Empleado creado y servicios asignados.');
        });
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load('servicios');

        $servicios = Servicio::query()
            ->orderBy('nombre_servicio')
            ->get();

        return view('admin.empleados.edit', compact('empleado', 'servicios'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $this->normalizeServiciosInput($request);

        $data = $this->validateEmpleado($request, $empleado);

        $serviciosIds = $data['servicios'] ?? [];
        unset($data['servicios']);

        return DB::transaction(function () use ($empleado, $data, $serviciosIds) {
            $empleado->update($data);

            // ✅ sincroniza pivots (si viene vacío, deja 0 servicios)
            $empleado->servicios()->sync($serviciosIds);

            return redirect()
                ->route('admin.empleados.index')
                ->with('success', 'Empleado actualizado y servicios sincronizados.');
        });
    }

    public function destroy(Empleado $empleado)
    {
        return DB::transaction(function () use ($empleado) {
            $empleado->servicios()->detach();
            $empleado->delete();

            return redirect()
                ->route('admin.empleados.index')
                ->with('success', 'Empleado eliminado.');
        });
    }

    private function validateEmpleado(Request $request, ?Empleado $empleado): array
    {
        return $request->validate([
            'user_id'  => ['nullable', 'integer'],
            'nombre'   => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'email'    => [
                'required',
                'email',
                'max:255',
                $empleado
                    ? Rule::unique('empleados', 'email')->ignore($empleado->id)
                    : Rule::unique('empleados', 'email'),
            ],
            'telefono' => ['nullable', 'string', 'max:30'],

            'fecha_contratacion' => ['nullable', 'date'],
            'estatus'            => ['nullable', Rule::in(['activo', 'inactivo'])],

            // ✅ Servicios (lista)
            'servicios'          => ['nullable', 'array'],
            'servicios.*'        => ['integer', 'distinct', 'exists:servicios,id_servicio'],
        ]);
    }

    /**
     * Normaliza "servicios" por si llega como:
     * - array: [1,2,3]
     * - JSON string: "[1,2,3]"
     * - CSV string: "1,2,3"
     */
    private function normalizeServiciosInput(Request $request): void
    {
        if (!$request->has('servicios')) return;

        $raw = $request->input('servicios');

        if (is_array($raw)) {
            $request->merge(['servicios' => $this->sanitizeServiciosArray($raw)]);
            return;
        }

        if (is_string($raw)) {
            $raw = trim($raw);

            if (str_starts_with($raw, '[')) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $request->merge(['servicios' => $this->sanitizeServiciosArray($decoded)]);
                    return;
                }
            }

            $parts = array_map('trim', explode(',', $raw));
            $request->merge(['servicios' => $this->sanitizeServiciosArray($parts)]);
        }
    }

    private function sanitizeServiciosArray(array $arr): array
    {
        $arr = array_map(fn ($v) => (int) $v, $arr);
        return array_values(array_unique(array_filter($arr, fn ($v) => $v > 0)));
    }
}
