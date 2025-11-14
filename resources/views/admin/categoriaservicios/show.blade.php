@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-light text-gray-800">Detalles de Categoría</h1>
                <p class="text-gray-500 mt-1">Información completa de la categoría</p>
            </div>
            <div class="flex space-x-2 mt-4 md:mt-0">
                <a href="{{ route('admin.categoriaservicios.edit', $categoria->id_categoria) }}" class="btn-primary text-white px-4 py-2 rounded-md flex items-center">
                    <i data-feather="edit" class="mr-2 w-4 h-4"></i>
                    Editar
                </a>
                <a href="{{ route('admin.categoriaservicios.index') }}" class="btn-secondary text-white px-4 py-2 rounded-md flex items-center">
                    <i data-feather="arrow-left" class="mr-2 w-4 h-4"></i>
                    Volver
                </a>
            </div>
        </div>

        <!-- Tarjeta de detalles -->
        <div class="card overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Imagen -->
                    <div class="md:col-span-1 flex justify-center">
                        @if($categoria->imagen)
                            <img src="{{ asset('storage/' . $categoria->imagen) }}" alt="{{ $categoria->nombre }}" class="h-40 w-40 object-cover rounded-lg">
                        @else
                            <div class="h-40 w-40 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i data-feather="image" class="text-gray-400 h-12 w-12"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Información -->
                    <div class="md:col-span-2">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $categoria->nombre }}</h2>
                        <p class="text-sm text-gray-500 mb-4">{{ $categoria->slug }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Estado</p>
                                <span class="px-2 py-1 text-xs rounded-full {{ $categoria->estado == 'activa' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($categoria->estado) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Servicios asociados</p>
                                <p class="text-sm text-gray-900">{{ $categoria->servicios->count() }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-600">Descripción</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $categoria->descripcion ?? 'Sin descripción' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Fecha de creación</p>
                                <p class="text-sm text-gray-900">{{ $categoria->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Última actualización</p>
                                <p class="text-sm text-gray-900">{{ $categoria->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios asociados -->
        @if($categoria->servicios->count() > 0)
        <div class="mt-6">
            <h2 class="text-xl font-light text-gray-800 mb-4">Servicios en esta categoría</h2>
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categoria->servicios as $servicio)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $servicio->nombre_servicio }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($servicio->precio, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $servicio->duracion_minutos }} min</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $servicio->estado == 'activo' ? 'badge-active' : 'badge-inactive' }}">
                                        {{ ucfirst($servicio->estado) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
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