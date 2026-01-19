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
                value="{{ old('fecha_cita', $cita->fecha_cita ?? ($fechaPrefill ?? '')) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required
            />

            @error('fecha_cita')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

                {{-- SERVICIOS (dinámicos) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-scissors text-gray-400 mr-1"></i>Servicios <span class="text-red-500">*</span>
                    </label>

                    <div id="servicios-wrapper" class="space-y-3">

@if($mode === 'edit' && $cita && $cita->servicios->count())

    @foreach($cita->servicios as $i => $svc)
        <div class="servicio-row bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center"
             data-index="{{ $i }}">

            {{-- CATEGORÍA --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                <select
                    class="categoria-select w-full border border-gray-300 rounded-lg px-4 py-3"
                    data-role="categoria">
                    <option value="">Seleccionar categoría</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat }}"
                            @selected($cat === $svc->categoria)>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SERVICIO --}}
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Servicio *</label>
                <select
                    name="servicios[{{ $i }}][id_servicio]"
                    data-role="servicio"
                    data-selected="{{ $svc->id_servicio }}"
                    class="servicio-select w-full border border-gray-300 rounded-lg px-4 py-3"
                    required>
                    <option value="">Cargando servicios…</option>
                </select>
            </div>

            {{-- PRECIO --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                <input type="number"
                       name="servicios[{{ $i }}][precio_snapshot]"
                       value="{{ $svc->pivot->precio_snapshot }}"
                       data-role="precio_snapshot"
                       class="precio-input w-full border border-gray-300 rounded-lg px-4 py-3">
            </div>

            {{-- DURACIÓN --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                <input type="number"
                       name="servicios[{{ $i }}][duracion_snapshot]"
                       value="{{ $svc->pivot->duracion_snapshot }}"
                       data-role="duracion_snapshot"
                       class="duracion-input w-full border border-gray-300 rounded-lg px-4 py-3">
            </div>

            {{-- QUITAR --}}
            <div class="md:col-span-1 flex items-center justify-center mt-7">
                <button type="button"
                        class="btn-remove-servicio w-12 h-12 rounded-lg bg-red-500 text-white hover:bg-red-600">
                    ✕
                </button>
            </div>

        </div>
    @endforeach

@else
    {{-- CREATE: aquí dejas EXACTAMENTE tu fila original --}}
@endif

                        {{-- Si vienes de old() por validación, podrías reconstruirlo en backend; por ahora 1 row inicial --}}
                    <div class="servicio-row bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center" data-index="0">

                        {{-- CATEGORÍA (filtro) --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                            <select id="categoria_main"
                                    class="categoria-select w-full border border-gray-300 rounded-lg px-4 py-3"
                                    data-role="categoria">
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SELECT DE SERVICIO --}}
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Servicio <span class="text-red-500">*</span>
                            </label>

                            <select id="servicio_main"
                                    name="servicios[0][id_servicio]"
                                    data-role="servicio"
                                    class="servicio-select w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    required>
                                <option value="">Selecciona primero una categoría</option>
                            </select>
                        </div>

                        {{-- PRECIO (editable) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                            <div class="relative">
                                <span class="absolute left-3 top-0 bottom-0 flex items-center text-gray-500">$</span>
                                <input type="number"
                                    step="1"
                                    min="0"
                                    name="servicios[0][precio_snapshot]"
                                    class="precio-input w-full border border-gray-300 rounded-lg px-4 py-3 pl-9 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    placeholder="0.00"
                                    data-role="precio_snapshot">
                            </div>
                        </div>

                        {{-- DURACIÓN (editable) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                            <input type="number"
                                step="1"
                                min="0"
                                name="servicios[0][duracion_snapshot]"
                                class="duracion-input w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                placeholder="min"
                                data-role="duracion_snapshot">
                        </div>
                        {{-- QUITAR --}}
                        <div class="md:col-span-1 flex items-center justify-center mt-7">
                            <button type="button"
                                    class="btn-remove-servicio remove-servicio w-12 h-12 inline-flex items-center justify-center leading-none rounded-lg bg-red-500 text-white hover:bg-red-600 transition"
                                    title="Quitar servicio">
                                <i class="fas fa-times text-lg leading-none"></i>
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        id="btn-add-servicio"
                        class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium"
                    >
                        <i class="fas fa-plus-circle mr-2"></i>Agregar otro servicio
                    </button>



                    {{-- Si quieres validar array: servicios.*.id_servicio (te paso reglas luego) --}}
                    @error('servicios')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                    {{-- TOTAL DURACIÓN --}}
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Duración total (min)
                        </label>
                        <input
                            id="duracion_total"
                            type="number"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50"
                            readonly
                            value="0"
                        >
                    </div>
                    {{--TOTAL PRECIO--}}
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
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-9 bg-gray-50"
                            readonly
                            value="0"
                            >
                        </div>
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
// ===========================
// Multi-servicio (UI) + snapshots (precio/duracion editables)
// ===========================
// ===========================
// Multi-servicio (UI) + snapshots (precio/duracion editables)
// ===========================
const serviciosWrapper = document.getElementById('servicios-wrapper');
const btnAddServicio   = document.getElementById('btn-add-servicio');

// Si por alguna razón no existe el wrapper, salimos para no romper el resto del script
if (serviciosWrapper) {

  // ✅ usa tu PHP mapping
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

        // datasets para autollenar
        opt.dataset.duracion = s.duracion ?? 0;
        opt.dataset.precio   = s.precio ?? 0;

        if (String(selectedId) === String(s.id)) opt.selected = true;
        selectEl.appendChild(opt);
      });
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
      const precio = row.querySelector('input[data-role="precio_snapshot"]');
      const dur    = row.querySelector('input[data-role="duracion_snapshot"]');

      if (svc)    svc.name    = `servicios[${i}][id_servicio]`;
      if (precio) precio.name = `servicios[${i}][precio_snapshot]`;
      if (dur)    dur.name    = `servicios[${i}][duracion_snapshot]`;
    });

    // habilitar quitar solo si hay más de 1 fila
    const canRemove = rows.length > 1;
    rows.forEach(row => {
      const btn = row.querySelector('.btn-remove-servicio');
      if (btn) btn.disabled = !canRemove;
    });
  }

  // ✅ Delegación: CHANGE (categoría o servicio)
  serviciosWrapper.addEventListener('change', (e) => {

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

      recalcAll();
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

      if (precioInp && (precioInp.value === '' || precioInp.value == 0)) precioInp.value = precio;
      if (durInp && (durInp.value === '' || durInp.value == 0)) durInp.value = duracion;

      recalcAll();
    }
  });

  // ✅ Delegación: INPUT (si editan manualmente precio o duración)
  serviciosWrapper.addEventListener('input', (e) => {
    if (e.target.matches('input[data-role="duracion_snapshot"]') ||
        e.target.matches('input[data-role="precio_snapshot"]')) {
      recalcAll();
    }
  });

  // ✅ Delegación: CLICK (quitar fila) — recalcula SIEMPRE
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

  // ✅ Agregar fila (clonando la primera)
  function addRow() {
    const base = serviciosWrapper.querySelector('.servicio-row');
    if (!base) return;

    const clone = base.cloneNode(true);

    // limpiar inputs
    clone.querySelectorAll('input').forEach(inp => inp.value = '');

    // reset selects
    const catSel = clone.querySelector('select[data-role="categoria"]');
    const svcSel = clone.querySelector('select[data-role="servicio"]');
    if (catSel) catSel.selectedIndex = 0;

    if (svcSel) {
      svcSel.innerHTML = `<option value="">Selecciona primero una categoría</option>`;
    }

    // quitar ids duplicados
    clone.querySelector('#servicio_main')?.removeAttribute('id');
    clone.querySelector('#categoria_main')?.removeAttribute('id');

    serviciosWrapper.appendChild(clone);

    reindexRows();
    recalcAll();
  }

  if (btnAddServicio) btnAddServicio.addEventListener('click', addRow);

  // ✅ INIT
// ✅ INIT: procesa TODAS las filas y usa data-selected
(() => {
  const rows = serviciosWrapper.querySelectorAll('.servicio-row');
  if (!rows.length) return;

  rows.forEach((row) => {
    const catSel = row.querySelector('select[data-role="categoria"]');
    const svcSel = row.querySelector('select[data-role="servicio"]');
    if (!catSel || !svcSel) return;

    // 👇 clave: en edit el select trae value vacío, pero data-selected sí trae el id
    const selectedId = svcSel.dataset.selected || svcSel.value || "";

    // Poblar opciones según categoría y seleccionar el id correcto
    buildOptionsForServiceSelect(svcSel, catSel.value, selectedId);

    // (Opcional) deja actualizado el dataset por si luego reindexas/clonas
    svcSel.dataset.selected = selectedId;
  });

  reindexRows();
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


        });
</script>
@endpush
