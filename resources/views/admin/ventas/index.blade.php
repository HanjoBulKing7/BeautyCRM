@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">💰 Ventas (Citas Completadas)</h1>
        <p class="text-gray-600 mt-1">Listado de citas completadas que generaron ventas automáticas</p>
    </div>

    <!-- Filtros básicos -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.ventas.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio ?? '' }}" 
                       class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin ?? '' }}" 
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
                    <p class="text-xl font-bold">${{ number_format($totalVentas ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-full mr-3">
                    <span class="text-blue-600 text-xl">📋</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Citas Completadas</p>
                    <p class="text-xl font-bold">{{ $ventasCount ?? 0 }}</p>
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
                    <p class="text-xl font-bold">${{ number_format($promedioVenta ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla simple de citas completadas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Cita</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Cita</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Venta</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ver Detalles</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($citasCompletadas as $cita)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-medium text-gray-900">
                                #{{ $cita->id_cita }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $cita->hora_cita }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $cita->cliente->name ?? 'Cliente' }}
                            </div>
                            @if($cita->cliente->email ?? false)
                            <div class="text-xs text-gray-500">{{ $cita->cliente->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $cita->servicio->nombre_servicio ?? 'Servicio' }}</div>
                            <div class="text-xs text-gray-500">
                                ${{ number_format($cita->servicio->precio ?? 0, 2) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cita->empleado->name ?? 'No asignado' }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($cita->venta)
                                <span class="font-bold text-green-600">
                                    ${{ number_format($cita->venta->total, 2) }}
                                </span>
                                <div class="text-xs text-gray-500">
                                    {{ $cita->venta->forma_pago ?? 'efectivo' }}
                                </div>
                            @else
                                <span class="text-gray-400">Sin venta registrada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            @if($cita->venta)
                                <a href="{{ route('admin.ventas.show', $cita->venta->id_venta) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3 inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Venta
                                </a>
                            @endif
                            <a href="{{ route('admin.citas.show', $cita->id_cita) }}" 
                               class="text-gray-600 hover:text-gray-900 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Ver Cita
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center">
                            <div class="text-gray-500">
                                <span class="text-4xl mb-2 block">📭</span>
                                <p class="font-medium">No hay citas completadas</p>
                                <p class="text-sm">Las ventas aparecerán aquí cuando marques citas como "completadas"</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación simple -->
        @if($citasCompletadas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $citasCompletadas->links() }}
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
                    <strong>Nota:</strong> Esta vista muestra las citas que han sido marcadas como "completadas". 
                    Cada cita completada genera automáticamente una venta. Para ver detalles completos de la venta, haz clic en "Ver Venta".
                </p>
            </div>
        </div>
    </div>
</div>
@endsection