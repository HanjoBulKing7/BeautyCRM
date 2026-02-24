@push('styles')
<style>
  /* Scope para NO afectar otras vistas */
  .bb-admin-datetime .bb-calendar{ margin-top: 10px; }

  .bb-admin-datetime .bb-cal__header{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    padding:12px 14px;
    border:1px solid rgba(0,0,0,0.08);
    border-radius:16px;
    background: linear-gradient(180deg, rgba(244,235,221,0.85), rgba(255,255,255,0.95));
    box-shadow: 0 12px 26px rgba(0,0,0,0.05);
  }
  .bb-admin-datetime .bb-cal__title{
    font-weight: 800;
    text-transform: capitalize;
    letter-spacing: .02em;
    color: rgba(26,26,26,0.86);
  }
  .bb-admin-datetime .bb-cal__nav{
    border: 1px solid rgba(201,162,74,0.35);
    background:#fff;
    color: rgba(201,162,74,0.95);
    width:38px; height:38px;
    border-radius:999px;
    cursor:pointer;
    font-size:20px;
    line-height:1;
    display:grid;
    place-items:center;
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
  }
  .bb-admin-datetime .bb-cal__nav:active{ transform: translateY(1px); }

  .bb-admin-datetime .bb-cal__dow{
    display:grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-top: 12px;
    padding: 0 4px;
    font-weight: 800;
    font-size: 12px;
    color: rgba(26,26,26,0.55);
    text-align:center;
  }

  .bb-admin-datetime .bb-cal__grid{
    display:grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-top: 10px;
  }

  .bb-admin-datetime .bb-cal__cell{
    position:relative;
    width: 100%;
    height: 64px;            /* compacto */
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.08);
    background: #fff;
    box-shadow: 0 10px 20px rgba(0,0,0,0.04);
    cursor:pointer;
    display:flex;
    align-items:flex-start;
    justify-content:flex-start;
    padding: 10px;
  }

  .bb-admin-datetime .bb-cal__cell.is-empty{
    border: 0;
    background: transparent;
    box-shadow: none;
    cursor: default;
  }

  .bb-admin-datetime .bb-cal__cell.is-disabled{
    opacity: .45;
    cursor: not-allowed;
    filter: grayscale(0.2);
  }

  .bb-admin-datetime .bb-cal__cell.is-selected{
    border-color: rgba(201,162,74,0.60);
    box-shadow: 0 12px 26px rgba(201,162,74,0.18);
    background: linear-gradient(180deg, rgba(244,235,221,0.95), rgba(255,255,255,0.95));
  }

  .bb-admin-datetime .bb-cal__day{
    font-weight: 900;
    color: rgba(26,26,26,0.86);
    font-size: 13px;
  }

  .bb-admin-datetime .bb-cal__dot{
    position:absolute;
    right: 10px;
    top: 10px;
    width: 10px;
    height: 10px;
    border-radius: 999px;
  }
  .bb-admin-datetime .bb-cal__dot.is-gold{
    background: rgba(201,162,74,0.95);
    box-shadow: 0 0 0 4px rgba(201,162,74,0.16);
  }
  .bb-admin-datetime .bb-cal__dot.is-muted{
    background: rgba(0,0,0,0.18);
    box-shadow: 0 0 0 4px rgba(0,0,0,0.06);
  }

  .bb-admin-datetime .bb-timesPanel{
    border-radius: 18px;
    border: 1px solid rgba(0,0,0,0.08);
    background: linear-gradient(180deg, rgba(244,235,221,0.60), rgba(255,255,255,0.95));
    padding: 14px 14px 16px;
    box-shadow: 0 14px 30px rgba(0,0,0,0.05);
  }

  .bb-admin-datetime .bb-timesGrid{
    margin-top: 12px;
    display:grid;
    grid-template-columns: repeat(6, minmax(0,1fr));
    gap: 10px;
  }
  @media (max-width: 1024px){
    .bb-admin-datetime .bb-timesGrid{ grid-template-columns: repeat(4, minmax(0,1fr)); }
  }
  @media (max-width: 640px){
    .bb-admin-datetime .bb-timesGrid{ grid-template-columns: repeat(3, minmax(0,1fr)); }
  }

  .bb-admin-datetime .bb-timeBtn{
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.08);
    background:#fff;
    padding: 12px 10px;
    font-weight: 900;
    letter-spacing: .02em;
    cursor:pointer;
    transition: transform 140ms ease, filter 140ms ease;
    box-shadow: 0 12px 22px rgba(0,0,0,0.04);
  }
  .bb-admin-datetime .bb-timeBtn:hover{ transform: translateY(-1px); filter: brightness(1.02); }

  .bb-admin-datetime .bb-timeBtn.is-selected{
    border-color: rgba(201,162,74,0.60);
    background: rgba(201,162,74,0.12);
    box-shadow: 0 16px 30px rgba(201,162,74,0.12);
  }
