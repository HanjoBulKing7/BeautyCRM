@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.18/dist/lenis.css">
@endpush

<section class="Normal-header-section">
        <header class="Normal-header">
            <nav class="Normal-navbar">
                <div class="Normal-header-container">
                    <a href="#" class="Normal-brand">
                        <!-- Cambié el SVG por una imagen de logo -->
                        <img src="{{ asset('iconos/logo.png') }}" alt="Beauty Bonita Logo">
                    </a>
                    <div class="Normal-burger">
                        <div class="Normal-burger-line-wrapper">
                            <span class="Normal-burger-line"></span>
                            <span class="Normal-burger-line"></span>
                            <span class="Normal-burger-line"></span>
                        </div>
                    </div>
                    <div class="Normal-menu">
                        <div class="Normal-menu-header">
                            <a href="#" class="Normal-brand">
                                <img src="imagenes/logo.png" alt="Beauty Bonita Logo">
                            </a>
                            <div class="Normal-burger is-active Normal-close-menu">
                                <div class="Normal-burger-line-wrapper">
                                    <span class="Normal-burger-line"></span>
                                    <span class="Normal-burger-line"></span>
                                    <span class="Normal-burger-line"></span>
                                </div>
                            </div>
                        </div>
                        <ul class="Normal-menu-inner">
                            <li class="Normal-menu-item">
                                <a href="#" class="Normal-menu-link">Servicios</a>
                            </li>
                            <li class="Normal-menu-item">
                                <a href="#" class="Normal-menu-link">Nosotros</a>
                            </li>
                            <li class="Normal-menu-item">
                                <a href="#" class="Normal-menu-link">Galería</a>
                            </li>
                            <li class="Normal-menu-item">
                                <a href="#" class="Normal-menu-link">Contacto</a>
                            </li>
                        </ul>
                    </div>
                    <div class="Normal-menu-block">
                        <a href="#" class="Normal-menu-block-link">
                            <i class="bx bx-user-circle"></i>
                            Reservar Cita
                        </a>
                    </div>
                </div>
            </nav>
            <div class="Normal-header-backdrop"></div>
        </header>
    </section>

    <section class="Normal-main-section">
        <main>
            <section class="Normal-hero-section section" id="section-00">
                <div class="Normal-hero-image-wrapper">
                    <img src="{{ asset('images/edited-photo (1).png') }}" class="sky" alt="" />
                    <img src="{{ asset('images/edited-photo (1).png') }}" class="mountains" alt="" />
                    <img src="{{ asset('images/edited-photo.png') }}" class="man-standing" alt="" />
                </div>
                <div class="Normal-hero-content">
                    <h5 class="Normal-hero-subtitle">Belleza Excepcional</h5>
                    <h1 class="Normal-hero-title">
                        <span>Beauty Bonita</span> <br />
                        <span>Tu Salón de Confianza</span>
                    </h1>
                    <a href="#section-01" class="Normal-hero-action">
                        Descubre más
                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 16L14.59 14.59L9 20.17V0H7V20.17L1.42 14.58L0 16L8 24L16 16Z" fill="currentColor"></path>
                        </svg>
                    </a>
                </div>
            </section>

            <section class="Normal-content-section section">
                <div class="container">
                    <div class="Normal-content-wrapper" id="section-01">
                        <div class="Normal-content-row">
                            <div class="Normal-content-image">
                                <img src="{{ asset('images/Faciales.jpg') }}" alt="Maquillaje Profesional" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">01</span>
                                    Maquillaje Profesional
                                </h5>
                                <h2 class="Normal-content-title">Realza tu Belleza Natural</h2>
                                <p class="Normal-content-copy">
                                    Resaltamos tu belleza con maquillaje profesional diseñado para tu estilo,
                                    ocasión y rasgos. Utilizamos productos de alta calidad y técnicas actuales
                                    para lograr un acabado impecable, elegante y duradero. Perfecto para eventos,
                                    sesiones fotográficas o un look sofisticado para tu día a día.
                                </p>
                                <a href="#" class="Normal-content-action">
                                    Ver más
                                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 -6.99382e-07L14.59 1.41L20.17 7L-3.93402e-07 7L-3.0598e-07 9L20.17 9L14.58 14.58L16 16L24 8L16 -6.99382e-07Z" fill="#b8860b" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="Normal-content-wrapper" id="section-02">
                        <div class="Normal-content-row">
                            <div class="Normal-content-image">
                                <img src="{{ asset('images/Faciales.jpg') }}" alt="Peinados Profesionales" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">02</span>
                                    Estilo y Color
                                </h5>
                                <h2 class="Normal-content-title">
                                    <span>Peinados Profesionales</span>
                                    <span>y Color Personalizado</span>
                                </h2>
                                <p class="Normal-content-copy">
                                    Transformamos tu estilo con peinados diseñados para realzar tus rasgos y tu
                                    personalidad, complementados con técnicas de coloración profesional que aportan
                                    luminosidad, dimensión y un acabado totalmente personalizado. Desde tonos
                                    naturales hasta cambios atrevidos, trabajamos con productos de alta calidad
                                    para lograr un look vibrante, elegante y hecho a tu medida.
                                </p>
                                <a href="#" class="Normal-content-action">
                                    Ver más
                                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 -6.99382e-07L14.59 1.41L20.17 7L-3.93402e-07 7L-3.0598e-07 9L20.17 9L14.58 14.58L16 16L24 8L16 -6.99382e-07Z" fill="#b8860b" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="Normal-content-wrapper" id="section-03">
                        <div class="Normal-content-row">
                            <div class="Normal-content-image">
                                <img src="{{ asset('images/Faciales.jpg') }}" alt="Tratamientos Capilares" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">03</span>
                                    Cuidado Avanzado
                                </h5>
                                <h2 class="Normal-content-title">
                                    <span>Tratamientos de</span>
                                    <span>Cabello Saludable</span>
                                </h2>
                                <p class="Normal-content-copy">
                                    Nuestros tratamientos capilares están diseñados para restaurar, fortalecer y
                                    revitalizar tu cabello desde la raíz hasta las puntas. Utilizamos productos de
                                    alta calidad que hidratan profundamente, reparan el daño y mejoran la textura,
                                    dejándolo más suave, brillante y manejable. Ideal para quienes buscan renovar
                                    la salud de su cabello y lucir un acabado elegante y lleno de vida.
                                </p>
                                <a href="#" class="Normal-content-action">
                                    Ver más
                                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 -6.99382e-07L14.59 1.41L20.17 7L-3.93402e-07 7L-3.0598e-07 9L20.17 9L14.58 14.58L16 16L24 8L16 -6.99382e-07Z" fill="#b8860b" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <nav class="Normal-slider">
                <div class="container">
                    <ul class="Normal-slider-list">
                        <li class="Normal-slider-list-item">
                            <a href="#section-00">Inicio</a>
                        </li>
                        <li class="Normal-slider-list-item">
                            <a href="#section-01">01</a>
                        </li>
                        <li class="Normal-slider-list-item">
                            <a href="#section-02">02</a>
                        </li>
                        <li class="Normal-slider-list-item">
                            <a href="#section-03">03</a>
                        </li>
                    </ul>
                    <div class="Normal-slider-progress">
                        <div class="Normal-slider-progress-bar" style="height: 20%;"></div>
                    </div>
                </div>
            </nav>
        </main>
    </section>

    <section class="Normal-footer-section">
        <footer class="Normal-footer">
            <div class="container">
                <div class="Normal-footer-row">
                    <div class="Normal-footer-column Normal-footer-column-logo">
                        <div class="Normal-footer-logo">
                            <a href="#">
                                <img src="imagenes/logo.png" alt="Beauty Bonita Logo">
                            </a>
                        </div>
                        <div class="Normal-footer-copy">
                            Descubre tu belleza única & transforma <br>tu estilo con nuestros servicios profesionales!
                        </div>
                        <div class="Normal-footer-copy-rights">© 2024 Beauty Bonita. Todos los derechos reservados.</div>
                    </div>
                    <div class="Normal-footer-column Normal-footer-column-link">
                        <h4 class="Normal-footer-heading">Nuestros Servicios</h4>
                        <ul class="Normal-footer-links-list">
                            <li class="Normal-footer-links-item">
                                <a href="#">Maquillaje Profesional</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Corte y Peinado</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Coloración</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Tratamientos Capilares</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Manicure y Pedicure</a>
                            </li>
                        </ul>
                    </div>
                    <div class="Normal-footer-column Normal-footer-column-link">
                        <h4 class="Normal-footer-heading">Contacto</h4>
                        <ul class="Normal-footer-links-list">
                            <li class="Normal-footer-links-item">
                                <a href="#">Reservar Cita</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Horarios</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Ubicación</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="#">Promociones</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </section>

@push('scripts')
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>

    {{-- Animaciones principales --}}
    <script src="{{ asset('js/script.js') }}"></script>
@endpush
