@csrf

@if(isset($ruta) && $ruta)
    <!-- Campo oculto para ruta_id -->
    <input type="hidden" name="ruta_id" value="{{ $ruta->id }}">
    
    <!-- Información de la ruta (solo lectura) -->
    <div class="md:col-span-2 bg-blue-50 p-4 rounded-xl border border-blue-200 mb-4">
        <label class="block text-sm font-medium mb-2 text-blue-700">
            <i class="fas fa-route text-blue-500 mr-2"></i>Gasto Asociado a Ruta
        </label>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                <i class="fas fa-truck text-white"></i>
            </div>
            <div>
                <p class="font-medium text-blue-900">{{ $ruta->nombre }}</p>
                <p class="text-sm text-blue-600">
                    Vendedor: {{ $ruta->empleado->nombre }} | 
                    Fecha: {{ $ruta->fecha->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <p class="text-xs text-blue-500 mt-2">Este gasto se asociará automáticamente a la ruta seleccionada</p>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Fila 1: Fecha y Categoría -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-calendar-day text-blue-500 mr-2"></i>Fecha *
        </label>
        <input type="date" name="fecha" value="{{ old('fecha', $gasto->fecha ? $gasto->fecha->format('Y-m-d') : date('Y-m-d')) }}" 
               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-tag text-green-500 mr-2"></i>Categoría *
        </label>
        <select name="categoria" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" required>
            <option value="">Seleccione una categoría</option>
            @foreach($categorias as $key => $value)
                <option value="{{ $key }}" {{ old('categoria', $gasto->categoria) == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Fila 2: Método de Pago y Monto -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-credit-card text-purple-500 mr-2"></i>Método de Pago *
        </label>
        <select name="metodo_pago" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" required>
            <option value="">Seleccione método</option>
            @foreach($metodosPago as $key => $value)
                <option value="{{ $key }}" {{ old('metodo_pago', $gasto->metodo_pago) == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-money-bill-wave text-yellow-500 mr-2"></i>Monto *
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600 font-medium">$</span>
            <input type="number" step="0.01" name="monto" value="{{ old('monto', $gasto->monto) }}" 
                   class="w-full border border-gray-300 rounded-lg p-3 pl-8 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" 
                   min="0" required placeholder="0.00">
        </div>
    </div>

    <!-- Fila 3: Descripción (ancho completo) -->
    <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-file-alt text-red-500 mr-2"></i>Descripción *
        </label>
        <input name="descripcion" value="{{ old('descripcion', $gasto->descripcion) }}" 
               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" 
               required placeholder="Describe el gasto realizado">
    </div>
</div>