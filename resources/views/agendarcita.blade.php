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

      <header class="bb-booking__header">
        <h1 class="bb-booking__title">Agendar cita</h1>
        <p class="bb-booking__subtitle">Elige tu servicio, fecha y horario. Nosotros confirmamos disponibilidad.</p>

        @if(session('success'))
          <p class="bb-note">{{ session('success') }}</p>
        @endif

        @if(session('error'))
          <p class="bb-note" style="opacity:.9;">{{ session('error') }}</p>
        @endif
      </header>

      <form id="bookingForm" method="POST" action="{{ route('agendarcita.store') }}">
        @csrf

        {{-- ✅ 1) Servicios seleccionados (ARRIBA, como admin) --}}
        <section class="bb-panel bb-selectedPanel">
          <p class="bb-badge">Servicios seleccionados</p>

          <div class="bb-selectedList bb-selectedList--compact" id="bbSelectedList">
            @if($principal)
              {{-- Placeholder visual (el JS lo re-renderiza al cargar) --}}
              <article class="bb-selectedCard" data-service-id="{{ $principal->id_servicio }}" data-order="1">
                <div class="bb-selectedCard__media">
                  <img
                    src="{{ $imgSrc }}"
                    alt="{{ $principal->nombre_servicio }}"
                    class="bb-selectedCard__img"
                    loading="lazy"
                  />
                </div>

                <div class="bb-selectedCard__info">
                  <h2 class="bb-selected__name">{{ $principal->nombre_servicio }}</h2>

                  <ul class="bb-selected__meta">
                    <li><strong>Duración:</strong> {{ (int)$principal->duracion_minutos }} min</li>
                    <li><strong>Desde:</strong> ${{ number_format($precioFinal, 2) }}</li>
                  </ul>

                  <div class="bb-selectedCard__emp" style="margin-top:.65rem;">
                    <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
                    <select class="bb-select" disabled>
                      <option>Cargando asignación...</option>
                    </select>
                  </div>
                </div>

                <button type="button" class="bb-selectedCard__remove" disabled title="Servicio principal">
                  ✕
                </button>
              </article>
            @else
              <div class="bb-empty">Selecciona un servicio para iniciar tu cita.</div>
            @endif
          </div>

          {{-- ✅ Aquí el JS inyecta los hidden inputs items[] --}}
          <div id="bbItemsHidden"></div>

          <p class="bb-hint" style="margin-top:.75rem;">
            Puedes agregar varios servicios. Te asignamos un empleado automáticamente, pero puedes cambiarlo.
          </p>
        </section>

        {{-- 2) Agregar un nuevo servicio (categorías -> cards) --}}
        <section class="bb-panel bb-add">
          <h3 class="bb-panel__title">Agregar otro servicio</h3>

          <div class="bb-formRow" style="align-items:flex-start;">
            <div style="width:100%;">
              <p class="bb-label" style="margin-bottom:.5rem;">Categorías</p>

              {{-- Chips de categorías --}}
              <div id="bbCategoryList" class="bb-cat__list"></div>

              <p class="bb-hint" style="margin-top:.5rem;">
                Selecciona una categoría para ver servicios.
              </p>
            </div>
          </div>

          {{-- Cards de servicios según categoría --}}
          <div style="margin-top: 1rem;">
            <div id="bbServiceCards" class="bb-svc__cards"></div>
          </div>
        </section>

        {{-- ✅ 3) Fecha y hora (solo calendario + horas) --}}
        <section class="bb-panel bb-datetime">
          <h3 class="bb-panel__title">Selecciona fecha y hora</h3>

          {{-- Lock: hasta que el JS tenga empleado asignado por cada servicio --}}
          <div id="bbDatetimeLock" class="bb-note" style="margin-bottom: 1rem;">
            Primero selecciona/permite la asignación de empleado para cada servicio para ver disponibilidad.
          </div>

          <div class="bb-datetimeGrid">
            {{-- Izquierda: Calendario --}}
            <div class="bb-datetimeGrid__left">
              <div id="bbCalendar" class="bb-calendar"></div>

              {{-- Hidden inputs para POST --}}
              <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ old('fecha_cita', '') }}">
              <input type="hidden" name="hora_cita" id="bbHourInput" value="{{ old('hora_cita', '') }}">
            </div>

            {{-- Derecha: Horas --}}
            <div class="bb-datetimeGrid__right">
              <div id="bbTimesPanel" class="bb-timesPanel" aria-hidden="true">
                <div class="bb-timesPanel__head">
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
              </div>
            </div>
          </div>
        </section>

        {{-- 4) Observaciones --}}
        <section class="bb-panel">
          <h3 class="bb-panel__title">Observaciones (opcional)</h3>
          <textarea
            class="bb-input"
            name="observaciones"
            rows="3"
            placeholder="Ej. Maquillaje natural, alergias, referencias..."
          >{{ old('observaciones', '') }}</textarea>
        </section>

        {{-- 5) Acción final --}}
        <section class="bb-final">
          <button type="submit" class="bb-btn bb-btn--primary" id="submitBooking" disabled>
            Solicitar cita
          </button>
        </section>

      </form>

    </div>
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

  @include('beauty.partials.footer')
@endsection
