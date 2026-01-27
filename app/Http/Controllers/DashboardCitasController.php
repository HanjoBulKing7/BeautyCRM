<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardCitasController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Seguridad: admin (3) y empleado (2) pueden entrar al dashboard CRM
        if (!Auth::check() || !in_array((int)Auth::user()->role_id, [2, 3], true)) {
            return redirect('/login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // ✅ Fecha seleccionada (GET ?fecha=YYYY-MM-DD)
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))->toDateString()
            : now()->toDateString();

        $prevDate  = Carbon::parse($fecha)->subDay()->toDateString();
        $nextDate  = Carbon::parse($fecha)->addDay()->toDateString();
        $todayDate = now()->toDateString();

        // =========================
        // MÉTRICAS (cards)
        // =========================
        $citasDia = DB::table('citas')
            ->whereDate('fecha_cita', $fecha)
            ->count();

        $confirmadas = DB::table('citas')
            ->whereDate('fecha_cita', $fecha)
            ->where('estado_cita', 'confirmada')
            ->count();

        $completadas = DB::table('citas')
            ->whereDate('fecha_cita', $fecha)
            ->where('estado_cita', 'completada')
            ->count();

        // "Generado": suma de ventas pagadas del día (por fecha_venta)
        $generado = DB::table('ventas')
            ->whereDate('fecha_venta', $fecha)
            ->where('estado_venta', 'pagada')
            ->sum('total');

        // =========================
        // LISTADO: citas del día
        // =========================

        // venta_total por cita (subquery)
        $ventasPorCita = DB::table('ventas')
            ->select('id_cita', DB::raw("SUM(CASE WHEN estado_venta != 'cancelada' THEN total ELSE 0 END) AS venta_total"))
            ->groupBy('id_cita');

        // ✅ OJO: columnas correctas en citas: cliente_id, empleado_id
        // ✅ YA NO EXISTE c.id_servicio (servicios vienen del pivot)
        $citas = DB::table('citas as c')
            ->join('clientes as cl', 'cl.id', '=', 'c.cliente_id')
            ->leftJoin('empleados as e', 'e.id', '=', 'c.empleado_id')
            ->leftJoinSub($ventasPorCita, 'vp', function ($join) {
                $join->on('vp.id_cita', '=', 'c.id_cita');
            })
            ->whereDate('c.fecha_cita', $fecha)
            ->orderBy('c.hora_cita')
            ->select([
                'c.id_cita',
                'c.fecha_cita',
                'c.hora_cita',
                'c.descuento',
                'c.estado_cita',

                'cl.nombre as cliente_nombre',
                'cl.email  as cliente_email',

                DB::raw("CONCAT(COALESCE(e.nombre,''),' ',COALESCE(e.apellido,'')) as empleado_nombre"),
                DB::raw("COALESCE(vp.venta_total, 0) as venta_total"),
            ])
            ->get();

        // 2) Resumen por pivot (multi-servicio) agrupado por cita
        $ids = $citas->pluck('id_cita')->all();

        $serviciosPivot = collect();
        if (!empty($ids)) {
            $serviciosPivot = DB::table('cita_servicio as cs')
                ->join('servicios as s', 's.id_servicio', '=', 'cs.id_servicio')
                ->whereIn('cs.id_cita', $ids)
                ->groupBy('cs.id_cita')
                ->select([
                    'cs.id_cita',
                    DB::raw("GROUP_CONCAT(s.nombre_servicio ORDER BY s.nombre_servicio SEPARATOR ', ') as servicios_label"),
                    DB::raw("SUM(COALESCE(cs.precio_snapshot, s.precio)) as servicios_total"),
                ])
                ->get()
                ->keyBy('id_cita');
        }

        // 3) Enriquecemos para el Blade: servicios_label + servicios_total_final
        $citas = $citas->map(function ($cita) use ($serviciosPivot) {
            $pivot = $serviciosPivot->get($cita->id_cita);

            $label = $pivot?->servicios_label ?? '—';
            $total = (float) ($pivot?->servicios_total ?? 0);

            $descuento = (float) ($cita->descuento ?? 0);
            $totalFinal = max(0, $total - $descuento);

            $cita->servicios_label = $label;
            $cita->servicios_total = $totalFinal;

            // limpia espacios doble en empleado
            $cita->empleado_nombre = trim((string)$cita->empleado_nombre) ?: '—';

            return $cita;
        });

        return view('admin.dashboard', [
            'fecha'       => Carbon::parse($fecha),
            'prevDate'    => $prevDate,
            'nextDate'    => $nextDate,
            'todayDate'   => $todayDate,

            'generado'    => (float) $generado,
            'citasDia'    => (int) $citasDia,
            'confirmadas' => (int) $confirmadas,
            'completadas' => (int) $completadas,

            'citas'       => $citas,
        ]);
    }
}
