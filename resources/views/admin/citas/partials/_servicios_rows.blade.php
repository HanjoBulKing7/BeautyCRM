{{-- ======================================
   PARTIAL: Servicios (rows dinámicos + botón)
   Archivo: resources/views/admin/citas/partials/_servicios_rows.blade.php
   Requiere: $mode, $cita, $categorias, $bbField, $bbIconColor
====================================== --}}

<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        <i class="fas fa-scissors mr-1" style="{{ $bbIconColor }}"></i>
        Servicios <span class="text-red-500">*</span>
    </label>

    <div id="servicios-wrapper" class="space-y-3">

        {{-- =========================
            EDIT: filas precargadas
        ========================== --}}
        @if($mode === 'edit' && isset($serviciosSeleccionados) && count($serviciosSeleccionados))
            @foreach($serviciosSeleccionados as $i => $svc)
                @php
                    // ✅ ESTE ES EL EMPLEADO ASIGNADO QUE TU JS DEBE PRESELECCIONAR
                    $preEmp = old("servicios.$i.id_empleado", $svc['id_empleado'] ?? '');
                @endphp

                <div
                    class="servicio-row bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center"
                    data-index="{{ $i }}"
                >
                    {{-- CATEGORÍA --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                        <select class="categoria-select {{ $bbField }}" data-role="categoria">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}" @selected($cat === ($svc['categoria'] ?? ''))>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SERVICIO --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Servicio <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="servicios[{{ $i }}][id_servicio]"
                            data-role="servicio"
                            data-selected="{{ $svc['id_servicio'] ?? '' }}"
                            class="servicio-select {{ $bbField }}"
                            required
                        >
                            <option value="">Cargando servicios…</option>
                        </select>
                    </div>

                    {{-- EMPLEADO (por servicio) --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>

                        {{-- ✅ IMPORTANTE:
                           - NO renderizamos lista completa aquí porque tu JS la reemplaza con fetch()
                           - data-preselect = el empleado asignado
                           - disabled al inicio: JS lo habilita cuando termine de cargar
                        --}}
                        <select
                            name="servicios[{{ $i }}][id_empleado]"
                            data-role="empleado"
                            data-preselect="{{ $preEmp }}"
                            class="{{ $bbField }}"
                            disabled
                        >
                            <option value="">
                                {{ $preEmp ? 'Cargando empleado…' : 'Selecciona un servicio primero' }}
                            </option>
                        </select>
                    </div>

                    {{-- PRECIO --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="servicios[{{ $i }}][precio_snapshot]"
                            value="{{ old("servicios.$i.precio_snapshot", $svc['precio_snapshot'] ?? '') }}"
                            data-role="precio_snapshot"
                            class="precio-input {{ $bbField }}"
                        >
                    </div>

                    {{-- DURACIÓN --}}
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                        <input
                            type="number"
                            step="1"
                            min="0"
                            name="servicios[{{ $i }}][duracion_snapshot]"
                            value="{{ old("servicios.$i.duracion_snapshot", $svc['duracion_snapshot'] ?? '') }}"
                            data-role="duracion_snapshot"
                            class="duracion-input {{ $bbField }}"
                        >
                    </div>

                    {{-- QUITAR --}}
                    <div class="md:col-span-1 flex items-center justify-center mt-7">
                        <button
                            type="button"
                            class="btn-remove-servicio w-12 h-12 rounded-lg bg-red-500 text-white hover:bg-red-600 transition"
                            title="Quitar servicio"
                        >
                            <i class="fas fa-times text-lg leading-none"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- =========================
            CREATE: row base (clonable)
        ========================== --}}
        @if($mode === 'create')
            <div
                class="servicio-row bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center"
                data-index="0"
            >
                {{-- CATEGORÍA --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select id="categoria_main" class="categoria-select {{ $bbField }}" data-role="categoria">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SERVICIO --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Servicio <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="servicio_main"
                        name="servicios[0][id_servicio]"
                        data-role="servicio"
                        class="servicio-select {{ $bbField }}"
                        required
                    >
                        <option value="">Selecciona primero una categoría</option>
                    </select>
                </div>

                {{-- EMPLEADO --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select
                        name="servicios[0][id_empleado]"
                        data-role="empleado"
                        class="{{ $bbField }}"
                        disabled
                    >
                        <option value="">Selecciona un servicio primero</option>
                    </select>
                </div>

                {{-- PRECIO --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                    <div class="relative">
                        <span class="absolute left-3 top-0 bottom-0 flex items-center text-gray-500">$</span>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="servicios[0][precio_snapshot]"
                            class="precio-input {{ $bbField }} pl-9"
                            placeholder="0.00"
                            data-role="precio_snapshot"
                        >
                    </div>
                </div>

                {{-- DURACIÓN --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                    <input
                        type="number"
                        step="1"
                        min="0"
                        name="servicios[0][duracion_snapshot]"
                        class="duracion-input {{ $bbField }}"
                        placeholder="min"
                        data-role="duracion_snapshot"
                    >
                </div>

                {{-- QUITAR --}}
                <div class="md:col-span-1 flex items-center justify-center mt-7">
                    <button
                        type="button"
                        class="btn-remove-servicio remove-servicio w-12 h-12 inline-flex items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600 transition"
                        title="Quitar servicio"
                    >
                        <i class="fas fa-times text-lg leading-none"></i>
                    </button>
                </div>
            </div>

            <button
                type="button"
                id="btn-add-servicio"
                class="mt-2 inline-flex items-center text-sm font-semibold transition"
                style="color: rgba(201,162,74,.95)"
            >
                <i class="fas fa-plus-circle mr-2"></i>
                Agregar otro servicio
            </button>
        @endif

        @error('servicios')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>