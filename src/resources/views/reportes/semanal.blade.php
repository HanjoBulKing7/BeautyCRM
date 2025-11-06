@php
use Carbon\Carbon;
@endphp
<!-- Reporte Semanal -->
<div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
    @php
        // CORRECCIÓN: Usar la misma lógica que el controlador
        $fechaInicio = Carbon::parse($fecha);
        $fechaFin = $fechaInicio->copy()->addDays(6);
        $semanaNumero = $fechaInicio->week;
        $anio = $fechaInicio->year;
    @endphp

    <h4 class="font-semibold text-lg mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-calendar-week text-blue-500 mr-2"></i>
            Reporte Semanal
            @if($sucursal_id && $sucursales->where('id', $sucursal_id)->first())
            <span class="ml-4 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                Sucursal: {{ $sucursales->where('id', $sucursal_id)->first()->nombre }}
            </span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <!-- Selector de fecha para semana -->
            <input type="date" id="fecha-semana" value="{{ $fecha }}" 
                   class="border rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
    </h4>

    <!-- Información de la semana -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6 dark:bg-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Semana {{ $semanaNumero }}, {{ $anio }}</h5>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <strong>Rango:</strong> 
                    {{ $fechaInicio->translatedFormat('d M Y') }} — 
                    {{ $fechaFin->translatedFormat('d M Y') }}
                </div>
            </div>
            <button onclick="irASemanaActual()" class="mt-2 md:mt-0 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-calendar-day mr-1"></i> Semana Actual
            </button>
        </div>
    </div>

    @if($datos && (count($datos['ventas_por_dia']) > 0 || (isset($datos['rutas_semanales']) && $datos['rutas_semanales']['estadisticas']->total_rutas > 0)))
    <!-- Sección de Rutas - Reporte Semanal -->
    @if(isset($datos['rutas_semanales']) && $datos['rutas_semanales']['estadisticas']->total_rutas > 0)
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-route text-blue-500 mr-2"></i>
            Reporte de Rutas de la Semana
        </h5>
<!-- Estadísticas principales de rutas - Gastos, Ventas, Balance -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @php
        // USAR LOS DATOS DE LA TABLA "RUTAS POR EMPLEADO" QUE SON LOS CORRECTOS
        $totalVentasRutas = 0;
        $totalGastosRutas = 0;
        $totalBalanceRutas = 0;
        
        if(isset($datos['rutas_semanales']['por_empleado']) && count($datos['rutas_semanales']['por_empleado']) > 0) {
            foreach($datos['rutas_semanales']['por_empleado'] as $ruta) {
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

        <!-- Rutas por día de la semana -->
        @if(isset($datos['rutas_semanales']['por_dia']) && count($datos['rutas_semanales']['por_dia']) > 0)
        <div class="mb-6">
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Rutas por Día</h6>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Día</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Rutas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Empleados Activos</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Ventas Totales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($datos['rutas_semanales']['por_dia'] as $dia)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $dia->dia_semana }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $dia->total_rutas }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $dia->empleados_activos }}</td>
                            <td class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format($dia->ventas_totales, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Rutas por empleado -->
        @if(isset($datos['rutas_semanales']['por_empleado']) && count($datos['rutas_semanales']['por_empleado']) > 0)
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
                        
                        @foreach($datos['rutas_semanales']['por_empleado'] as $ruta)
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

    <!-- Reporte Ventas Individuales Semanales -->
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-store text-green-500 mr-2"></i>
            Reporte Ventas Individuales
        </h5>

        <!-- Estadísticas de la semana - Gastos, Ventas, Balance -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @php
                $montoTotalSemana = $datos['ventas_por_dia']->sum('monto_total');
                $balanceVentas = $montoTotalSemana - ($datos['gastos'] ?? 0);
                $colorBalanceVentas = $balanceVentas >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
            @endphp
            
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">
                    ${{ number_format($datos['gastos'] ?? 0, 2) }}
                </div>
                <div class="text-red-700 dark:text-red-300">Gastos</div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">
                    ${{ number_format($montoTotalSemana, 2) }}
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

        <!-- Tabla de ventas por día -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Ventas por Día</h6>
        <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Día</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Ventas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Monto Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($datos['ventas_por_dia'] as $dia)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ Carbon::parse($dia->fecha_venta)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $dia->dia_semana }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $dia->total_ventas }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">${{ number_format($dia->monto_total, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${{ number_format($dia->promedio_venta, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-300">Total Semana</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-300">{{ $datos['ventas_por_dia']->sum('total_ventas') }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">${{ number_format($montoTotalSemana, 2) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-300">
                                ${{ number_format($montoTotalSemana / max($datos['ventas_por_dia']->sum('total_ventas'), 1), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @else
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3"></i>
        <p>No hay datos para la semana seleccionada</p>
        <p class="text-sm mt-2">Semana: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</p>
        <p class="text-sm">Selecciona otra semana o verifica que existan ventas en ese período.</p>
    </div>
    @endif
</div>

<script>
// Cambio de fecha para reporte semanal
document.getElementById('fecha-semana').addEventListener('change', function() {
    const fecha = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('fecha', fecha);
    url.searchParams.set('tipo', 'semanal');
    
    // Mantener el filtro de sucursal si existe
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect && sucursalSelect.value) {
        url.searchParams.set('sucursal_id', sucursalSelect.value);
    }
    
    window.location.href = url.toString();
});

// Función para ir a la semana actual
function irASemanaActual() {
    const hoy = new Date();
    const fechaActual = hoy.toISOString().split('T')[0];
    
    const url = new URL(window.location.href);
    url.searchParams.set('fecha', fechaActual);
    url.searchParams.set('tipo', 'semanal');
    
    // Mantener el filtro de sucursal si existe
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect && sucursalSelect.value) {
        url.searchParams.set('sucursal_id', sucursalSelect.value);
    }
    
    window.location.href = url.toString();
}
</script>