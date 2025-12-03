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
     * Display a listing of ventas (solo lectura)
     */
// En VentaController.php - método index
    public function index(Request $request)
    {
        // Obtener parámetros de filtro con valores por defecto
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        // **CAMBIO IMPORTANTE**: Obtener citas completadas que tengan venta
        $query = Cita::with([
            'servicio',
            'cliente:id,name,email',
            'empleado:id,name',
            'venta' // Incluir la relación venta
        ])->where('estado_cita', 'completada');
        
        // Aplicar filtros de fecha a través de la relación venta
        if ($request->filled('fecha_inicio')) {
            $query->whereHas('venta', function($q) use ($fechaInicio) {
                $q->whereDate('fecha_venta', '>=', $fechaInicio);
            });
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereHas('venta', function($q) use ($fechaFin) {
                $q->whereDate('fecha_venta', '<=', $fechaFin);
            });
        }
        
        // Ordenar por fecha de venta más reciente
        $citasCompletadas = $query->orderByDesc('fecha_cita')
                                ->orderByDesc('hora_cita')
                                ->paginate(20);
        
        // Calcular estadísticas
        $totalVentas = 0;
        $ventasCount = 0;
        
        foreach ($citasCompletadas as $cita) {
            if ($cita->venta) {
                $totalVentas += $cita->venta->total;
                $ventasCount++;
            }
        }
        
        $promedioVenta = $ventasCount > 0 ? $totalVentas / $ventasCount : 0;
        
        return view('admin.ventas.index', compact(
            'citasCompletadas',
            'totalVentas',
            'ventasCount',
            'promedioVenta',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Display a specific venta (solo lectura)
     */
    // En VentaController.php - método show
    public function show($id)
    {
        // Buscar la venta con todas las relaciones necesarias
        $venta = Venta::with([
            'cita.servicio',
            'cita.cliente',
            'cita.empleado'
        ])->findOrFail($id);
        
        // Si no existe la venta, redirigir a la cita completada
        if (!$venta) {
            // Buscar la cita completada
            $cita = Cita::with(['servicio', 'cliente', 'empleado'])
                        ->where('id_cita', $id)
                        ->where('estado_cita', 'completada')
                        ->firstOrFail();
            
            return view('admin.ventas.cita-detalle', compact('cita'));
        }
        
        return view('admin.ventas.show', compact('venta'));
    }

    /**
     * Reporte de ventas detallado (solo lectura)
     */
    public function reporte(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        $ventas = Venta::with(['cita.servicio', 'cita.empleado'])
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_venta')
            ->get();
            
        // Agrupar por servicio
        $ventasPorServicio = $ventas->groupBy('cita.servicio.nombre_servicio')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('total'),
                    'cantidad' => $group->count(),
                    'servicio' => $group->first()->cita->servicio
                ];
            })->sortByDesc('total');
            
        // Agrupar por empleado
        $ventasPorEmpleado = $ventas->groupBy('cita.empleado.name')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('total'),
                    'cantidad' => $group->count(),
                    'comisiones' => $group->sum('comision_empleado'),
                    'empleado' => $group->first()->cita->empleado
                ];
            })->sortByDesc('total');
            
        // Totales generales
        $totalGeneral = $ventas->sum('total');
        $totalComisiones = $ventas->sum('comision_empleado');
        
        return view('admin.ventas.reporte', compact(
            'ventas',
            'ventasPorServicio',
            'ventasPorEmpleado',
            'totalGeneral',
            'totalComisiones',
            'fechaInicio',
            'fechaFin'
        ));
    }
}