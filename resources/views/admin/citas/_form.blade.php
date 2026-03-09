@push('styles')
<style>
  /* ====== CALENDARIO MINIMALISTA ====== */
  .bb-admin-datetime .bb-calendar{ margin-top: 8px; }

  .bb-admin-datetime .bb-cal__header{
    display:flex; align-items:center; justify-content:space-between; gap:8px;
    padding:8px 12px;
    border:1px solid rgba(0,0,0,0.06);
    border-radius:12px;
    background: #fafafa;
  }
  .bb-admin-datetime .bb-cal__title{
    font-weight: 700; font-size: 14px;
    text-transform: capitalize; color: #333;
  }
  .bb-admin-datetime .bb-cal__nav{
    border: 1px solid rgba(201,162,74,0.3);
    background:#fff; color: rgba(201,162,74,0.95);
    width:30px; height:30px; border-radius:8px;
    cursor:pointer; font-size:16px;
    display:grid; place-items:center;
    transition: all 0.2s;
  }
  .bb-admin-datetime .bb-cal__nav:hover{ background: rgba(201,162,74,0.1); }

  .bb-admin-datetime .bb-cal__dow{
    display:grid; grid-template-columns: repeat(7, 1fr);
    gap: 4px; margin-top: 8px; font-weight: 700;
    font-size: 11px; color: #888; text-align:center;
  }

  .bb-admin-datetime .bb-cal__grid{
    display:grid; grid-template-columns: repeat(7, 1fr);
    gap: 6px; margin-top: 6px;
  }

  .bb-admin-datetime .bb-cal__cell{
    position:relative; width: 100%; height: 40px; /* Reducido de 64px a 40px */
    border-radius: 8px; border: 1px solid rgba(0,0,0,0.06);
    background: #fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    font-size: 13px; transition: all 0.2s;
  }

  .bb-admin-datetime .bb-cal__cell:hover:not(.is-disabled):not(.is-empty) {
    border-color: rgba(201,162,74,0.4);
  }

  .bb-admin-datetime .bb-cal__cell.is-empty{ border: 0; background: transparent; cursor: default; }
  .bb-admin-datetime .bb-cal__cell.is-disabled{ opacity: .4; cursor: not-allowed; background: #f9f9f9; }
  
  .bb-admin-datetime .bb-cal__cell.is-selected{
    border-color: rgba(201,162,74,0.8);
    background: rgba(201,162,74,0.1);
    font-weight: bold; color: #000;
  }

  /* Puntos indicadores más pequeños */
  .bb-admin-datetime .bb-cal__dot{
    position:absolute; right: 4px; top: 4px;
    width: 6px; height: 6px; border-radius: 50%;
  }
  .bb-admin-datetime .bb-cal__dot.is-gold{ background: rgba(201,162,74,0.95); }
  .bb-admin-datetime .bb-cal__dot.is-muted{ background: #ccc; }

  /* Panel de horas compacto */
  .bb-admin-datetime .bb-timesPanel{
    border-radius: 12px; border: 1px solid rgba(0,0,0,0.06);
    background: #fafafa; padding: 12px;
  }
  .bb-admin-datetime .bb-timesGrid{
    margin-top: 8px; display:grid; gap: 6px;
    grid-template-columns: repeat(4, minmax(0,1fr));
  }
  @media (max-width: 640px){ .bb-admin-datetime .bb-timesGrid{ grid-template-columns: repeat(3, minmax(0,1fr)); } }

  .bb-admin-datetime .bb-timeBtn{
    border-radius: 8px; border: 1px solid rgba(0,0,0,0.08);
    background:#fff; padding: 6px 4px; font-weight: 600; font-size: 12px;
    cursor:pointer; transition: all 0.2s; text-align:center;
  }
  .bb-admin-datetime .bb-timeBtn:hover{ border-color: rgba(201,162,74,0.4); }
  .bb-admin-datetime .bb-timeBtn.is-selected{
    border-color: rgba(201,162,74,0.8); background: rgba(201,162,74,0.1); color: #000;
  }
</style>
@endpush

@php
    $mode = $mode ?? 'create';
    $cita = $cita ?? null;

    $selectedClienteId = old('cliente_id', $cita->cliente_id ?? '');
    $selectedCliente   = $selectedClienteId ? ($clientes->firstWhere('id', (int) $selectedClienteId) ?? null) : null;
    $clienteLabel = trim(($selectedCliente->nombre ?? '') . (($selectedCliente && !empty($selectedCliente->email)) ? ' - ' . $selectedCliente->email : ''));

    // UI Minimalista: Inputs más pequeños (py-2 en lugar de py-3, text-sm)
    $bbField = "w-full border border-gray-200 rounded-md px-3 py-2 text-sm text-gray-800 bg-white transition focus:outline-none focus:ring-1 focus:ring-[rgba(201,162,74,.5)] focus:border-[rgba(201,162,74,.5)]";
    $bbIconColor = "color: rgba(201,162,74,.92)";
    $horaGuardada = substr((string) old('hora_cita', $cita->hora_cita ?? ''), 0, 5); 
@endphp

<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    {{-- PASO 1: SERVICIOS Y STAFF (Ahora es el protagonista) --}}
    <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-4">
        <h2 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2 uppercase tracking-wide">
            <span class="w-6 h-6 rounded-full bg-[rgba(201,162,74,0.2)] text-[rgba(201,162,74,1)] flex items-center justify-center text-xs">1</span>
            Servicios y Empleado
        </h2>
        
        @include('admin.citas.partials._servicios_rows', [
            'mode' => $mode,
            'cita' => $cita,
            'categorias' => $categorias,
            'bbField' => $bbField,
            'bbIconColor' => $bbIconColor,
            'empleados' => $empleados,
        ])

        {{-- Contenedor dinámico donde se mostrarán los chips de servicios seleccionados --}}
        <div id="resumen_servicios_seleccionados" class="mt-3 flex flex-wrap gap-2 empty:hidden">
            </div>
    </div>

    {{-- PASO 2: CLIENTE Y TOTALES (Agrupados en una sola fila compacta) --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        
        {{-- Cliente (Toma más espacio) --}}
        <div class="md:col-span-8 relative">
            <label for="cliente_search" class="block text-xs font-bold text-gray-600 mb-1 uppercase">
                <i class="fas fa-user mr-1" style="{{ $bbIconColor }}"></i> Cliente <span class="text-red-500">*</span>
            </label>
            <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $selectedClienteId }}" required>
            <div class="relative">
                <input type="text" id="cliente_search" autocomplete="off" placeholder="Buscar cliente..." value="{{ old('cliente_label', $clienteLabel) }}" class="{{ $bbField }} pr-8" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400"><i class="fas fa-search text-xs"></i></div>
            </div>
            <div id="cliente_dropdown" class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden hidden">
                <div id="cliente_results" class="max-h-48 overflow-auto text-sm"></div>
            </div>
            @error('cliente_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Totales (Minimalistas, tipo badge) --}}
        <div class="md:col-span-4 flex gap-3">
            <div class="flex-1 bg-gray-50 border border-gray-100 rounded-md p-2 flex flex-col justify-center items-center">
                <span class="text-[10px] uppercase font-bold text-gray-500">Duración</span>
                <input id="duracion_total" type="text" class="bg-transparent border-none text-center font-bold text-sm w-full p-0 focus:ring-0" readonly value="0 min">
            </div>
            <div class="flex-1 bg-green-50 border border-green-100 rounded-md p-2 flex flex-col justify-center items-center">
                <span class="text-[10px] uppercase font-bold text-green-700">Total</span>
                <input id="total_servicios" name="total_servicios" type="text" class="bg-transparent border-none text-center font-bold text-green-700 text-sm w-full p-0 focus:ring-0" readonly value="$0.00">
            </div>
        </div>
    </div>

    {{-- PASO 3: FECHA Y HORA (Más compacto) --}}
    @php
        $fechaInit = old('fecha_cita', !empty($cita?->fecha_cita) ? \Carbon\Carbon::parse($cita->fecha_cita)->format('Y-m-d') : ($fechaPrefill ?? ''));
        $horaInitRaw = old('hora_cita', $cita->hora_cita ?? '');
        $horaInit = $horaInitRaw ? substr((string)$horaInitRaw, 0, 5) : '';
    @endphp

    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">
            <i class="fas fa-calendar-alt mr-1" style="{{ $bbIconColor }}"></i> Fecha y Hora <span class="text-red-500">*</span>
        </label>
        
        <div class="bb-admin-datetime rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
            <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ $fechaInit }}">
            <input type="hidden" name="hora_cita"  id="bbHourInput" value="{{ $horaInit }}">

            <div id="bbDatetimeLock" class="text-xs px-3 py-2 rounded-md bg-amber-50 text-amber-800 border border-amber-100 mb-3" style="display:none;">
                Selecciona los servicios primero para ver disponibilidad.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                <div>
                    <div id="bbAdminCalendar" class="bb-calendar"></div>
                    @error('fecha_cita') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <div class="bb-timesPanel">
                        <div class="text-xs font-bold text-gray-700 mb-1" id="bbTimesTitle">Horas disponibles</div>
                        <div id="bbTimesGrid" class="bb-timesGrid"></div>
                        <p id="bbTimesEmpty" class="text-xs text-gray-400 mt-2" style="display:none;">Sin horarios para esta fecha.</p>
                        @error('hora_cita') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PASO 4: DETALLES EXTRA (Estado, Pago, Descuento en la misma fila) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <div>
            <label for="estado_cita" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Estado <span class="text-red-500">*</span></label>
            @php $estadoSelected = old('estado_cita', $cita->estado_cita ?? 'pendiente'); @endphp
            <select id="estado_cita" name="estado_cita" class="{{ $bbField }}" required>
                <option value="confirmada" @selected($estadoSelected === 'confirmada')>Confirmada</option>
                <option value="cancelada"  @selected($estadoSelected === 'cancelada')>Cancelada</option>
                <option value="completada" @selected($estadoSelected === 'completada')>Completada</option>
            </select>
        </div>

        <div id="metodo_pago_wrap" style="display:none;">
            <label for="metodo_pago" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Método Pago <span class="text-red-500">*</span></label>
            @php $metodoPagoSelected = old('metodo_pago', $cita->metodo_pago ?? ''); @endphp
            <select id="metodo_pago" name="metodo_pago" class="{{ $bbField }}">
                <option value="">Seleccionar...</option>
                <option value="efectivo" @selected($metodoPagoSelected === 'efectivo')>Efectivo</option>
                <option value="tarjeta_credito" @selected($metodoPagoSelected === 'tarjeta_credito')>Tarjeta (Crédito)</option>
                <option value="tarjeta_debito" @selected($metodoPagoSelected === 'tarjeta_debito')>Tarjeta (Débito)</option>
                <option value="transferencia" @selected($metodoPagoSelected === 'transferencia')>Transferencia</option>
            </select>
        </div>

        <div>
            <label for="descuento" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Descuento ($)</label>
            <input type="number" step="0.01" min="0" name="descuento" id="descuento" value="{{ old('descuento', $cita->descuento ?? 0) }}" class="{{ $bbField }}" placeholder="0.00" />
        </div>
    </div>

    {{-- OBSERVACIONES --}}
    <div>
        <label for="observaciones" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Observaciones</label>
        <textarea id="observaciones" name="observaciones" rows="2" class="{{ $bbField }} resize-none">{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>
    </div>

    {{-- BOTONES --}}
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-3 border-t border-gray-100">
        <a href="{{ route('admin.citas.index') }}" class="px-5 py-2 text-sm rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
            Cancelar
        </a>
        <button type="submit" class="px-5 py-2 text-sm rounded-md font-bold transition shadow-sm bg-[rgba(201,162,74,1)] text-white hover:bg-[rgba(180,140,60,1)]">
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
    // ✅ Solo para el primer render: que el mes se alinee a la fecha precargada (edit/old())
    snapToSelectedMonth: true,
  };

  // ===========================
  // ✅ Disponibilidad por día (cache por mes + selección actual)
  // ===========================
  const availState = {
    // key: `${itemsHash}|${YYYY-MM}` => Set(['YYYY-MM-DD', ...])
    cache: new Map(),
    loadingKey: null,
  };

  function monthKeyFromDate(d){
    return `${d.getFullYear()}-${pad2(d.getMonth()+1)}`;
  }

  function hashItems(items){
    // hash estable por selección de servicio/empleado
    return items
      .map(it => `${it.id_servicio}:${it.id_empleado}:${it.orden}`)
      .join('|');
  }

  async function preloadMonthAvailability() {
    if (dtState.locked || !isReadyForAvailability()) return;

    const items = getItemsFromRows();
    const itemsHash = hashItems(items);

    const mk = monthKeyFromDate(dtState.calendarMonth);
    const key = `${itemsHash}|${mk}`;

    if (availState.cache.has(key)) return; // ya cargado

    availState.loadingKey = key;

    const [yy, mm] = mk.split('-').map(n => parseInt(n, 10));
    const daysInMonth = new Date(yy, mm, 0).getDate(); // mm ya viene 1-12
    const todayYMD = toYMD(new Date());
    const disablePast = (MODE === 'create');

    const available = new Set();

    // ✅ Guarda selección actual para no afectarla mientras se precarga el mes
    const restoreDate = dtState.selectedDate;

    // ⚠️ 31 requests máx. (rápido de implementar sin endpoint nuevo)
    // Si luego quieres optimizar, lo cambiamos por un endpoint "fechasDisponibles".
    const tasks = [];
    for (let day = 1; day <= daysInMonth; day++) {
      const ymd = `${yy}-${pad2(mm)}-${pad2(day)}`;
      const isPast = disablePast ? (ymd < todayYMD) : false;
      if (isPast) continue;

      const qs = new URLSearchParams();
      dtState.selectedDate = ymd; // temporal para buildAvailabilityParams
      buildAvailabilityParams(qs, items);
      dtState.selectedDate = restoreDate; // restaura

      const url = URL_HORAS + '?' + qs.toString();

      tasks.push(
        fetch(url, { headers: { 'Accept': 'application/json' }})
          .then(r => r.json())
          .then(data => {
            let list = [];
            if (Array.isArray(data)) list = data;
            else if (Array.isArray(data?.horas)) list = data.horas;
            else list = [];
            if (list.length) available.add(ymd);
          })
          .catch(() => {})
      );
    }

    await Promise.all(tasks);

    // si mientras cargaba cambió la selección/mes, no guardes
    if (availState.loadingKey !== key) return;

    availState.cache.set(key, available);
  }

  function getAvailableSetForCurrentMonth(){
    const items = getItemsFromRows();
    const itemsHash = hashItems(items);
    const mk = monthKeyFromDate(dtState.calendarMonth);
    const key = `${itemsHash}|${mk}`;
    return availState.cache.get(key) || null;
  }

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

    if (dtState.snapToSelectedMonth && dtState.selectedDate) {
      const dSel = ymdToDate(dtState.selectedDate);
      if (dSel) dtState.calendarMonth = new Date(dSel.getFullYear(), dSel.getMonth(), 1);
      dtState.snapToSelectedMonth = false;
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

          const availSet = getAvailableSetForCurrentMonth();
          const availabilityKnown = !!availSet;
          const isAvailable = availabilityKnown ? availSet.has(ymd) : false;

          // ✅ Mientras se precarga la disponibilidad del mes, deshabilita para evitar selección incorrecta
          const disabled = dtState.locked || isPast || (!availabilityKnown ? true : !isAvailable);

          // Punto dorado SOLO si está disponible (si no se conoce aún, queda muted)
          const dotClass = (dtState.locked || !availabilityKnown || !isAvailable) ? 'is-muted' : 'is-gold';

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
      btn.addEventListener('click', async () => {
        const delta = Number(btn.getAttribute('data-nav'));
        dtState.calendarMonth = new Date(y, m + delta, 1);
        // ✅ al navegar meses NO queremos volver a forzar el mes al seleccionado
        dtState.snapToSelectedMonth = false;
        await syncCalendarAvailability({ clearInvalidSelection: false });
      });
    });

    elCalendar.querySelectorAll('.bb-cal__cell[data-date]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const ymd = btn.getAttribute('data-date');
        dtState.selectedDate = ymd;
        dtState.snapToSelectedMonth = false;
        if (elDateInput) elDateInput.value = ymd;

        dtState.selectedHour = null;
        if (elHourInput) elHourInput.value = '';

        // Si por alguna razón aún no está precargada la disponibilidad, precárgala
        if (!getAvailableSetForCurrentMonth()) {
          await syncCalendarAvailability({ clearInvalidSelection: false });
        } else {
          renderCalendar();
        }
        await refreshHorasDisponibles();
      });
    });
  }

  // ===========================
  // ✅ Render + precarga disponibilidad del mes y bloquea días no disponibles
  // ===========================
  async function syncCalendarAvailability({ clearInvalidSelection = true } = {}) {
    // Render inmediato (por UX)
    renderCalendar();

    if (dtState.locked) return;

    try {
      await preloadMonthAvailability();

      const availSet = getAvailableSetForCurrentMonth();

      // Si cambian servicios/empleados, la fecha seleccionada puede dejar de ser válida
      if (
        clearInvalidSelection &&
        MODE === 'create' &&
        dtState.selectedDate &&
        availSet &&
        !availSet.has(dtState.selectedDate)
      ) {
        dtState.selectedDate = null;
        dtState.selectedHour = null;

        if (elDateInput) elDateInput.value = '';
        if (elHourInput) elHourInput.value = '';

        if (elTimesGrid) elTimesGrid.innerHTML = '';
        if (elTimesEmpty) elTimesEmpty.style.display = 'none';
        if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
        if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';
      }
    } finally {
      // Render final con disponibilidad aplicada
      renderCalendar();
    }
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

    serviciosWrapper.addEventListener('click', async (e) => {
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
      await syncCalendarAvailability();
      await refreshHorasDisponibles();
    });

    async function addRow() {
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
      await syncCalendarAvailability();
      await refreshHorasDisponibles();
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

      await syncCalendarAvailability({ clearInvalidSelection: false });
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
        await syncCalendarAvailability();
        await refreshHorasDisponibles();
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
        await syncCalendarAvailability();
        await refreshHorasDisponibles();
        return;
      }

      const empSel = e.target.closest('select[data-role="empleado"]');
      if (empSel) {
        setLockUI();
        await syncCalendarAvailability();
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