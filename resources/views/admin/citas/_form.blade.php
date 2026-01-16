@php
    // Modo: 'create' | 'edit'
    $mode = $mode ?? 'create';

    // Defaults
    $cita = $cita ?? null;

    // Prefill cliente seleccionado (para el input visible)
    $selectedClienteId = old('id_cliente', $cita->id_cliente ?? '');
    $selectedCliente   = $selectedClienteId
        ? ($clientes->firstWhere('id', (int)$selectedClienteId) ?? null)
        : null;

    $clienteLabel = trim(
        ($selectedCliente->nombre ?? '') .
        (($selectedCliente && !empty($selectedCliente->email)) ? ' - '.$selectedCliente->email : '')
    );
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
                <i class="fas fa-user text-gray-400 mr-1"></i>Cliente <span class="text-red-500">*</span>
            </label>

            {{-- Valor real --}}
            <input type="hidden" name="id_cliente" id="id_cliente" value="{{ $selectedClienteId }}" required>

            {{-- Input visible --}}
            <div class="relative">
                <input
                    type="text"
                    id="cliente_search"
                    autocomplete="off"
                    placeholder="Escribe para buscar… (ej. Juan)"
                    value="{{ old('cliente_label', $clienteLabel) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
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

            @error('id_cliente')
                <p class="text-red-500 text-sm mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- FECHA --}}
        <div>
            <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar text-gray-400 mr-1"></i>Fecha de la Cita <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="fecha_cita"
                name="fecha_cita"
                value="{{ old('fecha_cita', $cita->fecha_cita ?? '') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required
            />

            @error('fecha_cita')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- SERVICIO PRINCIPAL --}}
        <div>
            <label for="id_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-scissors text-gray-400 mr-1"></i>Servicio <span class="text-red-500">*</span>
            </label>

            <div id="servicios-wrapper" class="space-y-3">
                <div class="servicio-row" data-row="primary">
                    {{-- CATEGORÍA (filtro) --}}
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría
                        </label>
                        <select id="categoria_main" class="w-full border border-gray-300 rounded-lg px-4 py-3 rounded-lg">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        </div>
                    {{-- SELECT DE SERVICIO --}}
                    <select
                        id="servicio_main"
                        name="id_servicio"
                        data-role="servicio"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        required
                    >
                        <option value="">Seleccionar Servicio</option>
                        @foreach($servicios as $s)
                            <option
                                value="{{ $s->id_servicio }}"
                                data-duracion="{{ (int)($s->duracion_minutos ?? 0) }}"
                                @selected(old('id_servicio', $cita->id_servicio ?? '') == $s->id_servicio)
                            >
                                {{ $s->nombre_servicio }} - ${{ number_format($s->precio, 2) }} ({{ (int)($s->duracion_minutos ?? 0) }} min)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Extras (si vienes de edit y ya tienes servicios en pivote, puedes precargarlos si quieres) --}}
            </div>

            <button
                type="button"
                id="btn-add-servicio"
                class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium"
            >
                <i class="fas fa-plus-circle mr-2"></i>Agregar otro servicio
            </button>

            @error('id_servicio')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- HORA --}}
        <div>
            <label for="hora_cita" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-clock text-gray-400 mr-1"></i>Hora de la Cita <span class="text-red-500">*</span>
            </label>

            <select
                id="hora_cita"
                name="hora_cita"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required
            >
                @php
                    $horaSelected = old('hora_cita', $cita->hora_cita ?? '');
                @endphp

                <option value="">Seleccionar Hora</option>
                @for($h = 9; $h <= 20; $h++)
                    @foreach([0, 30] as $m)
                        @php
                            $time = sprintf('%02d:%02d', $h, $m);
                        @endphp
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

        {{-- EMPLEADO (agrupado por departamento) --}}
        <div>
            <label for="id_empleado" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user-tie text-gray-400 mr-1"></i>Empleado
            </label>

            @php
                // Esperamos que $empleados ya tenga "departamento"
                $empleadosPorDepto = $empleados->groupBy(fn($e) => $e->departamento ?? 'Sin departamento');
                $empSelected = old('id_empleado', $cita->id_empleado ?? '');
            @endphp

            <select
                id="id_empleado"
                name="id_empleado"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            >
                <option value="">No asignado</option>

                @foreach($empleadosPorDepto as $depto => $emps)
                    <optgroup label="{{ $depto }}">
                        @foreach($emps as $e)
                            @php
                                $nombreEmp = trim(($e->nombre ?? $e->name ?? '').' '.($e->apellido ?? ''));
                                $correoEmp = $e->email ?? '';
                            @endphp
                            <option value="{{ $e->id }}" @selected((string)$empSelected === (string)$e->id)>
                                {{ $nombreEmp }}{{ $correoEmp ? ' - '.$correoEmp : '' }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            @error('id_empleado')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- ESTADO --}}
        <div>
            <label for="estado_cita" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-clipboard-check text-gray-400 mr-1"></i>Estado <span class="text-red-500">*</span>
            </label>

            @php $estadoSelected = old('estado_cita', $cita->estado_cita ?? 'pendiente'); @endphp

            <select
                id="estado_cita"
                name="estado_cita"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required
            >
                <option value="pendiente"  @selected($estadoSelected==='pendiente')>Pendiente</option>
                <option value="confirmada" @selected($estadoSelected==='confirmada')>Confirmada</option>
                <option value="cancelada"  @selected($estadoSelected==='cancelada')>Cancelada</option>
                <option value="completada" @selected($estadoSelected==='completada')>Completada</option>
            </select>

            @error('estado_cita')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- MÉTODO DE PAGO (solo si está COMPLETADA) --}}
        <div id="metodo_pago_wrap" style="display:none;">
            <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-credit-card text-gray-400 mr-1"></i>Método de pago <span class="text-red-500">*</span>
            </label>

            @php $metodoPagoSelected = old('metodo_pago', $cita->metodo_pago ?? ''); @endphp

            <select
                id="metodo_pago"
                name="metodo_pago"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            >
                <option value="">Seleccionar método</option>
                <option value="efectivo" @selected($metodoPagoSelected==='efectivo')>Efectivo</option>
                <option value="transferencia" @selected($metodoPagoSelected==='transferencia')>Transferencia</option>
                <option value="tarjeta" @selected($metodoPagoSelected==='tarjeta')>Tarjeta</option>
            </select>

            @error('metodo_pago')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- OBSERVACIONES --}}
        <div class="md:col-span-2">
            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-sticky-note text-gray-400 mr-1"></i>Observaciones
            </label>

            <textarea
                id="observaciones"
                name="observaciones"
                rows="3"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            >{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>

            @error('observaciones')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- DESCUENTO --}}
        <div class="md:col-span-2">
            <label for="descuento" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-tag text-gray-400 mr-1"></i>Descuento
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="descuento"
                id="descuento"
                value="{{ old('descuento', $cita->descuento ?? 0) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                placeholder="Monto en pesos (ej. 50.00). Si no aplica, deja en 0."
            />

            @error('descuento')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- BOTÓN --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.citas.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Volver a Citas
        </a>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
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
            'id' => $c->id,
            'label' => trim((($c->nombre ?? $c->name ?? '') . ' - ' . ($c->email ?? ''))),
            'nombre' => ($c->nombre ?? $c->name ?? ''),
            'email' => ($c->email ?? ''),
        ];
    })->values();

    $serviciosForJs = $servicios->map(function ($s) {
        return [
            'id' => $s->id_servicio,
            'nombre' => $s->nombre_servicio,
            'categoria' => $s->categoria,
            'duracion' => $s->duracion_minutos,
            'precio' => $s->precio,
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
            // Multi-servicio (UI)
            // ===========================
            const serviciosWrapper = document.getElementById('servicios-wrapper');
            const btnAddServicio = document.getElementById('btn-add-servicio');
            const horaSelect = document.getElementById('hora_cita');

            function getTotalDuracionMin() {
                const selects = serviciosWrapper.querySelectorAll('select[data-role="servicio"]');
                let total = 0;

                selects.forEach(sel => {
                    const opt = sel.options[sel.selectedIndex];
                    const dur = parseInt(opt?.dataset?.duracion || '0', 10);
                    total += isNaN(dur) ? 0 : dur;
                });

                return total;
            }

            function updateDuracionUI() {
                // aquí puedes seguir usando tu UI de duración/hora fin si ya la tienes
                // (lo dejé minimal para no romperte el layout)
                getTotalDuracionMin();
            }

            serviciosWrapper.addEventListener('change', function(e) {
                if (e.target && e.target.matches('select[data-role="servicio"]')) {
                    updateDuracionUI();
                }
            });

            if (horaSelect) {
                horaSelect.addEventListener('change', updateDuracionUI);
            }

            serviciosWrapper.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-servicio');
                if (!btn) return;
                const row = btn.closest('.servicio-row');
                if (row) {
                    row.remove();
                    updateDuracionUI();
                }
            });


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

                        // 👇 texto bonito como lo traías (precio + duracion)
                        const precio = Number(s.precio ?? 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
                        opt.textContent = `${s.nombre} - $${precio} (${s.duracion} min)`;

                        // 👇 esto es CLAVE para tu cálculo de duración
                        opt.dataset.duracion = s.duracion ?? 0;

                        if (String(selectedId) === String(s.id)) opt.selected = true;
                        selectEl.appendChild(opt);
                    });
            }

            function createExtraRow() {
                const row = document.createElement('div');
                row.className = "w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors";
                row.setAttribute('data-row', 'extra');

                // Reutilizamos las opciones del select principal de categoría (ya tiene tus categorías)
                const categoriaOptionsHtml = categoriaMain ? categoriaMain.innerHTML : `<option value="">Seleccionar categoría</option>`;

                row.innerHTML = `
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Categoría</label>
                        <select class="categoria-extra w-full border border-gray-300 rounded-lg px-4 py-3"
                                data-role="categoria">
                            ${categoriaOptionsHtml}
                        </select>
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Servicio</label>
                        <select name="id_servicios[]"
                                class="servicio-extra w-full border border-gray-300 rounded-lg px-4 py-3"
                                data-role="servicio">
                            <option value="">Selecciona primero una categoría</option>
                        </select>
                    </div>

                    <div class="col-span-12 md:col-span-2 flex md:justify-end">
                        <button type="button"
                                class="remove-servicio inline-flex items-center justify-center gap-3 w-full md:w-auto px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition-colors">
                            <i class="fas fa-times"></i> Quitar
                        </button>
                    </div>
                `;

                const catSel = row.querySelector('select[data-role="categoria"]');
                const svcSel = row.querySelector('select[data-role="servicio"]');

                // reset
                if (catSel) catSel.selectedIndex = 0;
                if (svcSel) svcSel.selectedIndex = 0;

                // cuando cambia categoría, filtramos SOLO el select de servicio de esa fila
                catSel?.addEventListener('change', () => {
                    buildOptionsForServiceSelect(svcSel, catSel.value, "");
                    updateDuracionUI();
                });

                // cuando cambia servicio, recalcula duración
                svcSel?.addEventListener('change', updateDuracionUI);

                return row;
            }

            if (btnAddServicio) {
                btnAddServicio.addEventListener('click', function() {
                    const row = createExtraRow();
                    serviciosWrapper.appendChild(row);
                    updateDuracionUI();
                });
            }

            updateDuracionUI();
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
            const hidden   = document.getElementById('id_cliente');

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

            //=======================================================
            //Filtrar servicios
            const serviciosAll = @json($serviciosForJs);


            const categoriaMain = document.getElementById('categoria_main');
            const servicioMain  = document.getElementById('servicio_main');

            function fillServiciosByCategoria(categoria, selectedId = "") {
            servicioMain.innerHTML = "";

            if (!categoria) {
                const opt = document.createElement("option");
                opt.value = "";
                opt.textContent = "Selecciona primero una categoría";
                servicioMain.appendChild(opt);
                return;
            }

            const opt0 = document.createElement("option");
            opt0.value = "";
            opt0.textContent = "Seleccionar servicio";
            servicioMain.appendChild(opt0);

            serviciosAll
                .filter(s => (s.categoria || '').toLowerCase() === categoria.toLowerCase())
                .forEach(s => {
                const opt = document.createElement("option");
                opt.value = s.id;
                opt.textContent = s.nombre;
                if (String(selectedId) === String(s.id)) opt.selected = true;
                servicioMain.appendChild(opt);
                });
            }

            categoriaMain.addEventListener('change', () => {
                fillServiciosByCategoria(categoriaMain.value, "");
            });

            // EDIT: preseleccionar categoría según servicio actual
            const selectedMainId = "{{ old('id_servicio', $cita->id_servicio ?? '') }}";
            if (selectedMainId) {
            const found = serviciosAll.find(s => String(s.id) === String(selectedMainId));
            if (found?.categoria) categoriaMain.value = found.categoria;
            }
            fillServiciosByCategoria(categoriaMain.value, selectedMainId);


        });
    </script>
@endpush
