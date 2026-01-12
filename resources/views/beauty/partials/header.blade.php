@push('styles')
        <link rel="stylesheet" href="{{ asset('css/hero.css') }}">

@endpush
<section class="Normal-header-section">
    <header class="Normal-header is-visible" id="NormalHeader">
        <nav class="Normal-navbar">
            <div class="Normal-header-container">
                <a href="home" class="Normal-brand" aria-label="Beauty Bonita - Inicio">
                    <img src="{{ asset('iconos/logo.png') }}" alt="Beauty Bonita Logo">
                </a>

                <button class="Normal-burger" type="button" aria-label="Abrir menú" aria-expanded="false">
                    <div class="Normal-burger-line-wrapper" aria-hidden="true">
                        <span class="Normal-burger-line"></span>
                        <span class="Normal-burger-line"></span>
                        <span class="Normal-burger-line"></span>
                    </div>
                </button>

                <div class="Normal-menu" aria-hidden="true">
                    <div class="Normal-menu-header">
                        <a href="#" class="Normal-brand" aria-label="Beauty Bonita - Inicio">
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
                            <a href="servicio" class="Normal-menu-link">Servicios</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="#section-02" class="Normal-menu-link">Nosotros</a>
                        </li>
                        <li class="Normal-menu-item">
                            <a href="galeria" class="Normal-menu-link">Galería</a>
                        </li>
                        
                    </ul>
                </div>

                <div class="Normal-menu-block">
                    <a href="admin/home" class="Normal-menu-block-link" data-scroll>
                        <i class="bx bx-user-circle"></i>
                        Iniciar Sesion
                    </a>
                </div>
            </div>
        </nav>

        <div class="Normal-header-backdrop"></div>
    </header>
</section>
@push('scripts')
  
    <script src="{{ asset('js/hero.js') }}"></script>

@endpush
    