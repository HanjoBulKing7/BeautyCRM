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

    <main class="bb-booking" id="agendarcita">
        <div class="bb-booking__container">

            <header class="bb-booking__header">
                <h1 class="bb-booking__title">Agendar cita</h1>
                <p class="bb-booking__subtitle">Elige tu servicio, fecha y horario. Nosotros confirmamos disponibilidad.</p>
            </header>

            {{-- 1) Servicios seleccionados (principal + extras) --}}
            <section class="bb-panel bb-selected">
                <p class="bb-badge">Servicios seleccionados</p>

                <div class="bb-selectedList" id="selectedServicesList">
                    {{-- Card inicial (simulado) --}}
                    <article class="bb-selectedCard" data-service="Maquillaje">
                        <div class="bb-selectedCard__media">
                            <img
                                src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}"
                                alt="Maquillaje"
                                class="bb-selectedCard__img"
                                loading="lazy"
                            />
                        </div>

                        <div class="bb-selectedCard__info">
                            <h2 class="bb-selectedCard__name">Maquillaje</h2>
                            <ul class="bb-selectedCard__meta">
                                <li><strong>Duración:</strong> 60 min</li>
                                <li><strong>Desde:</strong> $___</li>
                                <li><strong>Incluye:</strong> asesoría y acabado duradero</li>
                            </ul>
                        </div>

                        <button type="button" class="bb-selectedCard__remove" disabled title="Servicio principal">
                            ✓
                        </button>
                    </article>
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
                            <option value="Maquillaje casual">Maquillaje casual</option>
                            <option value="Peinado">Peinado</option>
                            <option value="Cabello (corte y color)">Cabello (corte y color)</option>
                            <option value="Manicura / Gelish">Manicura / Gelish</option>
                            <option value="Facial">Facial</option>
                            <option value="Pestañas">Pestañas</option>
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
                        <input class="bb-input" type="date" id="date" />
                    </div>

                    <div class="bb-formCol">
                        <label class="bb-label" for="time">Hora</label>
                        <input class="bb-input" type="time" id="time" />
                    </div>
                </div>

                <p class="bb-hint">Te confirmaremos por correo si hay disponibilidad para ese horario.</p>
            </section>

            {{-- 4) Acción final --}}
            <section class="bb-final">
                <button type="button" class="bb-btn bb-btn--primary" id="submitBooking">
                    Solicitar cita
                </button>

                <p class="bb-note" id="bookingNote" hidden>
                    Hemos recibido tu solicitud. En breve recibirás un correo para confirmar disponibilidad en la fecha y hora seleccionadas.
                </p>
            </section>

        </div>
    </main>

    @include('beauty.partials.footer')
@endsection
