<!-- Reporte Mensual -->
<div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
    @php
        $fechaInicio = \Carbon\Carbon::parse($fecha)->startOfMonth();
        $fechaFin = \Carbon\Carbon::parse($fecha)->endOfMonth();
        $mesAnterior = \Carbon\Carbon::parse($fecha)->subMonth();
        $mesSiguiente = \Carbon\Carbon::parse($fecha)->addMonth();
    @endphp

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <div class="flex items-center">
            <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
            <h4 class="font-semibold text-gray-800 dark:text-white">
                Reporte Mensual - {{ $fechaInicio->locale('es')->translatedFormat('F Y') }}
            </h4>
            @if($sucursal_id && $sucursales->where('id', $sucursal_id)->first())
            <span class="ml-4 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                Sucursal: {{ $sucursales->where('id', $sucursal_id)->first()->nombre }}
            </span>
            @endif
        </div>
        
        <div class="flex items-center gap-2 mt-2 md:mt-0">
            <!-- Selector de mes -->
            <input type="month" id="fecha-mes" value="{{ date('Y-m', strtotime($fecha)) }}" 
                   class="border rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            
            <div class="flex space-x-2">
                <a href="{{ route('reportes.index', array_merge(['tipo' => 'mensual', 'fecha' => $mesAnterior->format('Y-m-d')], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}" 
                   class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded-lg text-sm dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('reportes.index', array_merge(['tipo' => 'mensual', 'fecha' => now()->format('Y-m-d')], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-calendar-day mr-1"></i> Hoy
                </a>
                <a href="{{ route('reportes.index', array_merge(['tipo' => 'mensual', 'fecha' => $mesSiguiente->format('Y-m-d')], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}" 
                   class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded-lg text-sm dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    @if($datos && $datos['resumen_mensual']->total_ventas > 0)
    <!-- Sección de Rutas - Reporte Mensual -->
    @if(isset($datos['rutas_mensuales']) && $datos['rutas_mensuales']['estadisticas']->total_rutas > 0)
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-route text-purple-500 mr-2"></i>
            Reporte de Rutas del Mes
        </h5>

        <!-- Estadísticas principales de rutas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">
                    {{ $datos['rutas_mensuales']['estadisticas']->total_rutas }}
                </div>
                <div class="text-blue-700 text-sm dark:text-blue-300">Total Rutas</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <div class="text-green-600 font-bold text-2xl mb-1 dark:text-green-400">
                    {{ $datos['rutas_mensuales']['estadisticas']->empleados_activos }}
                </div>
                <div class="text-green-700 text-sm dark:text-green-300">Empleados Activos</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="text-purple-600 font-bold text-2xl mb-1 dark:text-purple-400">
                    ${{ number_format($datos['rutas_mensuales']['estadisticas']->ventas_rutas, 2) }}
                </div>
                <div class="text-purple-700 text-sm dark:text-purple-300">Ventas Rutas</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 dark:bg-orange-900/20 dark:border-orange-800">
                <div class="text-orange-600 font-bold text-2xl mb-1 dark:text-orange-400">
                    {{ $datos['rutas_mensuales']['estadisticas']->total_unidades_vendidas }}
                </div>
                <div class="text-orange-700 text-sm dark:text-orange-300">Unidades Vendidas</div>
            </div>
        </div>

        <!-- Top empleados del mes -->
        @if(isset($datos['rutas_mensuales']['top_empleados']) && count($datos['rutas_mensuales']['top_empleados']) > 0)
        <div class="mb-6">
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Top Empleados del Mes</h6>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Empleado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Rutas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Unidades Vendidas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Ventas Totales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($datos['rutas_mensuales']['top_empleados'] as $empleado)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $empleado->empleado }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $empleado->total_rutas }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $empleado->total_ventas_unidades }}</td>
                            <td class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format($empleado->total_ventas_monto, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Reporte Ventas Individuales Mensual -->
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-store text-green-500 mr-2"></i>
            Reporte Ventas Individuales
        </h5>

        <!-- Estadísticas principales del mes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="text-purple-600 font-bold text-2xl mb-1 dark:text-purple-400">{{ $datos['resumen_mensual']->total_ventas ?? 0 }}</div>
                <div class="text-purple-700 text-sm dark:text-purple-300">Ventas Totales</div>
                <div class="text-xs text-purple-500 mt-1 dark:text-purple-400">{{ $datos['resumen_mensual']->dias_con_ventas ?? 0 }} días con ventas</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">${{ number_format($datos['resumen_mensual']->monto_total ?? 0, 2) }}</div>
                <div class="text-blue-700 text-sm dark:text-blue-300">Ingreso Total</div>
                <div class="text-xs text-blue-500 mt-1 dark:text-blue-400">Promedio: ${{ number_format($datos['resumen_mensual']->promedio_diario ?? 0, 2) }}/día</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">${{ number_format($datos['resumen_mensual']->total_gastos ?? 0, 2) }}</div>
                <div class="text-red-700 text-sm dark:text-red-300">Gastos Totales</div>
                <div class="text-xs text-red-500 mt-1 dark:text-red-400">
                    Neto: ${{ number_format(($datos['resumen_mensual']->monto_total ?? 0) - ($datos['resumen_mensual']->total_gastos ?? 0), 2) }}
                </div>
            </div>
        </div>

        <!-- Métodos de Pago del Mes -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Distribución por Método de Pago</h6>
        <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $metodosPago = [
                        'efectivo' => ['monto' => $datos['metodos_pago_mensual']->efectivo ?? 0, 'color' => 'green', 'icon' => 'money-bill-wave'],
                        'transferencia' => ['monto' => $datos['metodos_pago_mensual']->transferencia ?? 0, 'color' => 'blue', 'icon' => 'exchange-alt'],
                        'tarjeta' => ['monto' => $datos['metodos_pago_mensual']->tarjeta ?? 0, 'color' => 'purple', 'icon' => 'credit-card']
                    ];
                    $totalMetodos = array_sum(array_column($metodosPago, 'monto'));
                @endphp
                
                @foreach($metodosPago as $metodo => $data)
                <div class="bg-{{ $data['color'] }}-50 p-4 rounded-lg border border-{{ $data['color'] }}-200 dark:bg-{{ $data['color'] }}-900/20 dark:border-{{ $data['color'] }}-800">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-{{ $data['icon'] }} text-{{ $data['color'] }}-500"></i>
                        @if($totalMetodos > 0)
                        <span class="text-xs bg-{{ $data['color'] }}-100 text-{{ $data['color'] }}-800 px-2 py-1 rounded dark:bg-{{ $data['color'] }}-800/30 dark:text-{{ $data['color'] }}-300">
                            {{ number_format(($data['monto'] / $totalMetodos) * 100, 1) }}%
                        </span>
                        @endif
                    </div>
                    <div class="text-{{ $data['color'] }}-600 font-bold text-xl mb-1 dark:text-{{ $data['color'] }}-400">
                        ${{ number_format($data['monto'], 2) }}
                    </div>
                    <div class="text-{{ $data['color'] }}-700 text-sm capitalize dark:text-{{ $data['color'] }}-300">{{ $metodo }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Resumen Financiero Mensual -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Resumen Financiero Mensual</h6>
        <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Ingresos Totales:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($datos['resumen_mensual']->monto_total ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Gastos Totales:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($datos['resumen_mensual']->total_gastos ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                        <span class="text-blue-700 font-bold dark:text-blue-300">Neto Mensual:</span>
                        <span class="font-bold text-blue-600 text-lg dark:text-blue-400">
                            ${{ number_format(($datos['resumen_mensual']->monto_total ?? 0) - ($datos['resumen_mensual']->total_gastos ?? 0), 2) }}
                        </span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Promedio Diario:</span>
                        <span class="font-semibold">${{ number_format($datos['resumen_mensual']->promedio_diario ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Días con Ventas:</span>
                        <span class="font-semibold">{{ $datos['resumen_mensual']->dias_con_ventas ?? 0 }} de {{ $fechaInicio->daysInMonth }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Eficiencia Mensual:</span>
                        <span class="font-semibold {{ (($datos['resumen_mensual']->dias_con_ventas ?? 0) / $fechaInicio->daysInMonth * 100) > 70 ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ number_format(($datos['resumen_mensual']->dias_con_ventas ?? 0) / $fechaInicio->daysInMonth * 100, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Semanal del Mes -->
        @if(isset($datos['semanas_del_mes']) && count($datos['semanas_del_mes']) > 0)
        <div class="mb-6">
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Desempeño por Semana</h6>
            <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Semana</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Ventas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Monto Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Ticket Promedio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">% del Mes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($datos['semanas_del_mes'] as $semana)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-300">{{ $semana->semana_nombre }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-300">{{ $semana->total_ventas }}</td>
                                    <td class="px-4 py-3 font-semibold text-green-600 dark:text-green-400">${{ number_format($semana->monto_total, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-300">${{ number_format($semana->promedio_venta, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mr-2 dark:bg-gray-600">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $semana->porcentaje_mes }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($semana->porcentaje_mes, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Top Días del Mes -->
        @if(isset($datos['mejores_dias']) && count($datos['mejores_dias']) > 0)
        <div>
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Mejores Días del Mes</h6>
            <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($datos['mejores_dias'] as $index => $dia)
                    <div class="bg-{{ $index == 0 ? 'yellow' : 'gray' }}-50 p-4 rounded-lg border border-{{ $index == 0 ? 'yellow' : 'gray' }}-200 dark:bg-{{ $index == 0 ? 'yellow' : 'gray' }}-900/20 dark:border-{{ $index == 0 ? 'yellow' : 'gray' }}-800">
                        <div class="flex justify-between items-start mb-2">
                            <div class="text-{{ $index == 0 ? 'yellow' : 'gray' }}-600 font-bold text-lg dark:text-{{ $index == 0 ? 'yellow' : 'gray' }}-400">
                                #{{ $index + 1 }}
                            </div>
                            @if($index == 0)
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded dark:bg-yellow-800/30 dark:text-yellow-300">Mejor día</span>
                            @endif
                        </div>
                        <div class="text-{{ $index == 0 ? 'yellow' : 'gray' }}-700 font-semibold dark:text-{{ $index == 0 ? 'yellow' : 'gray' }}-300">
                            {{ \Carbon\Carbon::parse($dia->fecha)->translatedFormat('l d F') }}
                        </div>
                        <div class="text-{{ $index == 0 ? 'yellow' : 'gray' }}-600 text-xl font-bold mt-2 dark:text-{{ $index == 0 ? 'yellow' : 'gray' }}-400">
                            ${{ number_format($dia->monto_total, 2) }}
                        </div>
                        <div class="text-{{ $index == 0 ? 'yellow' : 'gray' }}-500 text-sm mt-1 dark:text-{{ $index == 0 ? 'yellow' : 'gray' }}-400">
                            {{ $dia->total_ventas }} ventas
                        </div>
                        <div class="text-xs text-{{ $index == 0 ? 'yellow' : 'gray' }}-400 mt-2 dark:text-{{ $index == 0 ? 'yellow' : 'gray' }}-500">
                            Ticket promedio: ${{ number_format($dia->monto_total / max($dia->total_ventas, 1), 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    @else
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3"></i>
        <p>No hay datos para el mes seleccionado</p>
        <p class="text-sm mt-2">Selecciona otro mes o verifica que existan ventas en ese período.</p>
    </div>
    @endif
</div>

<script>
// Cambio de fecha para reporte mensual
document.getElementById('fecha-mes').addEventListener('change', function() {
    const fechaSeleccionada = this.value + '-01'; // Convertir YYYY-MM a YYYY-MM-01
    const url = new URL(window.location.href);
    url.searchParams.set('fecha', fechaSeleccionada);
    url.searchParams.set('tipo', 'mensual');
    
    // Mantener el filtro de sucursal si existe
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect && sucursalSelect.value) {
        url.searchParams.set('sucursal_id', sucursalSelect.value);
    }
    
    window.location.href = url.toString();
});
</script>