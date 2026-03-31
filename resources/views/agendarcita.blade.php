{{-- resources/views/agendarcita.blade.php --}}
@extends('layouts.website')

@section('title', 'Agendar cita - Beauty Bonita Studio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/agendarcita.css') }}">

@endpush

@push('scripts')
  <script src="{{ asset('js/agendarcita.js') }}" defer></script>
  <script>
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

      function changeImage(element) {
          const newImageUrl = element.getAttribute('data-image');
          const mainImage = document.getElementById('dynamic-service-image');
          
          if (mainImage.src !== newImageUrl) {
              mainImage.style.opacity = 0; 
              setTimeout(() => {
                  mainImage.src = newImageUrl;
                  mainImage.style.opacity = 1; 
              }, 200); 
          }
      }
  </script>
@endpush

@section('content')
  @include('beauty.partials.whatsapp-icon')
  @include('beauty.partials.header')

  @php
    $servicios = $servicios ?? collect();
    $principal = $servicioSeleccionado ?? null;

    $fallbackImg = asset('images/Beige Blogger Moderna Personal Sitio web.png');

    $imgSrc = $fallbackImg;
    if ($principal && !empty($principal->imagen_url)) {
      $imgSrc = $principal->imagen_url;
    }

    $precioFinal = $principal ? max(0, (float)$principal->precio - (float)$principal->descuento) : 0;
  @endphp

