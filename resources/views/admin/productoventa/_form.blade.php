@csrf
<style>
    .bb-glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .bb-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 0.75rem 1.25rem;
        transition: all 0.25s ease;
        font-size: 0.875rem;
    }
    .bb-input:focus {
        outline: none;
        border-color: rgba(201,162,74,0.6);
        box-shadow: 0 0 0 4px rgba(201,162,74,0.15);
    }
    .bb-label {
        display: block;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 900;
        color: #9ca3af;
        letter-spacing: 0.1em;
        margin-bottom: 0.5rem;
        margin-left: 0.5rem;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }
    .product-item-card {
        background: white;
        border: 2px solid #f3f4f6;
        border-radius: 1.5rem;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }
    .product-item-card.is-selected {
        border-color: #c9a24a;
        background: rgba(201,162,74,0.05);
    }
    .product-img { width: 65px; height: 65px; border-radius: 1rem; object-fit: cover; pointer-events: none; }
    .bb-input-sm { padding: 0.4rem; border-radius: 0.75rem; font-size: 0.85rem; font-weight: 800; text-align: center; }
    
    .total-badge-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.5rem;
        padding: 1rem;
        background: #111827;
        border-radius: 1.5rem;
        color: white;
    }
</style>

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bb-glass-card p-5 rounded-[2rem]">
            <label class="bb-label">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="bb-input">
                <option value="">Selecciona un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id', $venta->cliente_id ?? '') === (string) $cliente->id)>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="bb-glass-card p-5 rounded-[2rem]">
            <label class="bb-label">Método de Pago</label>
            <select name="metodo_pago" id="metodo_pago" class="bb-input">
                <option value="Efectivo" @selected(old('metodo_pago', $venta->metodo_pago ?? 'Efectivo') === 'Efectivo')>💵 Efectivo</option>
                <option value="Tarjeta" @selected(old('metodo_pago', $venta->metodo_pago ?? '') === 'Tarjeta')>💳 Tarjeta</option>
                <option value="Transferencia" @selected(old('metodo_pago', $venta->metodo_pago ?? '') === 'Transferencia')>🏦 Transferencia</option>
            </select>
        </div>
    </div>

    @php
        $categoriasProductos = collect($productos ?? [])->map(function ($producto) {
            return [
                'id' => $producto->id_categoria ?? null,
                'nombre' => $producto->categoria->nombre ?? 'Sin categoría',
            ];
        })->filter(fn($cat) => !is_null($cat['id']))
          ->unique('id')
          ->sortBy('nombre')
          ->values();
    @endphp

    <div class="bb-glass-card p-6 rounded-[2.5rem]">
        <label class="bb-label mb-4">Productos</label>

        <div class="grid grid-cols-1 md:grid-cols-[1.2fr,0.8fr] gap-3 mb-6">
            <div>
                <input
                    type="text"
                    id="bbProductSearch"
                    class="bb-input"
                    placeholder="Buscar producto..."
                    autocomplete="off"
                >
            </div>
            <div>
                <select id="bbProductCategory" class="bb-input">
                    <option value="">Todas las categorías</option>
                    @foreach($categoriasProductos as $cat)
                        <option value="{{ $cat['id'] }}">{{ $cat['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="productos-lista" class="product-grid">
            @foreach($productos as $producto)
                @php
                    $selected = $selectedProducts[$producto->id] ?? [];
                    $cantidadValue = old("productos.{$producto->id}.cantidad", $selected['cantidad'] ?? 0);
                    $precioValue = old("productos.{$producto->id}.precio", $selected['precio_unitario'] ?? $producto->precio);
                @endphp
                <div
                    class="product-item-card js-product-card {{ $cantidadValue > 0 ? 'is-selected' : '' }}"
                    data-name="{{ strtolower($producto->nombre) }}"
                    data-categoria-id="{{ $producto->id_categoria ?? '' }}"
                    onclick="toggleProduct(this)"
                >
                    <img src="{{ $producto->imagen_url ?? asset('images/no-image.png') }}" class="product-img">
                    <div class="flex-grow min-w-0">
                        <h4 class="text-sm font-black truncate m-0">{{ $producto->nombre }}</h4>
                        <span class="text-xs font-bold text-[#c9a24a]">${{ number_format($producto->precio, 2) }}</span>
                        <div class="flex items-center gap-2 mt-2" onclick="event.stopPropagation();">
                            <input type="number" name="productos[{{ $producto->id }}][cantidad]" min="0" value="{{ $cantidadValue }}" class="bb-input bb-input-sm w-16 js-qty-input" oninput="updateCardState(this)" placeholder="Cant.">
                            <input type="number" name="productos[{{ $producto->id }}][precio]" min="0" step="0.01" value="{{ $precioValue }}" class="bb-input bb-input-sm flex-grow js-price-input" oninput="calculateTotal()">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="total-badge-container">
            <div class="text-right">
                <span class="text-[10px] uppercase font-black text-gray-400">Total Estimado</span>
                <div id="grand-total-display" class="text-2xl font-black">$ 0.00</div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.js-product-card').forEach(card => {
            const qty = parseFloat(card.querySelector('.js-qty-input').value) || 0;
            const price = parseFloat(card.querySelector('.js-price-input').value) || 0;
            total += qty * price;
        });
        document.getElementById('grand-total-display').innerText = '$ ' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    function filterProducts() {
        const searchInput = document.getElementById('bbProductSearch');
        const categorySelect = document.getElementById('bbProductCategory');
        const query = (searchInput?.value || '').trim().toLowerCase();
        const category = categorySelect?.value || '';

        document.querySelectorAll('.js-product-card').forEach(card => {
            const name = card.dataset.name || '';
            const categoriaId = card.dataset.categoriaId || '';
            const matchesName = !query || name.includes(query);
            const matchesCategory = !category || categoriaId === category;

            card.style.display = matchesName && matchesCategory ? '' : 'none';
        });
    }
    function toggleProduct(card) {
        const input = card.querySelector('.js-qty-input');
        input.value = parseInt(input.value || 0) + 1;
        updateCardState(input);
    }
    function updateCardState(input) {
        const card = input.closest('.js-product-card');
        if (parseInt(input.value) > 0) card.classList.add('is-selected');
        else card.classList.remove('is-selected');
        calculateTotal();
    }
    document.addEventListener('DOMContentLoaded', () => {
        calculateTotal();
        const searchInput = document.getElementById('bbProductSearch');
        const categorySelect = document.getElementById('bbProductCategory');

        if (searchInput) {
            searchInput.addEventListener('input', filterProducts);
        }
        if (categorySelect) {
            categorySelect.addEventListener('change', filterProducts);
        }

        filterProducts();
    });
</script>