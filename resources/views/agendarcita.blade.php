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
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')

    @php
        $servicios = $servicios ?? collect();
        $principal = $servicioSeleccionado ?? null;

        $fallbackImg = asset('images/Beige Blogger Moderna Personal Sitio web.png');

        $imgSrc = $fallbackImg;
        if ($principal && $principal->imagen) {
            $img = $principal->imagen;

            if (\Illuminate\Support\Str::startsWith($img, ['images/', '/images/'])) {
                $imgSrc = asset(ltrim($img, '/'));
            } else {
                $imgSrc = asset('storage/' . ltrim($img, '/'));
            }
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

                {{-- 1) Servicios seleccionados --}}
                <section class="bb-panel bb-selected">
                    <p class="bb-badge">Servicios seleccionados</p>

                    {{-- IMPORTANTE: este contenedor lo va a controlar el JS (cards + empleado por servicio) --}}
                    <div class="bb-selectedList" id="bbSelectedList">

                        {{-- Si viene servicio principal, lo dejamos renderizado para mantener estética inicial --}}
                        @if($principal)
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
                                    <h2 class="bb-selectedCard__name">{{ $principal->nombre_servicio }}</h2>
                                    <ul class="bb-selectedCard__meta">
                                        <li><strong>Duración:</strong> {{ (int)$principal->duracion_minutos }} min</li>
                                        <li><strong>Desde:</strong> ${{ number_format($precioFinal, 2) }}</li>
                                        <li><strong>Incluye:</strong> {{ $features[0] ?? 'Servicio profesional con acabado duradero' }}</li>
                                    </ul>

                                    {{-- ✅ Placeholder para selector de empleado (JS lo reemplaza/inyecta) --}}
                                    <div class="bb-selectedCard__emp" style="margin-top:.75rem;">
                                        <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
                                        <select class="bb-select" disabled>
                                            <option>Selecciona empleado</option>
                                        </select>
                                        <p class="bb-hint" style="margin-top:.35rem;">
                                            Selecciona un empleado para habilitar disponibilidad.
                                        </p>
                                    </div>
                                </div>

                                <button type="button" class="bb-selectedCard__remove" disabled title="Servicio principal">
                                    ✓
                                </button>
                            </article>

                        @else
                            <p style="opacity:.7; margin: 0;">
                                Selecciona un servicio para iniciar tu cita.
                            </p>
                        @endif
                    </div>

                    {{-- ✅ Aquí el JS inyecta los hidden inputs items[] para backend --}}
                    <div id="bbItemsHidden"></div>

                    <p class="bb-hint" style="margin-top: .75rem;">
                        Selecciona un empleado para cada servicio para habilitar calendario y horas.
                    </p>
                </section>

                {{-- 2) Agregar un nuevo servicio (categorías -> cards) --}}
                <section class="bb-panel bb-add">
                    <h3 class="bb-panel__title">Agregar otro servicio</h3>

                    {{-- Categorías como “pestañas/botones” (elegante) --}}
                    <div class="bb-formRow" style="align-items:flex-start;">
                        <div style="width:100%;">
                            <p class="bb-label" style="margin-bottom:.5rem;">Pestañas</p>
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

                {{-- 3) Fecha y hora --}}
                <section class="bb-panel bb-datetime">
                    <h3 class="bb-panel__title">Selecciona fecha y hora</h3>

                    {{-- Lock: hasta elegir empleado por servicio --}}
                    <div id="bbDatetimeLock" class="bb-note" style="margin-bottom: 1rem;">
                        Primero selecciona un empleado para cada servicio para poder ver disponibilidad.
                    </div>

                    {{-- Calendario mensual fijo --}}
                    <div id="bbCalendar" class="bb-calendar"></div>

                    {{-- Fecha (hidden) para POST --}}
                    <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ old('fecha_cita', '') }}">

                    {{-- Hora (select) para POST --}}
                    <div class="bb-formCol" style="margin-top: 1rem;">
                        <label class="bb-label" for="bbHourSelect">Hora</label>
                        <select class="bb-select" id="bbHourSelect" name="hora_cita" disabled>
                            <option value="">Selecciona una fecha</option>
                        </select>
                        <p class="bb-hint" style="margin-top:.5rem;">
                            Solo se muestran horarios válidos según servicios, duración y disponibilidad.
                        </p>
                    </div>
                </section>

                {{-- 3.1) Observaciones (opcional) --}}
                <section class="bb-panel">
                    <h3 class="bb-panel__title">Observaciones (opcional)</h3>
                    <textarea class="bb-input" name="observaciones" rows="3" placeholder="Ej. Maquillaje natural, alergias, referencias..."></textarea>
                </section>

                {{-- 4) Acción final --}}
                <section class="bb-final">
                    <button type="submit" class="bb-btn bb-btn--primary" id="submitBooking" disabled>
                        Solicitar cita
                    </button>
                </section>
            </form>

        </div>
    </main>

    {{-- ✅ Contexto para JS (incluye URLs reales de tus rutas) --}}
    <script>
        window.__BOOKING_CTX__ = {
            servicioInicialId: {{ (int)($principal->id_servicio ?? 0) }},
            servicios: @json($serviciosJs ?? new \stdClass(), JSON_UNESCAPED_UNICODE),
            categorias: @json($categorias ?? [], JSON_UNESCAPED_UNICODE),
            empleados: @json($empleados ?? [], JSON_UNESCAPED_UNICODE),

            fallbackImg: @json($fallbackImg),
            assetRoot: @json(rtrim(asset(''), '/')),
            assetStorage: @json(rtrim(asset('storage'), '/')),

            urls: {
                horas: @json(route('agendarcita.horasDisponibles')),
                month: @json(route('agendarcita.availabilityMonth')),
            }
        };


        // Compatibilidad por si tu JS viejo aún usa __SERVICIOS__
        window.__SERVICIOS__ = window.__BOOKING_CTX__.servicios;
    </script>

    @include('beauty.partials.footer')
@endsection
