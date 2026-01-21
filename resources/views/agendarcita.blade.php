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

                {{-- 1) Servicios seleccionados (principal + extras) --}}
                <section class="bb-panel bb-selected">
                    <p class="bb-badge">Servicios seleccionados</p>

                    <div class="bb-selectedList" id="selectedServicesList">
                        @if($principal)
                            <article class="bb-selectedCard" data-service-id="{{ $principal->id_servicio }}">
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
                                </div>

                                <button type="button" class="bb-selectedCard__remove" disabled title="Servicio principal">
                                    ✓
                                </button>
                            </article>

                            <input type="hidden" name="id_servicio" id="id_servicio_principal" value="{{ $principal->id_servicio }}">
                        @else
                            <p style="opacity:.7; margin: 0;">
                                Selecciona un servicio desde la página de servicios para iniciar tu cita.
                            </p>
                        @endif
                        {{-- JS agregará extras + inputs hidden id_servicios[] --}}
                    </div>
                </section>

                {{-- 2) Agregar un nuevo servicio --}}
                <section class="bb-panel bb-add">
                    <h3 class="bb-panel__title">Agregar otro servicio</h3>

                    <div class="bb-formRow">
                        <div>
                            <label class="bb-label" for="extraService">Servicio</label>
                            <select class="bb-select" id="extraService">
                                <option value="">Selecciona un servicio</option>

                                @foreach($servicios->groupBy(fn($s) => optional($s->grupo)->nombre ?? 'Otros') as $grupoNombre => $items)
                                    <optgroup label="{{ $grupoNombre }}">
                                        @foreach($items as $s)
                                            @continue($principal && $s->id_servicio === $principal->id_servicio)
                                            <option value="{{ $s->id_servicio }}">
                                                {{ $s->nombre_servicio }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="bb-btn bb-btn--ghost" id="addServiceBtn">
                            Añadir
                        </button>
                    </div>

                    <p class="bb-hint">Cada servicio agregado aparecerá arriba con su imagen e información.</p>
                </section>

                {{-- 3) Fecha y hora --}}
                <section class="bb-panel bb-datetime">
                    <h3 class="bb-panel__title">Selecciona fecha y hora</h3>

                    <div class="bb-grid2">
                        <div class="bb-formCol">
                            <label class="bb-label" for="date">Fecha</label>
                            <input class="bb-input" type="date" id="date" name="fecha_cita" required />
                        </div>

                        <div class="bb-formCol">
                            <label class="bb-label" for="time">Hora</label>
                            <input class="bb-input" type="time" id="time" name="hora_cita" required />
                        </div>
                    </div>

                    <p class="bb-hint">Te confirmaremos por correo si hay disponibilidad para ese horario.</p>
                </section>

                {{-- 3.1) Observaciones (opcional) --}}
                <section class="bb-panel">
                    <h3 class="bb-panel__title">Observaciones (opcional)</h3>
                    <textarea class="bb-input" name="observaciones" rows="3" placeholder="Ej. Maquillaje natural, alergias, referencias..."></textarea>
                </section>

                {{-- 4) Acción final --}}
                <section class="bb-final">
                    <button type="submit" class="bb-btn bb-btn--primary" id="submitBooking">
                        Solicitar cita
                    </button>
                </section>
            </form>

        </div>
    </main>

    {{-- ✅ Catálogo para JS (extras por ID) - SIN ParseError --}}
    <script>
        window.__SERVICIOS__ = @json($serviciosJs, JSON_UNESCAPED_UNICODE);
    </script>

    @include('beauty.partials.footer')
@endsection
