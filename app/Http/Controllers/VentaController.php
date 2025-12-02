<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\User;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VentaController extends Controller
{
    /**
     * Mostrar listado SIMPLE de ventas
     */
    public function index(Request $request)
    {
        // Fechas por defecto: mes actual
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Obtener ventas con filtro de fechas
        $ventas = Venta::with(['cliente', 'empleado', 'servicio'])
            ->whereBetween('fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->orderBy('fecha_venta', 'desc')
            ->paginate(15); // Menos registros por página para más simple

        // Métricas básicas
        $totalVentas = $ventas->sum('total');
        $ventasCount = $ventas->total();
        $promedioVenta = $ventasCount > 0 ? $totalVentas / $ventasCount : 0;

        return view('admin.ventas.index', compact(
            'ventas',
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'ventasCount',
            'promedioVenta'
        ));
    }

    /**
     * Exportar a CSV (opcional y simple)
     */
    public function exportar(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $ventas = Venta::with(['cliente', 'servicio', 'empleado'])
            ->whereBetween('fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $filename = "ventas_{$fechaInicio}_a_{$fechaFin}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        $callback = function() use ($ventas) {
            $file = fopen('php://output', 'w');
            
            // Encabezados simples
            fputcsv($file, ['Fecha', 'Cliente', 'Servicio', 'Empleado', 'Total', 'Forma Pago']);

            // Datos
            foreach ($ventas as $venta) {
                fputcsv($file, [
                    $venta->fecha_venta->format('d/m/Y H:i'),
                    $venta->cliente->nombre . ' ' . $venta->cliente->apellido,
                    $venta->servicio->nombre_servicio,
                    $venta->empleado->nombre,
                    $venta->total,
                    $venta->forma_pago
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}