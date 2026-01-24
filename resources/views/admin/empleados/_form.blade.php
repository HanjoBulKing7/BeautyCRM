@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre y Apellido -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre"
            value="{{ old('nombre', $empleado->nombre) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Nombre del empleado"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user mr-2" style="color: rgba(201,162,74,.92)"></i>
            Apellido <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="apellido"
            value="{{ old('apellido', $empleado->apellido) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Apellido del empleado"
        >
    </div>

    <!-- Fila 2: Teléfono, Puesto & Email -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-phone mr-2" style="color: rgba(201,162,74,.92)"></i>
            Teléfono <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="telefono"
            value="{{ old('telefono', $empleado->telefono) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Número de teléfono"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-briefcase mr-2" style="color: rgba(201,162,74,.92)"></i>
            Puesto
        </label>
        <input
            type="text"
            name="puesto"
            value="{{ old('puesto', $empleado->puesto) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Puesto del empleado"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-envelope mr-2" style="color: rgba(201,162,74,.92)"></i>
            Email <span class="text-red-500">*</span>
        </label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $empleado->email ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Correo del empleado"
            required
        >
        @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Fila 3: Departamento y Fecha de Contratación -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-building mr-2" style="color: rgba(201,162,74,.92)"></i>
            Departamento
        </label>
        <input
            type="text"
            name="departamento"
            value="{{ old('departamento', $empleado->departamento) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Departamento"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-calendar mr-2" style="color: rgba(201,162,74,.92)"></i>
            Fecha de Contratación
        </label>
        <input
            type="date"
            name="fecha_contratacion"
            value="{{ old('fecha_contratacion', $empleado->fecha_contratacion) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
        >
    </div>

    <!-- Fila 4: Estatus y Información Legal -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-toggle-on mr-2" style="color: rgba(201,162,74,.92)"></i>
            Estatus <span class="text-red-500">*</span>
        </label>
        <select
            name="estatus"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
        >
            <option value="activo" {{ old('estatus', $empleado->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estatus', $empleado->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            <option value="vacaciones" {{ old('estatus', $empleado->estatus) == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
        </select>
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-file-contract mr-2" style="color: rgba(201,162,74,.92)"></i>
            Información Legal
        </label>
        <textarea
            name="informacion_legal"
            rows="4"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Información legal o contractual del empleado"
        >{{ old('informacion_legal', $empleado->informacion_legal) }}</textarea>
    </div>

</div>
