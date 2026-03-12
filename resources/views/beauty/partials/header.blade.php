@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hero.css') }}">
@endpush

<section class="Normal-header-section">
    <header class="Normal-header is-visible" id="NormalHeader">
        <nav class="Normal-navbar">
            <div class="Normal-header-container">

                {{-- Logo (izquierda) --}}
                <a href="{{ url('/home') }}" class="Normal-brand" aria-label="Beauty Bonita - Inicio">
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
                        <a href="{{ url('/home') }}" class="Normal-brand" aria-label="Beauty Bonita - Inicio">
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
                            <a href="{{ url('/home') }}" class="Normal-menu-link {{ request()->is('home') ? 'active' : '' }}">Inicio</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="{{ url('/servicio') }}" class="Normal-menu-link {{ request()->is('servicio') ? 'active' : '' }}">Servicios</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="{{ url('/nosotros') }}" class="Normal-menu-link {{ request()->is('nosotros') ? 'active' : '' }}">Nosotros</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="{{ url('/galeria') }}" class="Normal-menu-link {{ request()->is('galeria') ? 'active' : '' }}">Galería</a>
                        </li>
                    </ul>

                    {{-- CTA al fondo (solo móvil) --}}
                    <div class="Normal-menu-footer">
                        @auth
                            <div class="Normal-user-dropdown Normal-user-dropdown--mobile">
                                <button
                                    type="button"
                                    class="Normal-menu-footer-cta Normal-user-trigger"
                                    aria-expanded="false"
                                    aria-controls="NormalUserMenuMobile"
                                >
                                    <i class="bx bx-user-circle"></i>
                                    <span class="Normal-user-name">{{ Auth::user()->name }}</span>
                                    <i class="bx bx-chevron-down Normal-user-chevron" aria-hidden="true"></i>
                                </button>

                                <div id="NormalUserMenuMobile" class="Normal-user-menu" role="menu">
                                    <a href="{{ url('/mis-reservas') }}" class="Normal-user-item" role="menuitem">
                                        <i class="bx bx-calendar"></i>
                                        Ver reservas
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}" class="Normal-user-form">
                                        @csrf
                                        <button type="submit" class="Normal-user-item Normal-user-logout" role="menuitem">
                                            <i class="bx bx-log-out"></i>
                                            Cerrar sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="Normal-menu-footer-cta {{ request()->is('login') ? 'active' : '' }}">
                                <i class="bx bx-user-circle"></i>
                                Agendar cita
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- CTA derecha (solo desktop) --}}
                <div class="Normal-menu-block">
                    @auth
                        <div class="Normal-user-dropdown">
                            <button
                                type="button"
                                class="Normal-menu-block-link Normal-user-trigger"
                                aria-expanded="false"
                                aria-controls="NormalUserMenuDesktop"
                            >
                                <i class="bx bx-user-circle"></i>
                                <span class="Normal-user-name">{{ Auth::user()->name }}</span>
                                <i class="bx bx-chevron-down Normal-user-chevron" aria-hidden="true"></i>
                            </button>

                            <div id="NormalUserMenuDesktop" class="Normal-user-menu" role="menu">
                                <a href="{{ url('/mis-reservas') }}" class="Normal-user-item" role="menuitem">
                                    <i class="bx bx-calendar"></i>
                                    Ver reservas
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="Normal-user-form">
                                    @csrf
                                    <button type="submit" class="Normal-user-item Normal-user-logout" role="menuitem">
                                        <i class="bx bx-log-out"></i>
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="Normal-menu-block-link {{ request()->is('login') ? 'active' : '' }}" data-scroll>
                            <i class="bx bx-user-circle"></i>
                            Agendar cita
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
