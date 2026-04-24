    {{-- ╔══════════════════════════════════════════════╗
         ║  NOSOTROS — SECTIONS + NAV  (closes wrapper)║
         ╚══════════════════════════════════════════════╝ --}}

    {{-- ── Content sections ──────────────────── --}}
    <div class="ns-sections" id="ns-sections">

        {{-- 01 · Promoción de Cumpleaños --}}
        <section class="ns-section" id="ns-section-1">
            <div class="ns-section__img-wrap">
                <img src="{{ asset('images/Peinado2.png') }}"
                     alt="Promoción de Cumpleaños"
                     loading="lazy">
            </div>
            <div class="ns-section__content">
                <span class="ns-section__number">01</span>
                <p class="ns-section__label">Promoción de Cumpleaños</p>
                <h2 class="ns-section__title">Promociones</h2>
                <p class="ns-section__text">
                    ¡Queremos consentirte en tu mes! Celebra con nosotros y disfruta de un
                    <strong>descuento especial en nuestro salón de belleza</strong>. Solo presenta
                    tu identificación y obtén una tarifa preferencial en tu tratamiento favorito:
                    peinados, faciales revitalizantes o un servicio completo de uñas.
                    Regálate la experiencia de lujo que mereces.
                </p>
                <a href="{{ url('/agendar-cita') }}" class="ns-section__cta">
                    Agendar mi cita
                    <svg width="20" height="14" viewBox="0 0 24 16" fill="none" aria-hidden="true">
                        <path d="M16 0 14.59 1.41 20.17 7H0v2h20.17l-5.58 5.59L16 16l8-8-8-8z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
        </section>

        {{-- 02 · Academia y Formación --}}
        <section class="ns-section" id="ns-section-2">
            <div class="ns-section__img-wrap">
                <img src="{{ asset('images/eventos.jpg') }}"
                     alt="Academia y Formación"
                     loading="lazy">
            </div>
            <div class="ns-section__content">
                <span class="ns-section__number">02</span>
                <p class="ns-section__label">Academia y Formación</p>
                <h2 class="ns-section__title">
                    <span>Domina el Arte</span>
                    <span>del Peinado</span>
                </h2>
                <p class="ns-section__text">
                    Lleva tu pasión al siguiente nivel con nuestro
                    <strong>Curso de Peinado Profesional</strong>. Aprende técnicas modernas,
                    las últimas tendencias y los secretos del estilismo directamente de la mano
                    del experto <strong>Pako Rodríguez</strong>. Perfecciona tus habilidades
                    y prepárate para destacar en el mundo de la belleza.
                    <br><br>
                    <em>¡Próximas fechas por anunciarse!</em>
                </p>
                <a href="{{ url('/contacto') }}" class="ns-section__cta">
                    Solicitar información
                    <svg width="20" height="14" viewBox="0 0 24 16" fill="none" aria-hidden="true">
                        <path d="M16 0 14.59 1.41 20.17 7H0v2h20.17l-5.58 5.59L16 16l8-8-8-8z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
        </section>

        {{-- 03 · Masajes de Alta Tecnología --}}
        <section class="ns-section" id="ns-section-3">
            <div class="ns-section__img-wrap">
                <img src="{{ asset('images/servicios/masaje.jpg') }}"
                     alt="Masajes de Alta Tecnología"
                     loading="lazy">
            </div>
            <div class="ns-section__content">
                <span class="ns-section__number">03</span>
                <p class="ns-section__label">Masajes de Alta Tecnología</p>
                <h2 class="ns-section__title">
                    <span>Reactiva tu Cuerpo,</span>
                    <span>Renueva tu Energía</span>
                </h2>
                <p class="ns-section__text">
                    Transforma tu figura y despídete del estrés. Con nuestro exclusivo sistema
                    <strong>Rollaction</strong>, disfrutarás de un masaje profundo que mejora
                    la circulación, elimina líquidos retenidos y
                    <strong>remodela tu silueta</strong> de forma natural.
                    Vive una experiencia de lujo que equilibra tu cuerpo desde la primera sesión.
                </p>
                <a href="{{ url('/agendar-cita') }}" class="ns-section__cta">
                    Agendar mi masaje
                    <svg width="20" height="14" viewBox="0 0 24 16" fill="none" aria-hidden="true">
                        <path d="M16 0 14.59 1.41 20.17 7H0v2h20.17l-5.58 5.59L16 16l8-8-8-8z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
        </section>

    </div>{{-- /.ns-sections --}}

    {{-- ── Fixed side navigation ──────────────── --}}
    <nav class="ns-nav" aria-label="Secciones">
        <ul class="ns-nav__items">
            <li class="ns-nav__item is-active"><a href="#ns-hero">Inicio</a></li>
            <li class="ns-nav__item"><a href="#ns-section-1">01</a></li>
            <li class="ns-nav__item"><a href="#ns-section-2">02</a></li>
            <li class="ns-nav__item"><a href="#ns-section-3">03</a></li>
        </ul>
        <div class="ns-nav__bar-wrap" aria-hidden="true">
            <div class="ns-nav__bar"></div>
        </div>
    </nav>

</div>{{-- /.ns-wrapper --}}

@push('scripts')
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/nosotros-animations.js') }}"></script>
@endpush
