@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
        <!-- Header y botón -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-light text-gray-800">Gestión de Categorías</h1>
                <p class="text-gray-500 mt-1">Administra las categorías de servicios</p>
            </div>
            <a href="{{ route('admin.categoriaservicios.create') }}" class="btn-primary text-white px-4 py-2 rounded-md flex items-center mt-4 md:mt-0">
                <i data-feather="plus" class="mr-2 w-4 h-4"></i>
                Nueva Categoría
            </a>
        </div>

        <!-- Mostrar mensajes de éxito/error -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tarjeta de contenido -->
        <div class="card overflow-hidden">
            <!-- Barra de búsqueda y filtros -->
            <div class="p-4 border-b flex flex-col md:flex-row md:items-center justify-between">
                <div class="relative mb-4 md:mb-0">
                    <i data-feather="search" class="absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" placeholder="Buscar categorías..." class="pl-10 pr-4 py-2 w-full md:w-64 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-300">
                </div>
                <div class="flex space-x-2">
                    <select class="px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-300 text-sm">
                        <option>Todos los estados</option>
                        <option>Activas</option>
                        <option>Inactivas</option>
                    </select>
                </div>
            </div>

            <!-- Tabla de categorías -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicios</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categorias as $categoria)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($categoria->imagen)
                                    <img src="{{ asset('storage/' . $categoria->imagen) }}" alt="{{ $categoria->nombre }}" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i data-feather="image" class="text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $categoria->nombre }}</div>
                                <div class="text-sm text-gray-500">{{ $categoria->slug }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 max-w-xs truncate">{{ $categoria->descripcion ?? 'Sin descripción' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $categoria->servicios->count() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $categoria->estado == 'activa' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($categoria->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.categoriaservicios.show', $categoria->id_categoria) }}" class="text-gray-500 hover:text-gray-700 mr-3">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.categoriaservicios.edit',  $categoria->id_categoria) }}" class="text-gray-500 hover:text-gray-700 mr-3">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.categoriaservicios.destroy', $categoria->id_categoria) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-gray-700" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando <span class="font-medium">{{ $categorias->count() }}</span> resultados
                        </p>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
    <!-- JS -->
    <script>
        // Inicializar feather icons
        feather.replace();
        // Manejar el menú móvil
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    </script>
@endsection