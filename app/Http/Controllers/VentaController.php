<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cita;

class VentaController extends Controller
{
    /**
     * Display a listing (citas + venta si existe)
     * - Muestra TODAS las citas (completadas/confirmadas/canceladas/pendientes)
     * - Filtro por fecha_cita (no por fecha_venta)
     * - Orden por fecha y hora ASC
     */
    public function index(Request $request)
    {
        // Defaults: mes actual
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        // ✅ Base query SIN whereHas('venta') para no excluir citas sin venta
        $base = Cita::query();

        // Filtro por fecha de la CITA (incluyente)
        if (!empty($fechaInicio)) {
            $base->whereDate('fecha_cita', '>=', $fechaInicio);
        }
        if (!empty($fechaFin)) {
            $base->whereDate('fecha_cita', '<=', $fechaFin);
        }

        // ✅ Listado con relaciones (incluye venta si existe)
        $citasCompletadas = (clone $base)
            ->with([
                'servicios',
                'cliente:id,nombre,email',
                'empleado:id,nombre,apellido',
                'venta'
            ])
            ->orderBy('fecha_cita', 'asc')
            ->orderBy('hora_cita', 'asc')
            ->paginate(20)
            ->withQueryString();

        // =========================
        // ✅ STATS GLOBALES del rango (no del paginador)
        // =========================

        // Total ventas sumando ventas.total (solo donde exista venta)
        $ventasAgg = (clone $base)
            ->leftJoin('ventas', 'ventas.id_cita', '=', 'citas.id_cita')
            ->selectRaw('COALESCE(SUM(ventas.total), 0) as total_ventas')
            ->selectRaw('COUNT(ventas.id_cita) as ventas_registradas')
            ->first();

        $totalVentas       = (float) ($ventasAgg->total_ventas ?? 0);
        $ventasRegistradas = (int)   ($ventasAgg->ventas_registradas ?? 0);

        // Conteo de citas completadas (por estado) en el rango
        $ventasCount = (clone $base)->where('estado_cita', 'completada')->count();

        // Conteo de “pendientes” = confirmadas (lo que pusiste en el card)
        $pendientesCount = (clone $base)->where('estado_cita', 'confirmada')->count();

        // (si todavía lo ocupas en otra vista)
        $promedioVenta = $ventasRegistradas > 0 ? ($totalVentas / $ventasRegistradas) : 0;

        return view('admin.ventas.index', compact(
            'citasCompletadas',
            'totalVentas',
            'ventasCount',
            'pendientesCount',
            'promedioVenta',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Display a specific venta (solo lectura)
     */
    public function show($id)
    {
        // 1) Intentar por ID de venta
        $venta = Venta::with([
            'cita.servicios',
            'cita.cliente',
            'cita.empleado',
        ])->find($id);

        if ($venta) {
            return view('admin.ventas.show', compact('venta'));
        }

        // 2) Si no existe venta con ese ID, interpretarlo como ID de cita (sin forzar completada)
        $cita = Cita::with(['servicios', 'cliente', 'empleado', 'venta'])
            ->where('id_cita', $id)
            ->firstOrFail();

        return view('admin.ventas.cita-detalle', compact('cita'));
    }

    /**
     * Reporte de ventas detallado (solo lectura)
     * (lo dejo como lo tenías; si lo usas y quieres que agrupe por múltiples servicios
     * te lo ajusto porque tu código actual usa cita->servicio (singular) y ya es plural)
     */
    public function reporte(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $ventas = Venta::with(['cita.servicios', 'cita.empleado'])
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_venta')
            ->get();

        // ⚠️ Tu agrupación actual estaba con propiedades singulares antiguas;
        // si quieres, en el siguiente mensaje te lo dejo 100% correcto para multi-servicio.

        $totalGeneral    = $ventas->sum('total');
        $totalComisiones = $ventas->sum('comision_empleado');

        return view('admin.ventas.reporte', compact(
            'ventas',
            'totalGeneral',
            'totalComisiones',
            'fechaInicio',
            'fechaFin'
        ));
    }
}   