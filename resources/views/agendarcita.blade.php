{{-- resources/views/agendarcita.blade.php --}}
@extends('layouts.website')

@section('title', 'Agendar cita - Beauty Bonita Studio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/agendarcita.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('js/agendarcita.js') }}" defer></script>
@endpush

@section('content')
  @include('beauty.partials.whatsapp-icon')
  @include('beauty.partials.header')

  @php
    $servicios = $servicios ?? collect();
    $principal = $servicioSeleccionado ?? null;

    $fallbackImg = asset('images/Beige Blogger Moderna Personal Sitio web.png');

    $imgSrc = $fallbackImg;
    if ($principal && $principal->imagen_url) {
      $imgSrc = $principal->imagen_url;
    }

    $features = [];
    if ($principal && $principal->caracteristicas) {
        $decoded = json_decode($principal->caracteristicas, true);
        $features = is_array($decoded) ? $decoded : [];
    }

    $precioFinal = $principal ? max(0, (float)$principal->precio - (float)$principal->descuento) : 0;
  @endphp

  <main class="bb-booking" id="agendarcita">
    <div class="bb-booking__container">

      {{-- ── Header ── --}}
      <header class="bb-booking__header">
        <h1 class="bb-booking__title">Agendar cita</h1>
        <p class="bb-booking__subtitle">Elige tu servicio, fecha y horario. En minutos y sin complicaciones.</p>

        @if(session('success'))
          <p class="bb-alert bb-alert--success">{{ session('success') }}</p>
        @endif
        @if(session('error'))
          <p class="bb-alert bb-alert--error">{{ session('error') }}</p>
        @endif
      </header>

      {{-- ── Stepper ── --}}
      <div class="bb-stepper" id="bbStepper">
        <div class="bb-stepper__step is-active" data-step="1">
          <div class="bb-stepper__dot">
            <svg class="bb-stepper__check" viewBox="0 0 16 16" fill="none"><polyline points="3,8 7,12 13,4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="bb-stepper__num">1</span>
          </div>
          <span class="bb-stepper__label">Servicio</span>
        </div>
        <div class="bb-stepper__line"></div>
        <div class="bb-stepper__step" data-step="2">
          <div class="bb-stepper__dot">
            <svg class="bb-stepper__check" viewBox="0 0 16 16" fill="none"><polyline points="3,8 7,12 13,4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="bb-stepper__num">2</span>
          </div>
          <span class="bb-stepper__label">Fecha & Hora</span>
        </div>
        <div class="bb-stepper__line"></div>
        <div class="bb-stepper__step" data-step="3">
          <div class="bb-stepper__dot">
            <svg class="bb-stepper__check" viewBox="0 0 16 16" fill="none"><polyline points="3,8 7,12 13,4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="bb-stepper__num">3</span>
          </div>
          <span class="bb-stepper__label">Confirmar</span>
        </div>
      </div>

      <form id="bookingForm" method="POST" action="{{ route('agendarcita.store') }}">
        @csrf

        {{-- ── PASO 1: Servicios ── --}}
        <section class="bb-panel bb-selectedPanel bb-step-section" data-step-section="1">
          <div class="bb-panel__titleRow">
            <span class="bb-step-badge">Paso 1</span>
            <h3 class="bb-panel__title" style="margin:0;">Elige tu servicio</h3>
          </div>

          {{-- Categorías --}}
          <div class="bb-formRow" style="align-items:flex-start; margin-top:14px;">
            <div style="width:100%;">
              <p class="bb-label" style="margin-bottom:.5rem;">Categorías</p>
              <div id="bbCategoryList" class="bb-cat__list"></div>
            </div>
          </div>

          {{-- Cards de servicios --}}
          <div style="margin-top:14px;">
            <div id="bbServiceCards" class="bb-svc__cards"></div>
          </div>
        </section>

        {{-- ── Servicios seleccionados ── --}}
        <section class="bb-panel bb-selectedPanel" id="bbSelectedPanel" style="display:none;">
          <div class="bb-panel__titleRow">
            <p class="bb-badge">Tu selección</p>
            <span class="bb-hint" style="margin:0;">Puedes agregar varios servicios.</span>
          </div>

          <div class="bb-selectedList bb-selectedList--compact" id="bbSelectedList">
            @if($principal)
              <article class="bb-selectedCard" data-service-id="{{ $principal->id_servicio }}" data-order="1">
                <div class="bb-selectedCard__media">
                  <img src="{{ $imgSrc }}" alt="{{ $principal->nombre_servicio }}" class="bb-selectedCard__img" loading="lazy"/>
                </div>
                <div class="bb-selectedCard__info">
                  <h2 class="bb-selected__name">{{ $principal->nombre_servicio }}</h2>
                  <ul class="bb-selected__meta">
                    <li><strong>Duración:</strong> {{ (int)$principal->duracion_minutos }} min</li>
                    <li><strong>Desde:</strong> ${{ number_format($precioFinal, 2) }}</li>
                  </ul>
                  <div class="bb-selectedCard__emp" style="margin-top:.65rem;">
                    <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
                    <select class="bb-select" disabled><option>Cargando asignación...</option></select>
                  </div>
                </div>
                <button type="button" class="bb-selectedCard__remove" disabled title="Servicio principal">✕</button>
              </article>
            @else
              <div class="bb-empty" id="bbEmptyState">Selecciona un servicio arriba para iniciar tu cita.</div>
            @endif
          </div>

          <div id="bbItemsHidden"></div>
        </section>

        {{-- ── PASO 2: Fecha y hora ── --}}
        <section class="bb-panel bb-datetime bb-step-section" data-step-section="2">
          <div class="bb-panel__titleRow">
            <span class="bb-step-badge">Paso 2</span>
            <h3 class="bb-panel__title" style="margin:0;">Fecha & Hora</h3>
          </div>

          <div id="bbDatetimeLock" class="bb-lock-msg">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Primero elige un servicio para ver disponibilidad.</span>
          </div>

          <div class="bb-datetimeGrid">
            <div class="bb-datetimeGrid__left">
              <div id="bbCalendar" class="bb-calendar"></div>
              <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ old('fecha_cita', '') }}">
              <input type="hidden" name="hora_cita" id="bbHourInput" value="{{ old('hora_cita', '') }}">
            </div>
            <div class="bb-datetimeGrid__right">
              <div id="bbTimesPanel" class="bb-timesPanel" aria-hidden="true">
                <div class="bb-timesPanel__head">
                  <div>
                    <div class="bb-label" id="bbTimesTitle">Horas disponibles</div>
                    <p class="bb-hint" id="bbTimesHint" style="margin-top:.25rem;">Selecciona una fecha para ver horarios.</p>
                  </div>
                </div>
                <div id="bbTimesGrid" class="bb-timesGrid"></div>
                <p id="bbTimesEmpty" class="bb-hint" style="display:none; margin-top:.75rem;">No hay horas disponibles para ese día.</p>
              </div>
            </div>
          </div>
        </section>

        {{-- ── PASO 3: Confirmar ── --}}
        <section class="bb-panel bb-step-section" data-step-section="3">
          <div class="bb-panel__titleRow">
            <span class="bb-step-badge">Paso 3</span>
            <h3 class="bb-panel__title" style="margin:0;">Confirmar cita</h3>
          </div>

          <div style="margin-top:14px;">
            <p class="bb-label" style="margin-bottom:.5rem;">Observaciones (opcional)</p>
            <textarea class="bb-input" name="observaciones" rows="3"
              placeholder="Ej. Maquillaje natural, alergias, referencias...">{{ old('observaciones', '') }}</textarea>
          </div>

          {{-- Auth gate para invitados --}}
          @guest
          <div class="bb-auth-gate">
            <div class="bb-auth-gate__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div class="bb-auth-gate__text">
              <h4>Inicia sesión para confirmar</h4>
              <p>Crea una cuenta gratuita o inicia sesión. Solo toma un minuto.</p>
            </div>
            <div class="bb-auth-gate__actions">
              <a href="{{ route('login.form') }}" class="bb-btn bb-btn--primary bb-btn--sm">Iniciar sesión</a>
              <a href="{{ route('register.form') }}" class="bb-btn bb-btn--ghost bb-btn--sm">Crear cuenta</a>
            </div>
          </div>
          @endguest
        </section>

        {{-- ── Acción final ── --}}
        <section class="bb-final">
          @auth
          <button type="submit" class="bb-btn bb-btn--primary bb-btn--lg" id="submitBooking" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M20 6 9 17l-5-5"/></svg>
            Solicitar cita
          </button>
          <p class="bb-hint" style="margin-top:10px;" id="bbSubmitHint">Completa todos los pasos para habilitar el botón.</p>
          @else
          <a href="{{ route('login.form') }}" class="bb-btn bb-btn--primary bb-btn--lg">
            Inicia sesión para agendar
          </a>
          @endauth
        </section>

      </form>

    </div>
  </main>

  {{-- Resumen flotante (aparece cuando hay algo seleccionado) --}}
  <div class="bb-float-bar" id="bbFloatBar" aria-hidden="true">
    <div class="bb-float-bar__inner">
      <div class="bb-float-bar__info">
        <span class="bb-float-bar__label">Tu cita</span>
        <span class="bb-float-bar__detail" id="bbFloatDetail">—</span>
      </div>
      <div class="bb-float-bar__total">
        <span class="bb-float-bar__price" id="bbFloatPrice">$0.00</span>
        <span class="bb-float-bar__duration" id="bbFloatDuration">0 min</span>
      </div>
    </div>
  </div>

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

  {{-- ── Stepper + Float bar logic ── --}}
  <script>
  (function () {
    const stepper   = document.getElementById('bbStepper');
    const floatBar  = document.getElementById('bbFloatBar');
    const floatDetail   = document.getElementById('bbFloatDetail');
    const floatPrice    = document.getElementById('bbFloatPrice');
    const floatDuration = document.getElementById('bbFloatDuration');
    const selectedPanel = document.getElementById('bbSelectedPanel');

    if (!stepper) return;

    /* ── Helpers ── */
    function setStep(n) {
      stepper.querySelectorAll('.bb-stepper__step').forEach((s, i) => {
        const step = i + 1;
        s.classList.toggle('is-active', step === n);
        s.classList.toggle('is-done',   step < n);
      });
    }

    function updateFloat() {
      const cards = document.querySelectorAll('#bbSelectedList .bb-selectedCard');
      if (!cards.length) {
        floatBar.classList.remove('is-visible');
        floatBar.setAttribute('aria-hidden', 'true');
        if (selectedPanel) selectedPanel.style.display = 'none';
        return;
      }

      if (selectedPanel) selectedPanel.style.display = '';

      let totalPrice = 0, totalMin = 0;
      const names = [];

      cards.forEach(card => {
        const sid = card.dataset.serviceId;
        const svc = window.__SERVICIOS__?.[sid];
        if (svc) {
          totalPrice += parseFloat(svc.precio || 0);
          totalMin   += parseInt(svc.duracion_minutos || 0, 10);
          names.push(svc.nombre_servicio || '');
        } else {
          const nm = card.querySelector('.bb-selected__name');
          if (nm) names.push(nm.textContent.trim());
        }
      });

      floatDetail.textContent   = names.join(' · ') || '—';
      floatPrice.textContent    = '$' + totalPrice.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      floatDuration.textContent = totalMin + ' min';

      floatBar.classList.add('is-visible');
      floatBar.setAttribute('aria-hidden', 'false');
    }

    /* ── Watch selected list changes ── */
    const selectedList = document.getElementById('bbSelectedList');
    if (selectedList) {
      new MutationObserver(updateFloat).observe(selectedList, { childList: true, subtree: true });
    }

    /* ── Watch date input to advance stepper ── */
    const dateInput = document.getElementById('bbDateInput');
    const hourInput = document.getElementById('bbHourInput');

    function checkProgress() {
      const hasService = document.querySelectorAll('#bbSelectedList .bb-selectedCard').length > 0;
      const hasDate    = dateInput && dateInput.value;
      const hasHour    = hourInput && hourInput.value;

      if (hasDate && hasHour) {
        setStep(3);
      } else if (hasService) {
        setStep(2);
      } else {
        setStep(1);
      }
    }

    if (dateInput) dateInput.addEventListener('change', checkProgress);
    if (hourInput) hourInput.addEventListener('change', checkProgress);

    if (selectedList) {
      new MutationObserver(() => { checkProgress(); updateFloat(); }).observe(selectedList, { childList: true, subtree: true });
    }

    /* ── Submit button hint ── */
    const submitBtn  = document.getElementById('submitBooking');
    const submitHint = document.getElementById('bbSubmitHint');

    if (submitBtn) {
      new MutationObserver(() => {
        if (!submitBtn.disabled && submitHint) {
          submitHint.style.display = 'none';
        }
      }).observe(submitBtn, { attributes: true, attributeFilter: ['disabled'] });
    }

    /* ── Initial state ── */
    updateFloat();
    checkProgress();

    /* ── Pre-selected service (passed from URL) ── */
    @if($principal)
      setTimeout(() => { updateFloat(); checkProgress(); }, 300);
    @endif
  })();
  </script>

  @include('beauty.partials.footer')
@endsection
