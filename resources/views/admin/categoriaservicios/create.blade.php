@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-light text-gray-800">Crear Nueva Categoría</h1>
            <p class="text-gray-500 mt-1">Completa el formulario para agregar una nueva categoría de servicios</p>
        </div>

        <!-- Mostrar errores de validación -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <div class="card p-6">
            <form action="{{ route('admin.categoriaservicios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la categoría *</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-300"
                               required>
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-300">{{ old('descripcion') }}</textarea>
                    </div>

                    <!-- Imagen -->
                    <div class="md:col-span-2">
                        <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">Imagen</label>
                        <input type="file" name="imagen" id="imagen" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-300"
                               accept="image/jpeg,image/png,image/jpg,image/gif">
                        <p class="text-xs text-gray-500 mt-1">Formatos permitidos: jpeg, png, jpg, gif. Tamaño máximo: 2MB</p>
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-2">
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select name="estado" id="estado" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-300" required>
                            <option value="">Seleccionar estado</option>
                            <option value="activa" {{ old('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="inactiva" {{ old('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-8">
                    <a href="{{ route('admin.categoriaservicios.index') }}" class="btn-secondary text-white px-4 py-2 rounded-md flex items-center">
                        <i data-feather="arrow-left" class="mr-2 w-4 h-4"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary text-white px-4 py-2 rounded-md flex items-center">
                        <i data-feather="save" class="mr-2 w-4 h-4"></i>
                        Guardar Categoría
                    </button>
                </div>
            </form>
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