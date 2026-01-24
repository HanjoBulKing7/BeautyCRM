@php
    // por si no lo mandas desde la vista
    $showEstado = $showEstado ?? false;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Nombre -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-tag mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre"
            id="catNombre"
            value="{{ old('nombre', $categoria->nombre ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: Cabello, Uñas, Facial"
        >
        @error('nombre')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Slug preview (solo visual, no se envía; tu controller lo genera con Str::slug) -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-link mr-2" style="color: rgba(201,162,74,.92)"></i>
            Slug (auto)
        </label>
        <input
            type="text"
            id="catSlugPreview"
            value="{{ old('slug', $categoria->slug ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 text-gray-700"
            readonly
        >
        <p class="text-xs text-gray-500 mt-2">Se genera automáticamente a partir del nombre.</p>
    </div>

    <!-- Estado (solo en edit) -->
    @if($showEstado)
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
                <option value="activo" {{ old('estado', $categoria->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $categoria->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>

            @error('estado')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    @else
        <!-- En create tu controller exige estado, entonces lo mandamos hidden -->
        <input type="hidden" name="estado" value="{{ old('estado', 'activo') }}">
    @endif

    <!-- Descripción -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-align-left mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descripción
        </label>
        <textarea
            name="descripcion"
            rows="3"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Descripción de la categoría"
        >{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>

        @error('descripcion')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Imagen -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-image mr-2" style="color: rgba(201,162,74,.92)"></i>
            Foto de la Categoría
        </label>

        <input
            type="file"
            name="imagen"
            id="catImagenInput"
            accept="image/*"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
        >

        @error('imagen')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

        <p class="text-xs text-gray-500 mt-2">
            Formatos recomendados: JPG/PNG/WEBP (máx 2MB).
        </p>

        <!-- Preview -->
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Vista previa</p>

            <img
                id="catImagenPreview"
                src="{{ isset($categoria) && $categoria->imagen ? asset('storage/' . ltrim($categoria->imagen, '/')) : '' }}"
                alt="Preview"
                class="{{ isset($categoria) && $categoria->imagen ? '' : 'hidden' }} w-full max-w-md rounded-xl border border-gray-200 shadow-sm object-cover"
                style="aspect-ratio: 16 / 9;"
            >
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preview imagen
    const input = document.getElementById('catImagenInput');
    const preview = document.getElementById('catImagenPreview');

    if (input && preview) {
        input.addEventListener('change', function (e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('hidden');
        });
    }

    // Slug preview (solo visual)
    const nombre = document.getElementById('catNombre');
    const slugPreview = document.getElementById('catSlugPreview');

    const slugify = (str) => (str || '')
        .toString()
        .trim()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');

    if (nombre && slugPreview) {
        nombre.addEventListener('input', () => {
            slugPreview.value = slugify(nombre.value);
        });

        if (!slugPreview.value && nombre.value) {
            slugPreview.value = slugify(nombre.value);
        }
    }
});
</script>
