<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AgendarCitaPublicController extends Controller
{
    public function create(Request $request)
    {
        // Servicios activos (puedes agregar with('categoria') si lo usas)
        $servicios = Servicio::query()
            ->where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();

        // Servicio principal seleccionado desde query (?servicio=ID) o null
        $servicioId = (int) $request->query('servicio', 0);
        $servicioSeleccionado = $servicioId
            ? $servicios->firstWhere('id_servicio', $servicioId)
            : null;

        // Catálogo para JS (por ID)
        $serviciosJs = $servicios->mapWithKeys(function ($s) {
            return [
                $s->id_servicio => [
                    'id_servicio'       => $s->id_servicio,
                    'nombre_servicio'   => $s->nombre_servicio,
                    'precio'            => (float) $s->precio,
                    'descuento'         => (float) ($s->descuento ?? 0),
                    'duracion_minutos'  => (int) ($s->duracion_minutos ?? 0),
                    'imagen'            => $s->imagen,
                    'caracteristicas'   => $s->caracteristicas,
                ]
            ];
        });

        return view('agendarcita', [
            'servicios'            => $servicios,
            'servicioSeleccionado' => $servicioSeleccionado,
            'serviciosJs'          => $serviciosJs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_servicio'   => ['required', 'integer', 'exists:servicios,id_servicio'],
            'id_servicios'  => ['nullable', 'array'], // extras
            'id_servicios.*'=> ['integer', 'exists:servicios,id_servicio'],
            'fecha_cita'    => ['required', 'date', 'after_or_equal:today'],
            'hora_cita'     => ['required', 'date_format:H:i'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ]);

        $principalId = (int) $request->input('id_servicio');
        $extras      = collect($request->input('id_servicios', []))
            ->map(fn($v) => (int)$v)
            ->filter()
            ->values();

        // IDs finales: principal + extras (sin duplicados)
        $allIds = collect([$principalId])
            ->merge($extras)
            ->unique()
            ->values();

        // Traemos servicios desde BD
        $serviciosDb = Servicio::whereIn('id_servicio', $allIds)->get()->keyBy('id_servicio');

        // Seguridad: si algo no existe, abort
        if ($serviciosDb->count() !== $allIds->count()) {
            return back()->with('error', 'Uno o más servicios no son válidos.')->withInput();
        }

        // Total estimado / duración total (snapshots para cita_servicio)
        $total = 0.0;
        $duracionTotal = 0;

        foreach ($allIds as $sid) {
            $s = $serviciosDb[$sid];
            $precioFinal = max(0, (float)$s->precio - (float)($s->descuento ?? 0));
            $total += $precioFinal;
            $duracionTotal += (int)($s->duracion_minutos ?? 0);
        }

        return DB::transaction(function () use ($request, $principalId, $allIds, $serviciosDb, $total, $duracionTotal) {

            // Guardamos header en citas (NOTA: tu tabla exige id_servicio, usamos el principal)
            $dataCita = [
                'id_cliente'    => Auth::id(),
                'id_servicio'   => $principalId,
                'id_empleado'   => null, // público: se asigna luego
                'fecha_cita'    => $request->input('fecha_cita'),
                'hora_cita'     => $request->input('hora_cita') . ':00',
                'estado_cita'   => 'pendiente',
                'observaciones' => $request->input('observaciones'),
            ];

            // Si en tu BD ya existe columna duracion_total_minutos / total_estimado, la llenamos sin romper nada
            if (Schema::hasColumn('citas', 'duracion_total_minutos')) {
                $dataCita['duracion_total_minutos'] = max(1, (int)$duracionTotal);
            }
            if (Schema::hasColumn('citas', 'total_estimado')) {
                $dataCita['total_estimado'] = (float)$total;
            }

            $cita = Cita::create($dataCita);

            // Guardar servicios en cita_servicio con snapshots
            foreach ($allIds as $sid) {
                $s = $serviciosDb[$sid];
                $precioFinal = max(0, (float)$s->precio - (float)($s->descuento ?? 0));
                $dur = (int)($s->duracion_minutos ?? 0);

                DB::table('cita_servicio')->insert([
                    'id_cita'           => $cita->id_cita,
                    'id_servicio'       => $sid,
                    'precio_snapshot'   => $precioFinal,
                    'duracion_snapshot' => $dur,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            // ✅ NO creamos venta aquí porque ventas requiere forma_pago + total obligatorios.
            // La venta se crea cuando pague (anticipo/checkout/admin completar).

            return redirect()
                ->route('agendarcita.create')
                ->with('success', '¡Listo! Tu solicitud de cita fue registrada. Te confirmaremos disponibilidad.');
        });
    }

    /**
     * Opcional: endpoint para traer horas disponibles vía AJAX.
     * Si todavía no quieres disponibilidad real, puedes omitir este método.
     */
    public function horasDisponibles(Request $request)
    {
        // Aquí puedes reutilizar tu lógica avanzada de disponibilidad
        // (la de ServicioHorario + intersección + citas ocupadas).
        // Si quieres, en el próximo paso te la adapto limpia a este controller.

        return response()->json([]);
    }
}
