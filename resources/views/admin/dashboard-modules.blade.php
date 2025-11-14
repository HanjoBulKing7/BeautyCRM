@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="min-h-screen bg-gray-50 p-6 transition-colors duration-300 dark:bg-gray-900">

    <!-- Encabezado -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Panel de Administración</h1>
            <p class="mt-2 text-gray-700 dark:text-gray-300">
                Bienvenido, <span class="font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
            </p>
        </div>
        <div class="mt-3 flex items-center space-x-4 md:mt-0">
            <span class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition dark:bg-blue-700 dark:hover:bg-blue-600">
                Rol: {{ ucfirst(Auth::user()->rol) }}
            </span>
        </div>
    </div>

    <!-- Tarjetas de métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Citas Hoy -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-blue-100 rounded-lg dark:bg-blue-900/30">
                <i class="fas fa-calendar-check text-blue-600 text-2xl dark:text-blue-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Citas hoy</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['citas_hoy']) }}</p>
            </div>
        </div>

        <!-- Ingresos Mensuales -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-green-100 rounded-lg dark:bg-green-900/30">
                <i class="fas fa-dollar-sign text-green-600 text-2xl dark:text-green-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Ingresos este mes</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['ingresos_mes'], 2) }}</p>
            </div>
        </div>

        <!-- Clientes Nuevos -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-purple-100 rounded-lg dark:bg-purple-900/30">
                <i class="fas fa-users text-purple-600 text-2xl dark:text-purple-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Clientes nuevos</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['clientes_nuevos']) }}</p>
            </div>
        </div>

        <!-- Servicio Popular -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-yellow-100 rounded-lg dark:bg-yellow-900/30">
                <i class="fas fa-cut text-yellow-600 text-2xl dark:text-yellow-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Servicio popular</h2>
                <p class="text-xl font-bold text-gray-900 dark:text-white truncate">{{ $stats['servicio_popular'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Sección de reportes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Tabla de citas recientes -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Citas Recientes</h2>
                <a href="{{ url('/admin/citas') }}" class="text-sm text-blue-600 font-medium cursor-pointer hover:underline dark:text-blue-400">Ver todas</a>
            </div>
            @if($stats['citas_recientes']->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100 text-gray-800 text-sm font-semibold uppercase border-b border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                <th class="px-4 py-3 font-semibold">Cliente</th>
                                <th class="px-4 py-3 font-semibold">Servicio</th>
                                <th class="px-4 py-3 font-semibold">Hora</th>
                                <th class="px-4 py-3 font-semibold">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($stats['citas_recientes'] as $cita)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $cita->cliente->nombre ?? 'Sin cliente' }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $cita->hora }}</td>
                                    <td class="px-4 py-3">
                                        @if($cita->estado === 'confirmada')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Confirmada</span>
                                        @elseif($cita->estado === 'pendiente')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Pendiente</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">{{ ucfirst($cita->estado) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4 dark:text-gray-400">No hay citas recientes</p>
            @endif
        </div>

        <!-- Reporte gráfico - SOLO PARA ADMIN -->
        @if(Auth::user()->rol === 'admin')
            <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ingresos últimos 30 días</h2>
                    <div class="flex space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">2025</span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="chartIngresos" 
                            data-ingresos='@json($stats['ingresos_diarios']['datos'])'
                            data-labels='@json($stats['ingresos_diarios']['etiquetas'])'>
                    </canvas>
                </div>
            </div>
        @else
            <!-- Mensaje alternativo para usuarios sin permisos -->
            <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-lock text-gray-400 text-5xl mb-4 dark:text-gray-500"></i>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2 dark:text-gray-300">Contenido Restringido</h3>
                    <p class="text-gray-500 dark:text-gray-400">Solo administradores pueden ver esta gráfica</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Sección inferior - Clientes y Servicios -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        
        <!-- Clientes recientes -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Clientes Recientes</h2>
                <a href="{{ url('/admin/clientes') }}" class="text-sm text-blue-600 font-medium cursor-pointer hover:underline dark:text-blue-400">Ver todos</a>
            </div>
            @if($stats['clientes_recientes']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['clientes_recientes'] as $cliente)
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cliente->nombre }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cliente->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">{{ $cliente->citas_count ?? 0 }} citas</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4 dark:text-gray-400">No hay clientes recientes</p>
            @endif
        </div>

        <!-- Servicios populares -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Servicios Populares</h2>
                <a href="{{ url('/admin/servicios') }}" class="text-sm text-blue-600 font-medium cursor-pointer hover:underline dark:text-blue-400">Ver todos</a>
            </div>
            @if($stats['servicios_populares']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['servicios_populares'] as $servicio)
                        @php
                            $porcentaje = isset($stats['max_reservas']) && $stats['max_reservas'] > 0 
                                ? ($servicio->reservas_count / $stats['max_reservas']) * 100 
                                : 0;
                            $widthStyle = 'width: ' . round($porcentaje, 2) . '%';
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-900 dark:text-white">{{ $servicio->nombre }}</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $servicio->reservas_count }} reservas</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2 rounded-full dark:bg-blue-500" style="{{ $widthStyle }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4 dark:text-gray-400">No hay servicios disponibles</p>
            @endif
        </div>
    </div>
    
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartIngresos');
    
    if (!ctx) {
        console.error('No se encontró el elemento canvas');
        return;
    }
    
    try {
        const ingresosData = JSON.parse(ctx.dataset.ingresos);
        const labels = JSON.parse(ctx.dataset.labels);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ingresos',
                    data: ingresosData,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Ingresos: $' + context.parsed.y.toLocaleString('es-MX', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-MX');
                            }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error al crear la gráfica:', error);
    }
});
</script>
@endsection