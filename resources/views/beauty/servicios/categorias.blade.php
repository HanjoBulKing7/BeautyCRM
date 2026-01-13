<section class="bb-cats" id="servicios-categorias">
    <div class="bb-cats__container">
        <h2 class="bb-cats__mainTitle">Nuestros servicios</h2>

        {{-- ===================== MAQUILLAJE ===================== --}}
        <div class="bb-cat" id="maquillaje">
            <h3 class="bb-cat__title">Maquillaje</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Maquillaje casual',
                    'Maquillaje social / evento',
                    'Maquillaje de novia',
                    'Maquillaje de quinceañera'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== PEINADO ===================== --}}
        <div class="bb-cat" id="peinado">
            <h3 class="bb-cat__title">Peinado</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Peinado casual',
                    'Ondas / brushing',
                    'Recogido elegante',
                    'Peinado para evento'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== COLOR ===================== --}}
        <div class="bb-cat" id="color">
            <h3 class="bb-cat__title">Color</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Retoque de raíz',
                    'Baño de color',
                    'Mechas / highlights',
                    'Balayage'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== CORTE ===================== --}}
        <div class="bb-cat" id="corte">
            <h3 class="bb-cat__title">Corte</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Corte dama',
                    'Corte + secado',
                    'Fleco / ajuste',
                    'Cambio de look'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== MANICURA ===================== --}}
        <div class="bb-cat" id="manicura">
            <h3 class="bb-cat__title">Manicura</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Manicura clásica',
                    'Gelish',
                    'Acrílico (esculpidas)',
                    'Nail art premium'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== TRATAMIENTOS CAPILARES ===================== --}}
        <div class="bb-cat" id="tratamientos-capilares">
            <h3 class="bb-cat__title">Tratamientos capilares</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Hidratación profunda',
                    'Reparación / nutrición',
                    'Control de frizz',
                    'Brillo y sellado'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== PESTAÑAS (por tamaño) ===================== --}}
        <div class="bb-cat" id="pestañas">
            <h3 class="bb-cat__title">Pestañas</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Chicas (natural)',
                    'Medianas (definidas)',
                    'Grandes (intensas)',
                    'Extra grandes (glam)'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== PESTAÑAS 1x1 ===================== --}}
        <div class="bb-cat" id="pestanas-1x1">
            <h3 class="bb-cat__title">Pestañas 1x1</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    '1x1 chicas (natural)',
                    '1x1 medianas (definidas)',
                    '1x1 grandes (intensas)',
                    '1x1 extra grandes (glam)'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== PEDICURA ===================== --}}
        <div class="bb-cat" id="pedicura">
            <h3 class="bb-cat__title">Pedicura</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Pedicura básica',
                    'Pedicura spa',
                    'Pedicura + gelish',
                    'Pedicura premium'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ===================== FACIALES ===================== --}}
        <div class="bb-cat" id="faciales">
            <h3 class="bb-cat__title">Faciales</h3>

            <div class="bb-cat__grid">
                @foreach ([
                    'Limpieza facial',
                    'Hidratación profunda',
                    'Control de acné',
                    'Glow / rejuvenecimiento'
                ] as $item)
                    <article class="bb-svcCard">
                        <div class="bb-svcCard__img">
                            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="{{ $item }}">
                        </div>
                        <h4 class="bb-svcCard__name">{{ $item }}</h4>
                        <a class="bb-svcCard__link" href="{{ route('agendarcita') }}">Agendar</a>
                    </article>
                @endforeach
            </div>
        </div>

    </div>
</section>
