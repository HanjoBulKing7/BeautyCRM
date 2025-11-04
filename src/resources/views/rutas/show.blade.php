@extends('layouts.app')

@section('title', 'Detalle de Ruta - ' . $ruta->nombre)

@section('page-title', 'Detalle de Ruta: ' . $ruta->nombre)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        
        <!-- ================== TARJETA INFORMACIÓN RUTA ================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        🚚 {{ $ruta->nombre }}
                    </h1>
                    <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-blue-500"></i>
                            <span class="font-medium">Fecha:</span>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                {{ \Carbon\Carbon::parse($ruta->fecha)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-purple-500"></i>
                            <span class="font-medium">Vendedor:</span>
                            <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                {{ $ruta->empleado->nombre }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chart-line text-green-500"></i>
                            <span class="font-medium">Total Ventas:</span>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">
                                ${{ number_format($totalVentas, 2) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-boxes text-orange-500"></i>
                            <span class="font-medium">Total Unidades:</span>
                            <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full font-bold">
                                {{ $totalUnidades }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3 mt-4 md:mt-0">
                    <!-- Botón para abrir modal de agregar productos -->
                    <button type="button" 
                            onclick="abrirModalAgregarProductos()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition">
                        <i class="fas fa-plus"></i>
                        Agregar Productos
                    </button>
                    <a href="{{ route('rutas.edit', $ruta) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition">
                        <i class="fas fa-edit"></i>
                        Editar Ruta
                    </a>
                    <a href="{{ route('rutas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[70%_30%] gap-6">
            
            <!-- ================== COLUMNA IZQUIERDA - PRODUCTOS ================== -->
            <div class="space-y-6">
                <!-- 🔵 SECCIÓN: LISTA DE PRODUCTOS EN RUTA -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-500"></i>
                        Productos en la Ruta ({{ $ruta->detalles->count() }})
                    </h3>
                    
                    @if($ruta->detalles->count() > 0)
                        <form id="form-actualizar-productos" action="{{ route('rutas.bulk-update', $ruta) }}" method="POST">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr class="text-left text-sm font-medium text-gray-700 uppercase">
                                            <th class="px-4 py-3">Producto</th>
                                            <th class="px-4 py-3 text-center">Stock Ruta</th>
                                            <th class="px-4 py-3 text-center text-green-600">Ventas</th>
                                            <th class="px-4 py-3 text-center text-blue-600">Recargas</th>
                                            <th class="px-4 py-3 text-center text-red-600">Devoluciones</th>
                                            <th class="px-4 py-3 text-center">Precio Unitario</th>
                                            <th class="px-4 py-3 text-center">Total</th>
                                            <th class="px-4 py-3 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($ruta->detalles as $detalle)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900">
                                                        {{ $detalle->producto->nombre }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        SKU: {{ $detalle->producto->sku }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-sm font-bold">
                                                        {{ $detalle->carga_inicial }}
                                                    </span>
                                                </td>
                                                
                                                <!-- Ventas -->
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number" 
                                                           name="ventas[{{ $detalle->id }}]" 
                                                           value="{{ $detalle->ventas }}" 
                                                           min="0"
                                                           max="{{ $detalle->carga_inicial + $detalle->recargas }}"
                                                           class="w-20 mx-auto text-center border border-green-300 rounded-md text-sm p-1 focus:ring-green-500 focus:border-green-500 text-green-700 venta-input"
                                                           onchange="calcularTotalProducto(this, {{ $detalle->precio_unitario }})">
                                                </td>
                                                
                                                <!-- Recargas -->
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number" 
                                                           name="recargas[{{ $detalle->id }}]" 
                                                           value="{{ $detalle->recargas }}" 
                                                           min="0"
                                                           class="w-20 mx-auto text-center border border-blue-300 rounded-md text-sm p-1 focus:ring-blue-500 focus:border-blue-500 text-blue-700">
                                                </td>
                                                
                                                <!-- Devoluciones -->
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number" 
                                                           name="devoluciones[{{ $detalle->id }}]" 
                                                           value="{{ $detalle->devoluciones }}" 
                                                           min="0"
                                                           class="w-20 mx-auto text-center border border-red-300 rounded-md text-sm p-1 focus:ring-red-500 focus:border-red-500 text-red-700">
                                                </td>
                                                
                                                <td class="px-4 py-3 text-center font-medium text-gray-700">
                                                    ${{ number_format($detalle->precio_unitario, 2) }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="total-producto bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm font-bold">
                                                        ${{ number_format($detalle->total, 2) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" 
                                                            onclick="eliminarProducto({{ $detalle->id }})"
                                                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition tooltip"
                                                            title="Eliminar producto">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Resumen y Botón Guardar -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="text-lg font-bold text-gray-800">
                                        Total General: $<span id="total-general">{{ number_format($totalVentas, 2) }}</span>
                                    </div>
                                    <button type="submit" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2 transition">
                                        <i class="fas fa-save"></i>
                                        Guardar Todos los Cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Nota informativa -->
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-700 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                <strong>Nota:</strong> Las devoluciones se restan del stock de la ruta y se consideran merma.
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-500 mb-2">No hay productos en esta ruta</h4>
                            <p class="text-gray-400 text-sm">Haz clic en "Agregar Productos" para comenzar</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ================== COLUMNA DERECHA - RESUMEN ================== -->
            <div class="space-y-6">
                
                <!-- 🟠 RESUMEN DEL DÍA -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-orange-500"></i>
                        Resumen del Día
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="font-medium text-green-700">Total Ventas:</span>
                            <span class="font-bold text-green-700 text-lg">
                                ${{ number_format($totalVentas, 2) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="font-medium text-blue-700">Total Unidades:</span>
                            <span class="font-bold text-blue-700 text-lg">
                                {{ $totalUnidades }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                            <span class="font-medium text-purple-700">Productos en Ruta:</span>
                            <span class="font-bold text-purple-700 text-lg">
                                {{ $ruta->detalles->count() }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- 🔴 SECCIÓN: GASTOS DE LA RUTA -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-red-500"></i>
                            Gastos de la Ruta
                        </h3>
                        
                        @php
                            $gastosRuta = \App\Models\Gasto::where('ruta_id', $ruta->id)
                                ->orderBy('fecha', 'desc')
                                ->get();
                            $totalGastos = $gastosRuta->sum('monto');
                        @endphp
                        
                        @if($gastosRuta->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr class="text-left text-sm font-medium text-gray-700 uppercase">
                                            <th class="px-4 py-3">Fecha</th>
                                            <th class="px-4 py-3">Descripción</th>
                                            <th class="px-4 py-3">Categoría</th>
                                            <th class="px-4 py-3 text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($gastosRuta as $gasto)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-sm">{{ $gasto->fecha->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900">{{ $gasto->descripcion }}</div>
                                                    <div class="text-xs text-gray-500">{{ $gasto->metodo_pago }}</div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                        {{ $gasto->categoria }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium text-red-600">
                                                    ${{ number_format($gasto->monto, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-right font-bold">Total Gastos:</td>
                                            <td class="px-4 py-3 text-right font-bold text-red-600">
                                                ${{ number_format($totalGastos, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-money-bill-wave text-gray-300 text-5xl mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-500 mb-2">No hay gastos registrados</h4>
                                <p class="text-gray-400 text-sm">Haz clic en "Agregar Gasto Ruta" para comenzar</p>
                            </div>
                        @endif
                    </div>

                <!-- 🔵 ACCIONES RÁPIDAS -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt text-yellow-500"></i>
                        Acciones Rápidas
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('gastos.create', ['ruta_id' => $ruta->id]) }}" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition">               
                            <i class="fas fa-money-bill"></i> 
                            Agregar Gasto Ruta
                        </a>
                                    
                        <a href="{{ route('rutas.index') }}" 
                           class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition">
                            <i class="fas fa-list"></i>
                            Ver Todas las Rutas
                        </a>
                        
                        <form action="{{ route('rutas.destroy', $ruta) }}" method="POST" 
                              onsubmit="return confirm('¿Estás seguro de eliminar esta ruta completa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition">
                                <i class="fas fa-trash"></i>
                                Eliminar Ruta
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 🟢 INFORMACIÓN VENDEDOR -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-tie text-green-500"></i>
                        Información del Vendedor
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $ruta->empleado->nombre }}</p>
                                <p class="text-sm text-gray-500 capitalize">{{ $ruta->empleado->rol }}</p>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><span class="font-medium">Email:</span> {{ $ruta->empleado->email }}</p>
                            <p><span class="font-medium">Sucursal:</span> {{ $ruta->empleado->sucursal->nombre ?? 'N/A' }}</p>
                            <p><span class="font-medium">Estado:</span> 
                                <span class="{{ $ruta->empleado->activo ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ $ruta->empleado->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- ================== MODAL AGREGAR PRODUCTOS ================== -->
<div id="modalAgregarProductos" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-11/12 max-w-6xl max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-green-500"></i>
                Agregar Productos a la Ruta
            </h3>
        </div>
        
        <div class="p-6 overflow-auto max-h-[60vh]">
            <form id="form-agregar-productos" action="{{ route('rutas.bulk-add-productos', $ruta) }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-sm font-medium text-gray-700 uppercase">
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3 text-center">Stock Disponible</th>
                                <th class="px-4 py-3 text-center">Precio</th>
                                <th class="px-4 py-3 text-center">Carga Inicial</th>
                                <th class="px-4 py-3 text-center">Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($productos as $producto)
                                @php
                                    $existencia = $producto->existencias->where('sucursal_id', Auth::user()->sucursal_id)->first();
                                    $stockDisponible = $existencia ? $existencia->stock_actual : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">
                                            {{ $producto->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            SKU: {{ $producto->sku }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-sm font-bold">
                                            {{ $stockDisponible }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-700">
                                        ${{ number_format($producto->precio, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" 
                                               name="carga_inicial[{{ $producto->id }}]" 
                                               value="0"
                                               min="0"
                                               max="{{ $stockDisponible }}"
                                               class="w-24 mx-auto text-center border border-gray-300 rounded-md text-sm p-1 focus:ring-green-500 focus:border-green-500 carga-inicial-input"
                                               disabled>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" 
                                               name="productos_seleccionados[]" 
                                               value="{{ $producto->id }}"
                                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded producto-checkbox"
                                               onchange="toggleCargaInicial(this)">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Resumen del modal -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-600">Productos seleccionados: </span>
                            <span id="contador-productos" class="font-bold">0</span>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" 
                                    onclick="cerrarModalAgregarProductos()"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium flex items-center gap-2 transition">
                                <i class="fas fa-save"></i>
                                Agregar Productos Seleccionados
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Estilos para tooltips -->
<style>
.tooltip {
    position: relative;
}

.tooltip:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
}

/* Mejora visual para los inputs */
input[type="number"] {
    transition: all 0.2s ease;
}

input[type="number"]:focus {
    transform: scale(1.05);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>

<!-- Script para mensajes de éxito/error -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            timer: 4000,
            showConfirmButton: true
        });
    });
</script>
@endif

<!-- Script para funcionalidades -->
<script>
// ================== FUNCIONES DEL MODAL ==================
function abrirModalAgregarProductos() {
    document.getElementById('modalAgregarProductos').classList.remove('hidden');
}

function cerrarModalAgregarProductos() {
    document.getElementById('modalAgregarProductos').classList.add('hidden');
    // Resetear formulario
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('.carga-inicial-input').forEach(input => {
        input.value = '0';
        input.disabled = true;
    });
    actualizarContadorProductos();
}

function toggleCargaInicial(checkbox) {
    const row = checkbox.closest('tr');
    const input = row.querySelector('.carga-inicial-input');
    input.disabled = !checkbox.checked;
    
    if (!checkbox.checked) {
        input.value = '0';
    }
    actualizarContadorProductos();
}

function actualizarContadorProductos() {
    const seleccionados = document.querySelectorAll('.producto-checkbox:checked').length;
    document.getElementById('contador-productos').textContent = seleccionados;
}

// ================== CÁLCULO DE TOTALES ==================
function calcularTotalProducto(input, precioUnitario) {
    const ventas = parseInt(input.value) || 0;
    const total = ventas * precioUnitario;
    
    const row = input.closest('tr');
    const totalElement = row.querySelector('.total-producto');
    totalElement.textContent = `$${total.toFixed(2)}`;
    
    calcularTotalGeneral();
}

function calcularTotalGeneral() {
    let totalGeneral = 0;
    document.querySelectorAll('.venta-input').forEach(input => {
        const row = input.closest('tr');
        const precioText = row.querySelector('td:nth-child(6)').textContent;
        const precio = parseFloat(precioText.replace('$', '')) || 0;
        const ventas = parseInt(input.value) || 0;
        totalGeneral += ventas * precio;
    });
    
    document.getElementById('total-general').textContent = totalGeneral.toFixed(2);
}

// ================== ELIMINAR PRODUCTO ==================
function eliminarProducto(detalleId) {
    if (confirm('¿Estás seguro de eliminar este producto de la ruta?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/rutas-detalle/${detalleId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// ================== EVENT LISTENERS ==================
document.addEventListener('DOMContentLoaded', function() {
    // Calcular total general al cargar la página
    calcularTotalGeneral();
    
    // Event listeners para inputs de ventas
    document.querySelectorAll('.venta-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            const precioText = row.querySelector('td:nth-child(6)').textContent;
            const precio = parseFloat(precioText.replace('$', '')) || 0;
            calcularTotalProducto(this, precio);
        });
    });
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalAgregarProductos();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAgregarProductos').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalAgregarProductos();
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection