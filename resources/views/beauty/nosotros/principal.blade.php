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

        {{-- 03 · Nuestra Historia --}}
        <section class="ns-section" id="ns-section-3">
            <div class="ns-section__img-wrap">
                <img src="{{ asset('images/sucursal/16copia.webp') }}"
                     alt="Equipo Beauty Bonita Studio"
                     loading="lazy">
            </div>
            <div class="ns-section__content">
                <span class="ns-section__number">03</span>
                <p class="ns-section__label">Nuestra Historia</p>
                <h2 class="ns-section__title">
                    <span>Nacimos para</span>
                    <span>Transformar</span>
                </h2>
                <p class="ns-section__text">
                    Beauty Bonita Studio nació en <strong>Aguascalientes</strong> con una sola
                    misión: hacer que cada persona que cruce nuestra puerta salga sintiéndose
                    su mejor versión. Desde nuestros primeros años, construimos un espacio donde
                    la <strong>técnica profesional y el trato humano</strong> van de la mano.
                    <br><br>
                    Hoy, con más de <strong>5 años de experiencia</strong>, más de
                    <strong>1,200 servicios realizados</strong> y una calificación de
                    <strong>4.9 estrellas</strong> en Google, seguimos apostando por la
                    formación continua y las últimas tendencias internacionales para ofrecerte
                    siempre lo mejor.
                </p>
                <a href="{{ url('/agendar-cita') }}" class="ns-section__cta">
                    Conocernos en persona
                    <svg width="20" height="14" viewBox="0 0 24 16" fill="none" aria-hidden="true">
                        <path d="M16 0 14.59 1.41 20.17 7H0v2h20.17l-5.58 5.59L16 16l8-8-8-8z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
        </section>

        {{-- 04 · Masajes de Alta Tecnología --}}
        <section class="ns-section" id="ns-section-4">
            <div class="ns-section__img-wrap">
                <img src="{{ asset('images/servicios/masaje.jpg') }}"
                     alt="Masajes de Alta Tecnología"
                     loading="lazy">
            </div>
            <div class="ns-section__content">
                <span class="ns-section__number">04</span>
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
            <li class="ns-nav__item"><a href="#ns-section-4">04</a></li>
        </ul>
        <div class="ns-nav__bar-wrap" aria-hidden="true">
            <div class="ns-nav__bar"></div>
        </div>
    </nav>

</div>{{-- /.ns-wrapper --}}

{{-- ── Sección Equipo ──────────────────────────── --}}
<section class="ns-team" id="ns-equipo">
    <div class="ns-team__header">
        <span class="ns-team__kicker">El equipo</span>
        <h2 class="ns-team__title">Quienes nos hacen brillar</h2>
    </div>
    <div class="ns-team__grid">
        <div class="ns-team__card">
            <div class="ns-team__img-wrap">
                <img src="{{ asset('empleadas/carla.jpg') }}" alt="Carla — Especialista en cabello" loading="lazy">
            </div>
            <p class="ns-team__name">Carla</p>
            <p class="ns-team__role">Especialista en Cabello</p>
        </div>
        <div class="ns-team__card">
            <div class="ns-team__img-wrap">
                <img src="{{ asset('empleadas/lucia.jpg') }}" alt="Lucía — Nail artist" loading="lazy">
            </div>
            <p class="ns-team__name">Lucía</p>
            <p class="ns-team__role">Nail Artist</p>
        </div>
        <div class="ns-team__card">
            <div class="ns-team__img-wrap">
                <img src="{{ asset('empleadas/maria.jpg') }}" alt="María — Especialista en cejas y pestañas" loading="lazy">
            </div>
            <p class="ns-team__name">María</p>
            <p class="ns-team__role">Cejas & Pestañas</p>
        </div>
        <div class="ns-team__card">
            <div class="ns-team__img-wrap">
                <img src="{{ asset('empleadas/sofia.jpg') }}" alt="Sofía — Maquilladora profesional" loading="lazy">
            </div>
            <p class="ns-team__name">Sofía</p>
            <p class="ns-team__role">Maquilladora Profesional</p>
        </div>
    </div>
</section>

<style>
    .ns-team {
        padding: 80px 20px 100px;
        background: #fafaf8;
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
        text-align: center;
    }

    .ns-team__header {
        margin-bottom: 56px;
    }

    .ns-team__kicker {
        display: block;
        font-size: 0.75rem;
        letter-spacing: 4px;
        color: #8e6708;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .ns-team__title {
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        color: #11141c;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin: 0;
    }

    .ns-team__grid {
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 32px;
    }

    .ns-team__card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .ns-team__img-wrap {
        width: 180px;
        height: 220px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .ns-team__img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .ns-team__card:hover .ns-team__img-wrap img {
        transform: scale(1.05);
    }

    .ns-team__name {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #11141c;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .ns-team__role {
        margin: 0;
        font-size: 0.78rem;
        color: #8e6708;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    @media (max-width: 768px) {
        .ns-team__grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        .ns-team__img-wrap {
            width: 140px;
            height: 170px;
        }
    }

    @media (max-width: 400px) {
        .ns-team__grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }
</style>

@push('scripts')
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/nosotros-animations.js') }}"></script>
@endpush
