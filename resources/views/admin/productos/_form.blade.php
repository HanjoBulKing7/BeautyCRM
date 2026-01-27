@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Nombre -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-box mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre"
            value="{{ old('nombre', $producto->nombre ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            maxlength="120"
            placeholder="Ej: Shampoo, Crema, Mascarilla"
        >
        @error('nombre') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </div>

    <!-- Precio -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-dollar-sign mr-2" style="color: rgba(201,162,74,.92)"></i>
            Precio <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="precio"
            value="{{ old('precio', $producto->precio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: 199.99"
        >
        @error('precio') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </div>

    <!-- Categoría -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-layer-group mr-2" style="color: rgba(201,162,74,.92)"></i>
            Categoría <span class="text-red-500">*</span>
        </label>

        <select
            name="id_categoria"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
        >
            <option value="">— Selecciona una categoría —</option>
            @foreach($categorias as $cat)
                <option
                    value="{{ $cat->id_categoria }}"
                    {{ (string)old('id_categoria', $producto->id_categoria ?? '') === (string)$cat->id_categoria ? 'selected' : '' }}
                >
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>

        @error('id_categoria') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </div>

    <!-- Estado -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-toggle-on mr-2" style="color: rgba(201,162,74,.92)"></i>
            Estado <span class="text-red-500">*</span>
        </label>

        <select
            name="estado"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
        >
            @php $estadoVal = old('estado', $producto->estado ?? 'activo'); @endphp
            <option value="activo" {{ $estadoVal === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ $estadoVal === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>

        @error('estado') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </div>

    <!-- Descripción -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-align-left mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descripción
        </label>

        <textarea
            name="descripcion"
            rows="4"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Describe el producto (opcional)"
        >{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>

        @error('descripcion') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </div>

    <!-- ✅ Imagen -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-image mr-2" style="color: rgba(201,162,74,.92)"></i>
            Imagen del producto
        </label>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- Preview -->
            <div class="md:w-56">
                <div class="w-full aspect-square rounded-xl border border-gray-200 overflow-hidden bg-gray-50
                            flex items-center justify-center">
                    @if(!empty($producto->imagen))
                        <img
                            id="productoImagenPreview"
                            src="{{ asset('storage/' . $producto->imagen) }}"
                            alt="Imagen del producto"
                            class="w-full h-full object-cover"
                        >
                        <div id="productoImagenPlaceholder" class="hidden text-xs text-gray-500 px-3 text-center">
                            Vista previa
                        </div>
                    @else
                        <img
                            id="productoImagenPreview"
                            src=""
                            alt="Vista previa"
                            class="hidden w-full h-full object-cover"
                        >
                        <div id="productoImagenPlaceholder" class="text-xs text-gray-500 px-3 text-center">
                            Sin imagen (opcional)
                        </div>
                    @endif
                </div>
            </div>

            <!-- Input -->
            <div class="flex-1">
                <input
                    id="productoImagenInput"
                    type="file"
                    name="imagen"
                    accept="image/*"
                    class="w-full border border-gray-300 rounded-lg p-3 transition
                           focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
                >

                <p class="text-xs text-gray-500 mt-2">
                    Formatos: JPG, PNG o WEBP. Máx: 2MB.
                    @if(!empty($producto->imagen))
                        <span class="block">Si seleccionas otra, se reemplaza la actual.</span>
                    @endif
                </p>

                <div class="mt-3 flex gap-2">
                    <button
                        type="button"
                        id="productoImagenClear"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold transition"
                    >
                        Quitar selección
                    </button>
                </div>

                @error('imagen') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

</div>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('productoImagenInput');
    const img = document.getElementById('productoImagenPreview');
    const ph = document.getElementById('productoImagenPlaceholder');
    const clearBtn = document.getElementById('productoImagenClear');

    if (!input || !img || !ph) return;

    const showPreview = (src) => {
        img.src = src;
        img.classList.remove('hidden');
        ph.classList.add('hidden');
    };

    const showPlaceholder = () => {
        img.src = '';
        img.classList.add('hidden');
        ph.classList.remove('hidden');
    };

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;

        if (!file.type || !file.type.startsWith('image/')) {
            showPlaceholder();
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => showPreview(e.target.result);
        reader.readAsDataURL(file);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            input.value = '';
            // Si estás editando y ya había imagen, NO la borramos aquí (solo quita selección).
            // El borrado real lo hace el backend cuando subes otra o eliminas el producto.
            @if(!empty($producto->imagen))
                // Regresa a la imagen guardada
                showPreview("{{ asset('storage/' . $producto->imagen) }}");
            @else
                showPlaceholder();
            @endif
        });
    }
});
</script>
@endonce
