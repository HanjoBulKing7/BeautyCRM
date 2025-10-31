@extends('layouts.app')

@section('title', 'Reportes - CRM')

@section('page-title', 'Generar Reportes')

@section('content')
<div class="bg-white p-4 md:p-6 rounded-lg shadow">
    <!-- Encabezado con título -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mb-2 md:mb-0">
            <i class="fas fa-chart-line mr-3 text-yellow-500"></i>
            Reportes
        </h3>
        
        <div class="flex items-center gap-2">
            <!-- Selector de fecha -->
            <input type="date" id="fecha-reporte" value="{{ $fecha }}" 
                   class="border rounded-lg p-2 text-sm">
            
            <!-- Selector de sucursal (solo para administradores) -->
            @auth
                @if(auth()->user()->rol === 'admin')
                <select id="sucursal_id" class="border rounded-lg p-2 text-sm">
                    <option value="">Todas las sucursales</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ $sucursal_id == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
                @else
                    <!-- Mostrar nombre de sucursal para usuarios no administradores -->
                    <div class="bg-gray-100 px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-store mr-1 text-blue-500"></i>
                        {{ auth()->user()->sucursal->nombre ?? 'Sucursal' }}
                    </div>
                @endif
            @endauth
            
            <button onclick="actualizarReporte()" class="bg-blue-500 text-white px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-sync-alt mr-1"></i> Actualizar
            </button>
        </div>
    </div>

    <!-- Selector de tipo de reporte -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <div class="grid grid-cols-2 gap-4">
            <!-- Reporte Diario -->
            <a href="{{ route('reportes.index', array_merge(['tipo' => 'diario', 'fecha' => $fecha], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}"
               class="p-4 rounded-lg flex flex-col items-center justify-center transition duration-200 {{ $tipo == 'diario' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 border' }}">
                <i class="fas fa-calendar-day text-3xl mb-2"></i>
                <span class="text-lg font-semibold">Reporte Diario</span>
                <small class="{{ $tipo == 'diario' ? 'text-green-100' : 'text-gray-500' }} mt-1 text-center">
                    Resumen del día seleccionado
                </small>
            </a>
            
            <!-- Reporte Semanal -->
            <a href="{{ route('reportes.index', array_merge(['tipo' => 'semanal', 'fecha' => $fecha], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}"
               class="p-4 rounded-lg flex flex-col items-center justify-center transition duration-200 {{ $tipo == 'semanal' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                <i class="fas fa-calendar-week text-3xl mb-2"></i>
                <span class="text-lg font-semibold">Reporte Semanal</span>
                <small class="{{ $tipo == 'semanal' ? 'text-blue-100' : 'text-gray-500' }} mt-1 text-center">
                    Resumen de la semana
                </small>
            </a>
        </div>
    </div>

    <!-- Contenido del reporte -->
    <div class="bg-white p-4 rounded-lg border">
        @if($tipo == 'diario')
            <!-- Reporte Diario -->
            <h4 class="font-semibold text-lg mb-4 flex items-center">
                <i class="fas fa-calendar-day text-green-500 mr-2"></i>
                Reporte Diario - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                @if($sucursal_id && $sucursales->where('id', $sucursal_id)->first())
                <span class="ml-4 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                    Sucursal: {{ $sucursales->where('id', $sucursal_id)->first()->nombre }}
                </span>
                @endif
            </h4>
            
            @if($datos && $datos['ventas']->total_ventas > 0)
            <!-- Estadísticas principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-green-600 font-bold text-2xl mb-1">{{ $datos['ventas']->total_ventas ?? 0 }}</div>
                    <div class="text-green-700">Ventas Totales</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-blue-600 font-bold text-2xl mb-1">${{ number_format($datos['ventas']->monto_total ?? 0, 2) }}</div>
                    <div class="text-blue-700">Monto Total</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="text-purple-600 font-bold text-2xl mb-1">${{ number_format($datos['ventas']->promedio_venta ?? 0, 2) }}</div>
                    <div class="text-purple-700">Promedio por Venta</div>
                </div>
            </div>

            <!-- Tablas de resumen -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Ventas por Ruta -->
                <div class="bg-white p-4 rounded-lg border mb-6">
                    <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-route text-indigo-500 mr-2"></i>
                        Ventas por Ruta
                    </h5>
                    <div class="grid grid-cols-1 md:grid-rows-2 gap-4">
                        @forelse($datos['rutas'] as $nombreRuta => $datosRuta)
                            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                                <h6 class="font-semibold text-indigo-700 mb-2 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    {{ $nombreRuta }}
                                </h6>
                                <div class="space-x-2">
                                    <div class="flex justify-between">
                                        <span>Productos Vendidos:</span>
                                        <span class="font-semibold">{{ $datosRuta['productos'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Monto Total:</span>
                                        <span class="font-semibold">${{ number_format($datosRuta['monto'] ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-4 text-gray-500">
                                <i class="fas fa-route text-3xl mb-2"></i>
                                <p>No hay ventas por ruta para esta fecha</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <!-- FIN "Ventas por Ruta" -->
                
                <!-- Resumen de ventas -->
                <div class="bg-white p-4 rounded-lg border">
                    <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                        Resumen de Ventas
                    </h5>
                    <div class="space-y-2">
                        @php
                            $efectivo = $datos['metodosPago']->firstWhere('metodo_pago', 'efectivo')->total_metodo ?? 0;
                            $transferencia = $datos['metodosPago']->firstWhere('metodo_pago', 'transferencia')->total_metodo ?? 0;
                            $tarjeta = $datos['metodosPago']->firstWhere('metodo_pago', 'tarjeta')->total_metodo ?? 0;
                            $multipago = $datos['metodosPago']->firstWhere('metodo_pago', 'multipago')->total_metodo ?? 0;
                        @endphp
                        <div class="flex justify-between">
                            <span>Efectivo:</span>
                            <span class="font-semibold">${{ number_format($efectivo, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Transferencia:</span>
                            <span class="font-semibold">${{ number_format($transferencia, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tarjeta:</span>
                            <span class="font-semibold">${{ number_format($tarjeta, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Multipago:</span>
                            <span class="font-semibold">${{ number_format($multipago, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span>Gastos:</span>
                            <span class="font-semibold text-red-600">${{ number_format($datos['gastos'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detalles de transferencias -->
            @if($transferencia > 0)
            <div class="bg-white p-4 rounded-lg border mb-6">
                <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-exchange-alt text-purple-500 mr-2"></i>
                    Detalles de Transferencias
                </h5>
                <div class="space-y-2">
                    @foreach($datos['transferencias'] as $transferenciaItem)
                    <div class="flex justify-between">
                        <span>A {{ $transferenciaItem->destinatario_transferencia }}:</span>
                        <span class="font-semibold">${{ number_format($transferenciaItem->total_transferencia, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span>Total Transferencias:</span>
                        <span class="font-semibold">${{ number_format($transferencia, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p>No hay datos para la fecha seleccionada</p>
            </div>
            @endif

        @else
            <!-- Reporte Semanal -->
            @php
                $fechaInicio = \Carbon\Carbon::parse($fecha);
                $fechaFin = $fechaInicio->copy()->addDays(6);
            @endphp
            
            <h4 class="font-semibold text-lg mb-4 flex items-center">
                <i class="fas fa-calendar-week text-blue-500 mr-2"></i>
                Reporte Semanal - 
                {{ $fechaInicio->format('d/m/Y') }} 
                al 
                {{ $fechaFin->format('d/m/Y') }}
                @if($sucursal_id && $sucursales->where('id', $sucursal_id)->first())
                <span class="ml-4 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                    Sucursal: {{ $sucursales->where('id', $sucursal_id)->first()->nombre }}
                </span>
                @endif
            </h4>
            
            @if($datos && count($datos['ventas_por_dia']) > 0)
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Fecha</th>
                            <th class="px-4 py-2 text-left">Día</th>
                            <th class="px-4 py-2 text-left">Ventas</th>
                            <th class="px-4 py-2 text-left">Monto Total</th>
                            <th class="px-4 py-2 text-left">Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datos['ventas_por_dia'] as $dia)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($dia->fecha_venta)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $dia->dia_semana }}</td>
                            <td class="px-4 py-3">{{ $dia->total_ventas }}</td>
                            <td class="px-4 py-3">${{ number_format($dia->monto_total, 2) }}</td>
                            <td class="px-4 py-3">${{ number_format($dia->promedio_venta, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ventas por Ruta - Resumen Semanal -->
            <div class="bg-white p-4 rounded-lg border mb-6">
                <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-route text-indigo-500 mr-2"></i>
                    Ventas por Ruta - Resumen Semanal
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($datos['rutas'] as $nombreRuta => $datosRuta)
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <h6 class="font-semibold text-indigo-700 mb-2 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                {{ $nombreRuta }}
                            </h6>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Productos Vendidos:</span>
                                    <span class="font-semibold">{{ $datosRuta['productos'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Monto Total:</span>
                                    <span class="font-semibold">${{ number_format($datosRuta['monto'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-4 text-gray-500">
                            <i class="fas fa-route text-3xl mb-2"></i>
                            <p>No hay ventas por ruta para esta semana</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <!-- FIN Ventas por Ruta -->
            
            <!-- Total de la semana -->
            @php
                $totalVentasSemana = $datos['ventas_por_dia']->sum('total_ventas');
                $montoTotalSemana = $datos['ventas_por_dia']->sum('monto_total');
                $promedioVentaSemana = $montoTotalSemana / max($totalVentasSemana, 1);
            @endphp
            <div class="flex justify-between bg-white p-4 rounded-lg border mb-6">
                <h5 class="font-semibold text-gray-700 mb-3 flex items-center space-y-2">
                    <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                    Total de la Semana
                </h5>
                <div class="flex ml-2 space-y-2">
                    <span class="font-bold">Monto Total: ${{ number_format($montoTotalSemana, 2) }}</span>
                </div>
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p>No hay datos para la semana seleccionada</p>
            </div>
            @endif
        @endif
    </div>
</div>

<script>
function actualizarReporte() {
    const fecha = document.getElementById('fecha-reporte').value;
    const tipo = '{{ $tipo }}';
    const url = new URL(window.location.href);
    
    url.searchParams.set('fecha', fecha);
    url.searchParams.set('tipo', tipo);
    
    // Agregar sucursal si es administrador y hay selector de sucursal
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect) {
        if (sucursalSelect.value) {
            url.searchParams.set('sucursal_id', sucursalSelect.value);
        } else {
            url.searchParams.delete('sucursal_id');
        }
    }
    
    window.location.href = url.toString();
}
</script>
@endsection