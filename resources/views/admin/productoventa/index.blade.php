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
        border: none;
        cursor: pointer;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Estilos para modo oscuro (opcional) */
    @media (prefers-color-scheme: dark) {
        .bb-glass-card {
            background: rgba(30, 41, 59, 0.7);
            border-color: rgba(255, 255, 255, 0.1);
        }
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl">

    {{-- Encabezado --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-[rgba(201,162,74,0.1)] text-[rgba(201,162,74,1)] flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                Ventas de Productos
            </h1>
            <p class="text-sm text-gray-500 mt-1 ml-15">Historial de transacciones y facturación de productos.</p>
        </div>

        <a href="{{ route('admin.productoventa.create') }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-gray-900 text-white font-bold hover:bg-black shadow-lg hover:shadow-xl transition-all md:w-auto w-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Venta
        </a>
    </div>

    {{-- Alertas --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 text-green-800 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Vista de Escritorio --}}
    <div class="hidden md:block bb-glass-card rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200/60">
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">ID / Fecha</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Cliente</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Atendió</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Pago</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Total</th>
                        <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/60">
                    @forelse($ventas as $venta)
                        <tr class="hover:bg-white/40 transition-colors group">
                            <td class="py-4 px-4">
                                <div class="text-sm font-black text-gray-900 dark:text-white">#{{ $venta->id }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">
                                    {{ $venta->created_at->format('d M, Y H:i') }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-[rgba(201,162,74,0.08)] flex items-center justify-center text-[rgba(201,162,74,0.9)] border border-[rgba(201,162,74,0.2)] shrink-0">
                                        <i class="fas fa-user text-lg"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $venta->cliente->nombre ?? 'Venta Mostrador' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ $venta->user->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="status-badge bg-blue-50 text-blue-600 border border-blue-100">
                                    {{ $venta->metodo_pago }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="text-sm font-black text-gray-900 dark:text-white">
                                    ${{ number_format($venta->total, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.productoventa.show', $venta->id) }}" class="action-btn text-blue-500 bg-blue-50 hover:bg-blue-100" title="Ver Detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.productoventa.edit', $venta->id) }}" class="action-btn text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)] hover:bg-[rgba(201,162,74,.2)]" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.productoventa.destroy', $venta->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta venta?');" class="inline">
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
                            <td colspan="6" class="py-12 text-center">
                                <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center text-3xl mb-4 shadow-inner">✨</div>
                                <h3 class="text-lg font-bold text-gray-900">No hay registros de ventas</h3>
                                <p class="text-gray-500 mt-1">Las transacciones de productos aparecerán aquí.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($ventas, 'hasPages') && $ventas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100/60 bg-white/30">
                {{ $ventas->links() }}
            </div>
        @endif
    </div>

    {{-- Vista Móvil --}}
    <div class="md:hidden space-y-4">
        @forelse($ventas as $venta)
            <div class="bb-glass-card rounded-[2rem] p-5">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[rgba(201,162,74,0.08)] flex shrink-0 items-center justify-center text-[rgba(201,162,74,0.9)] border border-[rgba(201,162,74,0.2)]">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h3 class="text-base font-black text-gray-900 truncate">Venta #{{ $venta->id }}</h3>
                            <span class="text-sm font-black text-gray-900">${{ number_format($venta->total, 2) }}</span>
                        </div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5 mb-2">{{ $venta->created_at->format('d/m/Y H:i') }}</div>
                        
                        <div class="space-y-1.5 mt-3">
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                                <i class="fas fa-user text-gray-400 w-3 text-center"></i>
                                <span class="truncate">{{ $venta->cliente->nombre ?? 'Venta Mostrador' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                                <i class="fas fa-credit-card text-gray-400 w-3 text-center"></i>
                                <span>{{ $venta->metodo_pago }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100/60">
                    <a href="{{ route('admin.productoventa.show', $venta->id) }}" class="action-btn text-blue-500 bg-blue-50 hover:bg-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('admin.productoventa.edit', $venta->id) }}" class="action-btn text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)] hover:bg-[rgba(201,162,74,.2)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/></svg>
                    </a>
                    <form action="{{ route('admin.productoventa.destroy', $venta->id) }}" method="POST" onsubmit="return confirm('¿Eliminar venta?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn text-red-500 bg-red-50 hover:bg-red-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bb-glass-card rounded-[2.5rem] p-10 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-white flex items-center justify-center text-3xl mb-4 shadow-sm border border-gray-50">✨</div>
                <h3 class="text-lg font-bold text-gray-900">No hay ventas</h3>
                <p class="text-gray-500 mt-1">Tus transacciones aparecerán aquí.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection