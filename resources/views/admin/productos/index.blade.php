@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <span class="bb-icon-pill">
                    <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0H4m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4" />
                    </svg>
                </span>
                Productos
            </h1>
        </div>

        <a href="{{ route('admin.productos.create') }}" class="bb-btn-gold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Producto
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="bb-glass-card">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bb-thead">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Precio</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($productos as $p)
                        <tr class="bb-row">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if(!empty($p->imagen))
                                        <div class="w-30 h-20 rounded-xl overflow-hidden border border-gray-200 shadow-sm shrink-0">
                                            <img src="{{ asset('storage/' . ltrim($p->imagen,'/')) }}"
                                                 alt="Imagen producto"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        </div>
                                    @else
                                        <span class="bb-icon-pill" style="width:34px;height:34px;border-radius:12px;">
                                            <i class="fas fa-box" style="color: rgba(201,162,74,.92)"></i>
                                        </span>
                                    @endif

                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $p->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">ID: #{{ $p->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ $p->categoria->nombre ?? '—' }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                    ${{ number_format((float)$p->precio, 2) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    <a href="{{ route('admin.productos.show', $p->id) }}"
                                       class="bb-action text-gray-700 dark:text-gray-200"
                                       title="Ver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.productos.edit', $p->id) }}"
                                       class="bb-action bb-action-edit"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.productos.destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bb-action bb-action-del" title="Eliminar">
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
                            <td colspan="4" class="px-4 py-10 text-center">
                                <div class="text-gray-500">
                                    <div class="mx-auto bb-icon-pill" style="width:56px;height:56px;border-radius:18px;">
                                        <span class="text-2xl">✨</span>
                                    </div>
                                    <p class="font-semibold mt-3 text-gray-800">No hay productos registrados</p>
                                    <p class="text-sm">Crea tu primer producto para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $productos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
