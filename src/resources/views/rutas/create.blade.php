@extends('layouts.app')

@section('title', 'Nueva Ruta')
@section('page-title', 'Registrar Nueva Ruta')

@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-route text-blue-500 mr-3"></i> Crear Nueva Ruta
    </h2>

    <form action="{{ route('rutas.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('rutas._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('rutas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Guardar</button>
        </div>
    </form>
</div>
@endsection
