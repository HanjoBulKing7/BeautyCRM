@extends('layouts.app')

@section('title', 'Ventas - CRM')
@section('page-title', 'Gestión de Ventas')

@section('content')
<div class="bg-white p-4 md:p-6 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <!-- Encabezado con título -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-2 md:mb-0">
            <i class="fas fa-cash-register mr-3 text-red-500"></i>
            Ventas
        </h3>
        
        <div class="flex items-center gap-2">
            <!-- Filtro de fecha -->
            <form method="GET" action="{{ route('ventas.index') }}" class="flex items-center gap-2">
                <input 
                    type="date" 
                    name="fecha" 
                    value="{{ request('fecha') }}" 
                    onchange="this.form.submit()" 
                    class="border rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer"
                    title="Filtrar por fecha">
            </form>
            
            <!-- Botón Nueva Venta -->
            <a href="{{ route('ventas.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center text-sm font-medium
                      transform transition duration-200 hover:scale-105">
                <img src="{{ asset('_Iconos/_Default/Icon_Add.svg') }}" 
                     alt="Nueva Venta" 
                     class="w-5 h-5 mr-2">
                Nueva Venta
            </a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
        <!-- Tabla de ventas -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <th class="px-3 py-2 md:px-4 md:py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 dark:border-gray-600">ID</th>
                        <th class="px-3 py-2 md:px-4 md:py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 dark:border-gray-600">Fecha</th>
                        <th class="px-3 py-2 md:px-4 md:py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 dark:border-gray-600">Total</th>
                        <th class="px-3 py-2 md:px-4 md:py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 dark:border-gray-600">Método de Pago</th>
                        <th class="px-3 py-2 md:px-4 md:py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 dark:border-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($ventas->sortByDesc('fecha') as $venta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-3 py-2 md:px-4 md:py-3 border dark:border-gray-600">
                                <div class="font-medium dark:text-white">{{ $venta->id }}</div>
                                <div class="text-xs text-gray-500 md:hidden dark:text-gray-400">
                                    {{ $venta->cliente ? $venta->cliente->nombre : 'Cliente anónimo' }}
                                </div>
                            </td>
                            <td class="px-3 py-2 md:px-4 md:py-3 border dark:border-gray-600 dark:text-gray-300">{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 md:px-4 md:py-3 border dark:border-gray-600 dark:text-white font-semibold">${{ number_format($venta->total, 2) }}</td>
                            <td class="px-3 py-2 md:px-4 md:py-3 border dark:border-gray-600">
                                @if($venta->metodo_pago === 'multipago')
                                    <!-- Para pagos múltiples -->
                                    <div class="text-sm font-medium text-purple-600 dark:text-purple-400">Múltiples métodos</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @foreach($venta->pagos as $pago)
                                            <div class="mb-1">
                                                {{ ucfirst($pago->metodo_pago) }}: ${{ number_format($pago->monto, 2) }}
                                                @if($pago->metodo_pago === 'transferencia' && $pago->destinatario_transferencia)
                                                    <span class="text-blue-600 dark:text-blue-400 ml-1">(a {{ $pago->destinatario_transferencia }})</span>
                                                @endif
                                                @if($pago->referencia_pago)
                                                    <div class="text-gray-400 dark:text-gray-500 text-xs ml-2">Ref: {{ $pago->referencia_pago }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Para pagos únicos -->
                                    <div class="text-sm font-medium dark:text-gray-300">
                                        {{ ucfirst($venta->metodo_pago) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @if($venta->metodo_pago === 'transferencia' && $venta->pagos->isNotEmpty() && $venta->pagos->first()->destinatario_transferencia)
                                            <div class="text-blue-600 dark:text-blue-400">A: {{ $venta->pagos->first()->destinatario_transferencia }}</div>
                                        @endif
                                        @if($venta->referencia_pago)
                                            <div class="text-gray-400 dark:text-gray-500">Ref: {{ $venta->referencia_pago }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2 md:px-4 md:py-3 border dark:border-gray-600">
                                <div class="flex space-x-1 md:space-x-2">
                                    <a href="{{ route('ventas.ticket', $venta) }}" 
                                       class="text-purple-500 hover:text-purple-700 p-1 dark:text-purple-400 dark:hover:text-purple-300" 
                                       title="Imprimir Ticket"
                                       target="_blank">
                                        <i class="fas fa-receipt text-sm md:text-base"></i>
                                    </a>
                                    
                                    @if(Auth::user()->rol === 'admin')
                                        <a href="{{ route('ventas.edit', $venta) }}" class="text-green-500 hover:text-green-700 p-1 dark:text-green-400 dark:hover:text-green-300" title="Editar">
                                            <i class="fas fa-edit text-sm md:text-base"></i>
                                        </a>
                                        <form action="{{ route('ventas.destroy', $venta) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1 dark:text-red-400 dark:hover:text-red-300" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                                <i class="fas fa-trash text-sm md:text-base"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Mostrar iconos deshabilitados para usuarios no administradores -->
                                        <span class="text-gray-400 p-1 cursor-not-allowed dark:text-gray-500" title="Solo administradores pueden editar">
                                            <i class="fas fa-edit text-sm md:text-base"></i>
                                        </span>
                                        <span class="text-gray-400 p-1 cursor-not-allowed dark:text-gray-500" title="Solo administradores pueden eliminar">
                                            <i class="fas fa-trash text-sm md:text-base"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-3 md:px-4 md:py-4 border text-center text-gray-500 dark:text-gray-400 dark:border-gray-600">
                                No se encontraron ventas con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4 md:mt-6 dark:text-gray-300">
            {{ $ventas->links() }}
        </div>
    </div>
</div>
@endsection