<main class="bb-booking" id="agendarcita">
    
    <header class="bb-booking__header">
      @if(session('success'))
        <p class="bb-note" style="color: green; margin-top:10px;">{{ session('success') }}</p>
      @endif
      @if(session('error'))
        <p class="bb-note" style="color: red; margin-top:10px;">{{ session('error') }}</p>
      @endif
    </header>

    <form id="bookingForm" method="POST" action="{{ route('agendarcita.store') }}">
      @csrf

      {{-- SECCIÓN SUPERIOR: MENÚ DE SERVICIOS (Fuera del layout de 2 columnas) --}}
      <div class="bb-booking__top-section">
          <section class="salon-menu" id="servicios-categorias">
              <div class="salon-menu__header">
                  <span class="salon-menu__eyebrow">Descubre</span>
                  <h2 class="salon-menu__mainTitle">Agregar otro servicio</h2>
              </div>
      
              <div class="salon-menu__layout">
                  <div class="salon-menu__visual">
                      <div class="salon-menu__image-wrapper">
                          <img id="dynamic-service-image" src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Vista previa del servicio">
                      </div>
                  </div>
      
                  <div class="salon-menu__accordion-container" id="menu-scroll-area">
                      @php $coleccionServicios = collect($servicios); @endphp
                      @foreach($categorias as $categoria)
                          @php
                              $catId = is_object($categoria) ? ($categoria->id_categoria ?? $categoria->id ?? null) : ($categoria['id_categoria'] ?? $categoria['id'] ?? null);
                              $serviciosDeCat = $coleccionServicios->filter(function($s) use ($catId) {
                                  $sCatId = is_object($s) ? ($s->id_categoria ?? $s->categoria_id ?? null) : ($s['id_categoria'] ?? $s['categoria_id'] ?? null);
                                  return $sCatId == $catId;
                              });
                          @endphp

                          @if($serviciosDeCat->count() > 0)
                              <div class="accordion-item">
                                  <button type="button" class="accordion-header" onclick="toggleAccordion(this)">
                                      <div class="header-left">
                                          @php
                                              $catImgUrl = (is_object($categoria) ? ($categoria->imagen_url ?? null) : ($categoria['imagen_url'] ?? null)) ?: asset('images/Beige Blogger Moderna Personal Sitio web.png');
                                              $catNombre = is_object($categoria) ? ($categoria->nombre ?? 'Categoría') : ($categoria['nombre'] ?? 'Categoría');
                                          @endphp
                                          <img src="{{ $catImgUrl }}" alt="{{ $catNombre }}" class="category-thumbnail">
                                          <span>{{ $catNombre }}</span>
                                      </div>
                                      <span class="accordion-icon">+</span>
                                  </button>
      
                                  <div class="accordion-content">
                                      <ul class="service-list">
                                          @foreach($serviciosDeCat as $servicio)
                                              @php
                                                  $sImgUrl = (is_object($servicio) ? ($servicio->imagen_url ?? null) : ($servicio['imagen_url'] ?? null)) ?: asset('images/Beige Blogger Moderna Personal Sitio web.png');
                                                  $sNombre = is_object($servicio) ? ($servicio->nombre_servicio ?? 'Servicio') : ($servicio['nombre_servicio'] ?? 'Servicio');
                                                  $sDuracion = is_object($servicio) ? ($servicio->duracion_minutos ?? 0) : ($servicio['duracion_minutos'] ?? 0);
                                                  $sPrecio = is_object($servicio) ? ($servicio->precio ?? 0) : ($servicio['precio'] ?? 0);
                                                  $sId = is_object($servicio) ? ($servicio->id_servicio ?? null) : ($servicio['id_servicio'] ?? null);
                                              @endphp
                                              <li class="service-item" data-image="{{ $sImgUrl }}" onmouseenter="changeImage(this)" onclick="changeImage(this)">
                                                  <div class="service-left">
                                                      <img src="{{ $sImgUrl }}" alt="{{ $sNombre }}" class="service-thumbnail">
                                                      <div class="service-info">
                                                          <h4 class="service-name">{{ $sNombre }}</h4>
                                                          <div class="service-meta">
                                                              <span>{{ (int) $sDuracion }} min</span>
                                                              <span class="meta-divider">|</span>
                                                              <span class="service-price">${{ number_format((float) $sPrecio, 2) }}</span>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="service-action">
                                                      <button type="button" class="service-btn js-add-service-btn" data-service-id="{{ $sId }}">Agregar</button>
                                                  </div>
                                              </li>
                                          @endforeach
                                      </ul>
                                  </div>
                              </div>
                          @endif
                      @endforeach
                  </div>
              </div>
          </section>
      </div>

      {{-- SECCIÓN INFERIOR: LAYOUT DE 2 COLUMNAS (Calendario a la Izq, Tu Reserva a la Der) --}}
      <div class="bb-booking__layout">

        {{-- COLUMNA IZQUIERDA: CALENDARIO --}}
        <div class="bb-booking__main">

          {{-- 1) Fecha y Hora --}}
          <section class="bb-panel">
            <h3 class="bb-panel__title">Selecciona fecha y hora</h3>
            <div id="bbDatetimeLock" style="margin-bottom: 1rem; font-size: 0.85rem; color: #888;">
              Primero selecciona/permite la asignación de empleado para cada servicio para ver disponibilidad.
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
              <div style="flex: 1.5; min-width: 300px;">
                <div id="bbCalendar" class="bb-calendar"></div>
                <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ old('fecha_cita', '') }}">
                <input type="hidden" name="hora_cita" id="bbHourInput" value="{{ old('hora_cita', '') }}">
              </div>

              <div style="flex: 1; min-width: 250px;">
                <div id="bbTimesPanel" class="bb-timesPanel" aria-hidden="true">
                  <div style="margin-bottom: 15px;">
                    <div style="font-weight: 600; color: var(--color-dark);" id="bbTimesTitle">Horas disponibles</div>
                    <p style="font-size: 0.85rem; color: #888; margin-top: 5px;" id="bbTimesHint">Selecciona una fecha para ver horarios.</p>
                  </div>
                  <div id="bbTimesGrid" class="bb-timesGrid"></div>
                  <p id="bbTimesEmpty" style="display:none; font-size: 0.85rem; color: #888; margin-top:.75rem;">No hay horas disponibles para ese día.</p>
                </div>
              </div>
            </div>
          </section>

        </div>

        {{-- COLUMNA DERECHA: CONTENEDOR PARA TARJETA E INSIGNIA --}}
        <div class="bb-booking__right-col">
            
            {{-- TARJETA DE COMPRA --}}
            <aside class="bb-booking__sidebar">
              
              <p class="bb-badge">Tu Reserva</p>
    
              {{-- Servicios seleccionados --}}
              <div class="bb-selectedList" id="bbSelectedList">
                @if($principal)
                  <article class="bb-selectedCard" data-service-id="{{ $principal->id_servicio }}" data-order="1">
                    <div class="bb-selectedCard__media">
                      <img src="{{ $imgSrc }}" alt="{{ $principal->nombre_servicio }}" loading="lazy" />
                    </div>
    
                    <div style="flex: 1;">
                      <h2 class="bb-selected__name">{{ $principal->nombre_servicio }}</h2>
                      <ul class="bb-selected__meta">
                        <li><strong>Duración:</strong> {{ (int)$principal->duracion_minutos }} min</li>
                        <li><strong>Precio:</strong> ${{ number_format($precioFinal, 2) }}</li>
                      </ul>
    
                      <div style="margin-top: 10px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 3px; color: var(--color-dark);">Empleado</label>
                        <select class="bb-select" disabled>
                          <option>Cargando...</option>
                        </select>
                      </div>
                    </div>
    
                    <button type="button" class="bb-selectedCard__remove" disabled title="Servicio principal">✕</button>
                  </article>
                @else
                  <div style="text-align: center; color: #888; font-size: 0.95rem; padding: 20px 0;">
                    Selecciona un servicio para iniciar tu cita.
                  </div>
                @endif
              </div>
    
              <div id="bbItemsHidden"></div>
    
              <p style="font-size: 0.85rem; color: #666; margin-top: 15px; text-align: center;">
                Puedes agregar más servicios.
              </p>
    
              {{-- OBSERVACIONES --}}
              <div style="margin-top: 25px; margin-bottom: 20px;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--color-dark); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Observaciones (opcional)</label>
                <textarea
                  class="bb-input"
                  name="observaciones"
                  rows="2"
                  style="font-size: 0.85rem; padding: 12px; resize: none;"
                  placeholder="Ej. Maquillaje natural, alergias, referencias..."
                >{{ old('observaciones', '') }}</textarea>
              </div>
    
              {{-- Botón de envío --}}
              <button type="submit" class="bb-btn--primary" id="submitBooking" disabled>
                Reservar
              </button>
              
              {{-- Detalle final estilo Airbnb --}}
              <p style="text-align: center; font-size: 0.8rem; color: #666; margin-top: 15px;">
                Aún no se te cobrará nada
              </p>
    
            </aside>

            {{-- INSIGNIA DE ESTRELLAS --}}
            <div class="bb-reviews-badge">
                <div class="bb-reviews-badge__left">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16 2l4.9 10 11.1 1.6-8 7.8 1.9 11L16 26.5l-9.9 5.2 1.9-11-8-7.8L11.1 12 16 2z"></path></svg>
                    <span>Favorito entre<br>nuestros clientes</span>
                </div>
                <div class="bb-reviews-badge__right">
                    <div class="bb-reviews-badge__stat">
                        <span class="bb-reviews-badge__stat-num">4.95</span>
                        <span class="bb-reviews-badge__stat-text">Estrellas</span>
                    </div>
                    <div class="bb-reviews-badge__divider"></div>
                    <div class="bb-reviews-badge__stat">
                        <span class="bb-reviews-badge__stat-num">+500</span>
                        <span class="bb-reviews-badge__stat-text">Citas exitosas</span>
                    </div>
                </div>
            </div>

        </div>

      </div>
    </form>
  </main>

  {{-- Contexto para JS --}}
  <script>
    window.__BOOKING_CTX__ = {
      servicioInicialId: {{ (int)($principal->id_servicio ?? 0) }},
      servicios: @json($serviciosJs ?? new \stdClass(), JSON_UNESCAPED_UNICODE),
      categorias: @json($categorias ?? [], JSON_UNESCAPED_UNICODE),
      empleadosPorServicio: @json($empleadosPorServicio ?? new \stdClass(), JSON_UNESCAPED_UNICODE),
      cargaEmpleados: @json($cargaEmpleados ?? new \stdClass(), JSON_UNESCAPED_UNICODE),
      fallbackImg: @json($fallbackImg),
      assetRoot: @json(rtrim(asset(''), '/')),
      assetStorage: @json(rtrim(asset('storage'), '/')),
      urls: {
        horas: @json(route('agendarcita.horasDisponibles')),
        month: @json(route('agendarcita.availabilityMonth')),
      }
    };
    window.__SERVICIOS__ = window.__BOOKING_CTX__.servicios;
  </script>
