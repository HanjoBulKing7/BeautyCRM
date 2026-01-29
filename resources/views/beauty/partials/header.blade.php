@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hero.css') }}">
@endpush

<section class="Normal-header-section">
    <header class="Normal-header is-visible" id="NormalHeader">
        <nav class="Normal-navbar">
            <div class="Normal-header-container">

                {{-- Logo (izquierda) --}}
                <a href="{{ url('/') }}" class="Normal-brand" aria-label="Beauty Bonita - Inicio">
                    <img src="{{ asset('iconos/logo.png') }}" alt="Beauty Bonita Logo">
                </a>

                {{-- Burger (derecha) --}}
                <button class="Normal-burger" type="button" aria-label="Abrir menú" aria-expanded="false">
                    <div class="Normal-burger-line-wrapper" aria-hidden="true">
                        <span class="Normal-burger-line"></span>
                        <span class="Normal-burger-line"></span>
                        <span class="Normal-burger-line"></span>
                    </div>
                </button>

                {{-- MENU (mobile full screen + desktop inline) --}}
                <div class="Normal-menu" aria-hidden="true">
                    <div class="Normal-menu-header">
                        <a href="{{ url('/') }}" class="Normal-brand" aria-label="Beauty Bonita - Inicio">
                            <img src="{{ asset('iconos/logo.png') }}" alt="Beauty Bonita Logo">
                        </a>

                        <button class="Normal-burger is-active Normal-close-menu" type="button" aria-label="Cerrar menú">
                            <div class="Normal-burger-line-wrapper" aria-hidden="true">
                                <span class="Normal-burger-line"></span>
                                <span class="Normal-burger-line"></span>
                                <span class="Normal-burger-line"></span>
                            </div>
                        </button>
                    </div>

                    <ul class="Normal-menu-inner">
                        <li class="Normal-menu-item">
                            <a href="{{ url('/servicio') }}" class="Normal-menu-link">Servicios</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="#section-02" class="Normal-menu-link">Nosotros</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="{{ url('/galeria') }}" class="Normal-menu-link">Galería</a>
                        </li>
                    </ul>

                    {{-- CTA al fondo (solo móvil) --}}
                    <div class="Normal-menu-footer">
                        @auth
                            <a href="{{ url('/admin/home') }}" class="Normal-menu-footer-cta">
                                <i class="bx bx-user-circle"></i>
                                {{ Auth::user()->name }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="Normal-menu-footer-cta">
                                <i class="bx bx-user-circle"></i>
                                Login
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- CTA derecha (solo desktop) --}}
                <div class="Normal-menu-block">
                    @auth
                        <a href="{{ url('/admin/home') }}" class="Normal-menu-block-link" data-scroll>
                            <i class="bx bx-user-circle"></i>
                            {{ Auth::user()->name }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="Normal-menu-block-link" data-scroll>
                            <i class="bx bx-user-circle"></i>
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <div class="Normal-header-backdrop"></div>
    </header>
</section>

@push('scripts')
    <script src="{{ asset('js/hero.js') }}"></script>
@endpush
