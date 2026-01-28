@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <span class="bb-icon-pill">
                    <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14.121 14.121L19 19M5 5l4.879 4.879M15 7a3 3 0 11-6 0 3 3 0 016 0zm0 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                Servicios
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.servicios.create') }}"
                data-bb-open="modal"
                data-bb-title="Nuevo Servicio"
                data-bb-url="{{ route('admin.servicios.create') }}?modal=1"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                border border-[rgba(201,162,74,.35)]
                bg-[rgba(201,162,74,.12)] hover:bg-[rgba(201,162,74,.18)]
                text-gray-900 font-semibold shadow-sm hover:shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Servicio
            </a>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bb-glass-card">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bb-thead">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Duración</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-[120px]">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($servicios as $servicio)
                        <tr class="bb-row">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if(!empty($servicio->imagen))
                                        <div class="w-30 h-20 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm shrink-0">
                                            <img
                                                src="{{ asset('storage/' . $servicio->imagen) }}"
                                                alt="Foto del servicio"
                                                class="w-full h-full object-cover"
                                                loading="lazy"
                                            >
                                        </div>
                                    @else
                                        <span class="bb-icon-pill" style="width:34px;height:34px;border-radius:12px;">
                                            <svg class="w-4 h-4 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.121 14.121L19 19M5 5l4.879 4.879M15 7a3 3 0 11-6 0 3 3 0 016 0zm0 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </span>
                                    @endif

                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $servicio->nombre_servicio ?? $servicio->nombre ?? 'Servicio' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: #{{ $servicio->id_servicio ?? $servicio->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm font-bold bb-gold">
                                    ${{ number_format($servicio->precio ?? 0, 2) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ $servicio->duracion_minutos ?? $servicio->duracion ?? 60 }} min
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700 dark:text-gray-200 line-clamp-2">
                                    {{ $servicio->descripcion ?? '—' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Ver -->
                                    @php $sid = $servicio->id_servicio ?? $servicio->id; @endphp

                                    <td class="px-4 py-3 whitespace-nowrap align-middle w-[120px]">
                                    <div class="flex items-center justify-end gap-2">

                                        <!-- Ver -->
                                        <a href="{{ route('admin.servicios.show', $sid) }}"
                                        data-bb-open="modal"
                                        data-bb-title="Servicio"
                                        data-bb-url="{{ route('admin.servicios.show', $sid) }}?modal=1"
                                        class="bb-action inline-flex items-center justify-center w-9 h-9 rounded-xl
                                                bg-white/70 hover:bg-white border border-gray-200
                                                shadow-sm hover:shadow transition leading-none shrink-0"
                                        title="Ver">
                                        <svg class="w-4 h-4 block -translate-y-[0.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        </a>

                                        <!-- Editar -->
                                        <a href="{{ route('admin.servicios.edit', $sid) }}"
                                        data-bb-open="modal"
                                        data-bb-title="Editar Servicio"
                                        data-bb-url="{{ route('admin.servicios.edit', $sid) }}?modal=1"
                                        class="bb-action bb-action-edit inline-flex items-center justify-center w-9 h-9 rounded-xl
                                                bg-white/70 hover:bg-white border border-gray-200
                                                shadow-sm hover:shadow transition leading-none shrink-0"
                                        title="Editar">
                                        <svg class="w-4 h-4 block -translate-y-[0.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                                        </svg>
                                        </a>

                                        <!-- Eliminar -->
                                        <form class="inline-flex m-0"
                                            action="{{ route('admin.servicios.destroy', $sid) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Eliminar este servicio?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="modal" value="1">
                                        <button type="submit"
                                                class="bb-action bb-action-del inline-flex items-center justify-center w-9 h-9 rounded-xl
                                                        bg-white/70 hover:bg-white border border-gray-200
                                                        shadow-sm hover:shadow transition leading-none shrink-0"
                                                title="Eliminar">
                                            <svg class="w-4 h-4 block -translate-y-[0.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="text-gray-500 dark:text-gray-300">
                                    <div class="mx-auto bb-icon-pill" style="width:56px;height:56px;border-radius:18px;">
                                        <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.121 14.121L19 19M5 5l4.879 4.879M15 7a3 3 0 11-6 0 3 3 0 016 0zm0 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold mt-3 text-gray-800 dark:text-white">No hay servicios registrados</p>
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
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $servicios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection