@php
    // Modo: 'create' | 'edit'
    $mode = $mode ?? 'create';

    // Defaults
    $cita = $cita ?? null;

    // Prefill cliente seleccionado (para el input visible)
    $selectedClienteId = old('cliente_id', $cita->cliente_id ?? '');
    $selectedCliente   = $selectedClienteId
        ? ($clientes->firstWhere('id', (int) $selectedClienteId) ?? null)
        : null;

    $clienteLabel = trim(
        ($selectedCliente->nombre ?? '') .
        (($selectedCliente && !empty($selectedCliente->email)) ? ' - ' . $selectedCliente->email : '')
    );

    // ✅ UI: field + icon (dorado dashboard)
    $bbField = "w-full border border-gray-300 rounded-lg px-4 py-3 transition
                focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]";

    $bbIconColor = "color: rgba(201,162,74,.92)";
        $horaGuardada = substr((string) old('hora_cita', $cita->hora_cita ?? ''), 0, 5); // "HH:MM"

@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($mode === 'edit')
        @method('PUT')
    @endif

    {{-- =========================
        GRID FORM
    ========================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- CLIENTE (buscador) --}}
        <div class="relative">
            <label for="cliente_search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user mr-1" style="{{ $bbIconColor }}"></i>
                Cliente <span class="text-red-500">*</span>
            </label>

            {{-- Valor real --}}
            <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $selectedClienteId }}" required>

            {{-- Input visible --}}
            <div class="relative">
                <input
                    type="text"
                    id="cliente_search"
                    autocomplete="off"
                    placeholder="Escribe para buscar… (ej. Juan)"
                    value="{{ old('cliente_label', $clienteLabel) }}"
                    class="{{ $bbField }} pr-10"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            {{-- Dropdown --}}
            <div
                id="cliente_dropdown"
                class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden hidden"
            >
                <div id="cliente_results" class="max-h-60 overflow-auto"></div>
            </div>

            @error('cliente_id')
                <p class="text-red-500 text-sm mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror

        </div>

        {{-- FECHA --}}
        <div>
            <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar mr-1" style="{{ $bbIconColor }}"></i>
                Fecha de la Cita <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="fecha_cita"
                name="fecha_cita"
                value="{{ old('fecha_cita', $cita->fecha_cita ?? ($fechaPrefill ?? '')) }}"
                class="{{ $bbField }}"
                required
            />

            @error('fecha_cita')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- SERVICIOS ROWS (partial) --}}
        @include('admin.citas.partials._servicios_rows', [
            'mode' => $mode,
            'cita' => $cita,
            'categorias' => $categorias,
            'bbField' => $bbField,
            'bbIconColor' => $bbIconColor,
                'empleados' => $empleados,
            ])

        {{-- TOTAL DURACIÓN --}}
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Duración total (min)
            </label>
            <input
                id="duracion_total"
                type="number"
                class="{{ $bbField }} bg-gray-50"
                readonly
                value="0"
            >
        </div>

        {{-- TOTAL PRECIO --}}
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Total servicios (MXN)
            </label>

            <div class="relative">
                <span class="absolute left-3 top-0 bottom-0 flex items-center text-gray-500">$</span>
                <input
                    id="total_servicios"
                    name="total_servicios"
                    type="number"
                    step="0.01"
                    class="{{ $bbField }} pl-9 bg-gray-50"
                    readonly
                    value="0"
                >
            </div>
        </div>

        {{-- HORA --}}
        <div>
            <label for="hora_cita" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-clock mr-1" style="{{ $bbIconColor }}"></i>
                Hora de la Cita <span class="text-red-500">*</span>
            </label>

            <select
                id="hora_cita"
                name="hora_cita"
                class="{{ $bbField }}"
                required
            >
                @php
                    $horaRaw = old('hora_cita', $cita->hora_cita ?? '');
                    $horaSelected = '';
                    if ($horaRaw) {
                        try {
                            $horaSelected = \Carbon\Carbon::createFromFormat('H:i', $horaRaw)->format('H:i');
                        } catch (Exception $e) {
                            $horaSelected = $horaRaw;
                        }
                    }
                    @endphp
                    @php
                        $horaRaw = old('hora_cita', $cita->hora_cita ?? '');
                        $horaSelected = '';
                        if ($horaRaw) {
                            try {
                                $horaSelected = \Carbon\Carbon::createFromFormat('H:i', $horaRaw)->format('H:i');
                            } catch (Exception $e) {
                                $horaSelected = $horaRaw;
                            }
                        }
                @endphp

                <option value="">Seleccionar Hora</option>
                @for($h = 9; $h <= 20; $h++)
                    @foreach([0, 30] as $m)
                        @php $time = sprintf('%02d:%02d', $h, $m); @endphp
                        <option value="{{ $time }}" @selected($horaSelected == $time)>
                            {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                        </option>
                    @endforeach
                @endfor
            </select>

            @error('hora_cita')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- EMPLEADO GLOBAL (DESACTIVADO) --}}
        <!--
        <div> ... </div>
        -->

        {{-- ESTADO --}}
        <div>
            <label for="estado_cita" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-clipboard-check mr-1" style="{{ $bbIconColor }}"></i>
                Estado <span class="text-red-500">*</span>
            </label>

            @php $estadoSelected = old('estado_cita', $cita->estado_cita ?? 'pendiente'); @endphp

            <select
                id="estado_cita"
                name="estado_cita"
                class="{{ $bbField }}"
                required
            >
                <option value="confirmada" @selected($estadoSelected === 'confirmada')>Confirmada</option>
                <option value="cancelada"  @selected($estadoSelected === 'cancelada')>Cancelada</option>
                <option value="completada" @selected($estadoSelected === 'completada')>Completada</option>
            </select>

            @error('estado_cita')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- MÉTODO DE PAGO (solo si está COMPLETADA) --}}
        <div id="metodo_pago_wrap" style="display:none;">
            <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-credit-card mr-1" style="{{ $bbIconColor }}"></i>
                Método de pago <span class="text-red-500">*</span>
            </label>

            @php $metodoPagoSelected = old('metodo_pago', $cita->metodo_pago ?? ''); @endphp

            <select id="metodo_pago" name="metodo_pago" class="{{ $bbField }}">
            <option value="">Seleccionar método</option>
            <option value="efectivo" @selected($metodoPagoSelected === 'efectivo')>Efectivo</option>
            <option value="tarjeta_credito" @selected($metodoPagoSelected === 'tarjeta_credito')>Tarjeta (crédito)</option>
            <option value="tarjeta_debito" @selected($metodoPagoSelected === 'tarjeta_debito')>Tarjeta (débito)</option>
            <option value="transferencia" @selected($metodoPagoSelected === 'transferencia')>Transferencia</option>
            </select>
            @error('metodo_pago')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- OBSERVACIONES --}}
        <div class="md:col-span-2">
            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-sticky-note mr-1" style="{{ $bbIconColor }}"></i>
                Observaciones
            </label>

            <textarea
                id="observaciones"
                name="observaciones"
                rows="3"
                class="{{ $bbField }}"
            >{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>

            @error('observaciones')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- DESCUENTO --}}
        <div class="md:col-span-2">
            <label for="descuento" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-tag mr-1" style="{{ $bbIconColor }}"></i>
                Descuento
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="descuento"
                id="descuento"
                value="{{ old('descuento', $cita->descuento ?? 0) }}"
                class="{{ $bbField }}"
                placeholder="Monto en pesos (ej. 50.00). Si no aplica, deja en 0."
            />

            @error('descuento')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- BOTONES --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
        <a
            href="{{ route('admin.citas.index') }}"
            class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                   flex items-center justify-center gap-2 transition"
        >
            <i class="fas fa-arrow-left" style="color: rgba(17,24,39,.70)"></i>
            Volver a Citas
        </a>

        <button
            type="submit"
            class="px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition focus:outline-none"
            style="
                background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                border: 1px solid rgba(201,162,74,.35);
                box-shadow: 0 10px 22px rgba(201,162,74,.18);
                color: #111827;
            "
            onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
            onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
        >
            <i class="fas fa-save" style="color: rgba(17,24,39,.90)"></i>
            {{ $mode === 'edit' ? 'Actualizar Cita' : 'Crear Cita' }}
        </button>
    </div>
