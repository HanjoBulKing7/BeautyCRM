@extends('layouts.app')

@section('title', 'Clientes - Salón de Belleza')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Clientes</h1>
    
    <a href="{{ route('admin.clientes.create') }}" class="bg-azul-500 hover:bg-azul-600 text-white px-4 py-2 rounded-lg mb-4 inline-block">
        Nuevo Cliente
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-azul-50">
                    <th class="px-4 py-3 border text-left">Nombre</th>
                    <th class="px-4 py-3 border text-left">Email</th>
                    <th class="px-4 py-3 border text-left">Teléfono</th>
                    <th class="px-4 py-3 border text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                    <tr>
                        <td class="px-4 py-3 border">{{ $cliente->nombre }}</td>
                        <td class="px-4 py-3 border">{{ $cliente->email }}</td>
                        <td class="px-4 py-3 border">{{ $cliente->telefono ?? 'No especificado' }}</td>
                        <td class="px-4 py-3 border">
                            <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="text-verde-500 hover:text-verde-600 mr-2">
                                Ver
                            </a>
                            <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="text-azul-500 hover:text-azul-600 mr-2">
                                Editar
                            </a>
                            <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600" onclick="return confirm('¿Eliminar cliente?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
    .bg-azul-50 { background-color: #F0F9FF; }
    .bg-azul-500 { background-color: #3B82F6; }
    .hover\:bg-azul-600:hover { background-color: #2563EB; }
    .text-azul-500 { color: #3B82F6; }
    .hover\:text-azul-600:hover { color: #2563EB; }
    .text-verde-500 { color: #22C55E; }
    .hover\:text-verde-600:hover { color: #16A34A; }
</style>
@endsection