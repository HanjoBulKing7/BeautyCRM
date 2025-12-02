<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\Servicio;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'dashboard');
        
        // Para la pestaña de ventas
        if ($tab === 'ventas') {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $empleadoId = $request->input('empleado_id');
            
            $ventas = Venta::with(['cliente', 'servicio', 'empleado'])
                ->whereBetween('fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->when($empleadoId, function($query, $empleadoId) {
                    return $query->where('id_empleado', $empleadoId);
                })
                ->orderBy('fecha_venta', 'desc')
                ->get();
            
            $empleados = User::where('role_id', 2)->get(); // Empleados
            
            return view('admin.reportes.index', compact(
                'tab', 'ventas', 'empleados', 'fechaInicio', 'fechaFin'
            ));
        }
        
        // ... resto de tu código existente para otras pestañas ...
        
        return view('admin.reportes.index', compact('tab'));
    }
    
    public function exportarReporte($tipo, Request $request)
    {
        if ($tipo === 'ventas') {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));
            
            $ventas = Venta::with(['cliente', 'servicio', 'empleado'])
                ->whereBetween('fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->orderBy('fecha_venta', 'desc')
                ->get();
            
            $filename = "reporte_ventas_{$fechaInicio}_a_{$fechaFin}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\""
            ];
            
            return response()->stream(function() use ($ventas) {
                $file = fopen('php://output', 'w');
                
                // Encabezados
                fputcsv($file, ['Fecha', 'Hora', 'Cliente', 'Servicio', 'Empleado', 'Subtotal', 'Descuento', 'Total', 'Forma Pago', 'Estado']);
                
                // Datos
                foreach ($ventas as $venta) {
                    fputcsv($file, [
                        $venta->fecha_venta->format('d/m/Y'),
                        $venta->fecha_venta->format('H:i'),
                        $venta->cliente->nombre . ' ' . $venta->cliente->apellido,
                        $venta->servicio->nombre_servicio,
                        $venta->empleado->nombre,
                        $venta->subtotal,
                        $venta->descuento,
                        $venta->total,
                        $venta->forma_pago,
                        $venta->estado_venta
                    ]);
                }
                
                fclose($file);
            }, 200, $headers);
        }
        
        // ... resto de tu código existente para otros tipos de exportación ...
        
        return back()->with('error', 'Tipo de reporte no válido');
    }
}