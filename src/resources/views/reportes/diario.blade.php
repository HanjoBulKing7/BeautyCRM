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

        <!-- Estadísticas principales de rutas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">
                    {{ $datos['rutas']['estadisticas']->total_rutas }}
                </div>
                <div class="text-blue-700 text-sm dark:text-blue-300">Total Rutas</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <div class="text-green-600 font-bold text-2xl mb-1 dark:text-green-400">
                    {{ $datos['rutas']['estadisticas']->empleados_activos }}
                </div>
                <div class="text-green-700 text-sm dark:text-green-300">Empleados Activos</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="text-purple-600 font-bold text-2xl mb-1 dark:text-purple-400">
                    ${{ number_format($datos['rutas']['estadisticas']->ventas_rutas, 2) }}
                </div>
                <div class="text-purple-700 text-sm dark:text-purple-300">Ventas Rutas</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 dark:bg-orange-900/20 dark:border-orange-800">
                <div class="text-orange-600 font-bold text-2xl mb-1 dark:text-orange-400">
                    {{ $datos['rutas']['por_empleado']->sum('total_ventas_unidades') }}
                </div>
                <div class="text-orange-700 text-sm dark:text-orange-300">Unidades Vendidas</div>
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
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Rutas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Unidades Vendidas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Ventas Totales</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-300">Devoluciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($datos['rutas']['por_empleado'] as $empleado)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $empleado->empleado }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $empleado->total_rutas }}</td>
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ $empleado->total_ventas_unidades }}</td>
                            <td class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format($empleado->total_ventas_monto, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">{{ $empleado->total_devoluciones }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Productos más vendidos en rutas -->
        @if($datos['rutas']['productos_top']->count() > 0)
        <div>
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Productos Más Vendidos en Rutas</h6>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($datos['rutas']['productos_top'] as $producto)
                <div class="bg-gray-50 p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                    <div class="font-medium text-gray-800 dark:text-gray-300 mb-1">{{ $producto->producto }}</div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Vendidos:</span>
                        <span class="font-semibold">{{ $producto->total_vendido }} unid.</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Devoluciones:</span>
                        <span class="text-red-600 dark:text-red-400">{{ $producto->total_devoluciones }} unid.</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            ${{ number_format($producto->monto_total, 2) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
        
@if($datos && $datos['ventas']->total_ventas > 0)
    <!-- Reporte Ventas Individuales -->
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-store text-green-500 mr-2"></i>
            Reporte Ventas Individuales
        </h5>

        <!-- Estadísticas principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <div class="text-green-600 font-bold text-2xl mb-1 dark:text-green-400">{{ $datos['ventas']->total_ventas ?? 0 }}</div>
                <div class="text-green-700 dark:text-green-300">Ventas Totales</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">${{ number_format($datos['ventas']->monto_total ?? 0, 2) }}</div>
                <div class="text-blue-700 dark:text-blue-300">Monto Total</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">${{ number_format($datos['gastos'] ?? 0, 2) }}</div>
                <div class="text-red-700 dark:text-red-300">Gastos del Día</div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Métodos de Pago</h6>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @php
                $efectivo = $datos['metodosPago']->firstWhere('metodo_pago', 'efectivo')->total_metodo ?? 0;
                $transferencia = $datos['metodosPago']->firstWhere('metodo_pago', 'transferencia')->total_metodo ?? 0;
                $tarjeta = $datos['metodosPago']->firstWhere('metodo_pago', 'tarjeta')->total_metodo ?? 0;
            @endphp
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <div class="text-green-600 font-bold text-xl mb-1 dark:text-green-400">${{ number_format($efectivo, 2) }}</div>
                <div class="text-green-700 dark:text-green-300 flex items-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Efectivo
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-xl mb-1 dark:text-blue-400">${{ number_format($transferencia, 2) }}</div>
                <div class="text-blue-700 dark:text-blue-300 flex items-center">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Transferencia
                </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="text-purple-600 font-bold text-xl mb-1 dark:text-purple-400">${{ number_format($tarjeta, 2) }}</div>
                <div class="text-purple-700 dark:text-purple-300 flex items-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Tarjeta
                </div>
            </div>
        </div>

        <!-- Detalles de transferencias -->
        @if($transferencia > 0)
        <div class="mb-6">
            <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Detalles de Transferencias</h6>
            <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
                <div class="space-y-2">
                    @foreach($datos['transferencias'] as $transferenciaItem)
                    <div class="flex justify-between py-2 border-b dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">A {{ $transferenciaItem->destinatario_transferencia }}:</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">${{ number_format($transferenciaItem->total_transferencia, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between border-t pt-2 mt-2 font-bold">
                        <span class="text-gray-700 dark:text-gray-300">Total Transferencias:</span>
                        <span class="text-blue-600 dark:text-blue-400">${{ number_format($transferencia, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Resumen financiero -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Resumen Financiero</h6>
        <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-semibold">${{ number_format($datos['ventas']->subtotal_total ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Descuentos:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($datos['ventas']->descuento_total ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Impuestos:</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">${{ number_format($datos['ventas']->impuestos_total ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-700 dark:text-gray-300 font-bold">Total Ventas:</span>
                        <span class="font-bold text-green-600 dark:text-green-400">${{ number_format($datos['ventas']->monto_total ?? 0, 2) }}</span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Gastos del Día:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($datos['gastos'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-700 dark:text-gray-300 font-bold">Neto:</span>
                        <span class="font-bold text-purple-600 dark:text-purple-400">
                            ${{ number_format(($datos['ventas']->monto_total ?? 0) - ($datos['gastos'] ?? 0), 2) }}
                        </span>
                    </div>
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