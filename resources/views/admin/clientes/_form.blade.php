<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Nombre -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user mr-2" style="color: rgba(201,162,74,.92)"></i>Nombre <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="nombre"
            value="{{ old('nombre', $cliente->nombre ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 bg-white
                   focus:outline-none focus:ring-2 focus:ring-[#C9A24D]/30 focus:border-[#C9A24D]
                   transition"
            required
            placeholder="Nombre completo del cliente"
        >

        @error('nombre')
            <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-envelope mr-2" style="color: rgba(201,162,74,.92)"></i>Email <span class="text-red-500">*</span>
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email', $cliente->email ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 bg-white
                   focus:outline-none focus:ring-2 focus:ring-[#C9A24D]/30 focus:border-[#C9A24D]
                   transition"
            required
            placeholder="ejemplo@correo.com"
        >

        @error('email')
            <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    <!-- Teléfono -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-phone mr-2" style="color: rgba(201,162,74,.92)"></i>Teléfono
        </label>

        <input
        type="tel"
        name="telefono"
        value="{{ old('telefono', $cliente->telefono ?? '') }}"
        inputmode="tel"
        autocomplete="tel"
        class="..."
        placeholder="Número de teléfono"
        >

        @error('telefono')
            <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    <!-- Dirección -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-map-marker-alt mr-2" style="color: rgba(201,162,74,.92)"></i>Dirección
        </label>

        <textarea
            name="direccion"
            rows="3"
            class="w-full border border-gray-300 rounded-lg p-3 bg-white
                   focus:outline-none focus:ring-2 focus:ring-[#C9A24D]/30 focus:border-[#C9A24D]
                   transition"
            placeholder="Dirección completa del cliente"
        >{{ old('direccion', $cliente->direccion ?? '') }}</textarea>

        @error('direccion')
            <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

</div>
