@extends('layouts.app')
@section('title','Crear Empleado - Salón de Belleza')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Header elegante -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-pink-50 p-3 rounded-full">
            <i class="fas fa-user-plus text-pink-400 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Empleado</h1>
            <p class="text-gray-600 text-sm">Agregue un nuevo miembro al equipo del salón</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.empleados.store') }}" class="space-y-6">
        @csrf
        @include('empleados._form', ['empleado' => $empleado])

        <!-- Botones de acción -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="px-6 py-3 rounded-lg bg-pink-400 hover:bg-pink-500 text-gray-900 font-medium flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-save"></i>
                Guardar Empleado
            </button>
            <a href="{{ route('admin.empleados.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium flex items-center gap-2 transition-all duration-200">
                <i class="fas fa-times"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Estilos adicionales -->
<style>
    .bg-pink-50 { background-color: #FDF2F8; }
    .bg-pink-400 { background-color: #F8BBD9; }
    .hover\:bg-pink-500:hover { background-color: #F8A0C9; }
    .text-pink-400 { color: #F8BBD9; }
</style>
@endsection