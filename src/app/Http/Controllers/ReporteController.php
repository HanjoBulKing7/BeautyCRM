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
            'tipo' => 'sometimes|in:diario,semanal',
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
        
        if ($tipo === 'diario') {
            $datos = $this->obtenerReporteDiario($fecha, $sucursal_id);
        } else {
            $datos = $this->obtenerReporteSemanal($fecha, $sucursal_id);
        }
        
        return view('reportes.index', compact('tipo', 'fecha', 'datos', 'sucursal_id', 'sucursales'));
    }
    
    private function obtenerReporteDiario($fecha, $sucursal_id = null)
    {
        $ventasQuery = DB::table('ventas');
        
        // Filtrar por sucursal si está especificada
        if ($sucursal_id) {
            $ventasQuery->where('sucursal_id', $sucursal_id);
        }
        
        $ventas = $ventasQuery
            ->select(
                DB::raw('COUNT(*) as total_ventas'),
                DB::raw('SUM(total) as monto_total'),
                DB::raw('AVG(total) as promedio_venta'),
                DB::raw('SUM(subtotal) as subtotal_total'),
                DB::raw('SUM(descuento) as descuento_total'),
                DB::raw('SUM(impuestos) as impuestos_total')
            )
            ->whereDate('fecha', $fecha)
            ->first();
        
        // Consulta para artículos vendidos con filtro de sucursal
        $articulosQuery = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id');
            
        if ($sucursal_id) {
            $articulosQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $articulos = $articulosQuery
            ->select(
                DB::raw('SUM(venta_detalles.cantidad) as total_articulos'),
                DB::raw('COUNT(DISTINCT venta_detalles.producto_id) as productos_diferentes')
            )
            ->whereDate('ventas.fecha', $fecha)
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
                DB::raw('SUM(pagos.monto) as total_metodo')
            )
            ->where('pagos.estado', 'completado')
            ->whereDate('ventas.fecha', $fecha)
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
                DB::raw('SUM(pagos.monto) as total_transferencia')
            )
            ->where('pagos.metodo_pago', 'transferencia')
            ->where('pagos.estado', 'completado')
            ->whereDate('ventas.fecha', $fecha)
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
                ->select(DB::raw('SUM(monto) as total_gastos'))
                ->whereDate('fecha', $fecha)
                ->first();
            $gastosTotal = $gastos->total_gastos ?? 0;
        } catch (\Exception $e) {
            $gastosTotal = 0;
        }
        
        // CONSULTA MODIFICADA: Ventas por Ruta en lugar de Proveedor
        $ventasPorRutaQuery = DB::table('ventas')
            ->leftJoin('rutas', 'ventas.ruta_id', '=', 'rutas.id')
            ->leftJoin('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id');
            
        if ($sucursal_id) {
            $ventasPorRutaQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $ventasPorRuta = $ventasPorRutaQuery
            ->select(
                'rutas.nombre as ruta_nombre',
                DB::raw('COALESCE(SUM(venta_detalles.cantidad), 0) as total_productos'),
                DB::raw('COALESCE(SUM(venta_detalles.precio_unitario * venta_detalles.cantidad), 0) as monto_total')
            )
            ->whereDate('ventas.fecha', $fecha)
            ->groupBy('rutas.nombre')
            ->get();
        
        // Organizar los datos por ruta
        $rutas = [];
        foreach ($ventasPorRuta as $venta) {
            if ($venta->ruta_nombre) {
                $rutas[$venta->ruta_nombre] = [
                    'productos' => $venta->total_productos,
                    'monto' => $venta->monto_total
                ];
            }
        }
        
        // También obtener ventas sin ruta asignada
        $ventasSinRutaQuery = DB::table('ventas')
            ->leftJoin('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->whereNull('ventas.ruta_id');
            
        if ($sucursal_id) {
            $ventasSinRutaQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $ventasSinRuta = $ventasSinRutaQuery
            ->select(
                DB::raw('COALESCE(SUM(venta_detalles.cantidad), 0) as total_productos'),
                DB::raw('COALESCE(SUM(venta_detalles.precio_unitario * venta_detalles.cantidad), 0) as monto_total')
            )
            ->whereDate('ventas.fecha', $fecha)
            ->first();
        
        if ($ventasSinRuta->total_productos > 0) {
            $rutas['Sin Ruta'] = [
                'productos' => $ventasSinRuta->total_productos,
                'monto' => $ventasSinRuta->monto_total
            ];
        }
        
        return [
            'ventas' => $ventas,
            'articulos' => $articulos,
            'metodosPago' => $metodosPago,
            'transferencias' => $transferencias,
            'gastos' => $gastosTotal,
            'rutas' => $rutas  // Cambiado de 'proveedores' a 'rutas'
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
        
        if (config('database.default') === 'mysql') {
            $ventasPorDia = $ventasPorDiaQuery
                ->select(
                    DB::raw('COUNT(*) as total_ventas'),
                    DB::raw('SUM(total) as monto_total'),
                    DB::raw('AVG(total) as promedio_venta'),
                    DB::raw('DATE(fecha) as fecha_venta'),
                    DB::raw('ANY_VALUE(DAYNAME(fecha)) as dia_semana')
                )
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->groupBy(DB::raw('DATE(fecha)'))
                ->orderBy('fecha_venta')
                ->get();
        } else {
            $ventasPorDia = $ventasPorDiaQuery
                ->select(
                    DB::raw('COUNT(*) as total_ventas'),
                    DB::raw('SUM(total) as monto_total'),
                    DB::raw('AVG(total) as promedio_venta'),
                    DB::raw('DATE(fecha) as fecha_venta')
                )
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->groupBy(DB::raw('DATE(fecha)'))
                ->orderBy('fecha_venta')
                ->get()
                ->map(function ($item) {
                    $carbonDate = Carbon::parse($item->fecha_venta);
                    $item->dia_semana = $carbonDate->dayName;
                    return $item;
                });
        }

        // CONSULTA MODIFICADA: Ventas por Ruta en lugar de Proveedor
        $ventasPorRutaQuery = DB::table('ventas')
            ->leftJoin('rutas', 'ventas.ruta_id', '=', 'rutas.id')
            ->leftJoin('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id');
            
        if ($sucursal_id) {
            $ventasPorRutaQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $ventasPorRuta = $ventasPorRutaQuery
            ->select(
                'rutas.nombre as ruta_nombre',
                DB::raw('COALESCE(SUM(venta_detalles.cantidad), 0) as total_productos'),
                DB::raw('COALESCE(SUM(venta_detalles.precio_unitario * venta_detalles.cantidad), 0) as monto_total')
            )
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->groupBy('rutas.nombre')
            ->get();
        
        // Organizar los datos por ruta
        $rutas = [];
        foreach ($ventasPorRuta as $venta) {
            if ($venta->ruta_nombre) {
                $rutas[$venta->ruta_nombre] = [
                    'productos' => $venta->total_productos,
                    'monto' => $venta->monto_total
                ];
            }
        }
        
        // Ventas sin ruta para la semana
        $ventasSinRutaQuery = DB::table('ventas')
            ->leftJoin('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->whereNull('ventas.ruta_id');
            
        if ($sucursal_id) {
            $ventasSinRutaQuery->where('ventas.sucursal_id', $sucursal_id);
        }
        
        $ventasSinRuta = $ventasSinRutaQuery
            ->select(
                DB::raw('COALESCE(SUM(venta_detalles.cantidad), 0) as total_productos'),
                DB::raw('COALESCE(SUM(venta_detalles.precio_unitario * venta_detalles.cantidad), 0) as monto_total')
            )
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->first();
        
        if ($ventasSinRuta->total_productos > 0) {
            $rutas['Sin Ruta'] = [
                'productos' => $ventasSinRuta->total_productos,
                'monto' => $ventasSinRuta->monto_total
            ];
        }

        return [
            'ventas_por_dia' => $ventasPorDia,
            'rutas' => $rutas  // Cambiado de 'proveedores' a 'rutas'
        ];
    }
}