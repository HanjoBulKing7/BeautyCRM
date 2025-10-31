@csrf

<style>
    .asterisco{ color:red; font-weight:700; font-size:1.1em; }
    .currency-input, .precio-input { padding-left:2.5rem !important; }
    .form-section{ background:#f9fafb; border-radius:.5rem; padding:1rem; margin-bottom:1rem; border-left:4px solid #3b82f6; }
    .form-section-title{ font-weight:600; color:#374151; margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem; }
    .btn-primary{ background:#3b82f6; color:#fff; padding:.5rem 1rem; border-radius:.375rem; font-weight:500; transition:all .2s; }
    .btn-primary:hover{ background:#2563eb; transform:translateY(-1px); box-shadow:0 4px 6px -1px rgba(0,0,0,.1); }
    .btn-danger{ background:#ef4444; color:#fff; padding:.5rem; border-radius:.375rem; transition:all .2s; }
    .btn-danger:hover{ background:#dc2626; transform:translateY(-1px); }
    .input-field{ border:1px solid #d1d5db; border-radius:.375rem; padding:.5rem .75rem; width:100%; transition:border-color .2s, box-shadow .2s; }
    .input-field:focus{ outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    .select-field{ border:1px solid #d1d5db; border-radius:.375rem; padding:.5rem .75rem; width:100%; transition:border-color .2s, box-shadow .2s; }
    .select-field:focus{ outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    .pago-row,.producto-row{ background:#fff; border-radius:.5rem; padding:1rem; margin-bottom:.75rem; border:1px solid #e5e7eb; transition:all .2s; }
    .pago-row:hover,.producto-row:hover{ box-shadow:0 2px 4px rgba(0,0,0,.05); border-color:#d1d5db; }
    .relative .absolute{ color:#6b7280; }
    .stock-warning{ color:#ef4444; font-size:.75rem; margin-top:.25rem; }

.fixed-bottom-bar {
    position: fixed;
    bottom: 0;
    left: 255px; /* 👈 ancho de tu sidebar */
    width: calc(100% - 255px); /* 👈 resta ese ancho al total */
    background-color: white;
    border-top: 1px solid #e5e7eb;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 50;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

@media (max-width: 1024px) {
    .fixed-bottom-bar {
        left: 0;
        width: 100%;
    }
}
.asterisco{ color:red; font-weight:700; font-size:1.1em; }
    .currency-input, .precio-input { padding-left:2.5rem !important; }
    .form-section{ background:#f9fafb; border-radius:.5rem; padding:1rem; margin-bottom:1rem; border-left:4px solid #3b82f6; }
    .dark-mode .form-section{ background:#374151 !important; border-left-color:#60a5fa !important; }
    .form-section-title{ font-weight:600; color:#374151; margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem; }
    .dark-mode .form-section-title{ color:#e5e7eb !important; }
    .btn-primary{ background:#3b82f6; color:#fff; padding:.5rem 1rem; border-radius:.375rem; font-weight:500; transition:all .2s; }
    .btn-primary:hover{ background:#2563eb; transform:translateY(-1px); box-shadow:0 4px 6px -1px rgba(0,0,0,.1); }
    .dark-mode .btn-primary{ background:#2563eb !important; }
    .dark-mode .btn-primary:hover{ background:#1d4ed8 !important; }
    .btn-danger{ background:#ef4444; color:#fff; padding:.5rem; border-radius:.375rem; transition:all .2s; }
    .btn-danger:hover{ background:#dc2626; transform:translateY(-1px); }
    .dark-mode .btn-danger{ background:#dc2626 !important; }
    .dark-mode .btn-danger:hover{ background:#b91c1c !important; }
    .input-field{ border:1px solid #d1d5db; border-radius:.375rem; padding:.5rem .75rem; width:100%; transition:border-color .2s, box-shadow .2s; }
    .input-field:focus{ outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    .dark-mode .input-field{ background:#374151 !important; border-color:#6b7280 !important; color:#ffffff !important; }
    .dark-mode .input-field:focus{ border-color:#60a5fa !important; box-shadow:0 0 0 3px rgba(96,165,250,.1) !important; }
    .select-field{ border:1px solid #d1d5db; border-radius:.375rem; padding:.5rem .75rem; width:100%; transition:border-color .2s, box-shadow .2s; }
    .select-field:focus{ outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    .dark-mode .select-field{ background:#374151 !important; border-color:#6b7280 !important; color:#ffffff !important; }
    .dark-mode .select-field:focus{ border-color:#60a5fa !important; box-shadow:0 0 0 3px rgba(96,165,250,.1) !important; }
    .pago-row,.producto-row{ background:#fff; border-radius:.5rem; padding:1rem; margin-bottom:.75rem; border:1px solid #e5e7eb; transition:all .2s; }
    .pago-row:hover,.producto-row:hover{ box-shadow:0 2px 4px rgba(0,0,0,.05); border-color:#d1d5db; }
    .dark-mode .pago-row, .dark-mode .producto-row{ background:#1f2937 !important; border-color:#4b5563 !important; }
    .dark-mode .pago-row:hover, .dark-mode .producto-row:hover{ border-color:#6b7280 !important; }
    .relative .absolute{ color:#6b7280; }
    .dark-mode .relative .absolute{ color:#9ca3af !important; }
    .stock-warning{ color:#ef4444; font-size:.75rem; margin-top:.25rem; }
    .dark-mode .stock-warning{ color:#f87171 !important; }

.fixed-bottom-bar {
    position: fixed;
    bottom: 0;
    left: 255px; /* 👈 ancho de tu sidebar */
    width: calc(100% - 255px); /* 👈 resta ese ancho al total */
    background-color: white;
    border-top: 1px solid #e5e7eb;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 50;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}
.dark-mode .fixed-bottom-bar {
    background-color: #1f2937 !important;
    border-top-color: #374151 !important;
}

@media (max-width: 1024px) {
    .fixed-bottom-bar {
        left: 0;
        width: 100%;
    }
}

/* Agregar estas clases para los textos */
.dark-mode .text-gray-600 { color: #d1d5db !important; }
.dark-mode .text-green-600 { color: #4ade80 !important; }
.dark-mode .text-red-600 { color: #f87171 !important; }
.dark-mode .bg-gray-100 { background-color: #374151 !important; }

</style>

<!-- Contenedor principal con padding-bottom para no tapar contenido -->
<div class="pb-28">
    <!-- GRID 2/3 (izq) + 1/3 (der) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- IZQUIERDA: 2/3 -->
        <div class="lg:col-span-2 space-y-4">
            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
            <div class="form-section">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha <span class="asterisco">*</span></label>
                        <input type="date" name="fecha"
                               value="{{ old('fecha', isset($venta) && $venta->fecha ? $venta->fecha->format('Y-m-d') : date('Y-m-d')) }}"
                               class="input-field" required>
                    </div>
                </div>

                <!-- Cliente (oculto, para futuro) -->
                <div style="display:none;">
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" id="seleccionar_cliente" class="w-5 h-5 rounded border" />
                        <label for="seleccionar_cliente" class="text-sm font-medium">Seleccionar Cliente</label>
                    </div>
                    <div id="cliente_container" class="hidden">
                        <label class="block text-sm font-medium mb-1">Cliente <span class="asterisco">*</span></label>
                        <select name="cliente_id" id="cliente_select" class="select-field" required>
                            <option value="">Seleccione un cliente</option>
                            <option value="0">Cliente anónimo</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}"
                                    {{ old('cliente_id', isset($venta) ? $venta->cliente_id : '') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: PRODUCTOS -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-boxes text-green-500"></i> Productos
                </h3>

                <div id="productos-container" class="space-y-3 mb-3">
                    <!-- filas dinámicas por JS -->
                </div>

                <button type="button" id="agregar-producto" class="btn-primary">
                    <i class="fas fa-plus mr-1"></i> Agregar Producto
                </button>
            </div>
        </div>
<!-- DERECHA: 1/3 -->
<div class="space-y-4">
    <!-- SECCIÓN 3: MÉTODOS DE PAGO -->
    <div class="form-section">
        <h3 class="form-section-title">
            <i class="fas fa-credit-card text-purple-500"></i> Métodos de Pago
        </h3>

        <div id="pagos-container" class="space-y-3 mb-3">
            <!-- Pago por defecto -->
            <div class="pago-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Método (más ancho) -->
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium mb-1">
                        Método <span class="asterisco">*</span>
                    </label>
                    <select name="pagos[0][metodo]" class="pago-metodo select-field" required>
                        <option value="efectivo" selected>Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>

                <!-- Monto -->
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium mb-1">
                        Monto <span class="asterisco">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2">$</span>
                        <input type="number" step="0.01" name="pagos[0][monto]"
                            class="pago-monto input-field currency-input" min="0.01" placeholder="0.00">
                    </div>
                </div>

                <!-- Botón eliminar (más ancho y alineado) -->
                <div class="md:col-span-2 flex items-end">
                    <button type="button"
                        class="quitar-pago btn-danger w-full h-11 flex justify-center items-center"
                        disabled>
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Destinatario -->
                <div class="md:col-span-12 pago-destinatario-container hidden mt-2">
                    <label class="block text-sm font-medium mb-1">
                        Destinatario <span class="asterisco">*</span>
                    </label>
                    <select name="pagos[0][destinatario]" class="pago-destinatario select-field">
                        <option value="">Seleccione</option>
                        <option value="Karen">Karen</option>
                        <option value="Ethan">Ethan</option>
                    </select>
                </div>
            </div>
        </div>

                <div class="flex items-center gap-2 mb-3">
                    <span class="text-sm font-medium">Total pagado: </span>
                    <span id="total_pagado" class="font-bold text-green-600">$0.00</span>
                    <span id="restante_pago" class="ml-4 text-sm"></span>
                </div>

                <button type="button" id="agregar-pago" class="btn-primary w-full">
                    <i class="fas fa-plus mr-1"></i> Agregar Multi Pago
                </button>

                <!-- Campo oculto para método principal -->
                <input type="hidden" name="metodo_pago" id="metodo_pago_input" value="efectivo">
            </div>

            <!-- SECCIÓN 4: NOTAS -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-sticky-note text-yellow-500"></i> Notas
                </h3>
                <textarea name="notas" rows="3" class="input-field" placeholder="Agrega una nota o comentario">
{{ old('notas', isset($venta) ? $venta->notas : '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- Barra fija inferior (Total + acciones) -->
<div class="fixed-bottom-bar">
    <div class="flex flex-wrap items-center gap-4">
        <!-- Campos ocultos que usa tu JS/servidor -->
        <input type="hidden" step="0.01" name="subtotal" id="subtotal" value="{{ old('subtotal', isset($venta) ? $venta->subtotal : 0) }}">
        <input type="hidden" name="descuento" id="descuento" value="{{ old('descuento', isset($venta) ? $venta->descuento : 0) }}">
        <input type="hidden" name="impuestos" id="impuestos" value="{{ old('impuestos', isset($venta) ? $venta->impuestos : 0) }}">

        <!-- Total (mismo #total que ya usa tu JS) -->
        <div class="flex items-center gap-2">
            <label for="total" class="font-medium">Total:</label>
            <input
                type="number" step="0.01" name="total" id="total"
                value="{{ old('total', isset($venta) ? $venta->total : 0) }}"
                class="input-field bg-gray-100 w-32 text-right font-semibold" readonly>
        </div>

    </div>

    <div class="flex gap-2">
        <!-- Usa los mismos IDs/clases que en create/edit -->
            <button id="btn-guardar" type="submit"
            class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
            Guardar
        </button>
        <a href="{{ route('ventas.index') }}" class="px-4 py-2 rounded-lg border">Cancelar</a>
    </div>
</div>


<!-- Template: Producto -->
<template id="producto-template">
    <div class="producto-row grid grid-cols-1 md:grid-cols-12 gap-2 items-end">
        <div class="md:col-span-5">
            <label class="block text-sm font-medium mb-1">Producto</label>
            <select name="productos[INDEX][producto_id]" class="producto-select select-field" required>
                <option value="">Seleccione producto</option>
                @foreach($productos as $producto)
                    @php
                        $existencia = $producto->existencias->where('sucursal_id', Auth::user()->sucursal_id)->first();
                        $stock = $existencia ? $existencia->stock_actual : 0;
                    @endphp
                    <option value="{{ $producto->id }}"
                            data-precio="{{ $producto->precio }}"
                            data-stock="{{ $stock }}"
                            {{ $stock == 0 ? 'disabled' : '' }}>
                        {{ $producto->nombre }} - ${{ number_format($producto->precio, 2) }}
                        (Stock: {{ $stock }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Cantidad</label>
            <input type="number" name="productos[INDEX][cantidad]" class="cantidad-input input-field" min="1" value="1" required>
            <div class="stock-message stock-warning hidden"></div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Precio Unitario</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2">$</span>
                <input type="number" step="0.01" name="productos[INDEX][precio_unitario]"
                       class="precio-input input-field precio-input" min="0" required>
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Total Línea</label>
            <input type="number" step="0.01" name="productos[INDEX][total_linea]"
                   class="total-linea-input input-field bg-gray-100" readonly>
        </div>
        <div class="md:col-span-1">
            <button type="button" class="quitar-producto btn-danger w-full">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

<!-- Datos iniciales para tu JS -->
<div id="venta-data"
     data-productos-iniciales="{{ isset($venta) && $venta->detalles->count() > 0 ? 
        e(json_encode($venta->detalles->map(function($detalle){
            $existencia = $detalle->producto->existencias->where('sucursal_id', Auth::user()->sucursal_id)->first();
            $stock = $existencia ? $existencia->stock_actual : 0;
            return [
                'producto_id' => $detalle->producto_id,
                'cantidad' => $detalle->cantidad,
                'precio' => $detalle->precio_unitario,
                'total_linea' => $detalle->total_linea,
                'stock' => $stock
            ];
        }))) : '[]' }}"
     data-pagos-iniciales="{{ isset($venta) && $venta->pagos->count() > 0 ?
        e(json_encode($venta->pagos->map(function($pago){
            return [
                'metodo' => $pago->metodo_pago,
                'monto' => $pago->monto,
                'referencia' => $pago->referencia_pago,
                'destinatario' => $pago->destinatario_transferencia
            ];
        }))) : '[]' }}"
     style="display:none;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productosContainer = document.getElementById('productos-container');
    const agregarProductoBtn = document.getElementById('agregar-producto');
    const productoTemplate = document.getElementById('producto-template');
    const ventaData = document.getElementById('venta-data');
    let productoIndex = 0;
    
    // === CONTROL DE SELECCIÓN DE CLIENTE ===
    const seleccionarClienteCheckbox = document.getElementById('seleccionar_cliente');
    const clienteContainer = document.getElementById('cliente_container');
    const clienteSelect = document.getElementById('cliente_select');

    function toggleClienteField() {
        if (!seleccionarClienteCheckbox || !clienteContainer || !clienteSelect) return;
        
        if (seleccionarClienteCheckbox.checked) {
            clienteContainer.classList.remove('hidden');
            clienteSelect.setAttribute('required', 'required');
        } else {
            clienteContainer.classList.add('hidden');
            clienteSelect.removeAttribute('required');
            clienteSelect.value = ''; // Resetear a cliente anónimo
        }
    }

    // Inicializar y configurar event listener para cliente
    if (seleccionarClienteCheckbox) {
        seleccionarClienteCheckbox.addEventListener('change', toggleClienteField);
        toggleClienteField(); // Estado inicial
    }
    // === FIN CONTROL DE SELECCIÓN DE CLIENTE ===
    
    // === CONTROL DE PAGOS MÚLTIPLES (MODIFICADO) ===
    const pagosContainer = document.getElementById('pagos-container');
    const pagoTemplate = document.getElementById('pago-template');
    const agregarPagoBtn = document.getElementById('agregar-pago');
    const totalPagadoSpan = document.getElementById('total_pagado');
    const restantePagoSpan = document.getElementById('restante_pago');
    const metodoPagoInput = document.getElementById('metodo_pago_input');
    let pagoIndex = 1; // Empezar en 1 porque ya tenemos el pago 0 por defecto

    // Función para actualizar el método de pago principal
    function actualizarMetodoPagoPrincipal() {
        const cantidadPagos = pagosContainer.children.length;
        
        if (cantidadPagos > 1) {
            // Si hay múltiples pagos, establecer como multipago
            metodoPagoInput.value = 'multipago';
        } else {
            // Si hay solo un pago, usar el método seleccionado en ese pago
            const primerPago = pagosContainer.querySelector('.pago-row');
            if (primerPago) {
                const metodoSelect = primerPago.querySelector('.pago-metodo');
                metodoPagoInput.value = metodoSelect.value;
            }
        }
    }

    // Función para agregar fila de pago
    function agregarFilaPago(metodo = '', monto = 0, referencia = '', destinatario = '') {
        const template = pagoTemplate.innerHTML.replace(/INDEX/g, pagoIndex);
        const div = document.createElement('div');
        div.innerHTML = template;
        pagosContainer.appendChild(div);
        
        // Setear valores si se proporcionan
        if (metodo) {
            div.querySelector('.pago-metodo').value = metodo;
        }
        
        if (monto > 0) {
            div.querySelector('.pago-monto').value = monto;
        }
        
        if (referencia) {
            div.querySelector('.pago-referencia').value = referencia;
        }
        
        if (destinatario) {
            div.querySelector('.pago-destinatario').value = destinatario;
        }
        
        // Eventos para la nueva fila
        const quitarBtn = div.querySelector('.quitar-pago');
        quitarBtn.addEventListener('click', function() {
            if (pagosContainer.children.length > 1) {
                div.remove();
                calcularTotalPagado();
                actualizarMetodoPagoPrincipal();
                // Habilitar el botón de quitar del primer pago si quedan múltiples pagos
                const primerQuitarBtn = pagosContainer.querySelector('.quitar-pago');
                if (primerQuitarBtn && pagosContainer.children.length > 1) {
                    primerQuitarBtn.disabled = false;
                }
            }
        });
        
        const metodoSelect = div.querySelector('.pago-metodo');
        metodoSelect.addEventListener('change', function() {
            const destinatarioContainer = div.querySelector('.pago-destinatario-container');
            if (this.value === 'transferencia') {
                destinatarioContainer.classList.remove('hidden');
                destinatarioContainer.querySelector('select').setAttribute('required', 'required');
            } else {
                destinatarioContainer.classList.add('hidden');
                destinatarioContainer.querySelector('select').removeAttribute('required');
            }
            // Actualizar método principal si es el primer pago
            if (pagosContainer.children.length === 1) {
                actualizarMetodoPagoPrincipal();
            }
        });
        
        const montoInput = div.querySelector('.pago-monto');
        montoInput.addEventListener('input', calcularTotalPagado);
        
        // Inicializar visibilidad del destinatario
        const destinatarioContainer = div.querySelector('.pago-destinatario-container');
        if (metodoSelect.value === 'transferencia') {
            destinatarioContainer.classList.remove('hidden');
            destinatarioContainer.querySelector('select').setAttribute('required', 'required');
        }
        
        pagoIndex++;
        calcularTotalPagado();
        actualizarMetodoPagoPrincipal();
        
        // Habilitar el botón de quitar del primer pago ahora que hay múltiples pagos
        const primerQuitarBtn = pagosContainer.querySelector('.quitar-pago');
        if (primerQuitarBtn && pagosContainer.children.length > 1) {
            primerQuitarBtn.disabled = false;
        }
    }

    // Función para calcular total pagado
    function calcularTotalPagado() {
        let totalPagado = 0;
        document.querySelectorAll('.pago-monto').forEach(input => {
            totalPagado += parseFloat(input.value) || 0;
        });
        
        const totalVenta = parseFloat(document.getElementById('total').value) || 0;
        const restante = totalVenta - totalPagado;
        
        totalPagadoSpan.textContent = '$' + totalPagado.toFixed(2);
        
        if (restante > 0) {
            restantePagoSpan.textContent = 'Faltan: $' + restante.toFixed(2);
            restantePagoSpan.className = 'ml-4 text-sm text-red-600';
        } else if (restante < 0) {
            restantePagoSpan.textContent = 'Excedente: $' + Math.abs(restante).toFixed(2);
            restantePagoSpan.className = 'ml-4 text-sm text-orange-600';
        } else {
            restantePagoSpan.textContent = 'Pago completo';
            restantePagoSpan.className = 'ml-4 text-sm text-green-600';
        }
    }

    // Evento para agregar pago
    agregarPagoBtn.addEventListener('click', function() {
        agregarFilaPago();
    });

    // Configurar evento para el primer pago (efectivo por defecto)
    const primerPago = pagosContainer.querySelector('.pago-row');
    if (primerPago) {
        const metodoSelect = primerPago.querySelector('.pago-metodo');
        metodoSelect.addEventListener('change', function() {
            const destinatarioContainer = primerPago.querySelector('.pago-destinatario-container');
            if (this.value === 'transferencia') {
                destinatarioContainer.classList.remove('hidden');
                destinatarioContainer.querySelector('select').setAttribute('required', 'required');
            } else {
                destinatarioContainer.classList.add('hidden');
                destinatarioContainer.querySelector('select').removeAttribute('required');
            }
            // Actualizar método principal
            actualizarMetodoPagoPrincipal();
        });
        
        const montoInput = primerPago.querySelector('.pago-monto');
        montoInput.addEventListener('input', calcularTotalPagado);
    }

    // Cargar pagos iniciales si existen (excluyendo el primero que ya está)
    const pagosIniciales = JSON.parse(ventaData.dataset.pagosIniciales || '[]');
    if (pagosIniciales.length > 0) {
        // El primer pago ya está creado, cargar los adicionales
        for (let i = 1; i < pagosIniciales.length; i++) {
            agregarFilaPago(
                pagosIniciales[i].metodo,
                pagosIniciales[i].monto,
                pagosIniciales[i].referencia,
                pagosIniciales[i].destinatario
            );
        }
        
        // Si hay pagos iniciales, actualizar también el primer pago
        if (pagosIniciales.length > 0) {
            const primerPago = pagosContainer.querySelector('.pago-row');
            if (primerPago && pagosIniciales[0]) {
                primerPago.querySelector('.pago-metodo').value = pagosIniciales[0].metodo;
                primerPago.querySelector('.pago-monto').value = pagosIniciales[0].monto;
                primerPago.querySelector('.pago-referencia').value = pagosIniciales[0].referencia || '';
                
                // Manejar destinatario para transferencias
                if (pagosIniciales[0].metodo === 'transferencia' && pagosIniciales[0].destinatario) {
                    const destinatarioContainer = primerPago.querySelector('.pago-destinatario-container');
                    destinatarioContainer.classList.remove('hidden');
                    primerPago.querySelector('.pago-destinatario').value = pagosIniciales[0].destinatario;
                    primerPago.querySelector('.pago-destinatario').setAttribute('required', 'required');
                }
            }
        }
    }
    
    // Inicializar método de pago principal
    actualizarMetodoPagoPrincipal();
    // === FIN CONTROL DE PAGOS MÚLTIPLES ===
    
    // Obtener datos iniciales del contenedor oculto
    const productosIniciales = JSON.parse(ventaData.dataset.productosIniciales || '[]');

    // Cargar productos iniciales o agregar fila vacía
    if (productosIniciales.length > 0) {
        productosIniciales.forEach(producto => {
            agregarFilaProducto(
                producto.producto_id,
                producto.cantidad,
                producto.precio,
                producto.total_linea,
                producto.stock
            );
        });
    } else {
        agregarFilaProducto();
    }

    // Evento para agregar producto
    agregarProductoBtn.addEventListener('click', function() {
        agregarFilaProducto();
    });
    
    // Función para agregar fila de producto
    function agregarFilaProducto(productoId = null, cantidad = 1, precio = 0, totalLinea = 0, stock = 0) {
        const template = productoTemplate.innerHTML.replace(/INDEX/g, productoIndex);
        const div = document.createElement('div');
        div.innerHTML = template;
        productosContainer.appendChild(div);
        
        // Setear valores si se proporcionan
        if (productoId) {
            const select = div.querySelector('.producto-select');
            select.value = productoId;
            
            // Actualizar mensaje de stock
            const stockDisponible = parseInt(select.options[select.selectedIndex]?.dataset.stock || 0);
            actualizarMensajeStock(div, cantidad, stockDisponible);
        }
        
        if (precio > 0) {
            const precioInput = div.querySelector('.precio-input');
            precioInput.value = precio;
        }
        
        const cantidadInput = div.querySelector('.cantidad-input');
        cantidadInput.value = cantidad;
        
        const totalLineaInput = div.querySelector('.total-linea-input');
        totalLineaInput.value = totalLinea.toFixed(2);
        
        // Eventos para la nueva fila
        const quitarBtn = div.querySelector('.quitar-producto');
        quitarBtn.addEventListener('click', function() {
            div.remove();
            calcularTotales();
        });
        
        const productoSelect = div.querySelector('.producto-select');
        productoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.precio) {
                const precioInput = div.querySelector('.precio-input');
                precioInput.value = selectedOption.dataset.precio;
                
                // Actualizar mensaje de stock
                const stockDisponible = parseInt(selectedOption.dataset.stock || 0);
                const cantidadActual = parseFloat(div.querySelector('.cantidad-input').value) || 0;
                actualizarMensajeStock(div, cantidadActual, stockDisponible);
                
                calcularLinea(div);
            } 
        });
        
        const cantidadInputElement = div.querySelector('.cantidad-input');
        cantidadInputElement.addEventListener('input', function() {
            const selectedOption = div.querySelector('.producto-select').options[div.querySelector('.producto-select').selectedIndex];
            if (selectedOption) {
                const stockDisponible = parseInt(selectedOption.dataset.stock || 0);
                const cantidad = parseFloat(this.value) || 0;
                actualizarMensajeStock(div, cantidad, stockDisponible);
            }
            calcularLinea(div);
        });
        
        const precioInput = div.querySelector('.precio-input');
        precioInput.addEventListener('input', function() {
            calcularLinea(div);
        });
        
        // Calcular la línea si hay valores iniciales
        if (cantidad > 0 && precio > 0) {
            calcularLinea(div);
        }
        
        productoIndex++;
    }
    
    // Actualizar mensaje de stock
    function actualizarMensajeStock(row, cantidad, stockDisponible) {
        const mensajeStock = row.querySelector('.stock-message');
        const cantidadInput = row.querySelector('.cantidad-input');
        
        if (cantidad > stockDisponible) {
            mensajeStock.textContent = `Stock insuficiente. Disponible: ${stockDisponible}`;
            mensajeStock.classList.remove('hidden');
            cantidadInput.setCustomValidity('Cantidad excede el stock disponible');
        } else {
            mensajeStock.classList.add('hidden');
            cantidadInput.setCustomValidity('');
        }
    }
    
    // Calcular total por línea
    function calcularLinea(row) {
        const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
        const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
        const totalLinea = cantidad * precio;
        
        row.querySelector('.total-linea-input').value = totalLinea.toFixed(2);
        calcularTotales();
    }
    
    // Calcular totales generales
    function calcularTotales() {
        let subtotal = 0;
        document.querySelectorAll('.total-linea-input').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });
        const descuento = 0;
        const impuestos = 0;
        //document.getElementById('subtotal').value = subtotal.toFixed(2); Descomentar cuando se usen descuentos, impuestos "subtotal" entonces
        //document.getElementById('total').value = (subtotal - descuento + impuestos).toFixed(2); Descometar cuando se implmeneten las otras feats
        document.getElementById('total').value = subtotal.toFixed(2);
        
        // Actualizar también el total de pagos
        calcularTotalPagado();
    }
    
    // Calcular totales iniciales
    calcularTotales();
    
    // Validación adicional del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validar pagos (siempre requerido ahora)
            const totalPagado = parseFloat(totalPagadoSpan.textContent.replace('$', '')) || 0;
            const totalVenta = parseFloat(document.getElementById('total').value) || 0;
            
            if (Math.abs(totalPagado - totalVenta) > 0.01) {
                e.preventDefault();
                alert('La suma de los pagos ($' + totalPagado.toFixed(2) + ') debe ser igual al total de la venta ($' + totalVenta.toFixed(2) + ').');
                return;
            }
            
            // Validar que cada pago tenga método y monto
            let pagosValidos = true;
            let pagoErrors = [];
            
            document.querySelectorAll('.pago-row').forEach((row, index) => {
                const metodo = row.querySelector('.pago-metodo').value;
                const monto = parseFloat(row.querySelector('.pago-monto').value) || 0;
                
                if (!metodo) {
                    pagosValidos = false;
                    pagoErrors.push(`Pago ${index + 1}: Falta seleccionar método`);
                    row.style.border = '2px solid red';
                } else if (monto <= 0) {
                    pagosValidos = false;
                    pagoErrors.push(`Pago ${index + 1}: Monto debe ser mayor a 0`);
                    row.style.border = '2px solid red';
                } else {
                    row.style.border = '';
                }
                
                // Validar destinatario para transferencias
                if (metodo === 'transferencia') {
                    const destinatario = row.querySelector('.pago-destinatario').value;
                    if (!destinatario) {
                        pagosValidos = false;
                        pagoErrors.push(`Pago ${index + 1}: Transferencia requiere destinatario`);
                        row.style.border = '2px solid red';
                    }
                }
            });
            
            if (!pagosValidos) {
                e.preventDefault();
                alert('Errores en pagos:\n' + pagoErrors.join('\n'));
                return;
            }
        });
    }
});
</script>