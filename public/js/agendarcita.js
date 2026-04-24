(() => {
  const ctx = window.__BOOKING_CTX__ || {};

  const serviciosMap = ctx.servicios || {}; // { [id]: {id_servicio, id_categoria, nombre_servicio, precio, descuento, duracion_minutos, imagen...} }
  const categorias = Array.isArray(ctx.categorias) ? ctx.categorias : [];

  // ✅ empleados por servicio + carga para balanceo
  const empleadosPorServicio = ctx.empleadosPorServicio || {}; // { [servicioId]: [{id,nombre,apellido}] }
  const cargaEmpleados = ctx.cargaEmpleados || {}; // { [empleadoId]: total }

  // DOM
  const elSelectedList = document.getElementById('bbSelectedList');
  const elItemsHidden = document.getElementById('bbItemsHidden');
  const elCategoryList = document.getElementById('bbCategoryList');
  const elServiceCards = document.getElementById('bbServiceCards');

  const elCalendar = document.getElementById('bbCalendar');
  const elDateInput = document.getElementById('bbDateInput');
  const elHourInput = document.getElementById('bbHourInput');
  const elDatetimeLock = document.getElementById('bbDatetimeLock');

  const elTimesGrid = document.getElementById('bbTimesGrid');
  const elTimesTitle = document.getElementById('bbTimesTitle');
  const elTimesHint = document.getElementById('bbTimesHint');
  const elTimesEmpty = document.getElementById('bbTimesEmpty');

  const form = document.getElementById('bookingForm') || document.querySelector('form');
  const submitBtn = document.getElementById('submitBooking');

  // Estado
  let state = {
    activeCategoryId: null,
    items: [], // [{id_servicio, id_empleado, orden}]
    selectedDate: null,  // 'YYYY-MM-DD'
    selectedHour: null,  // 'HH:MM'
    calendarMonth: new Date(), // month displayed
    monthAvailability: null, // { 'YYYY-MM-DD': {disabled:boolean, slots:number} }
  };

  // Utils
  const pad2 = (n) => String(n).padStart(2, '0');

  function toYMD(d) {
    return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
  }
  function monthKey(d) {
    return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}`; // YYYY-MM
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
    const fallback = (ctx && ctx.fallbackImg) ? ctx.fallbackImg : '';
    const img = svc?.imagen;

    if (!img) return fallback;
    if (/^https?:\/\//i.test(img)) return img;

    // images/ o /images/ => público
    if (String(img).startsWith('images/') || String(img).startsWith('/images/')) {
      return joinUrl(ctx.assetRoot, img);
    }

    // lo demás => storage
    return joinUrl(ctx.assetStorage, img);
  }

  function serviceById(id) {
    return serviciosMap[id] || null;
  }

  function normalizeOrder() {
    state.items = state.items.map((it, i) => ({ ...it, orden: i + 1 }));
  }

  function isAllEmployeesSelected() {
    return state.items.length > 0 && state.items.every(it => Number(it.id_empleado) > 0);
  }

  function canSubmit() {
    return isAllEmployeesSelected() && !!state.selectedDate && !!state.selectedHour;
  }

  function updateSubmitState() {
    if (!submitBtn) return;
    submitBtn.disabled = !canSubmit();
  }

  function setItemsParams(url, items) {
    // limpia items previos
    [...url.searchParams.keys()].forEach(k => {
      if (k === 'items' || k.startsWith('items[')) url.searchParams.delete(k);
    });

    items.forEach((it, i) => {
      url.searchParams.set(`items[${i}][id_servicio]`, String(it.id_servicio));
      url.searchParams.set(`items[${i}][id_empleado]`, String(it.id_empleado ?? ''));
      url.searchParams.set(`items[${i}][orden]`, String(it.orden ?? (i + 1)));
    });
  }

  // ✅ Balanceo: escoger empleado con menor carga
  function pickDefaultEmpleado(servicioId) {
    const list = empleadosPorServicio[String(servicioId)] || empleadosPorServicio[servicioId] || [];
    if (!Array.isArray(list) || list.length === 0) return null;

    let best = list[0];
    let bestLoad = Number(cargaEmpleados[best.id] ?? 0);

    for (const emp of list) {
      const load = Number(cargaEmpleados[emp.id] ?? 0);
      if (load < bestLoad) { best = emp; bestLoad = load; }
    }
    return Number(best.id) || null;
  }

  function renderHiddenItems() {
    if (!elItemsHidden) return;
    const html = state.items.map((it, i) => `
      <input type="hidden" name="items[${i}][id_servicio]" value="${it.id_servicio}">
      <input type="hidden" name="items[${i}][id_empleado]" value="${it.id_empleado ?? ''}">
      <input type="hidden" name="items[${i}][orden]" value="${it.orden}">
    `).join('');
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

  // ---------- UI: categorías ----------
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

  // ---------- UI: cards de servicios ----------
  function renderServiceCards() {
    if (!elServiceCards) return;

    const catId = state.activeCategoryId;
    const list = Object.values(serviciosMap);

    if (!catId) {
      elServiceCards.innerHTML = `<div class="bb-empty">Selecciona una categoría para ver servicios.</div>`;
      return;
    }

    const filtered = list.filter(s => String(s.id_categoria ?? s.categoria_id ?? '') === String(catId));

    if (!filtered.length) {
      elServiceCards.innerHTML = `<div class="bb-empty">No hay servicios en esta categoría.</div>`;
      return;
    }

    elServiceCards.innerHTML = filtered.map(s => {
      const id = Number(s.id_servicio);
      const name = s.nombre_servicio ?? `Servicio #${id}`;
      const precio = Math.max(0, Number(s.precio) - Number(s.descuento || 0));
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

        const empDefault = pickDefaultEmpleado(id);

        state.items.push({
          id_servicio: id,
          id_empleado: empDefault,
          orden: state.items.length + 1,
        });

        onItemsChanged();
      });
    });
  }

  // ---------- UI: servicios seleccionados ----------
  function buildEmpleadoSelect(servicioId, currentId) {
    const list = empleadosPorServicio[String(servicioId)] || empleadosPorServicio[servicioId] || [];
    if (!Array.isArray(list) || list.length === 0) {
      return `
        <select class="bb-select bb-emp-select" disabled>
          <option value="">No hay empleados disponibles</option>
        </select>
      `;
    }

    const opts = list.map(e => {
      const name = `${e.nombre ?? ''} ${e.apellido ?? ''}`.trim();
      const sel = String(e.id) === String(currentId) ? 'selected' : '';
      return `<option value="${e.id}" ${sel}>${escapeHtml(name || ('Empleado #' + e.id))}</option>`;
    }).join('');

    return `
      <select class="bb-select bb-emp-select">
        ${opts}
      </select>
    `;
  }

  function renderSelected() {
    if (!elSelectedList) return;

    if (state.items.length === 0) {
      elSelectedList.innerHTML = `<div class="bb-empty">Selecciona un servicio para iniciar tu cita.</div>`;
      return;
    }

    const html = state.items.map((it, idx) => {
      const s = serviceById(it.id_servicio);
      const name = s?.nombre_servicio ?? `Servicio #${it.id_servicio}`;
      const precio = Math.max(0, Number(s?.precio || 0) - Number(s?.descuento || 0));
      const dur = Number(s?.duracion_minutos || 0);
      const imgSrc = resolveServiceImg(s);

      return `
        <article class="bb-selectedCard bb-selectedCard--compact" data-index="${idx}" data-service-id="${it.id_servicio}" data-order="${it.orden}">
          <div class="bb-selectedCard__media">
            <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(name)}" class="bb-selectedCard__img" loading="lazy" />
          </div>

          <div class="bb-selectedCard__info">
            <h2 class="bb-selected__name">${escapeHtml(name)}</h2>

            <ul class="bb-selected__meta">
              <li><strong>Duración:</strong> ${dur} min</li>
              <li><strong>Desde:</strong> $${precio.toFixed(2)}</li>
            </ul>

            <div class="bb-selectedCard__emp" style="margin-top:.75rem;">
              <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
              ${buildEmpleadoSelect(it.id_servicio, it.id_empleado)}
              <p class="bb-hint" style="margin-top:.35rem;">
                Te asignamos uno automáticamente, pero puedes elegir otro.
              </p>
            </div>
          </div>

          <button
            type="button"
            class="bb-selectedCard__remove"
            ${state.items.length === 1 ? 'disabled' : ''}
            aria-label="Quitar"
            title="Quitar servicio"
          >✕</button>
        </article>
      `;
    }).join('');

    elSelectedList.innerHTML = html;

    // remove
    elSelectedList.querySelectorAll('.bb-selectedCard__remove').forEach(btn => {
      btn.addEventListener('click', async () => {
        const card = btn.closest('.bb-selectedCard');
        const idx = Number(card?.getAttribute('data-index') || -1);
        if (idx < 0) return;
        if (state.items.length === 1) return;

        state.items.splice(idx, 1);
        onItemsChanged();
      });
    });

    // empleado change
    elSelectedList.querySelectorAll('.bb-emp-select').forEach((sel, idx) => {
      sel.addEventListener('change', async () => {
        state.items[idx].id_empleado = sel.value ? Number(sel.value) : null;
        await onEmployeesChanged();
      });
    });
  }

  async function onEmployeesChanged() {
    normalizeOrder();
    renderHiddenItems();

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

    resetDateTimeUI();
    renderDatetimeLock();

    await fetchMonthAvailabilityIfReady();
    renderCalendar();
    updateSubmitState();
  }

  // ---------- Calendar ----------
  async function fetchMonthAvailabilityIfReady() {
    state.monthAvailability = null;
    if (!isAllEmployeesSelected()) return;

    const mk = monthKey(state.calendarMonth);
    const base = ctx?.urls?.month;
    if (!base) return;

    const url = new URL(base, window.location.origin);
    url.searchParams.set('month', mk);
    setItemsParams(url, state.items);

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return;

    const data = await res.json().catch(() => null);
    if (data?.ok && data?.days) {
      state.monthAvailability = data.days;
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

          // ✅ SOLO punto dorado cuando el día está habilitado (sin punto rojo para deshabilitados)
          const showGoldDot = !locked && hasAv && !isPast && !isDisabledByAv;
          const dotHtml = showGoldDot ? '<span class="bb-cal__dot is-gold" aria-hidden="true"></span>' : '';

          return `
            <button type="button"
              class="bb-cal__cell ${isSelected ? 'is-selected' : ''} ${disabled ? 'is-disabled' : ''}"
              data-date="${ymd}"
              ${disabled ? 'disabled' : ''}>
              <span class="bb-cal__day">${cell.getDate()}</span>
              ${dotHtml}
            </button>
          `;
        }).join('')}
      </div>
    `;

    elCalendar.innerHTML = html;

    // nav
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

    // pick date
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

  // ---------- Hours panel ----------
  async function fetchHorasDisponibles() {
    if (!elTimesGrid) return;

    if (!state.selectedDate || !isAllEmployeesSelected()) {
      elTimesGrid.innerHTML = '';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';
      if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';
      return;
    }

    if (elTimesHint) elTimesHint.textContent = 'Cargando horarios...';
    if (elTimesEmpty) elTimesEmpty.style.display = 'none';
    elTimesGrid.innerHTML = '';

    const base = ctx?.urls?.horas;
    if (!base) {
      if (elTimesHint) elTimesHint.textContent = 'No se pudo cargar disponibilidad.';
      return;
    }

    const url = new URL(base, window.location.origin);
    url.searchParams.set('fecha', state.selectedDate);
    setItemsParams(url, state.items);

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    if (!res.ok) {
      if (elTimesHint) elTimesHint.textContent = 'No se pudieron cargar horas.';
      return;
    }

    const data = await res.json().catch(() => null);
    const horas = Array.isArray(data?.horas) ? data.horas : [];

    if (elTimesTitle) elTimesTitle.textContent = `Horas disponibles (${state.selectedDate})`;

    if (!horas.length) {
      if (elTimesHint) elTimesHint.textContent = 'Prueba otro día o cambia el empleado.';
      if (elTimesEmpty) elTimesEmpty.style.display = '';
      return;
    }

    if (elTimesHint) elTimesHint.textContent = 'Selecciona una hora para confirmar.';
    if (elTimesEmpty) elTimesEmpty.style.display = 'none';

    elTimesGrid.innerHTML = horas.map(h => {
      const selected = String(h) === String(state.selectedHour);
      return `
        <button type="button" class="bb-timeBtn ${selected ? 'is-selected' : ''}" data-hour="${escapeHtml(h)}">
          ${escapeHtml(h)}
        </button>
      `;
    }).join('');

    elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(btn => {
      btn.addEventListener('click', () => {
        const h = btn.getAttribute('data-hour');
        state.selectedHour = h;
        if (elHourInput) elHourInput.value = h;

        elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(x => x.classList.remove('is-selected'));
        btn.classList.add('is-selected');

        updateSubmitState();
      });
    });
  }

  // ---------- Init ----------
  async function init() {
    // Servicio inicial
    const initialId = Number(ctx.servicioInicialId || 0);
    if (initialId > 0 && serviceById(initialId)) {
      const empDefault = pickDefaultEmpleado(initialId);
      state.items = [{ id_servicio: initialId, id_empleado: empDefault, orden: 1 }];
    } else {
      state.items = [];
    }

    state.calendarMonth = new Date();
    state.monthAvailability = null;

    renderCategories();
    renderServiceCards();
    renderSelected();
    renderHiddenItems();

    renderDatetimeLock();

    await fetchMonthAvailabilityIfReady();
    renderCalendar();
    updateSubmitState();

    form?.addEventListener('submit', (e) => {
      if (!canSubmit()) {
        e.preventDefault();
        alert('Completa: empleado(s), fecha y hora antes de solicitar la cita.');
      }
    });
  }

  init();
})();
