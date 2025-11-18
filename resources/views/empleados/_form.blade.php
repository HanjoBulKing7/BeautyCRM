@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre y Apellido -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user text-pink-400 mr-2"></i>Nombre *
        </label>
        <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" 
            required placeholder="Nombre del empleado">
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user text-pink-400 mr-2"></i>Apellido *
        </label>
        <input type="text" name="apellido" value="{{ old('apellido', $empleado->apellido) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" 
            required placeholder="Apellido del empleado">
    </div>

    <!-- Fila 2: Teléfono y Puesto -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-phone text-pink-400 mr-2"></i>Teléfono *
        </label>
        <input type="text" name="telefono" value="{{ old('telefono', $empleado->telefono) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" 
            required placeholder="Número de teléfono">
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-briefcase text-pink-400 mr-2"></i>Puesto
        </label>
        <input type="text" name="puesto" value="{{ old('puesto', $empleado->puesto) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" 
            placeholder="Puesto del empleado">
    </div>

    <!-- Fila 3: Departamento y Fecha de Contratación -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-building text-pink-400 mr-2"></i>Departamento
        </label>
        <input type="text" name="departamento" value="{{ old('departamento', $empleado->departamento) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" 
            placeholder="Departamento">
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-calendar text-pink-400 mr-2"></i>Fecha de Contratación
        </label>
        <input type="date" name="fecha_contratacion" value="{{ old('fecha_contratacion', $empleado->fecha_contratacion) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition">
    </div>

    <!-- Fila 4: Estatus y Información Legal -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-toggle-on text-pink-400 mr-2"></i>Estatus *
        </label>
        <select name="estatus" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" required>
            <option value="activo" {{ old('estatus', $empleado->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estatus', $empleado->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            <option value="vacaciones" {{ old('estatus', $empleado->estatus) == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
        </select>
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-file-contract text-pink-400 mr-2"></i>Información Legal
        </label>
        <textarea name="informacion_legal" rows="4" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition"
            placeholder="Información legal o contractual del empleado">{{ old('informacion_legal', $empleado->informacion_legal) }}</textarea>
    </div>

</div>

<!-- Estilos personalizados -->
<style>
    .focus\:ring-gold-500:focus {
        --tw-ring-color: #D4AF37;
    }
    .focus\:border-gold-500:focus {
        border-color: #D4AF37;
    }
    .text-pink-400 {
        color: #F8BBD9;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
</style>