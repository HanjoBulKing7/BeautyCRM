<!-- Reporte Semanal -->
<div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
    @php
        $fechaInicio = \Carbon\Carbon::parse($fecha);
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
    </h4>

    <!-- Selector de semanas con calendario -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6 dark:bg-gray-700">
        <label for="semana_selector" class="block text-sm font-medium mb-2 dark:text-gray-300">Selecciona una semana:</label>
        <div class="flex items-center space-x-4">
            <input 
                type="text" 
                id="semana_selector" 
                class="w-full md:w-64 border rounded-lg p-2 bg-white cursor-pointer dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                placeholder="Seleccionar semana"
                value="Semana {{ $semanaNumero }}, {{ $anio }}"
                readonly
            >
            <button id="semana_hoy" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-calendar-day mr-1"></i> Hoy
            </button>
        </div>
        
        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-calendar-alt mr-1"></i>
            <strong>Rango:</strong> 
            {{ $fechaInicio->translatedFormat('d M Y') }} — 
            {{ $fechaFin->translatedFormat('d M Y') }}
        </div>
    </div>

    @if($datos && count($datos['ventas_por_dia']) > 0)
    <!-- Sección de Rutas - Reporte Semanal -->
    @if(isset($datos['rutas_semanales']) && $datos['rutas_semanales']['estadisticas']->total_rutas > 0)
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-route text-blue-500 mr-2"></i>
            Reporte de Rutas de la Semana
        </h5>

        <!-- Estadísticas principales de rutas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">
                    {{ $datos['rutas_semanales']['estadisticas']->total_rutas }}
                </div>
                <div class="text-blue-700 text-sm dark:text-blue-300">Total Rutas</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <div class="text-green-600 font-bold text-2xl mb-1 dark:text-green-400">
                    {{ $datos['rutas_semanales']['estadisticas']->empleados_activos }}
                </div>
                <div class="text-green-700 text-sm dark:text-green-300">Empleados Activos</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
                <div class="text-purple-600 font-bold text-2xl mb-1 dark:text-purple-400">
                    ${{ number_format($datos['rutas_semanales']['estadisticas']->ventas_rutas, 2) }}
                </div>
                <div class="text-purple-700 text-sm dark:text-purple-300">Ventas Rutas</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 dark:bg-orange-900/20 dark:border-orange-800">
                <div class="text-orange-600 font-bold text-2xl mb-1 dark:text-orange-400">
                    {{ $datos['rutas_semanales']['estadisticas']->total_unidades_vendidas }}
                </div>
                <div class="text-orange-700 text-sm dark:text-orange-300">Unidades Vendidas</div>
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
                            <td class="px-4 py-2 text-sm dark:text-gray-300">{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
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
    </div>
    @endif

    <!-- Reporte Ventas Individuales Semanales -->
    <div class="bg-white p-4 rounded-lg border mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="font-semibold text-gray-700 mb-4 flex items-center dark:text-gray-300">
            <i class="fas fa-store text-green-500 mr-2"></i>
            Reporte Ventas Individuales
        </h5>

        <!-- Estadísticas de la semana -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @php
                $totalVentasSemana = $datos['ventas_por_dia']->sum('total_ventas');
                $montoTotalSemana = $datos['ventas_por_dia']->sum('monto_total');
            @endphp
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                <div class="text-blue-600 font-bold text-2xl mb-1 dark:text-blue-400">${{ number_format($montoTotalSemana, 2) }}</div>
                <div class="text-blue-700 dark:text-blue-300">Total Vendido</div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <div class="text-green-600 font-bold text-2xl mb-1 dark:text-green-400">{{ $totalVentasSemana }}</div>
                <div class="text-green-700 dark:text-green-300">Total Ventas</div>
            </div>
            
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <div class="text-red-600 font-bold text-2xl mb-1 dark:text-red-400">${{ number_format($datos['gastos'] ?? 0, 2) }}</div>
                <div class="text-red-700 dark:text-red-300">Gastos Semanales</div>
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
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ \Carbon\Carbon::parse($dia->fecha_venta)->format('d/m/Y') }}</td>
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
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-300">{{ $totalVentasSemana }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">${{ number_format($montoTotalSemana, 2) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-300">
                                ${{ number_format($montoTotalSemana / max($totalVentasSemana, 1), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Resumen financiero semanal -->
        <h6 class="font-semibold text-gray-600 mb-3 dark:text-gray-400">Resumen Financiero Semanal</h6>
        <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Ventas:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($montoTotalSemana, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Gastos Semanales:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($datos['gastos'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-700 dark:text-gray-300 font-bold">Neto Semanal:</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">
                            ${{ number_format($montoTotalSemana - ($datos['gastos'] ?? 0), 2) }}
                        </span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Promedio Diario:</span>
                        <span class="font-semibold">${{ number_format($montoTotalSemana / 7, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Días con Ventas:</span>
                        <span class="font-semibold">{{ count($datos['ventas_por_dia']) }} de 7</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Eficiencia:</span>
                        <span class="font-semibold">{{ number_format((count($datos['ventas_por_dia']) / 7) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3"></i>
        <p>No hay datos para la semana seleccionada</p>
        <p class="text-sm mt-2">Selecciona otra semana o verifica que existan ventas en ese período.</p>
    </div>
    @endif
</div>

<!-- Agregar Flatpickr CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.css">

<style>
/* Estilos personalizados para el calendario */
.flatpickr-calendar {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* SEMANA COMPLETA SELECCIONADA - Esto es lo más importante */
.flatpickr-day.week.selected {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: white !important;
}

/* Días en el rango de la semana seleccionada */
.flatpickr-day.inRange {
    background: #dbeafe !important;
    border-color: #dbeafe !important;
    color: #1e40af !important;
}

/* Días individuales seleccionados */
.flatpickr-day.selected, 
.flatpickr-day.startRange, 
.flatpickr-day.endRange {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: white !important;
}

/* Efecto hover */
.flatpickr-day:hover {
    background: #e5e7eb !important;
}

.flatpickr-day.inRange:hover {
    background: #bfdbfe !important;
}

/* Día actual */
.flatpickr-day.today {
    border-color: #3b82f6;
    color: #3b82f6;
    font-weight: bold;
}

.flatpickr-day.today:hover {
    background: #3b82f6;
    color: white;
}

/* Números de semana */
.flatpickr-weekwrapper .flatpickr-weeks {
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
}

.flatpickr-weekwrapper span.flatpickr-day {
    color: #374151;
    font-weight: 600;
}

/* Header */
.flatpickr-current-month {
    font-size: 1.1em;
    font-weight: 600;
}

.flatpickr-weekdays {
    background: #f1f5f9;
}

.flatpickr-weekday {
    color: #475569;
    font-weight: 600;
}

/* Modo oscuro */
.dark .flatpickr-calendar {
    background: #374151;
    border: 1px solid #4b5563;
}

.dark .flatpickr-weekdays {
    background: #4b5563;
}

.dark .flatpickr-weekday {
    color: #d1d5db;
}

.dark .flatpickr-day {
    color: #d1d5db;
}

.dark .flatpickr-day:hover {
    background: #4b5563 !important;
}

.dark .flatpickr-day.inRange {
    background: #1e40af !important;
    color: white !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando Flatpickr con weekSelect...');
    
    // Función para colorear la semana
    function highlightWeek(instance, selectedDate) {
        // Calcular el lunes de la semana
        const date = new Date(selectedDate);
        const dayOfWeek = date.getDay();
        const diff = date.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
        const monday = new Date(date.setDate(diff));
        
        // Limpiar clases previas
        const allDays = instance.calendarContainer.querySelectorAll('.flatpickr-day');
        allDays.forEach(day => {
            day.classList.remove('week', 'inRange', 'selected', 'startRange', 'endRange');
        });
        
        // Colorear los 7 días de la semana
        for (let i = 0; i < 7; i++) {
            const currentDay = new Date(monday);
            currentDay.setDate(monday.getDate() + i);
            
            const dayElement = instance.calendarContainer.querySelector(
                `.flatpickr-day[aria-label="${instance.formatDate(currentDay, 'F j, Y')}"]`
            );
            
            if (dayElement && !dayElement.classList.contains('flatpickr-disabled')) {
                if (i === 0) {
                    dayElement.classList.add('startRange', 'selected', 'week');
                } else if (i === 6) {
                    dayElement.classList.add('endRange', 'selected', 'week');
                } else {
                    dayElement.classList.add('inRange', 'week');
                }
            }
        }
    }
    
    // Verificar que el plugin esté disponible
    if (typeof weekSelectPlugin === 'undefined') {
        console.error('weekSelectPlugin no está disponible');
        initBasicFlatpickr();
        return;
    }

    try {
        // Inicializar Flatpickr CON el plugin de semana
        const picker = flatpickr("#semana_selector", {
            locale: "es",
            weekNumbers: true,
            defaultDate: "{{ $fechaInicio->format('Y-m-d') }}",
            plugins: [
                new weekSelectPlugin({
                    weekNumbers: true
                })
            ],
            onReady: function(selectedDates, dateStr, instance) {
                // Colorear la semana inicial
                if (selectedDates.length > 0) {
                    highlightWeek(instance, selectedDates[0]);
                }
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                // Mantener coloreada la semana al cambiar de mes
                if (selectedDates.length > 0) {
                    setTimeout(() => highlightWeek(instance, selectedDates[0]), 10);
                }
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                // Mantener coloreada la semana al cambiar de año
                if (selectedDates.length > 0) {
                    setTimeout(() => highlightWeek(instance, selectedDates[0]), 10);
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    // Colorear la nueva semana seleccionada
                    highlightWeek(instance, selectedDates[0]);
                    
                    // Obtener el lunes de la semana seleccionada
                    const selectedDate = selectedDates[0];
                    const dayOfWeek = selectedDate.getDay();
                    const diff = selectedDate.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
                    const monday = new Date(selectedDate.setDate(diff));
                    
                    const year = monday.getFullYear();
                    const month = String(monday.getMonth() + 1).padStart(2, '0');
                    const day = String(monday.getDate()).padStart(2, '0');
                    const fechaInicio = `${year}-${month}-${day}`;
                    
                    const url = new URL(window.location.href);
                    url.searchParams.set('fecha', fechaInicio);
                    url.searchParams.set('tipo', 'semanal');
                    
                    const sucursalSelect = document.getElementById('sucursal_id');
                    if (sucursalSelect && sucursalSelect.value) {
                        url.searchParams.set('sucursal_id', sucursalSelect.value);
                    }
                    
                    window.location.href = url.toString();
                }
            }
        });

        console.log('Flatpickr con weekSelect inicializado correctamente');

        document.getElementById('semana_hoy').addEventListener('click', function() {
            picker.setDate(new Date());
        });

    } catch (error) {
        console.error('Error con weekSelect:', error);
        initBasicFlatpickr();
    }

    // Función fallback si el plugin no funciona
    function initBasicFlatpickr() {
        console.log('Usando configuración básica...');
        const picker = flatpickr("#semana_selector", {
            locale: "es",
            weekNumbers: true,
            defaultDate: "{{ $fechaInicio->format('Y-m-d') }}",
            mode: "single",
            onReady: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    highlightWeek(instance, selectedDates[0]);
                }
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    setTimeout(() => highlightWeek(instance, selectedDates[0]), 10);
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    highlightWeek(instance, selectedDates[0]);
                    
                    const selectedDate = selectedDates[0];
                    const dayOfWeek = selectedDate.getDay();
                    const diff = selectedDate.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
                    const monday = new Date(selectedDate.setDate(diff));
                    
                    const year = monday.getFullYear();
                    const month = String(monday.getMonth() + 1).padStart(2, '0');
                    const day = String(monday.getDate()).padStart(2, '0');
                    const fechaInicio = `${year}-${month}-${day}`;
                    
                    const url = new URL(window.location.href);
                    url.searchParams.set('fecha', fechaInicio);
                    url.searchParams.set('tipo', 'semanal');
                    
                    const sucursalSelect = document.getElementById('sucursal_id');
                    if (sucursalSelect && sucursalSelect.value) {
                        url.searchParams.set('sucursal_id', sucursalSelect.value);
                    }
                    
                    window.location.href = url.toString();
                }
            }
        });

        document.getElementById('semana_hoy').addEventListener('click', function() {
            picker.setDate(new Date());
        });
    }
});
</script>