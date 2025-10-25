<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="bg-gray-50 p-4 rounded-xl md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-tag text-green-500 mr-2"></i>Nombre de la Ruta
        </label>
        <input type="text" name="nombre" 
               value="{{ old('nombre', $nombreSugerido ?? ($ruta->nombre ?? '')) }}"
               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
               placeholder="Ejemplo: Ruta 1 o Ruta Norte">
        <p class="text-xs text-gray-500 mt-1">Si lo dejas vacío, se asignará automáticamente el siguiente número.</p>
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-calendar text-blue-500 mr-2"></i>Fecha *
        </label>
        <input type="date" name="fecha" value="{{ old('fecha', $ruta->fecha ?? date('Y-m-d')) }}"
               required
               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user text-purple-500 mr-2"></i>Empleado Asignado *
        </label>
        <select name="empleado_id" required
                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
            <option value="">Seleccione un empleado</option>
            @foreach($empleados as $empleado)
                <option value="{{ $empleado->id }}" {{ old('empleado_id', $ruta->empleado_id ?? '') == $empleado->id ? 'selected' : '' }}>
                    {{ $empleado->nombre }} ({{ ucfirst($empleado->rol) }})
                </option>
            @endforeach
        </select>
    </div>
</div>
