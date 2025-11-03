@extends('layouts.app')

@section('title', 'Inventario - El Progreso')

@section('content')
<div class="min-h-screen bg-gray-50 p-6 dark:bg-gray-800">
  <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-[70%_30%] gap-6">

    <!-- ================== COLUMNA IZQUIERDA ================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 dark:bg-gray-700 dark:border-gray-600">
      <!-- Encabezado -->
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-semibold flex items-center gap-2 text-gray-800 dark:text-white">
          📦 Inventario
        </h2>
        <div class="flex gap-2">
          <a href="{{ route('productos.create') }}" 
             class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow transition">
            + Producto nuevo
          </a>
        </div>
      </div>

      <!-- Filtros rápidos -->
      <div class="flex flex-wrap gap-3 mb-4 p-3 bg-gray-50 rounded-lg dark:bg-gray-600">
        <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Filtrar por estado:</span>
        <a href="{{ request()->fullUrlWithQuery(['estado_stock' => '']) }}" 
           class="px-3 py-1 rounded-full text-xs font-medium {{ !request('estado_stock') ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
          Todos
        </a>
        <a href="{{ request()->fullUrlWithQuery(['estado_stock' => 'bajo']) }}" 
           class="px-3 py-1 rounded-full text-xs font-medium {{ request('estado_stock') == 'bajo' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
          Stock Bajo
        </a>
        <a href="{{ request()->fullUrlWithQuery(['estado_stock' => 'medio']) }}" 
           class="px-3 py-1 rounded-full text-xs font-medium {{ request('estado_stock') == 'medio' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
          Stock Medio
        </a>
        <a href="{{ request()->fullUrlWithQuery(['estado_stock' => 'alto']) }}" 
           class="px-3 py-1 rounded-full text-xs font-medium {{ request('estado_stock') == 'alto' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
          Stock Alto
        </a>
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <form id="inventario-form" method="POST" action="{{ route('inventario.bulk-update') }}">
          @csrf
          <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm dark:border-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs dark:bg-gray-600 dark:text-gray-300">
              <tr>
                <th class="px-4 py-3 text-left">Producto</th>
                <th class="px-4 py-3 text-center">Precio Proveedor</th>
                <th class="px-4 py-3 text-center text-green-600 dark:text-green-400">Entradas</th>
                <th class="px-4 py-3 text-center text-red-600 dark:text-red-400">Salidas</th>
                <th class="px-4 py-3 text-center text-blue-600 dark:text-blue-400">Stock Actual</th>
                <th class="px-4 py-3 text-center">Stock Mínimo</th>
                <th class="px-4 py-3 text-center">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-600">

              @forelse($existencias as $existencia)
                @php
                  $estado = $existencia->stock_actual > $existencia->stock_minimo * 1.5 ? 'Alto' :
                            ($existencia->stock_actual > $existencia->stock_minimo ? 'Medio' : 'Bajo');
                @endphp

                <tr class="hover:bg-gray-50 transition text-center dark:hover:bg-gray-600" data-precio="{{ $existencia->producto->precio_proveedor ?? 0 }}">
                  <!-- Producto -->
                  <td class="px-4 py-3 font-medium text-gray-800 text-left dark:text-gray-200">
                    📦 {{ $existencia->producto->nombre }}
                    @if(!$existencia->producto->activo)
                      <span class="ml-2 bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Inactivo</span>
                    @endif
                  </td>

                  <!-- Precio Proveedor -->
                  <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                    ${{ number_format($existencia->producto->precio_proveedor ?? 0, 2) }}
                  </td>

                  <!-- Entradas -->
                  <td class="px-4 py-3">
                    <input type="number" 
                           name="entradas[{{ $existencia->id }}]" 
                           value="0"
                           class="entrada-input w-20 mx-auto text-center border border-green-300 rounded-md text-sm p-1 focus:ring-green-500 focus:border-green-500 text-green-700 dark:bg-gray-600 dark:border-green-500 dark:text-green-300"
                           min="0"
                           data-existencia-id="{{ $existencia->id }}">
                  </td>

                  <!-- Salidas -->
                  <td class="px-4 py-3">
                    <input type="number" 
                           name="salidas[{{ $existencia->id }}]" 
                           value="0"
                           class="w-20 mx-auto text-center border border-red-300 rounded-md text-sm p-1 focus:ring-red-500 focus:border-red-500 text-red-700 dark:bg-gray-600 dark:border-red-500 dark:text-red-300"
                           min="0">
                  </td>

                  <!-- Stock Actual -->
                  <td class="px-4 py-3">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold inline-block min-w-[55px] dark:bg-blue-900 dark:text-blue-300">
                      {{ $existencia->stock_actual }}
                    </span>
                  </td>

                  <!-- Stock Mínimo -->
                  <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                    {{ $existencia->stock_minimo }}
                  </td>

                  <!-- Estado -->
                  <td class="px-4 py-3">
                      @if($estado == 'Alto')
                          <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold inline-block min-w-[60px]">
                              {{ $estado }}
                          </span>
                      @elseif($estado == 'Medio')
                          <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold inline-block min-w-[60px]">
                              {{ $estado }}
                          </span>
                      @else
                          <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold inline-block min-w-[60px]">
                              {{ $estado }}
                          </span>
                      @endif
                  </td>

                  <!-- Botones - Solo Ver -->
                  <td class="px-4 py-3 flex justify-center gap-2">
                    <a href="{{ route('inventario.movimientos', $existencia->producto_id) }}"
                       class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-1 rounded-md text-xs font-medium shadow transition flex items-center gap-1">
                      🕓 <span>Ver</span>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                    No hay productos activos registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>

          <!-- Sección de Total y Botón Guardar -->
          <div class="flex justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex flex-col items-end gap-4">
              <!-- Resumen de Compras -->
              <div class="bg-gray-50 rounded-lg p-4 dark:bg-gray-600">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Resumen de Compras</h4>
                <div class="space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-300">Subtotal:</span>
                    <span class="font-medium text-gray-800 dark:text-white" id="subtotal">$0.00</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-300">Impuesto (8%):</span>
                    <span class="font-medium text-gray-800 dark:text-white" id="impuesto">$0.00</span>
                  </div>
                  <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-500">
                    <span class="text-lg font-bold text-gray-800 dark:text-white">Total:</span>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400" id="total">$0.00</span>
                  </div>
                </div>
              </div>

              <!-- Botón Guardar -->
              <div>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-3 rounded-lg text-sm shadow transition flex items-center gap-2">
                   <span> Guardar </span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Paginación -->
      <div class="mt-4">
        {{ $existencias->links() }}
      </div>
    </div>

    <!-- ================== COLUMNA DERECHA (SOLO REPORTES) ================== -->
    <div class="space-y-6">
      <!-- Reportes y Totales -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 dark:bg-gray-700 dark:border-gray-600">
        <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4 dark:text-white">📊 Reportes y Totales</h3>

        <!-- Ventas -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-3 text-center dark:bg-green-900 dark:border-green-700">
          <p class="text-sm font-medium text-green-800 dark:text-green-300">💵 Ventas del Día</p>
          <h4 class="text-3xl font-bold text-green-700 dark:text-green-400 mt-1">{{ number_format($ventasDia, 0) }}</h4>
        </div>

        <!-- Merma -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-3 text-center dark:bg-red-900 dark:border-red-700">
          <p class="text-sm font-medium text-red-800 dark:text-red-300">📉 Merma del Día</p>
          <h4 class="text-3xl font-bold text-red-700 dark:text-red-400 mt-1">{{ number_format($mermaDia, 0) }}</h4>
        </div>

        <!-- Stock agregado -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-3 text-center dark:bg-blue-900 dark:border-blue-700">
          <p class="text-sm font-medium text-blue-800 dark:text-blue-300">📦 Stock Agregado</p>
          <h4 class="text-3xl font-bold text-blue-700 dark:text-blue-400 mt-1">{{ number_format($stockAgregado, 0) }}</h4>
        </div>

        <!-- Acumulado semanal -->
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center dark:bg-orange-900 dark:border-orange-700">
          <p class="text-sm font-medium text-orange-800 dark:text-orange-300">📅 Acumulado Semana</p>
          <h4 class="text-3xl font-bold text-orange-700 dark:text-orange-400 mt-1">{{ number_format($acumuladoSemana, 0) }}</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// JavaScript para calcular el total en tiempo real basado en entradas y precio_proveedor
