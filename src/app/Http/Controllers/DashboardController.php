<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\Existencia;
use App\Models\Cliente;
use App\Models\VentaDetalle;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Aquí después puedes mandar datos de la BD
        return view('dashboard');
    }

       public function ventasDashboard()
    {
        $sucursalId = Auth::user()->sucursal_id;
        $hoy = now();
        $inicioMes = $hoy->copy()->startOfMonth();
        
        // Estadísticas principales (manteniendo los mismos nombres de cards)
        $stats = [
            // Ventas (total de ventas del mes)
            'total_ventas' => Venta::where('sucursal_id', $sucursalId)
                                ->whereBetween('fecha', [$inicioMes, $hoy])
                                ->count(),
            
            // Gastos (total de gastos del mes)
            'total_gastos' => Gasto::where('sucursal_id', $sucursalId)
                                ->whereBetween('fecha', [$inicioMes, $hoy])
                                ->sum('monto'),
            
            // Productos (total de productos activos en la sucursal)
            'total_productos' => Producto::whereHas('existencias', function($query) use ($sucursalId) {
                                    $query->where('sucursal_id', $sucursalId);
                                })
                                ->where('activo', true)
                                ->count(),
            
            // Inventario (suma total de stock en la sucursal)
            'total_inventario' => Existencia::where('sucursal_id', $sucursalId)
                                      ->sum('stock_actual'),
            
            // Últimas ventas con detalles de productos
            'ultimas_ventas' => Venta::with(['cliente', 'detalles.producto'])
                                ->where('sucursal_id', $sucursalId)
                                ->latest('fecha')
                                ->limit(5)
                                ->get(),
            
            // Ingresos mensuales para el gráfico (últimos 6 meses)
            'ingresos_mensuales' => $this->getIngresosMensuales($sucursalId)
        ];

        return view('ventas-dashboard', compact('stats'));
    }

    private function getIngresosMensuales($sucursalId)
    {
        // Obtener ingresos de los últimos 6 meses
        $seisMesesAtras = now()->subMonths(5)->startOfMonth();
        
        $ingresos = Venta::where('sucursal_id', $sucursalId)
                    ->where('fecha', '>=', $seisMesesAtras)
                    ->select(
                        DB::raw('MONTH(fecha) as mes'),
                        DB::raw('YEAR(fecha) as anio'), // ⭐ Cambio aquí: sin ñ
                        DB::raw('SUM(total) as total')
                    )
                    ->groupBy('anio', 'mes') // ⭐ Y aquí
                    ->orderBy('anio')
                    ->orderBy('mes')
                    ->get();

        // Crear array con los últimos 6 meses
        $ingresosData = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            
            $ingreso = $ingresos->first(function ($item) use ($mes) {
                return $item->mes == $mes->month && $item->anio == $mes->year; // ⭐ Cambio aquí
            });
            
            $ingresosData[] = $ingreso ? (float) $ingreso->total : 0;
            
            // Nombres de meses en español
            $labels[] = ucfirst($mes->locale('es')->translatedFormat('M'));
        }

        return [
            'datos' => $ingresosData,
            'etiquetas' => $labels
        ];
    }
}
