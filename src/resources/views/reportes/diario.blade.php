<!-- Reporte Diario -->
<div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
    <h4 class="font-semibold text-lg mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-calendar-day text-green-500 mr-2"></i>
            Reporte Diario - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
            @if($sucursal_id && $sucursales->where('id', $sucursal_id)->first())
            <span class="ml-4 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                Sucursal: {{ $sucursales->where('id', $sucursal_id)->first()->nombre }}
            </span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <!-- Selector de fecha para día -->
            <input type="date" id="fecha-dia" value="{{ $fecha }}" 
                class="border rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
    </h4>   

    <!-- Sección de Rutas - Reporte Diario -->
    @if(isset($datos['rutas']) && $datos['rutas']['estadisticas']->total_rutas > 0)
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-route text-blue-500 mr-2"></i>
            Reporte de Rutas del Día
        </h5>

        <!-- Balance de Rutas -->
        <div class="mt-6 p-4 bg-orange-50 rounded-lg border border-orange-200 dark:bg-orange-900/20 dark:border-orange-800">
            @php
                $gastosRuta = $datos['gastos_ruta'] ?? 0;
                $ventasRuta = $datos['rutas']['estadisticas']->ventas_rutas ?? 0;
                $balanceRuta = $ventasRuta - $gastosRuta;
                $esPositivo = $balanceRuta >= 0;
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Gastos de Ruta</div>
                    <div class="text-lg font-semibold text-red-600 dark:text-red-400">
                        ${{ number_format($gastosRuta, 2) }}
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Ventas de Ruta</div>
                    <div class="text-lg font-semibold text-green-600 dark:text-green-400">
                        ${{ number_format($ventasRuta, 2) }}
                    </div>
                </div>
        
                <div class="bg-white p-3 rounded-lg border {{ $esPositivo ? 'border-green-200' : 'border-red-200' }} dark:bg-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Balance (Ventas - Gastos)</div>
                    <div class="text-xl font-bold {{ $esPositivo ? 'text-green-600' : 'text-red-600' }} dark:{{ $esPositivo ? 'text-green-400' : 'text-red-400' }}">
                        ${{ number_format($balanceRuta, 2) }}
                    </div>
                    <div class="text-xs {{ $esPositivo ? 'text-green-500' : 'text-red-500' }} dark:{{ $esPositivo ? 'text-green-400' : 'text-red-400' }}">
                        {{ $esPositivo ? 'Ganancia' : 'Pérdida' }} en rutas
                    </div>
                </div>
            </div>
        </div>

        <!-- Rutas por empleado -->
        <div class="mb-6">
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Rutas por Empleado</h6>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Empleado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Nombre Ruta</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Unidades Vendidas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Ventas Totales</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Gastos</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Balance</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Devoluciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @php
                            $totalUnidades = 0;
                            $totalVentas = 0;
                            $totalGastos = 0;
                            $totalBalance = 0;
                            $totalDevoluciones = 0;
                        @endphp
                        
                        @foreach($datos['rutas']['por_empleado'] as $ruta)
                        @php
                            $balanceRuta = $ruta->total_ventas_monto - $ruta->gastos_ruta;
                            $esPositivo = $balanceRuta >= 0;
                            
                            $totalUnidades += $ruta->total_ventas_unidades;
                            $totalVentas += $ruta->total_ventas_monto;
                            $totalGastos += $ruta->gastos_ruta;
                            $totalBalance += $balanceRuta;
                            $totalDevoluciones += $ruta->total_devoluciones;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $ruta->empleado }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $ruta->nombre_ruta ?? 'Sin nombre' }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $ruta->total_ventas_unidades }}</td>
                            <td class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format($ruta->total_ventas_monto, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm font-semibold text-red-600 dark:text-red-400">
                                ${{ number_format($ruta->gastos_ruta, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm font-semibold {{ $esPositivo ? 'text-green-600' : 'text-red-600' }} dark:{{ $esPositivo ? 'text-green-400' : 'text-red-400' }}">
                                ${{ number_format($balanceRuta, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">{{ $ruta->total_devoluciones }}</td>
                        </tr>
                        @endforeach
                        
                        <!-- Fila de totales -->
                        <tr class="bg-gray-50 font-semibold dark:bg-gray-700">
                            <td class="px-4 py-2 text-sm dark:text-gray-300" colspan="2">TOTALES</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $totalUnidades }}</td>
                            <td class="px-4 py-2 text-sm text-green-600 dark:text-green-400">
                                ${{ number_format($totalVentas, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">
                                ${{ number_format($totalGastos, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm {{ $totalBalance >= 0 ? 'text-green-600' : 'text-red-600' }} dark:{{ $totalBalance >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                ${{ number_format($totalBalance, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">{{ $totalDevoluciones }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Reporte Ventas Individuales -->
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-store text-green-500 mr-2"></i>
            Reporte Ventas Individuales
        </h5>

        @if($datos && $datos['ventas']->total_ventas > 0)
<!-- Estadísticas principales -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
        <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">${{ number_format($datos['gastos'] ?? 0, 2) }}</div>
        <div class="text-red-700 dark:text-red-300">Gastos del Día</div>
    </div>
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
        <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">${{ number_format($datos['ventas']->monto_total ?? 0, 2) }}</div>
        <div class="text-blue-700 dark:text-blue-300">Total Ventas</div>
    </div>
    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
        @php
            $balance = ($datos['ventas']->monto_total ?? 0) - ($datos['gastos'] ?? 0);
            $colorBalance = $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
        @endphp
        <div class="font-bold text-2xl mb-1 {{ $colorBalance }}">${{ number_format($balance, 2) }}</div>
        <div class="text-purple-700 dark:text-purple-300">Balance</div>
    </div>
</div>
        <!-- Resumen de Ventas -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Resumen de Ventas</h6>
        <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
            @php
                $efectivo = $datos['metodosPago']->firstWhere('metodo_pago', 'efectivo')->total_metodo ?? 0;
                $transferencia = $datos['metodosPago']->firstWhere('metodo_pago', 'transferencia')->total_metodo ?? 0;
                $tarjeta = $datos['metodosPago']->firstWhere('metodo_pago', 'tarjeta')->total_metodo ?? 0;
                $totalVentas = $datos['ventas']->monto_total ?? 0;
                $gastos = $datos['gastos'] ?? 0;
                $totalNeto = $totalVentas - $gastos;
            @endphp
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Efectivo:</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($efectivo, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Transferencia:</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">${{ number_format($transferencia, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tarjeta:</span>
                    <span class="font-semibold text-purple-600 dark:text-purple-400">${{ number_format($tarjeta, 2) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 mt-2">
                    <span class="text-gray-700 dark:text-gray-300 font-bold">Total Ventas:</span>
                    <span class="font-bold text-green-600 dark:text-green-400">${{ number_format($totalVentas, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Gastos:</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">${{ number_format($gastos, 2) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 mt-2">
                    <span class="text-gray-700 dark:text-gray-300 font-bold">Total Neto:</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400">${{ number_format($totalNeto, 2) }}</span>
                </div>
            </div>
        </div>
                </div>
                @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No hay datos para la fecha seleccionada</p>
                </div>
                @endif
            </div>
        </div>

<script>
// Cambio de fecha para reporte diario
document.getElementById('fecha-dia').addEventListener('change', function() {
    const fecha = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('fecha', fecha);
    url.searchParams.set('tipo', 'diario');
    
    // Mantener el filtro de sucursal si existe
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect && sucursalSelect.value) {
        url.searchParams.set('sucursal_id', sucursalSelect.value);
    }
    
    window.location.href = url.toString();
});
</script>