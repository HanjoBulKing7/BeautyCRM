@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre del Servicio y Categoría -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-tag mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre del Servicio <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre_servicio"
            value="{{ old('nombre_servicio', $servicio->nombre_servicio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: Corte de Cabello, Manicure"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-layer-group mr-2" style="color: rgba(201,162,74,.92)"></i>
            Categoría
        </label>
        <input
            type="text"
            name="categoria"
            value="{{ old('categoria', $servicio->categoria ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Ej: Cabello, Uñas, Facial"
        >
    </div>

    <!-- Fila 2: Precio y Duración -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-dollar-sign mr-2" style="color: rgba(201,162,74,.92)"></i>
            Precio <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="precio"
            step="0.01"
            min="0"
            value="{{ old('precio', $servicio->precio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="0.00"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-clock mr-2" style="color: rgba(201,162,74,.92)"></i>
            Duración (minutos) <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="duracion_minutos"
            min="1"
            value="{{ old('duracion_minutos', $servicio->duracion_minutos ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: 60"
        >
    </div>

    <!-- Fila 3: Descuento y Estado -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-percent mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descuento
        </label>
        <input
            type="number"
            name="descuento"
            step="0.01"
            min="0"
            value="{{ old('descuento', $servicio->descuento ?? 0) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="0.00"
        >
    </div>

    @if(!empty($showEstado))
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
                <option value="activo" {{ old('estado', $servicio->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $servicio->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    @endif

    <!-- Fila 4: Descripción -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-align-left mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descripción
        </label>
        <textarea
            name="descripcion"
            rows="3"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Descripción detallada del servicio"
        >{{ old('descripcion', $servicio->descripcion ?? '') }}</textarea>
    </div>

    {{-- Imagen del servicio --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-image mr-2" style="color: rgba(201,162,74,.92)"></i>
            Foto del Servicio
        </label>

        <input
            type="file"
            name="imagen"
            id="imagenInput"
            accept="image/*"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
        >

        @error('imagen')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

        <p class="text-xs text-gray-500 mt-2">
            Formatos recomendados: JPG/PNG/WEBP (máx 2MB).
        </p>

        {{-- Preview --}}
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Vista previa</p>

            <img
                id="imagenPreview"
                src="{{ isset($servicio) && $servicio->imagen ? asset('storage/' . ltrim($servicio->imagen, '/')) : '' }}"
                alt="Preview"
                class="hidden w-full max-w-md rounded-xl border border-gray-200 shadow-sm object-cover"
                style="aspect-ratio: 16 / 9;"
            >
        </div>
    </div>

    {{-- =========================
    HORARIOS DEL SERVICIO
    ========================= --}}
    @php
    $dias = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    // Prefill para edit
    $horariosPrefill = [];
    if(isset($servicio) && $servicio && $servicio->exists) {
        foreach(($servicio->horarios ?? []) as $h) {
        $horariosPrefill[$h->dia_semana][] = [
            'hora_inicio' => substr((string)$h->hora_inicio, 0, 5),
            'hora_fin'    => substr((string)$h->hora_fin, 0, 5),
        ];
        }
    }
    @endphp

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
    <div class="flex items-start justify-between gap-3">
        <div>
        <label class="block text-sm font-medium text-gray-700">
            <i class="fas fa-calendar-alt mr-2" style="color: rgba(201,162,74,.92)"></i>
            Horarios del Servicio
        </label>
        <p class="text-xs text-gray-500 mt-1">
            Agrega uno o más rangos por día (ej. 10:00–14:00 y 16:00–19:00).
        </p>
        </div>
    </div>

    <div class="mt-4 space-y-4" id="bb-horarios-wrapper">
        @foreach($dias as $dia => $diaLabel)
        @php
            $rows = old("horarios.$dia") ?? ($horariosPrefill[$dia] ?? []);
            $rows = is_array($rows) ? $rows : [];
        @endphp

        <div class="rounded-lg border border-gray-200 p-3">
            <div class="flex items-center justify-between gap-3">
            <div class="font-medium text-gray-800">{{ $diaLabel }}</div>

            <button
                type="button"
                class="text-sm px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50"
                data-add-horario
                data-dia="{{ $dia }}"
            >
                + Agregar rango
            </button>
            </div>

            <div class="mt-3 space-y-2" data-dia-wrap="{{ $dia }}">
            @foreach($rows as $i => $r)
                @php
                $hi = data_get($r, 'hora_inicio', '');
                $hf = data_get($r, 'hora_fin', '');
                @endphp

                <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center bg-gray-50 rounded-lg p-2" data-horario-row>
                <div class="flex gap-2 items-center w-full sm:w-auto">
                    <label class="text-xs text-gray-600 w-16">Inicio</label>
                    <input type="time"
                        name="horarios[{{ $dia }}][{{ $i }}][hora_inicio]"
                        value="{{ $hi }}"
                        class="w-full sm:w-40 border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div class="flex gap-2 items-center w-full sm:w-auto">
                    <label class="text-xs text-gray-600 w-16">Fin</label>
                    <input type="time"
                        name="horarios[{{ $dia }}][{{ $i }}][hora_fin]"
                        value="{{ $hf }}"
                        class="w-full sm:w-40 border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <button type="button"
                        class="ml-auto text-xs px-3 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                        data-remove-horario>
                    Quitar
                </button>
                </div>
            @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @error('horarios')
    <div class="mt-3 p-3 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm space-y-1">
        @if(is_array($message))
        @foreach($message as $m)
            <div>• {{ $m }}</div>
        @endforeach
        @else
        <div>• {{ $message }}</div>
        @endif
    </div>
    @enderror

    </div>


</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("imagenInput");
    const preview = document.getElementById("imagenPreview");

    // Si vienes de EDIT y ya hay imagen, muéstrala
    if (preview && preview.getAttribute("src")) {
        preview.classList.remove("hidden");
    }

    if (!input) return;

    input.addEventListener("change", (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith("image/")) {
            alert("Selecciona un archivo de imagen válido.");
            input.value = "";
            return;
        }

        const url = URL.createObjectURL(file);
        preview.src = url;
        preview.classList.remove("hidden");
    });

        // ==========================
    // Horarios del servicio (UI)
    // ==========================
    const horariosWrapper = document.getElementById('bb-horarios-wrapper');
    if (horariosWrapper) {

        const nextIndexForDay = (day) => {
            const dayWrap = horariosWrapper.querySelector(`[data-dia-wrap="${day}"]`);
            const rows = dayWrap ? dayWrap.querySelectorAll('[data-horario-row]') : [];
            return rows.length;
        };

        horariosWrapper.addEventListener('click', (e) => {

            const addBtn = e.target.closest('[data-add-horario]');
            if (addBtn) {
                const day = addBtn.getAttribute('data-dia');
                const dayWrap = horariosWrapper.querySelector(`[data-dia-wrap="${day}"]`);
                if (!dayWrap) return;

                const i = nextIndexForDay(day);

                const row = document.createElement('div');
                row.className = 'flex flex-col sm:flex-row gap-2 items-start sm:items-center bg-gray-50 rounded-lg p-2';
                row.setAttribute('data-horario-row', '');

                row.innerHTML = `
                    <div class="flex gap-2 items-center w-full sm:w-auto">
                        <label class="text-xs text-gray-600 w-16">Inicio</label>
                        <input type="time"
                               name="horarios[${day}][${i}][hora_inicio]"
                               class="w-full sm:w-40 border border-gray-300 rounded-lg px-3 py-2">
                    </div>

                    <div class="flex gap-2 items-center w-full sm:w-auto">
                        <label class="text-xs text-gray-600 w-16">Fin</label>
                        <input type="time"
                               name="horarios[${day}][${i}][hora_fin]"
                               class="w-full sm:w-40 border border-gray-300 rounded-lg px-3 py-2">
                    </div>

                    <button type="button"
                            class="ml-auto text-xs px-3 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                            data-remove-horario>
                        Quitar
                    </button>
                `;

                dayWrap.appendChild(row);
                return;
            }

            const removeBtn = e.target.closest('[data-remove-horario]');
            if (removeBtn) {
                const row = removeBtn.closest('[data-horario-row]');
                if (row) row.remove();
            }
        });
    }

});
</script>
