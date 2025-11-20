@extends('layouts.app')
@section('title','Editar Cliente - Salón de Belleza')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Header elegante -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-azul-50 p-3 rounded-full">
            <i class="fas fa-user-edit text-azul-400 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Cliente</h1>
            <p class="text-gray-600 text-sm">Actualice la información de {{ $cliente->nombre }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.clientes.update', $cliente->id) }}" class="space-y-6">
        @method('PUT')
        @csrf
        @include('clientes._form', ['cliente' => $cliente])

        <!-- Botones de acción -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="px-6 py-3 rounded-lg bg-verde-500 hover:bg-verde-600 text-white font-medium flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-sync-alt"></i>
                Actualizar Cliente
            </button>
            <a href="{{ route('admin.clientes.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium flex items-center gap-2 transition-all duration-200">
                <i class="fas fa-times"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Estilos adicionales -->
<style>
    .bg-azul-50 { background-color: #F0F9FF; }
    .bg-verde-500 { background-color: #22C55E; }
    .hover\:bg-verde-600:hover { background-color: #16A34A; }
    .text-azul-400 { color: #60A5FA; }
</style>
@endsection