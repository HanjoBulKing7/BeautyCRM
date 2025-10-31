<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Sucursal;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'sometimes|in:diario,semanal,mensual',
            'fecha' => 'sometimes|date',
            'sucursal_id' => 'sometimes|nullable|exists:sucursales,id'
        ]);
        
        $tipo = $validated['tipo'] ?? 'diario';
        $fecha = $validated['fecha'] ?? now()->format('Y-m-d');
        $sucursal_id = $validated['sucursal_id'] ?? null;
        
        // Obtener lista de sucursales
        $sucursales = Sucursal::orderBy('nombre')->get();
        
        // Si no es administrador, usar su sucursal asignada
        if (Auth::user()->rol !== 'admin') {
            $sucursal_id = Auth::user()->sucursal_id;
        }
        
        // Inicializar variables
        $datos = [];
        $semanas = [];
        
        if ($tipo === 'diario') {
            $datos = $this->obtenerReporteDiario($fecha, $sucursal_id);
        } elseif ($tipo === 'semanal') {
            $datos = $this->obtenerReporteSemanal($fecha, $sucursal_id);
            $semanas = $this->generarSemanas(); 
        } elseif ($tipo === 'mensual') {
            $datos = $this->obtenerReporteMensual($fecha, $sucursal_id);
        }
        
        return view('reportes.index', compact('tipo', 'fecha', 'datos', 'sucursal_id', 'sucursales', 'semanas'));
    }

    private function obtenerReporteDiario($fecha, $sucursal_id = null)
    {
        \Log::info("=== INICIANDO REPORTE DIARIO ===");
        \Log::info("Fecha: " . $fecha);
        \Log::info("Sucursal ID: " . $sucursal_id);
        
        $ventasQuery = DB::table('ventas');
        
        // Filtrar por sucursal si está especificada
        if ($sucursal_id) {
            $ventasQuery->where('sucursal_id', $sucursal_id);
        }
        
        // DEBUG: Verificar las ventas que encuentra
        $ventasDebug = $ventasQuery->whereDate('fecha', $fecha)->get();
        \Log::info("Ventas encontradas: " . $ventasDebug->count());
        foreach($ventasDebug as $venta) {
            \Log::info("Venta ID: " . $venta->id . " - Fecha: " . $venta->fecha . " - Total: " . $venta->total);
        }
        
        $ventas = $ventasQuery
            ->select(
                DB::raw('COUNT(*) as total_ventas'),
                DB::raw('COALESCE(SUM(total), 0) as monto_total'),
                DB::raw('COALESCE(AVG(total), 0) as promedio_venta'),
                DB::raw('COALESCE(SUM(subtotal), 0) as subtotal_total'),
                DB::raw('COALESCE(SUM(descuento), 0) as descuento_total'),
                DB::raw('COALESCE(SUM(impuestos), 0) as impuestos_total')
            )
            ->where('fecha', '>=', $fecha . ' 00:00:00')
            ->where('fecha', '<=', $fecha . ' 23:59:59')
            ->first();
        
        \Log::info("Resultado consulta ventas:");
        \Log::info("Total ventas: " . ($ventas->total_ventas ?? 0));
        \Log::info("Monto total: " . ($ventas->monto_total ?? 0));
        
        // Consulta para artículos vendidos con filtro de sucursal
        $articulosQuery = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id');
            
        if ($sucursal_id) {
            $articulosQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $articulos = $articulosQuery
            ->select(
                DB::raw('COALESCE(SUM(venta_detalles.cantidad), 0) as total_articulos'),
                DB::raw('COUNT(DISTINCT venta_detalles.producto_id) as productos_diferentes')
            )
            ->where('ventas.fecha', '>=', $fecha . ' 00:00:00')
            ->where('ventas.fecha', '<=', $fecha . ' 23:59:59')
            ->first();
        
        // Consulta para métodos de pago con filtro de sucursal
        $metodosPagoQuery = DB::table('pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id');
            
        if ($sucursal_id) {
            $metodosPagoQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $metodosPago = $metodosPagoQuery
            ->select(
                'pagos.metodo_pago',
                DB::raw('COALESCE(SUM(pagos.monto), 0) as total_metodo')
            )
            ->where('pagos.estado', 'completado')
            ->where('ventas.fecha', '>=', $fecha . ' 00:00:00')
            ->where('ventas.fecha', '<=', $fecha . ' 23:59:59')
            ->groupBy('pagos.metodo_pago')
            ->get();
        
        // Consulta para transferencias con filtro de sucursal
        $transferenciasQuery = DB::table('pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id');
            
        if ($sucursal_id) {
            $transferenciasQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $transferencias = $transferenciasQuery
            ->select(
                'pagos.destinatario_transferencia',
                DB::raw('COALESCE(SUM(pagos.monto), 0) as total_transferencia')
            )
            ->where('pagos.metodo_pago', 'transferencia')
            ->where('pagos.estado', 'completado')
            ->where('ventas.fecha', '>=', $fecha . ' 00:00:00')
            ->where('ventas.fecha', '<=', $fecha . ' 23:59:59')
            ->whereNotNull('pagos.destinatario_transferencia')
            ->groupBy('pagos.destinatario_transferencia')
            ->get();
        
        // Gastos con filtro de sucursal
        try {
            $gastosQuery = DB::table('gastos');
            
            if ($sucursal_id) {
                $gastosQuery->where('sucursal_id', $sucursal_id);
            }
            
            $gastos = $gastosQuery
                ->select(DB::raw('COALESCE(SUM(monto), 0) as total_gastos'))
                ->whereDate('fecha', $fecha)
                ->first();
            $gastosTotal = $gastos->total_gastos ?? 0;
        } catch (\Exception $e) {
            $gastosTotal = 0;
        }


 // === NUEVO: Estadísticas de Rutas ===
    $rutasQuery = DB::table('rutas')
        ->join('users', 'rutas.empleado_id', '=', 'users.id');
    
    if ($sucursal_id) {
        $rutasQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $estadisticasRutas = $rutasQuery
        ->select(
            DB::raw('COUNT(DISTINCT rutas.id) as total_rutas'),
            DB::raw('COUNT(DISTINCT rutas.empleado_id) as empleados_activos'),
            DB::raw('COALESCE(SUM(rutas.total_venta), 0) as ventas_rutas')
        )
        ->whereDate('rutas.fecha', $fecha)
        ->first();

    // Detalles de rutas por empleado
    $rutasPorEmpleadoQuery = DB::table('rutas')
        ->join('users', 'rutas.empleado_id', '=', 'users.id')
        ->leftJoin('ruta_detalles', 'rutas.id', '=', 'ruta_detalles.ruta_id');
    
    if ($sucursal_id) {
        $rutasPorEmpleadoQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $rutasPorEmpleado = $rutasPorEmpleadoQuery
        ->select(
            'users.nombre as empleado',
            DB::raw('COUNT(DISTINCT rutas.id) as total_rutas'),
            DB::raw('COALESCE(SUM(ruta_detalles.ventas), 0) as total_ventas_unidades'),
            DB::raw('COALESCE(SUM(ruta_detalles.total), 0) as total_ventas_monto'),
            DB::raw('COALESCE(SUM(ruta_detalles.devoluciones), 0) as total_devoluciones')
        )
        ->whereDate('rutas.fecha', $fecha)
        ->groupBy('users.id', 'users.nombre')
        ->get();

    // Productos más vendidos en rutas
    $productosRutasQuery = DB::table('ruta_detalles')
        ->join('rutas', 'ruta_detalles.ruta_id', '=', 'rutas.id')
        ->join('productos', 'ruta_detalles.producto_id', '=', 'productos.id')
        ->join('users', 'rutas.empleado_id', '=', 'users.id');


                \Log::info("=== FIN REPORTE DIARIO ===");

    
    if ($sucursal_id) {
        $productosRutasQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $productosRutas = $productosRutasQuery
        ->select(
            'productos.nombre as producto',
            DB::raw('SUM(ruta_detalles.ventas) as total_vendido'),
            DB::raw('SUM(ruta_detalles.devoluciones) as total_devoluciones'),
            DB::raw('COALESCE(SUM(ruta_detalles.total), 0) as monto_total')
        )
        ->whereDate('rutas.fecha', $fecha)
        ->groupBy('productos.id', 'productos.nombre')
        ->orderBy('total_vendido', 'DESC')
        ->limit(5)
        ->get();

    return [
        'ventas' => $ventas,
        'articulos' => $articulos,
        'metodosPago' => $metodosPago,
        'transferencias' => $transferencias,
        'gastos' => $gastosTotal,
        // === NUEVO: Datos de Rutas ===
        'rutas' => [
            'estadisticas' => $estadisticasRutas,
            'por_empleado' => $rutasPorEmpleado,
            'productos_top' => $productosRutas
        ]
    ];
        
    }
        
    private function obtenerReporteSemanal($fecha, $sucursal_id = null)
    {
        $fechaInicio = Carbon::parse($fecha);
        $fechaFin = $fechaInicio->copy()->addDays(6);

        $ventasPorDiaQuery = DB::table('ventas');
        
        // Filtrar por sucursal si está especificada
        if ($sucursal_id) {
            $ventasPorDiaQuery->where('sucursal_id', $sucursal_id);
        }
        
        // Configurar Carbon en español
        Carbon::setLocale('es');
        
        $ventasPorDia = $ventasPorDiaQuery
            ->select(
                DB::raw('COUNT(*) as total_ventas'),
                DB::raw('COALESCE(SUM(total), 0) as monto_total'),
                DB::raw('COALESCE(AVG(total), 0) as promedio_venta'),
                DB::raw('DATE(fecha) as fecha_venta')
            )
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('fecha_venta')
            ->get()
            ->map(function ($item) {
                $carbonDate = Carbon::parse($item->fecha_venta);
                // Obtener el nombre del día en español
                $item->dia_semana = $carbonDate->translatedFormat('l');
                return $item;
            });

        // Obtener gastos totales de la semana
        $gastosQuery = DB::table('gastos');
        if ($sucursal_id) {
            $gastosQuery->where('sucursal_id', $sucursal_id);
        }
        $gastos = $gastosQuery
            ->select(DB::raw('COALESCE(SUM(monto), 0) as total_gastos'))
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->first();
        $gastosTotal = $gastos->total_gastos ?? 0;

        $semanas = $this->generarSemanas();

         // === NUEVO: Estadísticas de Rutas Semanales ===
    $rutasSemanalesQuery = DB::table('rutas')
        ->join('users', 'rutas.empleado_id', '=', 'users.id');
    
    if ($sucursal_id) {
        $rutasSemanalesQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $estadisticasRutasSemanales = $rutasSemanalesQuery
        ->select(
            DB::raw('COUNT(DISTINCT rutas.id) as total_rutas'),
            DB::raw('COUNT(DISTINCT rutas.empleado_id) as empleados_activos'),
            DB::raw('COALESCE(SUM(rutas.total_venta), 0) as ventas_rutas'),
            DB::raw('COALESCE(SUM(ruta_detalles.ventas), 0) as total_unidades_vendidas'),
            DB::raw('COALESCE(SUM(ruta_detalles.devoluciones), 0) as total_devoluciones')
        )
        ->leftJoin('ruta_detalles', 'rutas.id', '=', 'ruta_detalles.ruta_id')
        ->whereBetween('rutas.fecha', [$fechaInicio, $fechaFin])
        ->first();

    // Rutas por día de la semana
    $rutasPorDiaQuery = DB::table('rutas')
        ->join('users', 'rutas.empleado_id', '=', 'users.id');
    
    if ($sucursal_id) {
        $rutasPorDiaQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $rutasPorDia = $rutasPorDiaQuery
        ->select(
            DB::raw('DATE(rutas.fecha) as fecha'),
            DB::raw('COUNT(DISTINCT rutas.id) as total_rutas'),
            DB::raw('COUNT(DISTINCT rutas.empleado_id) as empleados_activos'),
            DB::raw('COALESCE(SUM(rutas.total_venta), 0) as ventas_totales')
        )
        ->whereBetween('rutas.fecha', [$fechaInicio, $fechaFin])
        ->groupBy(DB::raw('DATE(rutas.fecha)'))
        ->orderBy('fecha')
        ->get()
        ->map(function ($item) {
            $carbonDate = Carbon::parse($item->fecha);
            $item->dia_semana = $carbonDate->translatedFormat('l');
            return $item;
        });

    return [
        'ventas_por_dia' => $ventasPorDia,
        'semanas' => $semanas,
        'gastos' => $gastosTotal,
        // === NUEVO: Datos de Rutas Semanales ===
        'rutas_semanales' => [
            'estadisticas' => $estadisticasRutasSemanales,
            'por_dia' => $rutasPorDia
        ]
    ];
    
    }

    // Método para generar las semanas
    private function generarSemanas($numeroSemanas = 12)
    {
        $semanas = [];
        $fechaActual = Carbon::now();
        
        for ($i = 0; $i < $numeroSemanas; $i++) {
            $fechaInicioSemana = $fechaActual->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $fechaFinSemana = $fechaInicioSemana->copy()->addDays(6);
            
            $semanas[] = [
                'numero' => $numeroSemanas - $i,
                'inicio' => $fechaInicioSemana->format('Y-m-d'),
                'fin' => $fechaFinSemana->format('Y-m-d'),
                'rango' => $fechaInicioSemana->format('d/m') . ' - ' . $fechaFinSemana->format('d/m/Y')
            ];
        }
        
        return array_reverse($semanas);
    }

    private function obtenerReporteMensual($fecha, $sucursal_id = null)
    {
        $fechaInicio = Carbon::parse($fecha)->startOfMonth();
        $fechaFin = Carbon::parse($fecha)->endOfMonth();

        // Resumen mensual general
        $resumenMensualQuery = DB::table('ventas');
        
        if ($sucursal_id) {
            $resumenMensualQuery->where('sucursal_id', $sucursal_id);
        }
        
        $resumenMensual = $resumenMensualQuery
            ->select(
                DB::raw('COUNT(*) as total_ventas'),
                DB::raw('COALESCE(SUM(total), 0) as monto_total'),
                DB::raw('COALESCE(AVG(total), 0) as promedio_venta'),
                DB::raw('COUNT(DISTINCT DATE(fecha)) as dias_con_ventas')
            )
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->first();

        // Calcular promedio diario
        $resumenMensual->promedio_diario = $resumenMensual->monto_total / max($resumenMensual->dias_con_ventas, 1);

        // Gastos del mes
        try {
            $gastosQuery = DB::table('gastos');
            
            if ($sucursal_id) {
                $gastosQuery->where('sucursal_id', $sucursal_id);
            }
            
            $gastos = $gastosQuery
                ->select(DB::raw('COALESCE(SUM(monto), 0) as total_gastos'))
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->first();
            $totalGastos = $gastos->total_gastos ?? 0;
        } catch (\Exception $e) {
            $totalGastos = 0;
        }

        $resumenMensual->total_gastos = $totalGastos;

        // Métodos de pago del mes
        $metodosPagoMensualQuery = DB::table('pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id');
            
        if ($sucursal_id) {
            $metodosPagoMensualQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $metodosPago = $metodosPagoMensualQuery
            ->select(
                'pagos.metodo_pago',
                DB::raw('COALESCE(SUM(pagos.monto), 0) as total_metodo')
            )
            ->where('pagos.estado', 'completado')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->groupBy('pagos.metodo_pago')
            ->get();

        // Organizar métodos de pago en objeto
        $metodosPagoMensual = (object)[
            'efectivo' => 0,
            'transferencia' => 0,
            'tarjeta' => 0,
            'multipago' => 0
        ];

        foreach ($metodosPago as $metodo) {
            if (property_exists($metodosPagoMensual, $metodo->metodo_pago)) {
                $metodosPagoMensual->{$metodo->metodo_pago} = $metodo->total_metodo;
            }
        }

        // Semanas del mes
        $semanasDelMes = DB::table('ventas')
            ->select(
                DB::raw('WEEK(fecha, 1) - WEEK(DATE_SUB(fecha, INTERVAL DAYOFMONTH(fecha) - 1 DAY), 1) + 1 as semana'),
                DB::raw('COUNT(*) as total_ventas'),
                DB::raw('COALESCE(SUM(total), 0) as monto_total'),
                DB::raw('COALESCE(AVG(total), 0) as promedio_venta')
            )
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy(DB::raw('WEEK(fecha, 1) - WEEK(DATE_SUB(fecha, INTERVAL DAYOFMONTH(fecha) - 1 DAY), 1) + 1'))
            ->orderBy('semana')
            ->get();

        // Agregar nombre de semana
        $semanasDelMes = $semanasDelMes->map(function($semana) {
            $semana->semana_nombre = "Semana " . $semana->semana;
            return $semana;
        });

        // Calcular porcentaje del mes para cada semana
        foreach ($semanasDelMes as $semana) {
            if ($resumenMensual->monto_total > 0) {
                $semana->porcentaje_mes = ($semana->monto_total / $resumenMensual->monto_total) * 100;
            } else {
                $semana->porcentaje_mes = 0;
            }
        }

        // Mejores días del mes (top 3)
        $mejoresDiasQuery = DB::table('ventas');
        
        if ($sucursal_id) {
            $mejoresDiasQuery->where('sucursal_id', $sucursal_id);
        }
        
        $mejoresDias = $mejoresDiasQuery
            ->select(
                DB::raw('DATE(fecha) as fecha'),
                DB::raw('COUNT(*) as total_ventas'),
                DB::raw('COALESCE(SUM(total), 0) as monto_total')
            )
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('monto_total', 'DESC')
            ->limit(3)
            ->get();

      // === NUEVO: Estadísticas de Rutas Mensuales ===
    $rutasMensualesQuery = DB::table('rutas')
        ->join('users', 'rutas.empleado_id', '=', 'users.id');
    
    if ($sucursal_id) {
        $rutasMensualesQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $estadisticasRutasMensuales = $rutasMensualesQuery
        ->select(
            DB::raw('COUNT(DISTINCT rutas.id) as total_rutas'),
            DB::raw('COUNT(DISTINCT rutas.empleado_id) as empleados_activos'),
            DB::raw('COALESCE(SUM(rutas.total_venta), 0) as ventas_rutas'),
            DB::raw('COALESCE(SUM(ruta_detalles.ventas), 0) as total_unidades_vendidas'),
            DB::raw('COALESCE(SUM(ruta_detalles.devoluciones), 0) as total_devoluciones'),
            DB::raw('COUNT(DISTINCT DATE(rutas.fecha)) as dias_con_rutas')
        )
        ->leftJoin('ruta_detalles', 'rutas.id', '=', 'ruta_detalles.ruta_id')
        ->whereBetween('rutas.fecha', [$fechaInicio, $fechaFin])
        ->first();

    // Top empleados del mes
    $topEmpleadosRutasQuery = DB::table('rutas')
        ->join('users', 'rutas.empleado_id', '=', 'users.id')
        ->leftJoin('ruta_detalles', 'rutas.id', '=', 'ruta_detalles.ruta_id');
    
    if ($sucursal_id) {
        $topEmpleadosRutasQuery->where('users.sucursal_id', $sucursal_id);
    }
    
    $topEmpleadosRutas = $topEmpleadosRutasQuery
        ->select(
            'users.nombre as empleado',
            DB::raw('COUNT(DISTINCT rutas.id) as total_rutas'),
            DB::raw('COALESCE(SUM(ruta_detalles.ventas), 0) as total_ventas_unidades'),
            DB::raw('COALESCE(SUM(ruta_detalles.total), 0) as total_ventas_monto')
        )
        ->whereBetween('rutas.fecha', [$fechaInicio, $fechaFin])
        ->groupBy('users.id', 'users.nombre')
        ->orderBy('total_ventas_monto', 'DESC')
        ->limit(5)
        ->get();

    return [
        'resumen_mensual' => $resumenMensual,
        'metodos_pago_mensual' => $metodosPagoMensual,
        'semanas_del_mes' => $semanasDelMes,
        'mejores_dias' => $mejoresDias,
        // === NUEVO: Datos de Rutas Mensuales ===
        'rutas_mensuales' => [
            'estadisticas' => $estadisticasRutasMensuales,
            'top_empleados' => $topEmpleadosRutas
        ]
    ];
    }
}