@php
use Carbon\Carbon;
@endphp
<!-- Reporte Mensual -->
<div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
    @php
        $fechaInicio = Carbon::parse($fecha)->startOfMonth();
        $fechaFin = Carbon::parse($fecha)->endOfMonth();
        $mesAnterior = Carbon::parse($fecha)->subMonth();
        $mesSiguiente = Carbon::parse($fecha)->addMonth();
    @endphp

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <div class="flex items-center">
            <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
            <h4 class="font-semibold text-gray-800 dark:text-white">
                Reporte Mensual - {{ $fechaInicio->locale('es')->translatedFormat('F Y') }}
            </h4>
            @if(request('sucursal_id') && $sucursales->where('id', request('sucursal_id'))->first())
            <span class="ml-4 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                Sucursal: {{ $sucursales->where('id', request('sucursal_id'))->first()->nombre }}
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

    @if($datos && (
        (isset($datos['resumen_mensual']) && ($datos['resumen_mensual']->monto_total ?? 0) > 0) || 
        (isset($datos['rutas_mensuales']) && ($datos['rutas_mensuales']['estadisticas']->total_rutas ?? 0) > 0) ||
        (isset($datos['metodos_pago_mensual']) && (
            ($datos['metodos_pago_mensual']->efectivo ?? 0) > 0 ||
            ($datos['metodos_pago_mensual']->transferencia ?? 0) > 0 ||
            ($datos['metodos_pago_mensual']->tarjeta ?? 0) > 0
        ))
    ))
    <!-- Sección de Rutas - Reporte Mensual -->
    @if(isset($datos['rutas_mensuales']) && ($datos['rutas_mensuales']['estadisticas']->total_rutas ?? 0) > 0)
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-route text-purple-500 mr-2"></i>
            Reporte de Rutas del Mes
        </h5>

        <!-- Estadísticas principales de rutas - Gastos, Ventas, Balance -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @php
                // USAR LOS DATOS DE LA TABLA "RUTAS POR EMPLEADO" QUE SON LOS CORRECTOS
                $totalVentasRutas = 0;
                $totalGastosRutas = 0;
                $totalBalanceRutas = 0;
                
                if(isset($datos['rutas_mensuales']['por_empleado']) && count($datos['rutas_mensuales']['por_empleado']) > 0) {
                    foreach($datos['rutas_mensuales']['por_empleado'] as $ruta) {
                        $totalVentasRutas += $ruta->total_ventas_monto;
                        $totalGastosRutas += $ruta->gastos_ruta;
                        $totalBalanceRutas += ($ruta->total_ventas_monto - $ruta->gastos_ruta);
                    }
                }
                
                $colorBalanceRutas = $totalBalanceRutas >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
            @endphp
            
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">
                    ${{ number_format($totalGastosRutas, 2) }}
                </div>
                <div class="text-red-700 dark:text-red-300">Gastos de Ruta</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">
                    ${{ number_format($totalVentasRutas, 2) }}
                </div>
                <div class="text-blue-700 dark:text-blue-300">Ventas de Ruta</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="font-bold text-2xl mb-1 {{ $colorBalanceRutas }}">
                    ${{ number_format($totalBalanceRutas, 2) }}
                </div>
                <div class="text-purple-700 dark:text-purple-300">Balance Rutas</div>
            </div>
        </div>

        <!-- Rutas por empleado -->
        @if(isset($datos['rutas_mensuales']['por_empleado']) && count($datos['rutas_mensuales']['por_empleado']) > 0)
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
                        
                        @foreach($datos['rutas_mensuales']['por_empleado'] as $ruta)
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
        @endif
    </div>
    @endif

    <!-- Reporte Ventas Individuales Mensual -->
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-store text-green-500 mr-2"></i>
            Reporte Ventas Individuales
        </h5>

        <!-- Estadísticas de la semana - Gastos, Ventas, Balance -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @php
                $montoTotalMes = $datos['resumen_mensual']->monto_total ?? 0;
                $gastosMes = $datos['resumen_mensual']->total_gastos ?? 0;
                $balanceVentas = $montoTotalMes - $gastosMes;
                $colorBalanceVentas = $balanceVentas >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
            @endphp
            
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">
                    ${{ number_format($gastosMes, 2) }}
                </div>
                <div class="text-red-700 dark:text-red-300">Gastos</div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">
                    ${{ number_format($montoTotalMes, 2) }}
                </div>
                <div class="text-blue-700 dark:text-blue-300">Total Ventas</div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="font-bold text-2xl mb-1 {{ $colorBalanceVentas }}">
                    ${{ number_format($balanceVentas, 2) }}
                </div>
                <div class="text-purple-700 dark:text-purple-300">Balance</div>
            </div>
        </div>

        <!-- Métodos de Pago del Mes -->
        @if(isset($datos['metodos_pago_mensual']) && (
            ($datos['metodos_pago_mensual']->efectivo ?? 0) > 0 ||
            ($datos['metodos_pago_mensual']->transferencia ?? 0) > 0 ||
            ($datos['metodos_pago_mensual']->tarjeta ?? 0) > 0
        ))
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
        @endif

        <!-- Resumen Financiero Mensual -->
        @if(isset($datos['resumen_mensual']))
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Resumen Financiero Mensual</h6>
        <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Ingresos Totales:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($montoTotalMes, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Gastos Totales:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($gastosMes, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                        <span class="text-blue-700 font-bold dark:text-blue-300">Neto Mensual:</span>
                        <span class="font-bold text-blue-600 text-lg dark:text-blue-400">
                            ${{ number_format($balanceVentas, 2) }}
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
        @endif

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
                                                <div class="bg-green-500 h-2 rounded-full" @style(["width: {$semana->porcentaje_mes}%"])></div>
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
    </div>

    @else
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3"></i>
        <p>No hay datos para el mes seleccionado</p>
        <p class="text-sm mt-2">Selecciona otro mes o verifica que existan ventas en ese período.</p>
    </div>
    @endif
</div>

<!-- Gráfica de Ventas Mensuales -->
<div class="bg-white p-6 rounded-xl shadow mt-6 dark:bg-gray-800 dark:border dark:border-gray-700">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center dark:text-gray-200">
        <i class="fas fa-chart-bar text-purple-500 mr-2"></i>
        Ventas Mensuales - {{ $fechaInicio->locale('es')->translatedFormat('F Y') }}
    </h2>
    <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
        <canvas id="chartVentasMensuales" height="300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
// Pasar datos desde PHP a JavaScript
var datosGrafica = <?php echo json_encode($datos ?? []); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartVentasMensuales');
    
    if (!ctx) {
        console.error('No se encontró el elemento canvas con id chartVentasMensuales');
        return;
    }

    // Extraer datos de semanas
    const semanasData = datosGrafica.semanas_del_mes || [];
    const semana1 = semanasData[0]?.monto_total ?? 0;
    const semana2 = semanasData[1]?.monto_total ?? 0;
    const semana3 = semanasData[2]?.monto_total ?? 0;
    const semana4 = semanasData[3]?.monto_total ?? 0;
    const semana5 = semanasData[4]?.monto_total ?? 0;
    
    // Extraer datos de rutas
    const ventasRutas = datosGrafica.rutas_mensuales?.estadisticas?.ventas_rutas ?? 0;

    const ventasMensuales = {
        labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5'],
        datasets: [
            {
                label: 'Ventas Individuales',
                data: [semana1, semana2, semana3, semana4, semana5],
                backgroundColor: 'rgba(79, 70, 229, 0.7)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                borderRadius: 6,
                fill: true
            },
            {
                label: 'Ventas Rutas',
                data: [ventasRutas/4, ventasRutas/4, ventasRutas/4, ventasRutas/4, 0],
                backgroundColor: 'rgba(236, 72, 153, 0.7)',
                borderColor: 'rgba(236, 72, 153, 1)',
                borderWidth: 2,
                borderRadius: 6,
                fill: true
            }
        ]
    };

    try {
        // Configurar modo oscuro antes de crear la gráfica
        const isDarkMode = document.documentElement.classList.contains('dark');
        if (isDarkMode) {
            Chart.defaults.color = '#9CA3AF';
            Chart.defaults.borderColor = '#374151';
        }

        new Chart(ctx, {
            type: 'bar',
            data: ventasMensuales,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: isDarkMode ? '#9CA3AF' : '#6B7280',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: $${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: isDarkMode ? '#9CA3AF' : '#6B7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: isDarkMode ? '#9CA3AF' : '#6B7280',
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
        
        console.log('Gráfica creada exitosamente');
    } catch (error) {
        console.error('Error al crear la gráfica:', error);
    }
});
</script>
