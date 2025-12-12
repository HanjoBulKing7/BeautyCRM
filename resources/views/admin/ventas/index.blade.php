@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-pink-100 text-pink-700">
                    <!-- icon: cash -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m2-8h-6a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V11a2 2 0 00-2-2z"/>
                    </svg>
                </span>
                Ventas (Citas Completadas)
            </h1>
            <p class="text-gray-600 mt-1">Listado de citas completadas que generaron ventas automáticas</p>
        </div>
    </div>

    <!-- Filtros básicos -->
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.ventas.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input
                    type="date"
                    name="fecha_inicio"
                    value="{{ $fechaInicio ?? '' }}"
                    class="w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500"
                >
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input
                    type="date"
                    name="fecha_fin"
                    value="{{ $fechaFin ?? '' }}"
                    class="w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500"
                >
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-400"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                    Filtrar
                </button>

                <a
                    href="{{ route('admin.ventas.index') }}"
                    class="inline-flex items-center gap-2 bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 00-14.828-2M4 16a8 8 0 0014.828 2"/>
                    </svg>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Resumen rápido -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-full mr-3">
                    <span class="text-pink-700 text-xl">💰</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Ventas</p>
                    <p class="text-xl font-bold text-pink-700">${{ number_format($totalVentas ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-full mr-3">
                    <span class="text-pink-700 text-xl">📋</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Citas Completadas</p>
                    <p class="text-xl font-bold">{{ $ventasCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-full mr-3">
                    <span class="text-pink-700 text-xl">📊</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ticket Promedio</p>
                    <p class="text-xl font-bold">${{ number_format($promedioVenta ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de citas completadas (sin líneas divisorias) -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-pink-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID Cita</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha Cita</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Empleado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Venta</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($citasCompletadas as $cita)
                        <tr class="hover:bg-pink-50/40 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">
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
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $cita->cliente->name ?? 'Cliente' }}
                                </div>
                                @if($cita->cliente->email ?? false)
                                    <div class="text-xs text-gray-500">{{ $cita->cliente->email }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">
                                    {{ $cita->servicio->nombre_servicio ?? 'Servicio' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    ${{ number_format($cita->servicio->precio ?? 0, 2) }}
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $cita->empleado->name ?? 'No asignado' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($cita->venta)
                                    <span class="font-bold text-pink-700">
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
                                <div class="flex items-center justify-end gap-2">
                                    @if($cita->venta)
                                        <a
                                            href="{{ route('admin.ventas.show', $cita->venta->id_venta) }}"
                                            class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-pink-700 hover:bg-pink-50"
                                            title="Ver Venta"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Venta
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('admin.citas.show', $cita->id_cita) }}"
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
                                        title="Ver Cita"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Ver Cita
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
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

        <!-- Paginación -->
        @if($citasCompletadas->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $citasCompletadas->links() }}
            </div>
        @endif
    </div>

    <!-- Información del sistema -->
    <div class="mt-6 p-4 bg-pink-50 border border-pink-200 rounded-xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="text-pink-700">ℹ️</span>
            </div>
            <div class="ml-3">
                <p class="text-sm text-pink-800">
                    <strong>Nota:</strong> Esta vista muestra las citas que han sido marcadas como "completadas".
                    Cada cita completada genera automáticamente una venta. Para ver detalles completos de la venta, haz clic en "Ver Venta".
                </p>
            </div>
        </div>
    </div>

</div>
@endsection