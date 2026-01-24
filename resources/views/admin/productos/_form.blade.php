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
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
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

    <!-- Imagen -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-image mr-2" style="color: rgba(201,162,74,.92)"></i>
            Imagen
        </label>

        <input
            type="file"
            name="imagen"
            id="prodImagenInput"
            accept="image/*"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
        >
        @error('imagen') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror

        <p class="text-xs text-gray-500 mt-2">
            Recomendado: JPG/PNG/WEBP (máx 2MB).
        </p>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Vista previa</p>

            <img
                id="prodImagenPreview"
                src="{{ !empty($producto->imagen) ? asset('storage/' . ltrim($producto->imagen,'/')) : '' }}"
                class="{{ !empty($producto->imagen) ? '' : 'hidden' }} w-full max-w-md rounded-xl border border-gray-200 shadow-sm object-cover"
                style="aspect-ratio: 16 / 9;"
                alt="Preview"
            >
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("prodImagenInput");
    const preview = document.getElementById("prodImagenPreview");

    if (input && preview) {
        input.addEventListener("change", (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith("image/")) return;

            preview.src = URL.createObjectURL(file);
            preview.classList.remove("hidden");
        });
    }
});
</script>
