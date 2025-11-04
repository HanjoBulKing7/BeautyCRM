@extends('layouts.app')

@section('title', 'Gastos - CRM')

@section('page-title', 'Gestión de Gastos')

@section('content')
<div class="bg-white p-3 md:p-6 rounded-lg shadow">
    <!-- Encabezado con botones -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
<h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mb-2 md:mb-0">
    <i class="fas fa-money-bill-wave text-green-600"></i>
    Gastos
</h3>
<div>
    <a href="{{ route('gastos.create') }}" 
       class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center text-sm md:text-base 
              transform transition duration-200 hover:scale-105">

        <img src="{{ asset('_Iconos/_Default/Icon_Add.svg') }}" 
             alt="Agregar Gasto" 
             class="w-6 h-6 mr-2">
        Nuevo Gasto
    </a>
</div>


    </div>

    <!-- Filtros + Total del día -->
    <div class="bg-gray-50 p-3 md:p-4 rounded-lg mb-4 md:mb-6">
        <h4 class="font-medium text-gray-700 mb-2 md:mb-3">Buscar gastos</h4>

        <div class="md:flex md:items-end md:gap-4">
            <form method="GET" action="{{ route('gastos.index') }}" class="flex-1 space-y-2 md:space-y-0 md:grid md:grid-cols-4 md:gap-4 md:items-end">
                @php
                    $fechaSeleccionada = isset($fecha) ? $fecha : now()->toDateString();
                @endphp

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select name="categoria" class="w-full p-2 border rounded-md text-sm">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $key => $value)
                            <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="tipo" class="w-full p-2 border rounded-md text-sm">
                        <option value="">Todos los tipos</option>
                        <option value="general" {{ request('tipo') == 'general' ? 'selected' : '' }}>Gastos Generales</option>
                        <option value="ruta" {{ request('tipo') == 'ruta' ? 'selected' : '' }}>Gastos de Ruta</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input
                        type="date"
                        name="fecha"
                        value="{{ request('fecha', $fechaSeleccionada) }}"
                        class="w-full p-2 border rounded-md text-sm">
                </div>

                <div class="flex gap-2 pt-2 md:pt-0">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg flex items-center text-sm flex-1 justify-center">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('gastos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg flex items-center text-sm flex-1 justify-center">
                        <i class="fas fa-times mr-1"></i> Limpiar
                    </a>
                </div>
            </form>

            <!-- Total del día (a la derecha en desktop, abajo en móvil) -->
            <div class="mt-3 md:mt-0 md:ml-auto md:w-64">
                <div class="bg-white border rounded-lg p-3 shadow-sm">
                    <div class="text-xs uppercase text-gray-500">Total del día</div>
                    <div class="text-2xl font-bold text-gray-800">
                        ${{ number_format($totalDia ?? 0, 2) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de gastos optimizada para móviles -->
    <div class="overflow-x-auto">
        <!-- Vista para móviles (tarjetas) -->
        <div class="md:hidden space-y-3">
            @forelse($gastos as $gasto)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-medium text-gray-900">{{ $gasto->descripcion }}</div>
                            <div class="text-sm text-gray-500">{{ $gasto->fecha->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-red-600">${{ number_format($gasto->monto, 2) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center mb-2">
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            {{ $categorias[$gasto->categoria] ?? $gasto->categoria }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">{{ $gasto->metodo_pago }}</span>
                    </div>

                    @if($gasto->sucursal)
                        <div class="text-xs text-gray-500 mb-3">{{ $gasto->sucursal->nombre }}</div>
                    @endif

                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="{{ route('gastos.edit', $gasto) }}" class="text-green-500 hover:text-green-700 p-1" title="Editar">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('gastos.destroy', $gasto) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este gasto?')">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                            @if($gasto->comprobante_url)
                                <a href="{{ route('gastos.download.comprobante', $gasto) }}" class="text-purple-500 hover:text-purple-700 p-1" title="Descargar comprobante">
                                    <i class="fas fa-download text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-500">
                    No se encontraron gastos con los filtros aplicados.
                </div>
            @endforelse
        </div>

        <!-- Vista para desktop (tabla) -->
        <table class="min-w-full bg-white border border-gray-200 hidden md:table">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta</th>
                    <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($gastos as $gasto)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">{{ $gasto->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 border">
                            <div class="font-medium">{{ $gasto->descripcion }}</div>
                            <div class="text-xs text-gray-500">{{ $gasto->metodo_pago }}</div>
                        </td>
                        <td class="px-4 py-3 border">
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $categorias[$gasto->categoria] ?? $gasto->categoria }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border font-medium text-red-600">${{ number_format($gasto->monto, 2) }}</td>
                        <td class="px-4 py-3 border">
                            @if($gasto->ruta)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-route text-blue-500 text-xs"></i>
                                    <span class="text-xs text-blue-600">{{ $gasto->ruta->nombre }}</span>
                                </div>
                                <div class="text-xs text-gray-500">{{ $gasto->ruta->empleado->nombre }}</div>
                            @else
                                <span class="text-xs text-gray-400">General</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border">
                            <div class="flex space-x-2">
                                <a href="{{ route('gastos.edit', $gasto) }}" class="text-green-500 hover:text-green-700 p-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('gastos.destroy', $gasto) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este gasto?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 border text-center text-gray-500">
                            No se encontraron gastos con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación responsive -->
    <div class="mt-4 md:mt-6">
        {{ $gastos->links() }}
    </div>
</div>
@endsection