<script>
  (() => {
  const ctx = window.__BOOKING_CTX__ || {};

  const serviciosMap = ctx.servicios || {}; 
  const categorias = Array.isArray(ctx.categorias) ? ctx.categorias : [];

  // empleados por servicio + carga para balanceo
  const empleadosPorServicio = ctx.empleadosPorServicio || {}; 
  const cargaEmpleados = ctx.cargaEmpleados || {}; 

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
    calendarMonth: new Date(), 
    monthAvailability: null, 
  };

  // Utils
  const pad2 = (n) => String(n).padStart(2, '0');

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
    const fallback = (ctx && ctx.fallbackImg) ? ctx.fallbackImg : '';
    const img = svc?.imagen_url || svc?.imagen; // Soporte para imagen_url

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
    [...url.searchParams.keys()].forEach(k => {
      if (k === 'items' || k.startsWith('items[')) url.searchParams.delete(k);
    });

    items.forEach((it, i) => {
      url.searchParams.set(`items[${i}][id_servicio]`, String(it.id_servicio));
      url.searchParams.set(`items[${i}][id_empleado]`, String(it.id_empleado ?? ''));
      url.searchParams.set(`items[${i}][orden]`, String(it.orden ?? (i + 1)));
    });
  }

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

  // ---------- ✅ NUEVO: Integración con el menú acordeón Blade ----------
  function syncAccordionButtons() {
    // Busca todos los botones de "Agregar" en el menú acordeón
    document.querySelectorAll('.js-add-service-btn').forEach(btn => {
      const id = Number(btn.getAttribute('data-service-id'));
      const already = state.items.some(it => Number(it.id_servicio) === id);

      if (already) {
        btn.textContent = 'Agregado';
        btn.disabled = true;
        btn.style.backgroundColor = '#8e6708';
        btn.style.color = '#ffffff';
        btn.style.cursor = 'default';
      } else {
        btn.textContent = 'Agregar';
        btn.disabled = false;
        btn.style.backgroundColor = 'transparent';
        btn.style.color = '#8e6708';
        btn.style.cursor = 'pointer';
      }
    });
  }

  function bindAccordionEvents() {
    document.querySelectorAll('.js-add-service-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation(); // Evita que se dispare algo más del acordeón
        
        const id = Number(btn.getAttribute('data-service-id'));
        if (!id) return;

        // Si ya está agregado, ignorar
        if (state.items.some(it => Number(it.id_servicio) === id)) return;

        const empDefault = pickDefaultEmpleado(id);

        state.items.push({
          id_servicio: id,
          id_empleado: empDefault,
          orden: state.items.length + 1,
        });

        // Refrescar vistas
        onItemsChanged();
      });
    });
  }

  // ---------- UI: categorías antiguas (Mantenido por compatibilidad) ----------
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

  // ---------- UI: cards de servicios antiguas (Mantenido por compatibilidad) ----------
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

    // Quitar servicio
    elSelectedList.querySelectorAll('.bb-selectedCard__remove').forEach(btn => {
      btn.addEventListener('click', async () => {
        const card = btn.closest('.bb-selectedCard');
        const idx = Number(card?.getAttribute('data-index') || -1);
        if (idx < 0) return;
        if (state.items.length === 1) return; // No permitir quitar el último

        state.items.splice(idx, 1);
        onItemsChanged();
      });
    });

    // Cambiar empleado
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

    // ✅ Actualiza los botones de agregar/agregado en el menú acordeón
    syncAccordionButtons(); 

    // ✅ Actualiza los botones de agregar/agregado del antiguo diseño si aún existiera
    renderServiceCards(); 

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
    const startDow = (first.getDay() + 6) % 7; 
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
    const initialId = Number(ctx.servicioInicialId || 0);
    if (initialId > 0 && serviceById(initialId)) {
      const empDefault = pickDefaultEmpleado(initialId);
      state.items = [{ id_servicio: initialId, id_empleado: empDefault, orden: 1 }];
    } else {
      state.items = [];
    }

    state.calendarMonth = new Date();
    state.monthAvailability = null;

    // ✅ Activadores para el nuevo menú Acordeón
    bindAccordionEvents();
    syncAccordionButtons();

    // Mantener esto para compatibilidad si alguna vez se reutiliza ese elemento
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
</script>
  @include('beauty.partials.footer')
@endsection