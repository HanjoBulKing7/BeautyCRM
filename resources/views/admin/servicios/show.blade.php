@extends('layouts.app')
@section('title', ' - Salón de Belleza')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Header elegante -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-pink-50 p-3 rounded-full">
            <i class="fas fa-spa text-pink-400 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $servicio->nombre_servicio }}</h1>
            <p class="text-gray-600 text-sm">Detalles del servicio</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información principal -->
        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-2">Información Básica</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Categoría:</span> {{ $servicio->categoria ?? 'No especificada' }}</p>
                    <p><span class="font-medium">Precio:</span> ${{ number_format($servicio->precio, 2) }}</p>
                    <p><span class="font-medium">Duración:</span> {{ $servicio->duracion_minutos }} minutos</p>
                    <p><span class="font-medium">Estado:</span> 
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $servicio->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($servicio->estado) }}
                        </span>
                    </p>
                    @if($servicio->descuento > 0)
                    <p><span class="font-medium">Descuento:</span> ${{ number_format($servicio->descuento, 2) }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Descripción y características -->
        <div class="space-y-4">
            @if($servicio->descripcion)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-2">Descripción</h3>
                <p class="text-gray-600">{{ $servicio->descripcion }}</p>
            </div>
            @endif

            @if($servicio->caracteristicas)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-2">Características</h3>
                <p class="text-gray-600">{{ $servicio->caracteristicas }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex gap-3 pt-6 border-t border-gray-200">
        <a href="{{ route('admin.servicios.edit', $servicio->id_servicio) }}" class="px-6 py-3 rounded-lg bg-gold-500 hover:bg-gold-600 text-gray-900 font-medium flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md">
            <i class="fas fa-edit"></i>
            Editar Servicio
        </a>
        <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-medium flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md" onclick="return confirm('¿Estás seguro de eliminar este servicio?')">
                <i class="fas fa-trash"></i>
                Eliminar
            </button>
        </form>
        <a href="{{ route('admin.servicios.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium flex items-center gap-2 transition-all duration-200">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .bg-pink-50 { background-color: #FDF2F8; }
    .bg-gold-500 { background-color: #D4AF37; }
    .hover\:bg-gold-600:hover { background-color: #B8941F; }
</style>
@endsection