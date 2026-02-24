(() => {
  const ctx = window.__ADMIN_BOOKING_CTX__ || {};

  const serviciosMap = ctx.servicios || {};
  const categorias = Array.isArray(ctx.categorias) ? ctx.categorias : [];
  const clientes = Array.isArray(ctx.clientes) ? ctx.clientes : [];

  const urls = ctx.urls || {};
  const urlHoras = urls.horas;
  const urlMonth = urls.month; // opcional
  const urlEmps  = urls.empleadosPorServicio;

  // DOM
  const elSelectedList  = document.getElementById('bbSelectedList');
  const elItemsHidden   = document.getElementById('bbItemsHidden');
  const elCategoryList  = document.getElementById('bbCategoryList');
  const elServiceCards  = document.getElementById('bbServiceCards');

  const elCalendar      = document.getElementById('bbCalendar');
  const elDateInput     = document.getElementById('bbDateInput');
  const elHourInput     = document.getElementById('bbHourInput');
  const elDatetimeLock  = document.getElementById('bbDatetimeLock');

  const elTimesGrid     = document.getElementById('bbTimesGrid');
  const elTimesTitle    = document.getElementById('bbTimesTitle');
  const elTimesHint     = document.getElementById('bbTimesHint');
  const elTimesEmpty    = document.getElementById('bbTimesEmpty');

  const elDurTotal      = document.getElementById('bbDuracionTotal');
  const elTotalServ     = document.getElementById('bbTotalServicios');
  const elTotalFinal    = document.getElementById('bbTotalFinal');
  const elDescuento     = document.getElementById('descuento');

  const form            = document.getElementById('bookingForm') || document.querySelector('form');
  const submitBtn       = document.getElementById('submitBooking');

  const clienteHidden   = document.getElementById('cliente_id');
  const clienteInput    = document.getElementById('cliente_search');
  const clienteDropdown = document.getElementById('cliente_dropdown');
  const clienteResults  = document.getElementById('cliente_results');

  // Cache empleados por servicio
  const empCache = new Map();

  // Estado
  let state = {
    activeCategoryId: null,
    items: [], // [{id_servicio, id_empleado, orden}]
    selectedDate: null,
    selectedHour: null,
    calendarMonth: new Date(),
    monthAvailability: null, // opcional
  };

  // Utils
  const pad2 = (n) => String(n).padStart(2, '0');
  const norm = (v) => (v ?? '').toString().trim().toLowerCase();

  function toYMD(d) {
    return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
  }
  function monthKey(d) {
    return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}`;
  }
  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }
  function joinUrl(base, path) {
    if (!base) return path || '';
    const b = String(base).replace(/\/+$/, '');
    const p = String(path || '').replace(/^\/+/, '');
    return `${b}/${p}`;
  }
  function resolveServiceImg(svc) {
    const fallback = ctx.fallbackImg || '';
    const img = svc?.imagen;
    if (!img) return fallback;
    if (/^https?:\/\//i.test(img)) return img;
    if (String(img).startsWith('images/') || String(img).startsWith('/images/')) {
      return joinUrl(ctx.assetRoot, img);
    }
    return joinUrl(ctx.assetStorage, img);
  }

  function serviceById(id) {
    return serviciosMap[id] || null;
  }
  function priceFinal(svc) {
    const p = Number(svc?.precio ?? 0);
    const d = Number(svc?.descuento ?? 0);
    return Math.max(0, p - d);
  }

  function normalizeOrder() {
    state.items = state.items.map((it, i) => ({ ...it, orden: i + 1 }));
  }

  function isClienteSelected() {
    return Number(clienteHidden?.value || 0) > 0;
  }
  function isAllEmployeesSelected() {
    return state.items.length > 0 && state.items.every(it => Number(it.id_empleado) > 0);
  }
  function canSubmit() {
    return isClienteSelected() && isAllEmployeesSelected() && !!state.selectedDate && !!state.selectedHour;
  }
  function updateSubmitState() {
    if (!submitBtn) return;
    submitBtn.disabled = !canSubmit();
  }

  function setItemsParams(url, items) {
    // compat: items[i][...]
    items.forEach((it, i) => {
      url.searchParams.set(`items[${i}][id_servicio]`, String(it.id_servicio));
      url.searchParams.set(`items[${i}][id_empleado]`, String(it.id_empleado ?? ''));
      url.searchParams.set(`items[${i}][orden]`, String(it.orden ?? (i + 1)));
    });
  }

  // Totales
  function renderTotals() {
    let dur = 0;
    let total = 0;

    state.items.forEach(it => {
      const s = serviceById(it.id_servicio);
      dur += Number(s?.duracion_minutos ?? 0);
      total += priceFinal(s);
    });

    const desc = Number(elDescuento?.value || 0);
    const final = Math.max(0, total - desc);

    if (elDurTotal)  elDurTotal.value = String(dur);
    if (elTotalServ) elTotalServ.value = `$${total.toFixed(2)}`;
    if (elTotalFinal) elTotalFinal.value = `$${final.toFixed(2)}`;
  }

  // Hidden inputs: servicios[i][...]
  function renderHiddenItems() {
    if (!elItemsHidden) return;

    const html = state.items.map((it, i) => {
      const s = serviceById(it.id_servicio);
      const dur = Number(s?.duracion_minutos ?? 0);
      const precio = priceFinal(s);

      return `
        <input type="hidden" name="servicios[${i}][id_servicio]" value="${it.id_servicio}">
        <input type="hidden" name="servicios[${i}][id_empleado]" value="${it.id_empleado ?? ''}">
        <input type="hidden" name="servicios[${i}][duracion_snapshot]" value="${dur}">
        <input type="hidden" name="servicios[${i}][precio_snapshot]" value="${precio}">
      `;
    }).join('');

    elItemsHidden.innerHTML = html;
  }

  function resetDateTimeUI() {
    state.selectedDate = null;
    state.selectedHour = null;

    if (elDateInput) elDateInput.value = '';
    if (elHourInput) elHourInput.value = '';

    if (elTimesGrid) elTimesGrid.innerHTML = '';
    if (elTimesEmpty) elTimesEmpty.style.display = 'none';
    if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
    if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';

    updateSubmitState();
  }

  function renderDatetimeLock() {
    if (!elDatetimeLock) return;
    const locked = !isAllEmployeesSelected();
    elDatetimeLock.style.display = locked ? 'block' : 'none';

    if (locked) {
      state.monthAvailability = null;
      resetDateTimeUI();
    }
  }

  // ======= Categorías =======
  function renderCategories() {
    if (!elCategoryList) return;

    if (!categorias.length) {
      elCategoryList.innerHTML = `<div class="bb-empty">No hay categorías disponibles.</div>`;
      return;
    }

    if (!state.activeCategoryId) {
      state.activeCategoryId = String(categorias[0].id_categoria ?? categorias[0].id ?? '');
    }

    elCategoryList.innerHTML = categorias.map(cat => {
      const id = cat.id_categoria ?? cat.id ?? cat.value;
      const name = cat.nombre ?? cat.name ?? 'Categoría';
      const active = String(id) === String(state.activeCategoryId);

      return `
        <button type="button" class="bb-cat-btn ${active ? 'is-active' : ''}" data-cat="${escapeHtml(id)}">
          ${escapeHtml(name)}
        </button>
      `;
    }).join('');

    elCategoryList.querySelectorAll('.bb-cat-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        state.activeCategoryId = btn.getAttribute('data-cat');
        renderCategories();
        renderServiceCards();
      });
    });
  }

  // ======= Cards de servicios =======
  function renderServiceCards() {
    if (!elServiceCards) return;

    const catId = state.activeCategoryId;
    const list = Object.values(serviciosMap);

    if (!catId) {
      elServiceCards.innerHTML = `<div class="bb-empty">Selecciona una categoría.</div>`;
      return;
    }

    const filtered = list.filter(s => String(s.id_categoria ?? '') === String(catId));

    if (!filtered.length) {
      elServiceCards.innerHTML = `<div class="bb-empty">No hay servicios en esta categoría.</div>`;
      return;
    }

    elServiceCards.innerHTML = filtered.map(s => {
      const id = Number(s.id_servicio);
      const name = s.nombre_servicio ?? `Servicio #${id}`;
      const precio = priceFinal(s);
      const dur = Number(s.duracion_minutos || 0);

      const already = state.items.some(it => Number(it.id_servicio) === id);
      const imgSrc = resolveServiceImg(s);

      return `
        <article class="bb-svc-card ${already ? 'is-disabled' : ''}" data-svc="${id}">
          <div class="bb-svc-card__media">
            <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(name)}" loading="lazy">
          </div>

          <div class="bb-svc-card__body">
            <div class="bb-svc-card__name">${escapeHtml(name)}</div>
            <div class="bb-svc-card__meta">
              <span>${dur} min</span>
              <span><strong>$${precio.toFixed(2)}</strong></span>
            </div>
            <button type="button" class="bb-btn bb-btn--soft" ${already ? 'disabled' : ''}>
              ${already ? 'Agregado' : 'Agregar'}
            </button>
          </div>
        </article>
      `;
    }).join('');

    elServiceCards.querySelectorAll('.bb-svc-card').forEach(card => {
      card.addEventListener('click', () => {
        const id = Number(card.getAttribute('data-svc'));
        if (!id) return;
        if (state.items.some(it => Number(it.id_servicio) === id)) return;

        state.items.push({ id_servicio: id, id_empleado: null, orden: state.items.length + 1 });
        onItemsChanged();
      });
    });
  }

  // ======= Empleados (lazy) =======
  async function fetchEmpleados(servicioId) {
    const key = String(servicioId);
    if (empCache.has(key)) return empCache.get(key);

    if (!urlEmps) {
      empCache.set(key, []);
      return [];
    }

    try {
      const u = new URL(urlEmps, window.location.origin);
      u.searchParams.set('servicio_id', key);

      const res = await fetch(u.toString(), { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      // tu endpoint admin devuelve [{id,label}] según tu _form anterior
      const list = Array.isArray(data) ? data : [];
      empCache.set(key, list);
      return list;
    } catch (e) {
      console.error(e);
      empCache.set(key, []);
      return [];
    }
  }

  function buildEmpleadoSelect(list, currentId) {
    if (!Array.isArray(list) || list.length === 0) {
      return `
        <select class="bb-select bb-emp-select" disabled>
          <option value="">No hay empleados</option>
        </select>
      `;
    }

    const opts = list.map(e => {
      const sel = String(e.id) === String(currentId) ? 'selected' : '';
      return `<option value="${e.id}" ${sel}>${escapeHtml(e.label || ('Empleado #' + e.id))}</option>`;
    }).join('');

    return `<select class="bb-select bb-emp-select">${opts}</select>`;
  }

  // ======= Seleccionados =======
  function renderSelected() {
    if (!elSelectedList) return;

    if (state.items.length === 0) {
      elSelectedList.innerHTML = `<div class="bb-empty">Selecciona un servicio para iniciar.</div>`;
      return;
    }

    const html = state.items.map((it, idx) => {
      const s = serviceById(it.id_servicio);
      const name = s?.nombre_servicio ?? `Servicio #${it.id_servicio}`;
      const precio = priceFinal(s);
      const dur = Number(s?.duracion_minutos || 0);
      const imgSrc = resolveServiceImg(s);

      return `
        <article class="bb-selectedCard" data-index="${idx}" data-service-id="${it.id_servicio}">
          <div class="bb-selectedCard__media">
            <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(name)}" class="bb-selectedCard__img" loading="lazy" />
          </div>

          <div class="bb-selectedCard__info">
            <h2 class="bb-selected__name">${escapeHtml(name)}</h2>

            <ul class="bb-selected__meta">
              <li><strong>Duración:</strong> ${dur} min</li>
              <li><strong>Precio:</strong> $${precio.toFixed(2)}</li>
            </ul>

            <div class="bb-selectedCard__emp" style="margin-top:.75rem;">
              <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
              <div class="bb-emp-slot" data-idx="${idx}" data-svc="${it.id_servicio}">
                <select class="bb-select" disabled><option>Cargando...</option></select>
              </div>
              <p class="bb-hint" style="margin-top:.35rem;">Puedes cambiar el empleado si lo deseas.</p>
            </div>
          </div>

          <button type="button"
                  class="bb-selectedCard__remove"
                  ${state.items.length === 1 ? 'disabled' : ''}
                  title="Quitar servicio">✕</button>
        </article>
      `;
    }).join('');

    elSelectedList.innerHTML = html;

    // remove
    elSelectedList.querySelectorAll('.bb-selectedCard__remove').forEach(btn => {
      btn.addEventListener('click', () => {
        const card = btn.closest('.bb-selectedCard');
        const idx = Number(card?.getAttribute('data-index') || -1);
        if (idx < 0) return;
        if (state.items.length === 1) return;

        state.items.splice(idx, 1);
        onItemsChanged();
      });
    });

    // hydrate employee selects (lazy)
    hydrateEmployeeSelects();
  }

  async function hydrateEmployeeSelects() {
    const slots = document.querySelectorAll('.bb-emp-slot');
    for (const slot of slots) {
      const idx = Number(slot.getAttribute('data-idx'));
      const sid = Number(slot.getAttribute('data-svc'));
      if (!sid || idx < 0) continue;

      const list = await fetchEmpleados(sid);

      // auto-assign si aún no hay
      if (!state.items[idx].id_empleado && list.length >= 1) {
        state.items[idx].id_empleado = Number(list[0].id);
      }

      slot.innerHTML = buildEmpleadoSelect(list, state.items[idx].id_empleado);

      const sel = slot.querySelector('select.bb-emp-select');
      sel?.addEventListener('change', async () => {
        state.items[idx].id_empleado = sel.value ? Number(sel.value) : null;
        await onEmployeesChanged();
      });
    }
  }

  async function onEmployeesChanged() {
    normalizeOrder();
    renderHiddenItems();
    renderTotals();

    resetDateTimeUI();
    renderDatetimeLock();

    await fetchMonthAvailabilityIfReady();
    renderCalendar();
    updateSubmitState();
  }

  async function onItemsChanged() {
    normalizeOrder();
    renderSelected();
    renderHiddenItems();
    renderTotals();

    resetDateTimeUI();
    renderDatetimeLock();

    await fetchMonthAvailabilityIfReady();
    renderCalendar();
    updateSubmitState();

    renderServiceCards(); // deshabilita “Agregado”
  }

  // ======= Calendar (con degrade si no hay month endpoint) =======
  async function fetchMonthAvailabilityIfReady() {
    state.monthAvailability = null;
    if (!urlMonth) return;
    if (!isAllEmployeesSelected()) return;

    const mk = monthKey(state.calendarMonth);
    const url = new URL(urlMonth, window.location.origin);
    url.searchParams.set('month', mk);

    // mandamos items[] estilo cliente (si tu endpoint lo soporta, perfecto)
    setItemsParams(url, state.items);

    try {
      const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
      const data = await res.json().catch(() => null);
      if (data?.ok && data?.days) state.monthAvailability = data.days;
    } catch (e) {
      console.error(e);
      state.monthAvailability = null;
    }
  }

  function renderCalendar() {
    if (!elCalendar) return;

    const d = state.calendarMonth;
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

    const html = `
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
          const isPast = ymd < todayYMD;
          const isSelected = state.selectedDate === ymd;

          const av = state.monthAvailability?.[ymd];
          const hasAv = !!av;
          const isDisabledByAv = hasAv ? !!av.disabled : false;

          const locked = !isAllEmployeesSelected();
          const disabled = locked || isPast || isDisabledByAv;

          let dotClass = 'is-muted';
          if (locked || !hasAv) dotClass = 'is-muted';
          else dotClass = isDisabledByAv ? 'is-red' : 'is-gold';

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

    elCalendar.innerHTML = html;

    elCalendar.querySelectorAll('.bb-cal__nav').forEach(btn => {
      btn.addEventListener('click', async () => {
        const delta = Number(btn.getAttribute('data-nav'));
        state.calendarMonth = new Date(y, m + delta, 1);

        resetDateTimeUI();
        await fetchMonthAvailabilityIfReady();
        renderCalendar();
        updateSubmitState();
      });
    });

    elCalendar.querySelectorAll('.bb-cal__cell[data-date]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const ymd = btn.getAttribute('data-date');
        state.selectedDate = ymd;
        if (elDateInput) elDateInput.value = ymd;

        state.selectedHour = null;
        if (elHourInput) elHourInput.value = '';

        renderCalendar();
        await fetchHorasDisponibles();
        updateSubmitState();
      });
    });
  }

  // ======= Hours (usa TU endpoint admin existente) =======
  async function fetchHorasDisponibles() {
    if (!elTimesGrid) return;

    if (!state.selectedDate || !isAllEmployeesSelected() || !urlHoras) {
      elTimesGrid.innerHTML = '';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';
      if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';
      return;
    }

    if (elTimesHint) elTimesHint.textContent = 'Cargando horarios...';
    if (elTimesEmpty) elTimesEmpty.style.display = 'none';
    elTimesGrid.innerHTML = '';

    try {
      const url = new URL(urlHoras, window.location.origin);

      // compat: tu backend admin anterior usa `date` + servicios[] + empleados[]
      url.searchParams.set('date', state.selectedDate);
      url.searchParams.set('fecha', state.selectedDate); // por si también lo soportas

      state.items.forEach(it => {
        url.searchParams.append('servicios[]', String(it.id_servicio));
        if (it.id_empleado) url.searchParams.append('empleados[]', String(it.id_empleado));
      });

      const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
      const data = await res.json().catch(() => null);

      // Soportamos 2 formatos:
      // 1) [{value:'09:00', label:'9:00 AM'}]
      // 2) {horas:['09:00','09:30']}
      let items = [];
      if (Array.isArray(data)) {
        items = data.map(x => ({ value: x.value, label: x.label || x.value }));
      } else if (Array.isArray(data?.horas)) {
        items = data.horas.map(h => ({ value: h, label: h }));
      }

      if (elTimesTitle) elTimesTitle.textContent = `Horas disponibles (${state.selectedDate})`;

      if (!items.length) {
        if (elTimesHint) elTimesHint.textContent = 'Prueba otro día o cambia el empleado.';
        if (elTimesEmpty) elTimesEmpty.style.display = '';
        return;
      }

      if (elTimesHint) elTimesHint.textContent = 'Selecciona una hora para confirmar.';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';

      elTimesGrid.innerHTML = items.map(t => {
        const selected = String(t.value) === String(state.selectedHour);
        return `
          <button type="button"
                  class="bb-timeBtn ${selected ? 'is-selected' : ''}"
                  data-hour="${escapeHtml(t.value)}">
            ${escapeHtml(t.label)}
          </button>
        `;
      }).join('');

      elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          const v = btn.getAttribute('data-hour');
          state.selectedHour = v;
          if (elHourInput) elHourInput.value = v;

          elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(x => x.classList.remove('is-selected'));
          btn.classList.add('is-selected');

          updateSubmitState();
        });
      });

      // re-aplicar selección inicial si existe
      if (state.selectedHour) {
        const exists = items.some(x => String(x.value) === String(state.selectedHour));
        if (!exists) {
          state.selectedHour = null;
          if (elHourInput) elHourInput.value = '';
          updateSubmitState();
        }
      }
    } catch (e) {
      console.error(e);
      if (elTimesHint) elTimesHint.textContent = 'Error cargando horarios.';
      if (elTimesEmpty) elTimesEmpty.style.display = '';
    }
  }

  // ======= Cliente search =======
  function hideClienteResults() {
    if (!clienteDropdown) return;
    clienteDropdown.style.display = 'none';
    if (clienteResults) clienteResults.innerHTML = '';
  }

  function showClienteResults(items) {
    if (!clienteDropdown || !clienteResults) return;

    if (!items.length) {
      clienteResults.innerHTML = `<div style="padding:12px 14px; font-size:13px; color:rgba(0,0,0,.55);">Sin resultados</div>`;
      clienteDropdown.style.display = '';
      return;
    }

    clienteResults.innerHTML = items.map(c => `
      <button type="button"
              style="width:100%; text-align:left; padding:12px 14px; border:0; background:#fff; cursor:pointer;"
              data-id="${c.id}"
              data-label="${escapeHtml(c.label || '')}">
        <div style="font-weight:800; color:rgba(0,0,0,.78);">${escapeHtml(c.nombre || 'Sin nombre')}</div>
        ${c.email ? `<div style="font-size:12px; color:rgba(0,0,0,.55); margin-top:2px;">${escapeHtml(c.email)}</div>` : ''}
      </button>
    `).join('');

    // hover
    clienteResults.querySelectorAll('button').forEach(b => {
      b.addEventListener('mouseenter', () => b.style.background = 'rgba(0,0,0,.03)');
      b.addEventListener('mouseleave', () => b.style.background = '#fff');
    });

    clienteDropdown.style.display = '';
  }

  function initClienteSearch() {
    if (!clienteInput || !clienteHidden) return;

    clienteInput.addEventListener('input', () => {
      const q = norm(clienteInput.value);
      if (!q) {
        clienteHidden.value = '';
        hideClienteResults();
        updateSubmitState();
        return;
      }

      const filtered = clientes.filter(c =>
        norm(c.nombre).includes(q) || norm(c.email).includes(q)
      ).slice(0, 10);

      showClienteResults(filtered);
    });

    clienteResults?.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-id]');
      if (!btn) return;

      clienteHidden.value = btn.dataset.id;
      clienteInput.value  = btn.dataset.label || '';
      hideClienteResults();
      updateSubmitState();
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('#cliente_search') && !e.target.closest('#cliente_dropdown')) {
        hideClienteResults();
      }
    });
  }

  // ======= Init =======
  async function init() {
    const initial = ctx.initial || {};
    const initialItems = Array.isArray(initial.items) ? initial.items : [];

    state.items = initialItems.length ? initialItems : [];
    normalizeOrder();

    state.selectedDate = initial.fecha || null;
    state.selectedHour = initial.hora || null;

    if (state.selectedDate && elDateInput) elDateInput.value = state.selectedDate;
    if (state.selectedHour && elHourInput) elHourInput.value = state.selectedHour;

    state.calendarMonth = state.selectedDate ? new Date(state.selectedDate + 'T00:00:00') : new Date();

    initClienteSearch();

    renderCategories();
    renderServiceCards();
    renderSelected();
    renderHiddenItems();
    renderTotals();
    renderDatetimeLock();

    await fetchMonthAvailabilityIfReady();
    renderCalendar();

    // si ya viene fecha (edit), cargar horas
    if (state.selectedDate && isAllEmployeesSelected()) {
      await fetchHorasDisponibles();
    }

    updateSubmitState();

    elDescuento?.addEventListener('input', () => {
      renderTotals();
    });

    form?.addEventListener('submit', (e) => {
      if (!canSubmit()) {
        e.preventDefault();
        alert('Completa: cliente, empleado(s), fecha y hora antes de guardar la cita.');
      }
    });
  }

  init();
})();
