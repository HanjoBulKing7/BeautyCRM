@extends('layouts.app')

@section('title', 'Empleados - Salón de Belleza')

@section('page-title', 'Gestión de Empleados')

@section('content')
<div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-200">
    <!-- Encabezado con botones -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mb-2 md:mb-0">
            <i class="fas fa-users mr-3 text-pink-400"></i>
            Empleados
        </h3>
        <div>
            <a href="{{ route('admin.empleados.create') }}" 
               class="bg-pink-400 hover:bg-pink-500 text-gray-900 px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center text-sm md:text-base 
                      transform transition duration-200 hover:scale-105 shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Empleado
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-gray-50 p-3 md:p-4 rounded-lg mb-4 md:mb-6 border border-gray-200">
        <form method="GET" action="{{ route('admin.empleados.index') }}" class="space-y-2 md:space-y-0 md:flex md:gap-4 md:items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500" 
                       placeholder="Nombre, apellido, puesto o departamento">
            </div>

            <div class="flex gap-2 pt-2 md:pt-0">
                <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-gray-900 px-3 py-2 rounded-lg flex items-center text-sm flex-1 justify-center shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                <a href="{{ route('admin.empleados.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 rounded-lg flex items-center text-sm flex-1 justify-center shadow-sm">
                    <i class="fas fa-times mr-1"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de empleados -->
    <div class="overflow-x-auto">
        <!-- Vista para móviles (tarjetas) -->
        <div class="md:hidden space-y-3">
            @forelse($empleados as $empleado)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-medium text-gray-900">{{ $empleado->nombre }} {{ $empleado->apellido }}</div>
                            <div class="text-sm text-gray-500">{{ $empleado->puesto }}</div>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-gold-600 capitalize">{{ $empleado->departamento }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $empleado->estatus == 'activo' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $empleado->estatus == 'inactivo' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $empleado->estatus == 'vacaciones' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ ucfirst($empleado->estatus) }}
                        </span>
                        <span class="text-xs text-gray-500">
                            Tel: {{ $empleado->telefono }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.empleados.edit', $empleado) }}" class="text-gold-500 hover:text-gold-700 p-1 transition-colors" title="Editar">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.empleados.destroy', $empleado) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 p-1 transition-colors" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-users text-3xl text-gray-300 mb-2"></i>
                    <p>No se encontraron empleados.</p>
                </div>
            @endforelse
        </div>

        <!-- Vista para desktop (tabla) -->
        <table class="min-w-full bg-white border border-gray-200 hidden md:table">
            <thead>
                <tr class="bg-pink-50">
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Apellido</th>
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Teléfono</th>
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Puesto</th>
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Departamento</th>
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estatus</th>
                    <th class="px-4 py-3 border text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($empleados as $empleado)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 border font-medium text-gray-900">{{ $empleado->nombre }}</td>
                        <td class="px-4 py-3 border text-gray-700">{{ $empleado->apellido }}</td>
                        <td class="px-4 py-3 border text-gray-700">{{ $empleado->telefono }}</td>
                        <td class="px-4 py-3 border text-gray-700">{{ $empleado->puesto }}</td>
                        <td class="px-4 py-3 border text-gray-700">{{ $empleado->departamento }}</td>
                        <td class="px-4 py-3 border">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $empleado->estatus == 'activo' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $empleado->estatus == 'inactivo' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $empleado->estatus == 'vacaciones' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($empleado->estatus) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.empleados.edit', $empleado) }}" class="text-gold-500 hover:text-gold-700 p-1 transition-colors" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.empleados.destroy', $empleado) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 p-1 transition-colors" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 border text-center text-gray-500">
                            <i class="fas fa-users text-2xl text-gray-300 mb-2 block"></i>
                            No se encontraron empleados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4 md:mt-6">
        {{ $empleados->links() }}
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .bg-pink-50 { background-color: #FDF2F8; }
    .bg-pink-400 { background-color: #F8BBD9; }
    .hover\:bg-pink-500:hover { background-color: #F8A0C9; }
    .text-gold-500 { color: #D4AF37; }
    .text-gold-600 { color: #B8941F; }
    .hover\:text-gold-700:hover { color: #9C7A1A; }
    .bg-gold-500 { background-color: #D4AF37; }
    .hover\:bg-gold-600:hover { background-color: #B8941F; }
</style>
@endsection