@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre y Email -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user text-azul-400 mr-2"></i>Nombre *
        </label>
        <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-verde-500 focus:border-verde-500 transition" 
            required placeholder="Nombre completo del cliente">
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-envelope text-azul-400 mr-2"></i>Email *
        </label>
        <input type="email" name="email" value="{{ old('email', $cliente->email ?? '') }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-verde-500 focus:border-verde-500 transition" 
            required placeholder="ejemplo@correo.com">
    </div>

    <!-- Fila 2: Teléfono y Dirección -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-phone text-azul-400 mr-2"></i>Teléfono
        </label>
        <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-verde-500 focus:border-verde-500 transition" 
            placeholder="Número de teléfono">
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-map-marker-alt text-azul-400 mr-2"></i>Dirección
        </label>
        <textarea name="direccion" rows="3" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-verde-500 focus:border-verde-500 transition"
            placeholder="Dirección completa del cliente">{{ old('direccion', $cliente->direccion ?? '') }}</textarea>
    </div>

</div>

<!-- Estilos personalizados -->
<style>
    .focus\:ring-verde-500:focus { --tw-ring-color: #22C55E; }
    .focus\:border-verde-500:focus { border-color: #22C55E; }
    .text-azul-400 { color: #60A5FA; }
    .transition { transition: all 0.2s ease-in-out; }
</style>