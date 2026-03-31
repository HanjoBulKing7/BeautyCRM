function toggleAccordion(button) {
    const item = button.parentElement;
    const content = item.querySelector('.accordion-content');
    const icon = item.querySelector('.accordion-icon');
    
    if (content.style.maxHeight) {
        content.style.maxHeight = null;
        icon.style.transform = "rotate(0deg)";
    } else {
        document.querySelectorAll('.accordion-content').forEach(el => el.style.maxHeight = null);
        document.querySelectorAll('.accordion-icon').forEach(el => el.style.transform = "rotate(0deg)");
        
        setTimeout(() => {
            content.style.maxHeight = content.scrollHeight + "px";
        }, 10);
        icon.style.transform = "rotate(45deg)";
    }
}

document.addEventListener('DOMContentLoaded', function () {
  const cfg = window.AdminCitasFormConfig || {};
  const MODE = cfg.mode;
  const CITA_ID = cfg.citaId;

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

  const URL_EMPLEADOS = cfg.urlEmpleados;
  const URL_HORAS     = cfg.urlHoras;

  const dtState = {
    calendarMonth: new Date(),
    selectedDate: (elDateInput?.value || '').trim() || null,
    selectedHour: (elHourInput?.value || '').trim() || null,
    locked: true,
    snapToSelectedMonth: true,
  };

  const availState = {
    cache: new Map(),
    loadingKey: null,
  };

  function monthKeyFromDate(d){
    return `${d.getFullYear()}-${pad2(d.getMonth()+1)}`;
  }

  function hashItems(items){
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

    if (availState.cache.has(key)) return;
    availState.loadingKey = key;

    const [yy, mm] = mk.split('-').map(n => parseInt(n, 10));
    const daysInMonth = new Date(yy, mm, 0).getDate();
    const todayYMD = toYMD(new Date());
    const disablePast = (MODE === 'create');

    const available = new Set();
    const restoreDate = dtState.selectedDate;

    const tasks = [];
    for (let day = 1; day <= daysInMonth; day++) {
      const ymd = `${yy}-${pad2(mm)}-${pad2(day)}`;
      const isPast = disablePast ? (ymd < todayYMD) : false;
      if (isPast) continue;

      const qs = new URLSearchParams();
      dtState.selectedDate = ymd;
      buildAvailabilityParams(qs, items);
      dtState.selectedDate = restoreDate;

      const url = URL_HORAS + '?' + qs.toString();

      tasks.push(
        fetch(url, { headers: { 'Accept': 'application/json' }})
          .then(r => r.json())
          .then(data => {
            let list = [];
            if (Array.isArray(data)) list = data;
            else if (Array.isArray(data?.horas)) list = data.horas;
            if (list.length) available.add(ymd);
          })
          .catch(() => {})
      );
    }

    await Promise.all(tasks);
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
    const startDow = (first.getDay() + 6) % 7;
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

          const disabled = dtState.locked || isPast || (!availabilityKnown ? true : !isAvailable);
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

        if (!getAvailableSetForCurrentMonth()) {
          await syncCalendarAvailability({ clearInvalidSelection: false });
        } else {
          renderCalendar();
        }
        await refreshHorasDisponibles();
      });
    });
  }

  async function syncCalendarAvailability({ clearInvalidSelection = true } = {}) {
    renderCalendar();
    if (dtState.locked) return;

    try {
      await preloadMonthAvailability();
      const availSet = getAvailableSetForCurrentMonth();

      if (clearInvalidSelection && MODE === 'create' && dtState.selectedDate && availSet && !availSet.has(dtState.selectedDate)) {
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
      return `<button type="button" class="bb-timeBtn ${selected ? 'is-selected' : ''}" data-hour="${escapeHtml(value)}">${escapeHtml(label)}</button>`;
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

  // Multi-servicio
  if (serviciosWrapper) {
    const serviciosAll = cfg.serviciosAll || [];
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
      serviciosAll.filter(s => norm(s.categoria) === catN).forEach(s => {
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

      empleadoSelect.innerHTML = `<option value="">Selecciona un empleado</option>` + (data || []).map(e => `<option value="${e.id}">${e.label}</option>`).join('');
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
      if (e.target.matches('input[data-role="duracion_snapshot"]') || e.target.matches('input[data-role="precio_snapshot"]')) {
        recalcAll();
      }
    });
  }

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

  const CLIENTES = cfg.clientes || [];
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
      <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm" data-id="${c.id}" data-label="${escapeHtml(c.label || '')}">
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
