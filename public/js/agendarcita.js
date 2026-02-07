(() => {
  const ctx = window.__BOOKING_CTX__ || {};
  const serviciosMap = ctx.servicios || {}; // { [id]: {id_servicio, nombre_servicio, precio, descuento, duracion_minutos, ...} }
  const categorias = Array.isArray(ctx.categorias) ? ctx.categorias : [];
  const empleados = Array.isArray(ctx.empleados) ? ctx.empleados : [];

  // DOM
  const elSelectedList   = document.getElementById('bbSelectedList');
  const elItemsHidden    = document.getElementById('bbItemsHidden');
  const elCategoryList   = document.getElementById('bbCategoryList');
  const elServiceCards   = document.getElementById('bbServiceCards');

  const elCalendar       = document.getElementById('bbCalendar');
  const elDateInput      = document.getElementById('bbDateInput');
  const elHourSelect     = document.getElementById('bbHourSelect');
  const elDatetimeLock   = document.getElementById('bbDatetimeLock');

  const form = document.querySelector('form'); // tu form principal
  const submitBtn = document.getElementById('submitBooking');

  // Estado
  let state = {
    activeCategoryId: null,
    // items: [{id_servicio, id_empleado, orden}]
    items: [],
    selectedDate: null,  // 'YYYY-MM-DD'
    selectedHour: null,  // 'HH:MM'
    calendarMonth: new Date(), // month currently displayed
    monthAvailability: null, // { 'YYYY-MM-DD': {disabled:boolean, slots:number} }
  };

  // Util
  const pad2 = (n) => String(n).padStart(2, '0');

  function toYMD(d) {
    return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
  }
  function monthKey(d) {
    return `${d.getFullYear()}-${pad2(d.getMonth()+1)}`; // YYYY-MM
  }
  function isAllEmployeesSelected() {
    return state.items.length > 0 && state.items.every(it => !!it.id_empleado);
  }
  function canSubmit() {
    return isAllEmployeesSelected() && !!state.selectedDate && !!state.selectedHour;
  }

  function serviceById(id) {
    return serviciosMap[id] || null;
  }

  function buildEmpleadoSelect(currentId) {
  const opts = [`<option value="">Selecciona empleado</option>`].concat(
    empleados.map(e => {
      const name = `${e.nombre ?? ''} ${e.apellido ?? ''}`.trim();
      const sel = String(e.id) === String(currentId) ? 'selected' : '';
      return `<option value="${e.id}" ${sel}>${escapeHtml(name || ('Empleado #' + e.id))}</option>`;
    })
  );

  return `<select class="bb-select bb-emp-select">${opts.join('')}</select>`;
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


  // Render: selected items cards
  function renderSelected() {
  if (!elSelectedList) return;

  if (state.items.length === 0) {
    elSelectedList.innerHTML = `
      <div class="bb-empty">
        Selecciona un servicio para iniciar tu cita.
      </div>
    `;
    return;
  }

  const html = state.items.map((it, idx) => {
    const s = serviceById(it.id_servicio);
    const name = s?.nombre_servicio ?? `Servicio #${it.id_servicio}`;
    const precio = (s ? (Number(s.precio) - Number(s.descuento || 0)) : 0);
    const dur = Number(s?.duracion_minutos || 0);
    const imgSrc = resolveServiceImg(s);

    return `
      <article class="bb-selectedCard" data-index="${idx}" data-service-id="${it.id_servicio}" data-order="${it.orden}">
        <div class="bb-selectedCard__media">
          <img
            src="${escapeHtml(imgSrc)}"
            alt="${escapeHtml(name)}"
            class="bb-selectedCard__img"
            loading="lazy"
          />
        </div>

        <div class="bb-selectedCard__info">
          <h2 class="bb-selected__name">${escapeHtml(name)}</h2>

          <ul class="bb-selected__meta">
            <li><strong>Duración:</strong> ${dur} min</li>
            <li><strong>Desde:</strong> $${precio.toFixed(2)}</li>
          </ul>

          <div class="bb-selectedCard__emp" style="margin-top:.75rem;">
            <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
            ${buildEmpleadoSelect(it.id_empleado)}
            <p class="bb-hint" style="margin-top:.35rem;">
              Selecciona un empleado para habilitar disponibilidad.
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

  // Remove
  elSelectedList.querySelectorAll('.bb-selectedCard__remove').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.bb-selectedCard');
      const idx = Number(card?.getAttribute('data-index') || -1);
      if (idx < 0) return;
      if (state.items.length === 1) return;

      state.items.splice(idx, 1);

      // reset date/hour because disponibilidad cambia
      state.selectedDate = null;
      state.selectedHour = null;
      if (elDateInput) elDateInput.value = '';
      if (elHourSelect) {
        elHourSelect.innerHTML = `<option value="">Selecciona una fecha</option>`;
        elHourSelect.disabled = true;
      }

      renderSelected();
      syncHiddenInputs();
      refreshDatetimeLock();
      fetchMonthAvailabilityIfReady().then(renderCalendar);
      updateSubmitState();
    });
  });

  // Empleado change
  elSelectedList.querySelectorAll('.bb-emp-select').forEach((sel, idx) => {
    sel.addEventListener('change', async () => {
      state.items[idx].id_empleado = sel.value ? Number(sel.value) : null;

      // reset date/hour if employees change
      state.selectedDate = null;
      state.selectedHour = null;
      if (elDateInput) elDateInput.value = '';
      if (elHourSelect) {
        elHourSelect.innerHTML = `<option value="">Selecciona una fecha</option>`;
        elHourSelect.disabled = true;
      }

      syncHiddenInputs();
      refreshDatetimeLock();
      await fetchMonthAvailabilityIfReady();
      renderCalendar();
      updateSubmitState();
    });
  });
}


  function normalizeOrder() {
    state.items = state.items.map((it, i) => ({ ...it, orden: i + 1 }));
  }
  // ✅ Alias para mantener compatibilidad con llamadas existentes
function syncHiddenInputs() {
  renderHiddenItems();
}


  // Hidden inputs items[] for backend
  function renderHiddenItems() {
    if (!elItemsHidden) return;
    const html = state.items.map((it, i) => `
      <input type="hidden" name="items[${i}][id_servicio]" value="${it.id_servicio}">
      <input type="hidden" name="items[${i}][id_empleado]" value="${it.id_empleado ?? ''}">
      <input type="hidden" name="items[${i}][orden]" value="${it.orden}">
    `).join('');
    elItemsHidden.innerHTML = html;
  }

  // Categories render
  function renderCategories() {
    if (!elCategoryList) return;
    if (!categorias.length) {
      elCategoryList.innerHTML = `<div class="bb-empty">No hay categorías disponibles.</div>`;
      return;
    }

    // Botones elegantes (tu CSS ya manda la estética)
    elCategoryList.innerHTML = categorias.map(cat => {
      const id = cat.id_categoria ?? cat.id ?? cat.value;
      const name = cat.nombre ?? cat.name ?? 'Categoría';
      const active = String(id) === String(state.activeCategoryId);
      return `<button type="button" class="bb-cat-btn ${active ? 'is-active' : ''}" data-cat="${id}">
        ${escapeHtml(name)}
      </button>`;
    }).join('');

    elCategoryList.querySelectorAll('.bb-cat-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        state.activeCategoryId = btn.getAttribute('data-cat');
        renderCategories();
        renderServiceCards();
      });
    });
  }

  // Services cards filtered by category
  function renderServiceCards() {
    if (!elServiceCards) return;

    const catId = state.activeCategoryId;
    const list = Object.values(serviciosMap);

    // Si tu servicio trae id_categoria en serviciosJs, úsalo
    const filtered = catId
      ? list.filter(s => String(s.id_categoria ?? s.categoria_id ?? '') === String(catId))
      : [];

    if (!catId) {
      elServiceCards.innerHTML = `<div class="bb-empty">Selecciona una categoría para ver servicios.</div>`;
      return;
    }

    if (!filtered.length) {
      elServiceCards.innerHTML = `<div class="bb-empty">No hay servicios en esta categoría.</div>`;
      return;
    }

    elServiceCards.innerHTML = filtered.map(s => {
      const id = s.id_servicio;
      const name = s.nombre_servicio;
      const precio = (Number(s.precio) - Number(s.descuento || 0));
      const dur = Number(s.duracion_minutos || 0);

      const already = state.items.some(it => it.id_servicio === id);

      return `
        <article class="bb-svc-card ${already ? 'is-disabled' : ''}" data-svc="${id}">
          <div class="bb-svc-card__name">${escapeHtml(name)}</div>
          <div class="bb-svc-card__meta">
            <div><strong>$${precio.toFixed(2)}</strong></div>
            <div>${dur} min</div>
          </div>
          <button type="button" class="bb-btn bb-btn--soft" ${already ? 'disabled' : ''}>
            Agregar
          </button>
        </article>
      `;
    }).join('');

    elServiceCards.querySelectorAll('.bb-svc-card').forEach(card => {
      card.addEventListener('click', (e) => {
        const id = Number(card.getAttribute('data-svc'));
        if (!id) return;

        // evita duplicados
        if (state.items.some(it => it.id_servicio === id)) return;

        state.items.push({ id_servicio: id, id_empleado: null, orden: state.items.length + 1 });
        onItemsChanged();
      });
    });
  }

  // Datetime lock overlay
  function renderDatetimeLock() {
    if (!elDatetimeLock) return;
    const locked = !isAllEmployeesSelected();
    elDatetimeLock.style.display = locked ? 'block' : 'none';

    // Habilitar/deshabilitar calendario/hora
    if (elHourSelect) {
      elHourSelect.disabled = locked || !state.selectedDate;
    }
  }

  function updateSubmitState() {
    const ok = canSubmit();
    if (submitBtn) submitBtn.disabled = !ok;
  }

  // Calendar
  function renderCalendar() {
    if (!elCalendar) return;

    const d = state.calendarMonth;
    const y = d.getFullYear();
    const m = d.getMonth(); // 0-11

    const first = new Date(y, m, 1);
    const startDow = (first.getDay() + 6) % 7; // Lunes=0
    const daysInMonth = new Date(y, m + 1, 0).getDate();

    // header
    const monthName = first.toLocaleString('es-MX', { month: 'long', year: 'numeric' });

    const grid = [];
    for (let i = 0; i < startDow; i++) grid.push(null);
    for (let day = 1; day <= daysInMonth; day++) grid.push(new Date(y, m, day));
    while (grid.length % 7 !== 0) grid.push(null);

    const today = new Date();
    const todayYMD = toYMD(today);

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

          // availability-month info
          const av = state.monthAvailability?.[ymd];
          const isFull = av ? !!av.disabled : false;

          const disabled = isPast || isFull || !isAllEmployeesSelected();
          const badge = av && !av.disabled ? `<span class="bb-cal__badge">${av.slots ?? 0}</span>` : '';

          return `
            <button type="button"
              class="bb-cal__cell ${isSelected ? 'is-selected' : ''} ${disabled ? 'is-disabled' : ''}"
              data-date="${ymd}"
              ${disabled ? 'disabled' : ''}>
              <span class="bb-cal__day">${cell.getDate()}</span>
              ${badge}
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
        state.selectedDate = null;
        state.selectedHour = null;
        elDateInput.value = '';
        if (elHourSelect) {
          elHourSelect.innerHTML = `<option value="">Selecciona una fecha</option>`;
          elHourSelect.disabled = true;
        }
        await fetchMonthAvailabilityIfReady();
        renderCalendar();
        updateSubmitState();
      });
    });

    elCalendar.querySelectorAll('.bb-cal__cell[data-date]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const ymd = btn.getAttribute('data-date');
        state.selectedDate = ymd;
        elDateInput.value = ymd;
        state.selectedHour = null;

        renderCalendar();
        await fetchHorasDisponibles();
        updateSubmitState();
      });
    });
  }
  function setItemsParams(url, items) {
  // limpia cualquier items anterior
  [...url.searchParams.keys()].forEach(k => {
    if (k === 'items' || k.startsWith('items[')) url.searchParams.delete(k);
  });

  items.forEach((it, i) => {
    url.searchParams.set(`items[${i}][id_servicio]`, String(it.id_servicio));
    url.searchParams.set(`items[${i}][id_empleado]`, String(it.id_empleado ?? ''));
    url.searchParams.set(`items[${i}][orden]`, String(it.orden ?? (i + 1)));
  });
}


  async function fetchMonthAvailabilityIfReady() {
    // Solo si ya eligió empleados (tu regla)
    state.monthAvailability = null;
    if (!isAllEmployeesSelected()) return;

    const mk = monthKey(state.calendarMonth);
    const base = ctx?.urls?.month || '/agendar-cita/availability-month';
const url = new URL(base, window.location.origin);

url.searchParams.set('month', mk);
setItemsParams(url, state.items);


    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
    if (!res.ok) return;
    const data = await res.json();

    // esperamos: { ok:true, days: { 'YYYY-MM-DD': {disabled:boolean, slots:number} } }
    if (data?.ok && data?.days) {
      state.monthAvailability = data.days;
    }
  }

  async function fetchHorasDisponibles() {
    if (!elHourSelect) return;
    if (!state.selectedDate || !isAllEmployeesSelected()) {
      elHourSelect.disabled = true;
      elHourSelect.innerHTML = `<option value="">Selecciona una fecha</option>`;
      return;
    }

    elHourSelect.disabled = true;
    elHourSelect.innerHTML = `<option value="">Cargando horas...</option>`;

    const base = ctx?.urls?.horas || '/agendar-cita/horas-disponibles';
const url = new URL(base, window.location.origin);

url.searchParams.set('fecha', state.selectedDate);
setItemsParams(url, state.items);


    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
    if (!res.ok) {
      elHourSelect.innerHTML = `<option value="">No se pudieron cargar horas</option>`;
      return;
    }
    const data = await res.json();
    const horas = Array.isArray(data?.horas) ? data.horas : [];

    if (!horas.length) {
      elHourSelect.innerHTML = `<option value="">No hay horas disponibles</option>`;
      elHourSelect.disabled = true;
      return;
    }

    elHourSelect.innerHTML = [`<option value="">Selecciona hora</option>`]
      .concat(horas.map(h => `<option value="${h}">${h}</option>`))
      .join('');

    elHourSelect.disabled = false;

    elHourSelect.onchange = () => {
      state.selectedHour = elHourSelect.value || null;
      updateSubmitState();
    };
  }

  async function onItemsChanged() {
    normalizeOrder();
    renderSelected();

    // reset datetime because schedule changed
    state.selectedDate = null;
    state.selectedHour = null;
    if (elDateInput) elDateInput.value = '';
    if (elHourSelect) {
      elHourSelect.innerHTML = `<option value="">Selecciona una fecha</option>`;
      elHourSelect.disabled = true;
    }

    await fetchMonthAvailabilityIfReady();
    renderCalendar();
    updateSubmitState();
  }

  // Init
  function init() {
    // Servicio inicial
    const initialId = Number(ctx.servicioInicialId || 0);
    if (initialId > 0 && serviceById(initialId)) {
      state.items = [{ id_servicio: initialId, id_empleado: null, orden: 1 }];
    } else {
      state.items = [];
    }

    // Calendario arranca en mes actual
    state.calendarMonth = new Date();
    state.monthAvailability = null;

    renderCategories();
    renderServiceCards();
    renderSelected();
    renderCalendar();
    renderDatetimeLock();
    updateSubmitState();

    // Evitar submit si falta algo (UX)
    form?.addEventListener('submit', (e) => {
      if (!canSubmit()) {
        e.preventDefault();
        alert('Completa: empleado(s), fecha y hora antes de solicitar la cita.');
      }
    });
  }
  function ensureStateHasService(id_servicio){
  id_servicio = Number(id_servicio || 0);
  if (!id_servicio) return null;

  let it = state.items.find(x => Number(x.id_servicio) === id_servicio);
  if (!it) {
    state.items.push({ id_servicio, id_empleado: null, orden: state.items.length + 1 });
    normalizeOrder();
    it = state.items.find(x => Number(x.id_servicio) === id_servicio);
  }
  return it;
}

// ✅ Escucha cambios en CUALQUIER select dentro de los servicios seleccionados
elSelectedList?.addEventListener('change', async (e) => {
  const sel = e.target.closest('select');
  if (!sel) return;

  // buscamos el article que tenga data-service-id (como en tu Blade)
  const card = sel.closest('[data-service-id]');
  if (!card) return;

  const serviceId = Number(card.getAttribute('data-service-id') || 0);
  const empId = Number(sel.value || 0) || null;

  const it = ensureStateHasService(serviceId);
  if (!it) return;

  it.id_empleado = empId;

  // reset datetime al cambiar empleados
  state.selectedDate = null;
  state.selectedHour = null;
  if (elDateInput) elDateInput.value = '';
  if (elHourSelect) {
    elHourSelect.innerHTML = `<option value="">Selecciona una fecha</option>`;
    elHourSelect.disabled = true;
  }

  // 🔥 esto es lo que te faltaba: forzar que se quite el lock y se re-renderice calendar
  renderDatetimeLock();
  await fetchMonthAvailabilityIfReady();
  renderCalendar();
  updateSubmitState();
});


  init();
})();
