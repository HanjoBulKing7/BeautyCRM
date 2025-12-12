@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-pink-100 text-pink-700">
                    <!-- icon: scissors -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14.121 14.121L19 19M5 5l4.879 4.879M15 7a3 3 0 11-6 0 3 3 0 016 0zm0 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                Servicios
            </h1>
            <p class="text-gray-600 mt-1">Administra los servicios disponibles del salón</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.servicios.create') }}"
               class="inline-flex items-center gap-2 bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Servicio
            </a>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-pink-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Precio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duración</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($servicios as $servicio)
                        <tr class="hover:bg-pink-50/40 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex items-center justify-center w-8 h-8 rounded-lg bg-pink-100 text-pink-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M14.121 14.121L19 19M5 5l4.879 4.879M15 7a3 3 0 11-6 0 3 3 0 016 0zm0 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>

                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $servicio->nombre_servicio ?? $servicio->nombre ?? 'Servicio' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: #{{ $servicio->id_servicio ?? $servicio->id ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm font-bold text-pink-700">
                                    ${{ number_format($servicio->precio ?? 0, 2) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-700">
                                    {{ $servicio->duracion ?? 60 }} min
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700 line-clamp-2">
                                    {{ $servicio->descripcion ?? '—' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Ver -->
                                    <a href="{{ route('admin.servicios.show', $servicio->id_servicio ?? $servicio->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
                                       title="Ver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Editar -->
                                    <a href="{{ route('admin.servicios.edit', $servicio->id_servicio ?? $servicio->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-pink-700 hover:bg-pink-50"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                                        </svg>
                                    </a>

                                    <!-- Eliminar -->
                                    <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio ?? $servicio->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este servicio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-red-600 hover:bg-red-50"
                                                title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center">
                                <div class="text-gray-500">
                                    <span class="text-4xl mb-2 block">💗</span>
                                    <p class="font-semibold">No hay servicios registrados</p>
                                    <p class="text-sm">Crea tu primer servicio para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if(method_exists($servicios, 'hasPages') && $servicios->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $servicios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