</style>
@endpush

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- CLIENTE (buscador) --}}
        <div class="relative">
            <label for="cliente_search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user mr-1" style="{{ $bbIconColor }}"></i>
                Cliente <span class="text-red-500">*</span>
            </label>

            <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $selectedClienteId }}" required>

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

        {{-- SERVICIOS (partial con UI chips/cards + rows ocultos) --}}
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

        {{-- FECHA + HORA (Calendario izq / Horas der) --}}
        @php
          $fechaInit = old('fecha_cita', $cita->fecha_cita ?? ($fechaPrefill ?? ''));
          $horaInit  = old('hora_cita',  $cita->hora_cita ?? '');
        @endphp

        <div class="md:col-span-2">
          <div class="bb-admin-datetime rounded-2xl border border-gray-200 bg-white p-5 shadow-[0_18px_40px_rgba(0,0,0,0.06)]">
            <div class="flex items-start justify-between gap-3 mb-4">
              <div>
                <h3 class="text-sm font-bold tracking-wider uppercase text-gray-800">
                  <i class="fas fa-calendar-alt mr-2" style="{{ $bbIconColor }}"></i>
                  Fecha y hora
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                  Selecciona la fecha en el calendario y luego el horario disponible.
                </p>
              </div>

              <div id="bbDatetimeLock"
                   class="text-xs px-3 py-2 rounded-xl border border-amber-200 bg-amber-50 text-amber-800"
                   style="display:none;">
                Primero selecciona servicio(s) y empleado(s) para ver disponibilidad.
              </div>
            </div>

            <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ $fechaInit }}">
            <input type="hidden" name="hora_cita"  id="bbHourInput" value="{{ $horaInit }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div id="bbAdminCalendar" class="bb-calendar"></div>

                @error('fecha_cita')
                  <p class="text-red-500 text-sm mt-2">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                  </p>
                @enderror
              </div>

              <div>
                <div class="bb-timesPanel">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="bb-label" id="bbTimesTitle">Horas disponibles</div>
                      <p class="bb-hint" id="bbTimesHint" style="margin-top:.25rem;">
                        Selecciona una fecha para ver horarios.
                      </p>
                    </div>
                  </div>

                  <div id="bbTimesGrid" class="bb-timesGrid"></div>

                  <p id="bbTimesEmpty" class="bb-hint" style="display:none; margin-top:.75rem;">
                    No hay horas disponibles para ese día.
                  </p>

                  @error('hora_cita')
                    <p class="text-red-500 text-sm mt-2">
                      <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                  @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

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
  const MODE = @json($mode);
  const CITA_ID = @json($cita->id ?? null);

  const serviciosWrapper = document.getElementById('servicios-wrapper');
  const btnAddServicio   = document.getElementById('btn-add-servicio');

  const elCalendar   = document.getElementById('bbAdminCalendar');
  const elTimesGrid  = document.getElementById('bbTimesGrid');
  const elTimesTitle = document.getElementById('bbTimesTitle');
  const elTimesHint  = document.getElementById('bbTimesHint');
  const elTimesEmpty = document.getElementById('bbTimesEmpty');
  const elLock       = document.getElementById('bbDatetimeLock');

  const elDateInput  = document.getElementById('bbDateInput');
  const elHourInput  = document.getElementById('bbHourInput');

  const URL_EMPLEADOS = @json(route('admin.citas.empleadosPorServicio'));
  const URL_HORAS     = @json(route('admin.citas.horasDisponibles'));

  const dtState = {
    calendarMonth: new Date(),
    selectedDate: (elDateInput?.value || '').trim() || null,
    selectedHour: (elHourInput?.value || '').trim() || null,
    locked: true,
  };

  function pad2(n){ return String(n).padStart(2,'0'); }
  function toYMD(d){ return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`; }
  function ymdToDate(ymd){
    const [y,m,d] = String(ymd).split('-').map(x => parseInt(x,10));
    if (!y || !m || !d) return null;
    return new Date(y, m-1, d);
  }
  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // ✅ IMPORTANTE: ignora filas vacías (sin servicio)
  function getItemsFromRows() {
    if (!serviciosWrapper) return [];

    const rows = [...serviciosWrapper.querySelectorAll('.servicio-row')];

    const raw = rows.map((row) => {
      const svcSel = row.querySelector('select[data-role="servicio"]');
      const empSel = row.querySelector('select[data-role="empleado"]');

      return {
        id_servicio: (svcSel?.value || '').trim(),
        id_empleado: (empSel?.value || '').trim(),
      };
    }).filter(it => it.id_servicio);

    return raw.map((it, idx) => ({ ...it, orden: idx + 1 }));
  }

  function isReadyForAvailability() {
    const items = getItemsFromRows();
    if (!items.length) return false;
    return items.every(it => String(it.id_servicio).length > 0 && String(it.id_empleado).length > 0);
  }

  function setLockUI() {
    const locked = !isReadyForAvailability();
    dtState.locked = locked;
    if (elLock) elLock.style.display = locked ? '' : 'none';

    if (locked) {
      dtState.selectedDate = null;
      dtState.selectedHour = null;
      if (elDateInput) elDateInput.value = '';
      if (elHourInput) elHourInput.value = '';
      if (elTimesGrid) elTimesGrid.innerHTML = '';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';
      if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
      if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';
    }
  }

  function renderCalendar() {
    if (!elCalendar) return;

    if (dtState.selectedDate) {
      const d = ymdToDate(dtState.selectedDate);
      if (d) dtState.calendarMonth = new Date(d.getFullYear(), d.getMonth(), 1);
    }

    const d = dtState.calendarMonth;
    const y = d.getFullYear();
    const m = d.getMonth();

    const first = new Date(y, m, 1);
    const startDow = (first.getDay() + 6) % 7; // lunes=0
    const daysInMonth = new Date(y, m + 1, 0).getDate();

    const monthName = first.toLocaleString('es-MX', { month: 'long', year: 'numeric' });

    const grid = [];
    for (let i = 0; i < startDow; i++) grid.push(null);
    for (let day = 1; day <= daysInMonth; day++) grid.push(new Date(y, m, day));
    while (grid.length % 7 !== 0) grid.push(null);

    const todayYMD = toYMD(new Date());
    const disablePast = (MODE === 'create');

    elCalendar.innerHTML = `
      <div class="bb-cal__header">
        <button type="button" class="bb-cal__nav" data-nav="-1">‹</button>
        <div class="bb-cal__title">${escapeHtml(monthName)}</div>
        <button type="button" class="bb-cal__nav" data-nav="1">›</button>
      </div>

      <div class="bb-cal__dow">
        <div>L</div><div>M</div><div>M</div><div>J</div><div>V</div><div>S</div><div>D</div>
      </div>

      <div class="bb-cal__grid">
        ${grid.map(cell => {
          if (!cell) return `<div class="bb-cal__cell is-empty"></div>`;

          const ymd = toYMD(cell);
          const isPast = disablePast ? (ymd < todayYMD) : false;
          const isSelected = dtState.selectedDate === ymd;

          const disabled = dtState.locked || isPast;
          const dotClass = dtState.locked ? 'is-muted' : 'is-gold';

          return `
            <button type="button"
              class="bb-cal__cell ${isSelected ? 'is-selected' : ''} ${disabled ? 'is-disabled' : ''}"
              data-date="${ymd}"
              ${disabled ? 'disabled' : ''}>
              <span class="bb-cal__day">${cell.getDate()}</span>
              <span class="bb-cal__dot ${dotClass}" aria-hidden="true"></span>
            </button>
          `;
        }).join('')}
      </div>
    `;

    elCalendar.querySelectorAll('.bb-cal__nav').forEach(btn => {
      btn.addEventListener('click', () => {
        const delta = Number(btn.getAttribute('data-nav'));
        dtState.calendarMonth = new Date(y, m + delta, 1);
        renderCalendar();
      });
    });

    elCalendar.querySelectorAll('.bb-cal__cell[data-date]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const ymd = btn.getAttribute('data-date');
        dtState.selectedDate = ymd;
        if (elDateInput) elDateInput.value = ymd;

        dtState.selectedHour = null;
        if (elHourInput) elHourInput.value = '';

        renderCalendar();
        await refreshHorasDisponibles();
      });
    });
  }

  function buildAvailabilityParams(qs, items) {
    if (dtState.selectedDate) {
      qs.set('date', dtState.selectedDate);
      qs.set('fecha', dtState.selectedDate);
    }

    if (CITA_ID) qs.set('cita_id', String(CITA_ID));

    [...qs.keys()].forEach(k => {
      if (k === 'servicios[]' || k === 'empleados[]' || k.startsWith('items[')) qs.delete(k);
    });

    items.forEach((it, i) => {
      qs.append('servicios[]', String(it.id_servicio));
      qs.append('empleados[]', String(it.id_empleado));

      qs.set(`items[${i}][id_servicio]`, String(it.id_servicio));
      qs.set(`items[${i}][id_empleado]`, String(it.id_empleado));
      qs.set(`items[${i}][orden]`, String(it.orden));
    });
  }

  function renderTimesButtons(list) {
    if (!elTimesGrid) return;

    elTimesGrid.innerHTML = '';

    if (!list.length) {
      if (elTimesEmpty) elTimesEmpty.style.display = '';
      return;
    }

    if (elTimesEmpty) elTimesEmpty.style.display = 'none';

    elTimesGrid.innerHTML = list.map(it => {
      const value = String(it.value ?? it);
      const label = String(it.label ?? it);
      const selected = (dtState.selectedHour && String(dtState.selectedHour) === value);

      return `
        <button type="button" class="bb-timeBtn ${selected ? 'is-selected' : ''}"
                data-hour="${escapeHtml(value)}">
          ${escapeHtml(label)}
        </button>
      `;
    }).join('');

    elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(btn => {
      btn.addEventListener('click', () => {
        const h = btn.getAttribute('data-hour');
        dtState.selectedHour = h;
        if (elHourInput) elHourInput.value = h;

        elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(x => x.classList.remove('is-selected'));
        btn.classList.add('is-selected');
      });
    });
  }

  async function refreshHorasDisponibles() {
    if (!elTimesGrid) return;

    if (dtState.locked || !dtState.selectedDate) {
      if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
      if (elTimesHint) elTimesHint.textContent = dtState.locked
        ? 'Selecciona servicio(s) y empleado(s) para ver disponibilidad.'
        : 'Selecciona una fecha para ver horarios.';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';
      elTimesGrid.innerHTML = '';
      return;
    }

    if (elTimesHint) elTimesHint.textContent = 'Cargando horarios...';
    if (elTimesEmpty) elTimesEmpty.style.display = 'none';
    elTimesGrid.innerHTML = '';

    try {
      const items = getItemsFromRows();
      const qs = new URLSearchParams();
      buildAvailabilityParams(qs, items);

      const url = URL_HORAS + '?' + qs.toString();
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      let list = [];
      if (Array.isArray(data)) list = data;
      else if (Array.isArray(data?.horas)) list = data.horas.map(h => ({ value: h, label: h }));
      else if (Array.isArray(data?.items)) list = data.items;
      else list = [];

      if (elTimesTitle) elTimesTitle.textContent = `Horas disponibles (${dtState.selectedDate})`;
      if (!list.length) {
        if (elTimesHint) elTimesHint.textContent = 'No hay horas disponibles, prueba otro día o cambia empleado.';
        renderTimesButtons([]);
        return;
      }

      if (elTimesHint) elTimesHint.textContent = 'Selecciona una hora para confirmar.';

      const current = dtState.selectedHour;
      renderTimesButtons(list);

      if (current) {
        const exists = list.some(x => String(x.value ?? x) === String(current));
        if (!exists) {
          dtState.selectedHour = current;
          if (elHourInput) elHourInput.value = current;
        }
      }

    } catch (e) {
      console.error(e);
      if (elTimesHint) elTimesHint.textContent = 'Error cargando horarios.';
      renderTimesButtons([]);
    }
  }

  // ===========================
  // Multi-servicio (rows ocultos) + empleados + snapshots
  // ===========================
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

      const url = URL_EMPLEADOS + `?servicio_id=${encodeURIComponent(servicioId)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      empleadoSelect.innerHTML =
        `<option value="">Selecciona un empleado</option>` +
        (data || []).map(e => `<option value="${e.id}">${e.label}</option>`).join('');

      empleadoSelect.disabled = false;

      if (preselectId) {
        empleadoSelect.value = String(preselectId);
      } else if (Array.isArray(data) && data.length === 1) {
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

    function recalcAll() { recalcTotalDuracion(); recalcTotalMonto(); }

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

      setLockUI();
      renderCalendar();
      refreshHorasDisponibles();
    });

    function addRow() {
      const base = serviciosWrapper.querySelector('.servicio-row');
      if (!base) return;

      const clone = base.cloneNode(true);
      // ✅ importante: evitar que el uid se copie al clonar
      clone.removeAttribute('data-bb-uid');
      if (clone.dataset) delete clone.dataset.bbUid;


      clone.querySelectorAll('input').forEach(inp => inp.value = '');

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

      clone.querySelector('#servicio_main')?.removeAttribute('id');
      clone.querySelector('#categoria_main')?.removeAttribute('id');

      serviciosWrapper.appendChild(clone);

      reindexRows();
      recalcAll();

      setLockUI();
      renderCalendar();
      refreshHorasDisponibles();
    }

    if (btnAddServicio) btnAddServicio.addEventListener('click', addRow);

    (async () => {
      const rows = serviciosWrapper.querySelectorAll('.servicio-row');
      if (!rows.length) return;

      for (const row of rows) {
        const catSel = row.querySelector('select[data-role="categoria"]');
        const svcSel = row.querySelector('select[data-role="servicio"]');
        if (!catSel || !svcSel) continue;

        const selectedId = svcSel.dataset.selected || svcSel.value || "";
        buildOptionsForServiceSelect(svcSel, catSel.value, selectedId);
        svcSel.dataset.selected = selectedId;

        const empSel = row.querySelector('select[data-role="empleado"]');
        const preEmp = empSel?.dataset.preselect || null;

        const currentServiceId = selectedId || svcSel.value || null;
        if (currentServiceId) {
          await loadEmpleadosForRow(row, currentServiceId, preEmp);
        }
      }

      reindexRows();
      recalcAll();

      setLockUI();

      if (dtState.selectedDate) {
        const d = ymdToDate(dtState.selectedDate);
        if (d) dtState.calendarMonth = new Date(d.getFullYear(), d.getMonth(), 1);
      }

      renderCalendar();
      await refreshHorasDisponibles();
    })();

    serviciosWrapper.addEventListener('change', async (e) => {
      const catSel = e.target.closest('select[data-role="categoria"]');
      if (catSel) {
        const row = catSel.closest('.servicio-row');
        const svcSel = row?.querySelector('select[data-role="servicio"]');
        if (!row || !svcSel) return;

        buildOptionsForServiceSelect(svcSel, catSel.value, "");

        const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
        const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');
        if (precioInp) precioInp.value = '';
        if (durInp) durInp.value = '';

        const empSel = row.querySelector('select[data-role="empleado"]');
        if (empSel) {
          empSel.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
          empSel.disabled = true;
          empSel.value = '';
          empSel.removeAttribute('data-preselect');
        }

        recalcAll();

        setLockUI();
        renderCalendar();
        refreshHorasDisponibles();
        return;
      }

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

        await loadEmpleadosForRow(row, svcSel.value || null);

        recalcAll();

        setLockUI();
        renderCalendar();
        await refreshHorasDisponibles();
        return;
      }

      const empSel = e.target.closest('select[data-role="empleado"]');
      if (empSel) {
        setLockUI();
        renderCalendar();
        await refreshHorasDisponibles();
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
  }

  // Método de pago (solo completada)
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
  if (estadoSelect) estadoSelect.addEventListener('change', toggleMetodoPago);
  toggleMetodoPago();

  // Buscador de clientes
  const CLIENTES = @json($clientesForJs);
  const input    = document.getElementById('cliente_search');
  const dropdown = document.getElementById('cliente_dropdown');
  const results  = document.getElementById('cliente_results');
  const hidden   = document.getElementById('cliente_id');

  function hideResults() {
    dropdown.classList.add('hidden');
    results.innerHTML = '';
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
        data-label="${escapeHtml(c.label || '')}">
        <div class="font-medium text-gray-800">${escapeHtml(c.nombre || 'Sin nombre')}</div>
        ${c.email ? `<div class="text-gray-500">${escapeHtml(c.email)}</div>` : ''}
      </button>
    `).join('');

    dropdown.classList.remove('hidden');
  }

  if (input) {
    input.addEventListener('input', () => {
      const q = input.value.trim().toLowerCase();

      if (!q) {
        if (hidden) hidden.value = '';
        hideResults();
        return;
      }

      const filtered = CLIENTES.filter(c =>
        (c.nombre || '').toLowerCase().includes(q) ||
        (c.email  || '').toLowerCase().includes(q)
      ).slice(0, 8);

      showResults(filtered);
    });
  }

  if (results) {
    results.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-id]');
      if (!btn) return;

      if (hidden) hidden.value = btn.dataset.id;
      if (input)  input.value  = btn.dataset.label || '';
      hideResults();
    });
  }

  document.addEventListener('click', (e) => {
    if (!e.target.closest('#cliente_search') && !e.target.closest('#cliente_dropdown')) {
      hideResults();
    }
  });
});
</script>
@endpush
