@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre y Email -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user text-green-500 mr-2"></i>Nombre *
        </label>
        <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
            required placeholder="Nombre completo del empleado">
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-envelope text-blue-500 mr-2"></i>Email *
        </label>
        <input type="email" name="email" value="{{ old('email', $empleado->email) }}" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
            required placeholder="correo@ejemplo.com">
    </div>

    <!-- Fila 2: Contraseña y Confirmar Contraseña -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-lock text-yellow-500 mr-2"></i>
            {{ isset($empleado) && $empleado->id ? 'Nueva Contraseña' : 'Contraseña *' }}
        </label>
        <input type="password" name="password" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" 
            {{ isset($empleado) && $empleado->id ? '' : 'required' }} 
            placeholder="{{ isset($empleado) && $empleado->id ? 'Dejar en blanco para mantener actual' : 'Mínimo 8 caracteres' }}">
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-lock text-yellow-500 mr-2"></i>Confirmar Contraseña {{ isset($empleado) && $empleado->id ? '' : '*' }}
        </label>
        <input type="password" name="password_confirmation" 
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" 
            {{ isset($empleado) && $empleado->id ? '' : 'required' }} 
            placeholder="Repetir contraseña">
    </div>

    <!-- Fila 3: Rol y Sucursal -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user-tag text-purple-500 mr-2"></i>Rol *
        </label>
        <select name="rol" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" required>
            <option value="">Seleccione un rol</option>
            <option value="admin" {{ old('rol', $empleado->rol) == 'admin' ? 'selected' : '' }}>Administrador</option>
            <option value="vendedor" {{ old('rol', $empleado->rol) == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
            <option value="gerente" {{ old('rol', $empleado->rol) == 'gerente' ? 'selected' : '' }}>Gerente</option>
        </select>
    </div>

    <div class="bg-gray-50 p-4 rounded-xl">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-store text-orange-500 mr-2"></i>Sucursal *
        </label>
        <select name="sucursal_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" required>
            <option value="">Seleccione una sucursal</option>
            @foreach($sucursales as $sucursal)
                <option value="{{ $sucursal->id }}" {{ old('sucursal_id', $empleado->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                    {{ $sucursal->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Fila 4: Estado -->
    <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl flex items-center justify-between">
        <label class="block text-sm font-medium text-gray-700 mb-0">
            <i class="fas fa-toggle-on text-indigo-500 mr-2"></i>Estado del empleado
        </label>
        <div class="flex items-center">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" 
                   {{ old('activo', $empleado->activo ?? 1) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <span class="ml-2 text-sm text-gray-600">Activo</span>
        </div>
    </div>

</div>

<!-- Estilos -->
<style>
    .focus\:ring-2:focus {
        ring-width: 2px;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    .border-gray-300 {
        border-color: #d1d5db;
    }
</style>
