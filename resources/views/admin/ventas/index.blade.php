@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📊 Ventas Registradas</h1>
        <p class="text-gray-600 mt-1">Ventas automáticas generadas desde citas completadas</p>
    </div>

    <!-- Filtros básicos -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.ventas.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" 
                       class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" 
                       class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    🔍 Filtrar
                </button>
                <a href="{{ route('admin.ventas.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                    🔄 Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Resumen rápido -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-full mr-3">
                    <span class="text-green-600 text-xl">💰</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Ventas</p>
                    <p class="text-xl font-bold">${{ number_format($totalVentas, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-full mr-3">
                    <span class="text-blue-600 text-xl">📋</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transacciones</p>
                    <p class="text-xl font-bold">{{ $ventasCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-full mr-3">
                    <span class="text-purple-600 text-xl">📊</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ticket Promedio</p>
                    <p class="text-xl font-bold">${{ number_format($promedioVenta, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla simple de ventas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pago</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ventas as $venta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $venta->fecha_venta->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $venta->fecha_venta->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $venta->servicio->nombre_servicio }}</div>
                            <div class="text-xs text-gray-500">${{ number_format($venta->servicio->precio, 2) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $venta->empleado->nombre }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-green-600">
                                ${{ number_format($venta->total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $badgeColors = [
                                    'efectivo' => 'bg-green-100 text-green-800',
                                    'tarjeta_credito' => 'bg-blue-100 text-blue-800',
                                    'tarjeta_debito' => 'bg-purple-100 text-purple-800',
                                    'transferencia' => 'bg-yellow-100 text-yellow-800',
                                    'mixto' => 'bg-gray-100 text-gray-800'
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $badgeColors[$venta->forma_pago] ?? 'bg-gray-100' }}">
                                {{ ucfirst(str_replace('_', ' ', $venta->forma_pago)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center">
                            <div class="text-gray-500">
                                <span class="text-4xl mb-2 block">📭</span>
                                <p class="font-medium">No hay ventas registradas</p>
                                <p class="text-sm">Las ventas aparecerán aquí automáticamente cuando completes una cita</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación simple -->
        @if($ventas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $ventas->links() }}
        </div>
        @endif
    </div>

    <!-- Información del sistema -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="text-blue-500">ℹ️</span>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Nota:</strong> Las ventas se generan automáticamente al marcar una cita como "completada".
                    Esta vista es solo para consulta.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection