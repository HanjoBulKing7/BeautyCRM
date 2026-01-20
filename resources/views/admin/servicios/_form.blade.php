@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre del Servicio y Categoría -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-tag mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre del Servicio <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre_servicio"
            value="{{ old('nombre_servicio', $servicio->nombre_servicio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: Corte de Cabello, Manicure"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-layer-group mr-2" style="color: rgba(201,162,74,.92)"></i>
            Categoría
        </label>
        <input
            type="text"
            name="categoria"
            value="{{ old('categoria', $servicio->categoria ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Ej: Cabello, Uñas, Facial"
        >
    </div>

    <!-- Fila 2: Precio y Duración -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-dollar-sign mr-2" style="color: rgba(201,162,74,.92)"></i>
            Precio <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="precio"
            step="0.01"
            min="0"
            value="{{ old('precio', $servicio->precio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="0.00"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-clock mr-2" style="color: rgba(201,162,74,.92)"></i>
            Duración (minutos) <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="duracion_minutos"
            min="1"
            value="{{ old('duracion_minutos', $servicio->duracion_minutos ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: 60"
        >
    </div>

    <!-- Fila 3: Descuento y Estado -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-percent mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descuento
        </label>
        <input
            type="number"
            name="descuento"
            step="0.01"
            min="0"
            value="{{ old('descuento', $servicio->descuento ?? 0) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="0.00"
        >
    </div>

    @if(!empty($showEstado))
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
                <option value="activo" {{ old('estado', $servicio->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $servicio->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    @endif

    <!-- Fila 4: Descripción -->
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
            placeholder="Descripción detallada del servicio"
        >{{ old('descripcion', $servicio->descripcion ?? '') }}</textarea>
    </div>

    {{-- Imagen del servicio --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-image mr-2" style="color: rgba(201,162,74,.92)"></i>
            Foto del Servicio
        </label>

        <input
            type="file"
            name="imagen"
            id="imagenInput"
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

        {{-- Preview --}}
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Vista previa</p>

            <img
                id="imagenPreview"
                src="{{ isset($servicio) && $servicio->imagen ? asset('storage/'.$servicio->imagen) : '' }}"
                alt="Preview"
                class="hidden w-full max-w-md rounded-xl border border-gray-200 shadow-sm object-cover"
                style="aspect-ratio: 16 / 9;"
            >
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("imagenInput");
    const preview = document.getElementById("imagenPreview");

    // Si vienes de EDIT y ya hay imagen, muéstrala
    if (preview && preview.getAttribute("src")) {
        preview.classList.remove("hidden");
    }

    if (!input) return;

    input.addEventListener("change", (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith("image/")) {
            alert("Selecciona un archivo de imagen válido.");
            input.value = "";
            return;
        }

        const url = URL.createObjectURL(file);
        preview.src = url;
        preview.classList.remove("hidden");
    });
});
</script>
