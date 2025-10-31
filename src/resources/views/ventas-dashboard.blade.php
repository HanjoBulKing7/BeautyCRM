@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="min-h-screen bg-gray-50 p-6 transition-colors duration-300 dark:bg-gray-900">

    <!-- Encabezado -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Panel de Control</h1>
            <p class="mt-2 text-gray-700 dark:text-gray-300">
                Bienvenido, <span class="font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                @if(Auth::user()->sucursal)
                    — <span class="text-blue-600 dark:text-blue-400">{{ Auth::user()->sucursal->nombre }}</span>
                @endif
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
        <!-- Ventas -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-red-100 rounded-lg dark:bg-red-900/30">
                <i class="fas fa-cash-register text-red-600 text-2xl dark:text-red-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Ventas</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_ventas']) }}</p>
            </div>
        </div>

        <!-- Gastos -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-green-100 rounded-lg dark:bg-green-900/30">
                <i class="fas fa-money-bill-wave text-green-600 text-2xl dark:text-green-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Gastos</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_gastos'], 2) }}</p>
            </div>
        </div>

        <!-- Productos -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-purple-100 rounded-lg dark:bg-purple-900/30">
                <i class="fas fa-box-open text-purple-600 text-2xl dark:text-purple-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Productos</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_productos']) }}</p>
            </div>
        </div>

        <!-- Inventario -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 flex items-center transform transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer dark:bg-gray-800 dark:border-gray-700">
            <div class="p-3 bg-blue-100 rounded-lg dark:bg-blue-900/30">
                <i class="fas fa-warehouse text-blue-600 text-2xl dark:text-blue-400"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Inventario</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_inventario']) }}</p>
            </div>
        </div>
    </div>

    <!-- Sección de reportes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Tabla de últimas ventas -->
        <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Últimas Ventas</h2>
                <span class="text-sm text-blue-600 font-medium cursor-pointer hover:underline dark:text-blue-400">Ver todas</span>
            </div>
            @if($stats['ultimas_ventas']->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100 text-gray-800 text-sm font-semibold uppercase border-b border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                <th class="px-4 py-3 font-semibold">Cliente</th>
                                <th class="px-4 py-3 font-semibold">Producto</th>
                                <th class="px-4 py-3 font-semibold">Monto</th>
                                <th class="px-4 py-3 font-semibold">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($stats['ultimas_ventas'] as $venta)
                                @php
                                    $primerProducto = $venta->detalles->first();
                                    $nombreProducto = $primerProducto ? $primerProducto->producto->nombre : 'N/A';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-200 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $venta->cliente ? $venta->cliente->nombre : 'Cliente General' }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $nombreProducto }}</td>
                                    <td class="px-4 py-3 font-semibold text-green-600 dark:text-green-400">${{ number_format($venta->total, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $venta->fecha->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4 dark:text-gray-400">No hay ventas recientes</p>
            @endif
        </div>

        <!-- ⭐ Reporte gráfico - SOLO PARA ADMIN -->
        @if(Auth::user()->rol === 'admin')
            <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ingresos Mensuales (Últimos 6 meses)</h2>
                    <div class="flex space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">2025</span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="chartIngresos" 
                            data-ingresos='@json($stats['ingresos_mensuales']['datos'])'
                            data-labels='@json($stats['ingresos_mensuales']['etiquetas'])'>
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