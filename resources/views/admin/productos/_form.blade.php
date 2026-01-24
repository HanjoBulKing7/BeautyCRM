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

</div>