document.addEventListener('DOMContentLoaded', function() {
    const entradaInputs = document.querySelectorAll('.entrada-input');
    
    function calcularTotal() {
        let subtotal = 0;
        
        // Calcular subtotal basado en entradas y precio_proveedor
        entradaInputs.forEach(input => {
            const cantidad = parseInt(input.value) || 0;
            const existenciaId = input.getAttribute('data-existencia-id');
            const fila = input.closest('tr');
            const precioProveedor = parseFloat(fila.getAttribute('data-precio')) || 0;
            
            subtotal += cantidad * precioProveedor;
        });
        
        const impuesto = subtotal * 0.08;
        const total = subtotal + impuesto;
        
        // Actualizar los valores en la interfaz
        document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('impuesto').textContent = `$${impuesto.toFixed(2)}`;
        document.getElementById('total').textContent = `$${total.toFixed(2)}`;
    }
    
    // Escuchar cambios en los inputs de entrada
    entradaInputs.forEach(input => {
        input.addEventListener('input', calcularTotal);
    });
    
    // Inicializar cálculo
    calcularTotal();
});
</script>

<style>
/* Estilos para mejorar la apariencia de la sección de total */
.bg-gray-50 {
    background-color: #f9fafb;
}
.dark .bg-gray-600 {
    background-color: #4b5563;
}
</style>
@endsection