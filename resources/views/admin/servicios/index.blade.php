@extends('layouts.app')

@section('title', 'Servicios - Salón de Belleza')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Servicios</h1>
    
    <a href="{{ route('admin.servicios.create') }}" class="bg-pink-400 hover:bg-pink-500 text-gray-900 px-4 py-2 rounded-lg mb-4 inline-block">
        Nuevo Servicio
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-pink-50">
                    <th class="px-4 py-3 border text-left">Nombre</th>
                    <th class="px-4 py-3 border text-left">Precio</th>
                    <th class="px-4 py-3 border text-left">Duración</th>
                    <th class="px-4 py-3 border text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                    <tr>
                        <td class="px-4 py-3 border">{{ $servicio->nombre_servicio }}</td>
                        <td class="px-4 py-3 border">${{ number_format($servicio->precio, 2) }}</td>
                        <td class="px-4 py-3 border">{{ $servicio->duracion_minutos }} min</td>
                        <td class="px-4 py-3 border">
                            <a href="{{ route('admin.servicios.edit', $servicio->id_servicio) }}" class="text-blue-500">Editar</a>
                            <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 ml-2">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $servicios->links() }}
    </div>
</div>
@endsection