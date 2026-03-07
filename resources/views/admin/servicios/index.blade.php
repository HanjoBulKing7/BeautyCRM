@extends('layouts.app')

@section('content')
<style>
    /* Efecto Glass Premium */
    .bb-glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    
    /* Botones de acción suaves */
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        transition: all 0.2s ease;
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-[rgba(201,162,74,0.1)] text-[rgba(201,162,74,1)] flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19M5 5l4.879 4.879M15 7a3 3 0 11-6 0 3 3 0 016 0zm0 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                Catálogo de Servicios
            </h1>
            <p class="text-sm text-gray-500 mt-1 ml-15">Gestiona los servicios, precios y duraciones.</p>
        </div>

        <a href="{{ route('admin.servicios.create') }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-gray-900 text-white font-bold hover:bg-black shadow-lg hover:shadow-xl transition-all md:w-auto w-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Servicio
        </a>
    </div>

    <div class="hidden md:block bb-glass-card rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200/60">
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Servicio</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Precio</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Duración</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/60">
                    @forelse($servicios as $servicio)
                        @php $sid = $servicio->id_servicio ?? $servicio->id; @endphp
                        <tr class="hover:bg-white/40 transition-colors group">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-4">
                                    @if(!empty($servicio->imagen))
                                        <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="Foto" class="w-14 h-14 rounded-2xl object-cover shadow-sm border border-gray-100">
                                    @else
                                        <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white">
                                            {{ $servicio->nombre_servicio ?? $servicio->nombre ?? 'Servicio' }}
                                        </div>
                                        <div class="text-xs text-gray-500 line-clamp-1 max-w-xs">{{ $servicio->descripcion ?? 'Sin descripción' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)] px-3 py-1.5 rounded-xl">
                                    ${{ number_format($servicio->precio ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $servicio->duracion_minutos ?? $servicio->duracion ?? 60 }} min
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.servicios.show', $sid) }}" class="action-btn text-blue-500 bg-blue-50 hover:bg-blue-100" title="Ver detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.servicios.edit', $sid) }}" class="action-btn text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)] hover:bg-[rgba(201,162,74,.2)]" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.servicios.destroy', $sid) }}" method="POST" class="inline-block m-0 p-0" onsubmit="return confirm('¿Seguro que deseas eliminar este servicio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn text-red-500 bg-red-50 hover:bg-red-100" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <div class="w-20 h-20 mx-auto rounded-full bg-gray-50 flex items-center justify-center text-3xl mb-4 shadow-inner">✨</div>
                                <h3 class="text-lg font-bold text-gray-900">No hay servicios registrados</h3>
                                <p class="text-gray-500 mt-1">Comienza creando tu primer servicio para tus clientes.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($servicios, 'hasPages') && $servicios->hasPages())
            <div class="px-6 py-4 border-t border-gray-100/60 bg-white/30">
                {{ $servicios->links() }}
            </div>
        @endif
    </div>

    <div class="md:hidden space-y-4">
        @forelse($servicios as $servicio)
            @php $sid = $servicio->id_servicio ?? $servicio->id; @endphp
            <div class="bb-glass-card rounded-[2rem] p-5">
                <div class="flex items-start gap-4">
                    @if(!empty($servicio->imagen))
                        <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="Foto" class="w-16 h-16 rounded-2xl object-cover shadow-sm border border-white">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex shrink-0 items-center justify-center text-gray-400 border border-white shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-black text-gray-900 truncate">{{ $servicio->nombre_servicio ?? $servicio->nombre ?? 'Servicio' }}</h3>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $servicio->descripcion ?? 'Sin descripción' }}</p>
                        
                        <div class="flex items-center gap-3 mt-3">
                            <span class="text-xs font-bold text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)] px-2.5 py-1 rounded-lg">
                                ${{ number_format($servicio->precio ?? 0, 2) }}
                            </span>
                            <span class="text-xs font-semibold text-gray-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $servicio->duracion_minutos ?? $servicio->duracion ?? 60 }} min
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100/60">
                    <a href="{{ route('admin.servicios.show', $sid) }}" class="action-btn text-blue-500 bg-blue-50 hover:bg-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('admin.servicios.edit', $sid) }}" class="action-btn text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/></svg>
                    </a>
                    <form action="{{ route('admin.servicios.destroy', $sid) }}" method="POST" class="inline-block m-0 p-0" onsubmit="return confirm('¿Seguro que deseas eliminar este servicio?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn text-red-500 bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bb-glass-card rounded-[2.5rem] p-10 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-white flex items-center justify-center text-3xl mb-4 shadow-sm border border-gray-50">✨</div>
                <h3 class="text-lg font-bold text-gray-900">No hay servicios</h3>
                <p class="text-gray-500 mt-1">Tus servicios aparecerán aquí.</p>
            </div>
        @endforelse

        @if(method_exists($servicios, 'hasPages') && $servicios->hasPages())
            <div class="py-4">
                {{ $servicios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection