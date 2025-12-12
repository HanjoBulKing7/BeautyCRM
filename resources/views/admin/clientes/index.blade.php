@extends('layouts.app')

@section('title', 'Clientes - Salón de Belleza')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-pink-100 text-pink-700">
                    <!-- icon: users -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H2v-2a4 4 0 013-3.87m11-3.13a4 4 0 10-8 0 4 4 0 008 0zM20 8a4 4 0 00-6.2-3.33"/>
                    </svg>
                </span>
                Clientes
            </h1>
            <p class="text-gray-600 mt-1">Administra los clientes registrados del salón</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.clientes.create') }}"
               class="inline-flex items-center gap-2 bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Cliente
            </a>
        </div>
    </div>

    <!-- Tabla (sin líneas divisorias) -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-pink-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($clientes as $cliente)
                        <tr class="hover:bg-pink-50/40 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex items-center justify-center w-8 h-8 rounded-lg bg-pink-100 text-pink-700">
                                        <!-- icon: user -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </span>

                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $cliente->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: #{{ $cliente->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">{{ $cliente->email }}</div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ $cliente->telefono ?? 'No especificado' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Ver -->
                                    <a href="{{ route('admin.clientes.show', $cliente->id) }}"
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
                                    <a href="{{ route('admin.clientes.edit', $cliente->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-pink-700 hover:bg-pink-50"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                                        </svg>
                                    </a>

                                    <!-- Eliminar -->
                                    <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar cliente?')" class="inline">
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
                            <td colspan="4" class="px-4 py-10 text-center">
                                <div class="text-gray-500">
                                    <span class="text-4xl mb-2 block">👥</span>
                                    <p class="font-semibold">No hay clientes registrados</p>
                                    <p class="text-sm">Crea tu primer cliente para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
