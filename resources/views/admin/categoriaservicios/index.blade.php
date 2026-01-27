@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <span class="bb-icon-pill">
                    <!-- icon: layers -->
                    <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 2l9 5-9 5-9-5 9-5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l9 5 9-5" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 17l9 5 9-5" />
                    </svg>
                </span>
                Categorías de Servicios
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.categoriaservicios.create') }}" class="bb-btn-gold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Categoría
            </a>
        </div>
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

    <!-- Tabla -->
    <div class="bb-glass-card">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bb-thead">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categorias as $categoria)
                        <tr class="bb-row">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if(!empty($categoria->imagen))
                                        <div class="w-30 h-20 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm shrink-0">
                                            <img
                                                src="{{ asset('storage/' . $categoria->imagen) }}"
                                                alt="Foto de la categoría"
                                                class="w-full h-full object-cover"
                                                loading="lazy"
                                            >
                                        </div>
                                    @else
                                        <span class="bb-icon-pill" style="width:34px;height:34px;border-radius:12px;">
                                            <!-- icon: tag -->
                                            <svg class="w-4 h-4 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0L21 13.414a2 2 0 000-2.828L12.414 2H6a2 2 0 00-2 2v6z" />
                                            </svg>
                                        </span>
                                    @endif

                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $categoria->nombre ?? 'Categoría' }}
                                        </div>
                                        <div class="text-xs text-gray-500">ID: #{{ $categoria->id_categoria }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ ($categoria->estado ?? 'activo') == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($categoria->estado ?? 'activo') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    <!-- Ver -->
                                    <a href="{{ route('admin.categoriaservicios.show', $categoria->id_categoria) }}"
                                       class="bb-action text-gray-700 dark:text-gray-200"
                                       title="Ver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Editar -->
                                    <a href="{{ route('admin.categoriaservicios.edit', $categoria->id_categoria) }}"
                                       class="bb-action bb-action-edit"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                                        </svg>
                                    </a>

                                    <!-- Eliminar -->
                                    <form action="{{ route('admin.categoriaservicios.destroy', $categoria->id_categoria) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bb-action bb-action-del"
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
                                <div class="text-gray-500 dark:text-gray-300">
                                    <div class="mx-auto bb-icon-pill" style="width:56px;height:56px;border-radius:18px;">
                                        <span class="text-2xl">✨</span>
                                    </div>
                                    <p class="font-semibold mt-3 text-gray-800 dark:text-white">No hay categorías registradas</p>
                                    <p class="text-sm">Crea tu primera categoría para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación (si luego cambias a paginate) --}}
        @if(method_exists($categorias, 'hasPages') && $categorias->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $categorias->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