</form>

@push('scripts')
    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @php
        $clientesForJs = $clientes->map(function ($c) {
            return [
                'id'     => $c->id,
                'label'  => trim((($c->nombre ?? $c->name ?? '') . ' - ' . ($c->email ?? ''))),
                'nombre' => ($c->nombre ?? $c->name ?? ''),
                'email'  => ($c->email ?? ''),
            ];
        })->values();

        $serviciosForJs = $servicios->map(function ($s) {
            return [
                'id'       => $s->id_servicio,
                'nombre'   => $s->nombre_servicio,
                'categoria'=> $s->categoria->nombre ?? 'Sin categoría',
                'duracion' => $s->duracion_minutos,
                'precio'   => $s->precio,
            ];
        })->values();
    @endphp

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Flatpickr: create = minDate today, edit = permitir fechas pasadas y set defaultDate
    flatpickr("#fecha_cita", {
        locale: "es",
        minDate: @json($mode === 'create' ? 'today' : null),
        defaultDate: document.getElementById('fecha_cita').value || null,
        dateFormat: "Y-m-d",
        disableMobile: true,
    });

    // ===========================
    // Multi-servicio (UI) + snapshots + empleado por servicio
    // ===========================
    const serviciosWrapper = document.getElementById('servicios-wrapper');
    const btnAddServicio   = document.getElementById('btn-add-servicio');

    // ✅ Hora guardada desde backend (funciona aunque DB traiga HH:MM:SS)
    const HORA_GUARDADA = @json($horaGuardada);
    if (serviciosWrapper) {

        const serviciosAll = @json($serviciosForJs);
        const norm = (v) => (v ?? '').toString().trim().toLowerCase();

        function buildOptionsForServiceSelect(selectEl, categoria, selectedId = "") {
            selectEl.innerHTML = "";

            if (!categoria) {
                const opt = document.createElement("option");
                opt.value = "";
                opt.textContent = "Selecciona primero una categoría";
                selectEl.appendChild(opt);
                return;
            }

            const opt0 = document.createElement("option");
            opt0.value = "";
            opt0.textContent = "Seleccionar servicio";
            selectEl.appendChild(opt0);

            const catN = norm(categoria);

            serviciosAll
                .filter(s => norm(s.categoria) === catN)
                .forEach(s => {
                    const opt = document.createElement("option");
                    opt.value = s.id;

                    const precio = Number(s.precio ?? 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
                    opt.textContent = `${s.nombre} - $${precio} (${s.duracion} min)`;

                    opt.dataset.duracion = s.duracion ?? 0;
                    opt.dataset.precio   = s.precio ?? 0;

                    if (String(selectedId) === String(s.id)) opt.selected = true;
                    selectEl.appendChild(opt);
                });
        }

        async function loadEmpleadosForRow(rowEl, servicioId, preselectId = null) {
            const empleadoSelect = rowEl.querySelector('select[data-role="empleado"]');
            if (!empleadoSelect) return;

            empleadoSelect.innerHTML = `<option value="">Cargando...</option>`;
            empleadoSelect.disabled = true;

            if (!servicioId) {
                empleadoSelect.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
                return;
            }

            const url = `{{ route('admin.citas.empleadosPorServicio') }}?servicio_id=${encodeURIComponent(servicioId)}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            const data = await res.json();

            empleadoSelect.innerHTML =
                `<option value="">Selecciona un empleado</option>` +
                data.map(e => `<option value="${e.id}">${e.label}</option>`).join('');

            empleadoSelect.disabled = false;

            if (preselectId) {
                empleadoSelect.value = String(preselectId);
            } else if (data.length === 1) {
                empleadoSelect.value = String(data[0].id);
            }
        }

        function recalcTotalDuracion() {
            let total = 0;
            serviciosWrapper.querySelectorAll('input[data-role="duracion_snapshot"]').forEach(inp => {
                const v = parseInt(inp.value || '0', 10);
                total += isNaN(v) ? 0 : v;
            });

            const totalInput = document.getElementById('duracion_total');
            if (totalInput) totalInput.value = total;
        }

        function recalcTotalMonto() {
            let total = 0;

            serviciosWrapper.querySelectorAll('input[data-role="precio_snapshot"]').forEach(inp => {
                const v = parseFloat(inp.value || '0');
                total += isNaN(v) ? 0 : v;
            });

            const totalInput = document.getElementById('total_servicios');
            if (totalInput) totalInput.value = total.toFixed(2);
        }

        function recalcAll() {
            recalcTotalDuracion();
            recalcTotalMonto();
        }

        function reindexRows() {
            const rows = serviciosWrapper.querySelectorAll('.servicio-row');

            rows.forEach((row, i) => {
                const svc    = row.querySelector('select[data-role="servicio"]');
                const emp    = row.querySelector('select[data-role="empleado"]');
                const precio = row.querySelector('input[data-role="precio_snapshot"]');
                const dur    = row.querySelector('input[data-role="duracion_snapshot"]');

                if (svc)    svc.name    = `servicios[${i}][id_servicio]`;
                if (emp)    emp.name    = `servicios[${i}][id_empleado]`;
                if (precio) precio.name = `servicios[${i}][precio_snapshot]`;
                if (dur)    dur.name    = `servicios[${i}][duracion_snapshot]`;
            });

            const canRemove = rows.length > 1;
            rows.forEach(row => {
                const btn = row.querySelector('.btn-remove-servicio');
                if (btn) btn.disabled = !canRemove;
            });
        }

        // CLICK (quitar fila)
        serviciosWrapper.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-remove-servicio');
            if (!btn) return;

            const row = btn.closest('.servicio-row');
            if (!row) return;

            const rows = serviciosWrapper.querySelectorAll('.servicio-row');
            if (rows.length <= 1) return;

            row.remove();
            reindexRows();
            recalcAll();
        });

        // Agregar fila (clonando la primera)
        function addRow() {
            const base = serviciosWrapper.querySelector('.servicio-row');
            if (!base) return;

            const clone = base.cloneNode(true);

            // limpiar inputs
            clone.querySelectorAll('input').forEach(inp => inp.value = '');

            // reset selects
            const catSel = clone.querySelector('select[data-role="categoria"]');
            const svcSel = clone.querySelector('select[data-role="servicio"]');
            const empSel = clone.querySelector('select[data-role="empleado"]');

            if (catSel) catSel.selectedIndex = 0;

            if (svcSel) {
                svcSel.innerHTML = `<option value="">Selecciona primero una categoría</option>`;
                svcSel.removeAttribute('data-selected');
            }

            if (empSel) {
                empSel.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
                empSel.disabled = true;
                empSel.value = '';
                empSel.removeAttribute('data-preselect');
            }

            // quitar ids duplicados
            clone.querySelector('#servicio_main')?.removeAttribute('id');
            clone.querySelector('#categoria_main')?.removeAttribute('id');

            serviciosWrapper.appendChild(clone);

            reindexRows();
            recalcAll();
        }

        if (btnAddServicio) btnAddServicio.addEventListener('click', addRow);

        // INIT: procesa TODAS las filas (edit + create)
        (async () => {
            const rows = serviciosWrapper.querySelectorAll('.servicio-row');
            if (!rows.length) return;

            for (const row of rows) {
                const catSel = row.querySelector('select[data-role="categoria"]');
                const svcSel = row.querySelector('select[data-role="servicio"]');
                if (!catSel || !svcSel) continue;

                const selectedId = svcSel.dataset.selected || svcSel.value || "";

                // ✅ Si no viene categoría preseleccionada pero sí servicio (EDIT),
                // inferimos la categoría a partir del servicio.
                if (!catSel.value && selectedId) {
                    const srv = serviciosAll.find(s => String(s.id) === String(selectedId));
                    if (srv?.categoria) catSel.value = srv.categoria;
                }

                buildOptionsForServiceSelect(svcSel, catSel.value, selectedId);
                svcSel.dataset.selected = selectedId;

                // ✅ Si los snapshots vienen vacíos, rellenarlos desde el option
                if (selectedId && svcSel.selectedIndex > -1) {
                    const opt = svcSel.options[svcSel.selectedIndex];
                    const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
                    const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');

                    if (precioInp && (!precioInp.value || Number(precioInp.value) === 0)) {
                        if (opt?.dataset?.precio != null) precioInp.value = opt.dataset.precio;
                    }
                    if (durInp && (!durInp.value || Number(durInp.value) === 0)) {
                        if (opt?.dataset?.duracion != null) durInp.value = opt.dataset.duracion;
                    }
                }

                // ✅ precargar empleados en edit (si existe data-preselect)
                const empSel = row.querySelector('select[data-role="empleado"]');
                const preEmp = empSel?.dataset.preselect || null;

                const currentServiceId = selectedId || svcSel.value || null;
                if (currentServiceId) {
                    await loadEmpleadosForRow(row, currentServiceId, preEmp);
                }
            }

            reindexRows();

            const fechaInput = document.getElementById('fecha_cita');
            const horaSelect = document.getElementById('hora_cita');

            function setHoraOptions(items, placeholder = 'Seleccionar Hora') {
                if (!horaSelect) return;

                horaSelect.innerHTML = '';
                const opt0 = document.createElement('option');
                opt0.value = '';
                opt0.textContent = placeholder;
                horaSelect.appendChild(opt0);

                (items || []).forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.value;       // "HH:MM"
                    opt.textContent = t.label; // "5:30 PM"
                    horaSelect.appendChild(opt);
                });
            }

            function ensureHoraOption(value, label) {
                if (!horaSelect || !value) return;

                const exists = Array.from(horaSelect.options).some(o => o.value === value);
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = value;
                    opt.textContent = label || value;
                    horaSelect.appendChild(opt);
                }
            }

            // ✅ Backend espera: date, servicios[], empleados[]
            async function refreshHorasDisponibles() {
                if (!horaSelect) return;

                const date = (fechaInput?.value || '').trim();

                // ✅ toma TODOS los servicios seleccionados (NO solo la primer fila)
                const svcIds = Array.from(serviciosWrapper.querySelectorAll('select[data-role="servicio"]'))
                    .map(s => (s.value || '').trim())
                    .filter(Boolean);

                // ✅ empleados seleccionados (opcional)
                const empIds = Array.from(serviciosWrapper.querySelectorAll('select[data-role="empleado"]'))
                    .map(e => (e.value || '').trim())
                    .filter(Boolean);

                if (!date || svcIds.length === 0) {
                    setHoraOptions([], 'Selecciona fecha y servicio');
                    horaSelect.disabled = true;
                    return;
                }

                // ✅ conserva hora previa / guardada (normalizada HH:MM)
                const prevRaw = (horaSelect.value || HORA_GUARDADA || '').trim();
                const prev = prevRaw ? prevRaw.slice(0, 5) : '';

                horaSelect.disabled = true;
                setHoraOptions([], 'Cargando horarios...');

                try {
                    const qs = new URLSearchParams();
                    qs.set('date', date);

                    svcIds.forEach(id => qs.append('servicios[]', id));
                    empIds.forEach(id => qs.append('empleados[]', id));

                    const url = `{{ route('admin.citas.horasDisponibles') }}?` + qs.toString();
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                    const data = await res.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        setHoraOptions([], 'Sin horas disponibles');
                        horaSelect.disabled = false;

                        // ✅ deja visible la hora actual aunque no haya disponibilidad
                        if (prev) {
                            ensureHoraOption(prev, `${prev} (hora actual)`);
                            horaSelect.value = prev;
                        } else {
                            horaSelect.value = '';
                        }
                        return;
                    }

                    setHoraOptions(data, 'Seleccionar Hora');
                    horaSelect.disabled = false;

                    // ✅ re-selecciona hora guardada si existe
                    if (prev && data.some(x => x.value === prev)) {
                        horaSelect.value = prev;
                    } else if (prev) {
                        // ✅ si no está en disponibles, igual la mostramos
                        ensureHoraOption(prev, `${prev} (hora actual)`);
                        horaSelect.value = prev;
                    } else {
                        horaSelect.value = '';
                    }

                } catch (e) {
                    console.error(e);
                    setHoraOptions([], 'Error cargando horarios');
                    horaSelect.disabled = false;

                    // ✅ no pierdas la hora
                    const prevRaw = (horaSelect.value || HORA_GUARDADA || '').trim();
                    const prev = prevRaw ? prevRaw.slice(0, 5) : '';
                    if (prev) {
                        ensureHoraOption(prev, `${prev} (hora actual)`);
                        horaSelect.value = prev;
                    }
                }
            }

            // ===========================
            // HOOKS para refrescar horas
            // ===========================
            if (fechaInput) {
                fechaInput.addEventListener('change', () => setTimeout(refreshHorasDisponibles, 0));
            }

            serviciosWrapper.addEventListener('change', async (e) => {

                // cambio de categoría
                const catSel = e.target.closest('select[data-role="categoria"]');
                if (catSel) {
                    const row = catSel.closest('.servicio-row');
                    const svcSel = row?.querySelector('select[data-role="servicio"]');
                    if (!row || !svcSel) return;

                    buildOptionsForServiceSelect(svcSel, catSel.value, "");

                    // limpiar snapshots al cambiar categoría
                    const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
                    const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');
                    if (precioInp) precioInp.value = '';
                    if (durInp) durInp.value = '';

                    // reset empleado
                    const empSel = row.querySelector('select[data-role="empleado"]');
                    if (empSel) {
                        empSel.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
                        empSel.disabled = true;
                        empSel.value = '';
                        empSel.removeAttribute('data-preselect');
                    }

                    recalcAll();
                    setTimeout(refreshHorasDisponibles, 0);
                    return;
                }

                // cambio de servicio
                const svcSel = e.target.closest('select[data-role="servicio"]');
                if (svcSel) {
                    const row = svcSel.closest('.servicio-row');
                    const opt = svcSel.options[svcSel.selectedIndex];
                    if (!row || !opt) return;

                    const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
                    const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');

                    const precio   = opt.dataset.precio ?? '';
                    const duracion = opt.dataset.duracion ?? '';

                    if (precioInp && (precioInp.value === '' || Number(precioInp.value) === 0)) precioInp.value = precio;
                    if (durInp && (durInp.value === '' || Number(durInp.value) === 0)) durInp.value = duracion;

                    // ✅ cargar empleados para este servicio
                    await loadEmpleadosForRow(row, svcSel.value || null);

                    recalcAll();

                    // ✅ refrescar horas al cambiar servicio
                    await refreshHorasDisponibles();
                    return;
                }

                // cambio de empleado
                const empSel = e.target.closest('select[data-role="empleado"]');
                if (empSel) {
                    setTimeout(refreshHorasDisponibles, 0);
                    return;
                }
            });

            serviciosWrapper.addEventListener('input', (e) => {
                if (
                    e.target.matches('input[data-role="duracion_snapshot"]') ||
                    e.target.matches('input[data-role="precio_snapshot"]')
                ) {
                    recalcAll();
                }
            });

            if (btnAddServicio) {
                btnAddServicio.addEventListener('click', () => setTimeout(refreshHorasDisponibles, 0));
            }

            serviciosWrapper.addEventListener('click', (e) => {
                if (e.target.closest('.btn-remove-servicio')) {
                    setTimeout(refreshHorasDisponibles, 0);
                }
            });

            // primer load
            await refreshHorasDisponibles();
            recalcAll();
        })();
    }

    // ===========================
    // Método de pago (solo completada)
    // ===========================
    const estadoSelect = document.getElementById('estado_cita');
    const metodoWrap   = document.getElementById('metodo_pago_wrap');
    const metodoSelect = document.getElementById('metodo_pago');

    function toggleMetodoPago() {
        const show = (estadoSelect?.value === 'completada');

        if (metodoWrap) metodoWrap.style.display = show ? '' : 'none';

        if (metodoSelect) {
            metodoSelect.required = show;
            if (!show) metodoSelect.value = '';
        }
    }

    if (estadoSelect) {
        estadoSelect.addEventListener('change', toggleMetodoPago);
    }

    toggleMetodoPago();

    // ===========================
    // Buscador de clientes
    // ===========================
    const CLIENTES = @json($clientesForJs);
    const input    = document.getElementById('cliente_search');
    const dropdown = document.getElementById('cliente_dropdown');
    const results  = document.getElementById('cliente_results');
    const hidden   = document.getElementById('cliente_id');

    function hideResults() {
        dropdown.classList.add('hidden');
        results.innerHTML = '';
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function showResults(items) {
        if (!items.length) {
            results.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500">Sin resultados</div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        results.innerHTML = items.map(c => `
            <button type="button"
                class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm"
                data-id="${c.id}"
                data-label="${escapeHtml(c.label || '')}"
            >
                <div class="font-medium text-gray-800">${escapeHtml(c.nombre || 'Sin nombre')}</div>
                ${c.email ? `<div class="text-gray-500">${escapeHtml(c.email)}</div>` : ''}
            </button>
        `).join('');

        dropdown.classList.remove('hidden');
    }

    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();

        if (!q) {
            hidden.value = '';
            hideResults();
            return;
        }

        const filtered = CLIENTES.filter(c =>
            (c.nombre || '').toLowerCase().includes(q) ||
            (c.email  || '').toLowerCase().includes(q)
        ).slice(0, 8);

        showResults(filtered);
    });

    results.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-id]');
        if (!btn) return;

        hidden.value = btn.dataset.id;
        input.value  = btn.dataset.label || '';
        hideResults();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#cliente_search') && !e.target.closest('#cliente_dropdown')) {
            hideResults();
        }
    });
});
</script>

@endpush
