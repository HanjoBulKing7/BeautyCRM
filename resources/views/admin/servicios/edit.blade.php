@extends('layouts.app')
@section('title','Editar Servicio - Salón de Belleza')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Header elegante -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-gold-50 p-3 rounded-full">
            <i class="fas fa-edit text-gold-500 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Servicio</h1>
            <p class="text-gray-600 text-sm">Actualice la información de {{ $servicio->nombre_servicio }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.servicios.update', $servicio->id_servicio) }}" class="space-y-6">
        @method('PUT')
        @csrf
        @include('admin.servicios._form', [
            'servicio' => $servicio,
            'showEstado' => true
        ])

        <!-- Botones de acción -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="px-6 py-3 rounded-lg bg-gold-500 hover:bg-gold-600 text-gray-900 font-medium flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-sync-alt"></i>
                Actualizar Servicio
            </button>
            <a href="{{ route('admin.servicios.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium flex items-center gap-2 transition-all duration-200">
                <i class="fas fa-times"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Estilos adicionales -->
<style>
    .bg-gold-50 { background-color: #FEF9E7; }
    .bg-gold-500 { background-color: #D4AF37; }
    .hover\:bg-gold-600:hover { background-color: #B8941F; }
    .text-gold-500 { color: #D4AF37; }
</style>
@endsection