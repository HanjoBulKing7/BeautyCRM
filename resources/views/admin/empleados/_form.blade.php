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

    @php
        $servicios = \App\Models\Servicio::all();
        $selectedServicios = old('servicios', isset($empleado) && $empleado->relationLoaded('servicios') ? $empleado->servicios->pluck('id_servicio')->toArray() : (isset($empleado) ? $empleado->servicios->pluck('id_servicio')->toArray() : []));
    @endphp

<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">

    {{-- 1️⃣ Título --}}
    <label class="block text-sm font-medium mb-2 text-gray-700">
        <i class="fas fa-concierge-bell mr-2" style="color: rgba(201,162,74,.92)"></i>
        Servicios asignados al empleado
    </label>

    {{-- 2️⃣ Servicios seleccionados (TIEMPO REAL) --}}
    <div
        id="servicios-seleccionados"
        class="flex flex-wrap gap-2 mb-4"
    >
        {{-- aquí se van agregando automáticamente --}}
    </div>

    {{-- 3️⃣ Select --}}
    <select
        id="servicio-selector"
        class="w-full border border-gray-300 rounded-lg p-3 transition
               focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]
               focus:border-[rgba(201,162,74,.55)]"
    >
        <option value="">Selecciona un servicio</option>
        @foreach($servicios as $servicio)
            <option value="{{ $servicio->id_servicio }}">
                {{ $servicio->nombre_servicio }}
            </option>
        @endforeach
    </select>

    <p class="mt-2 text-sm text-gray-500">
        Selecciona un servicio y se agregará automáticamente.
    </p>
</div>



</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('servicios-seleccionados');

    const ts = new TomSelect('#servicio-selector', {
        create: false,
        placeholder: 'Selecciona un servicio',
        onItemAdd(value, text) {

            // ❌ Evitar duplicados
            if (contenedor.querySelector(`[data-id="${value}"]`)) {
                this.clear();
                return;
            }

            const tag = document.createElement('span');
            tag.className =
                'flex items-center gap-2 bg-[rgba(201,162,74,.12)] text-gray-800 ' +
                'px-3 py-1 rounded-full text-sm border border-[rgba(201,162,74,.35)]';
            tag.dataset.id = value;

            tag.innerHTML = `
                ${text}
                <button type="button"
                        class="text-gray-500 hover:text-red-600 remove-servicio">
                    ✕
                </button>
                <input type="hidden" name="servicios[]" value="${value}">
            `;

            contenedor.appendChild(tag);

            // ✅ limpiar selector para permitir seguir agregando
            this.clear();
        }
    });

    // eliminar servicio
    contenedor.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-servicio')) {
            e.target.closest('span').remove();
        }
    });
});
</script>
