@extends('layouts.app')

@section('title', 'Editar Ruta')
@section('page-title', 'Editar Ruta')

@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-route text-blue-500 mr-3"></i> Editar Ruta
    </h2>

    <form action="{{ route('rutas.update', $ruta) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('rutas._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('rutas.show', $ruta) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Actualizar</button>
        </div>
    </form>
</div>
@endsection