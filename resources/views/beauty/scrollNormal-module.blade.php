@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.18/dist/lenis.css">
@endpush

    <section class="Normal-main-section">
        <main>
            <section class="Normal-hero-section section" id="section-00">
                <div class="Normal-hero-image-wrapper">
                    <img src="{{ asset('images/sucursal/01.png') }}" class="sky" alt="" />
                    <img src="{{ asset('images/sucursal/02.png') }}" class="mountains" alt="" />
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



@push('scripts')
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>

    {{-- Animaciones principales --}}
    <script src="{{ asset('js/script.js') }}"></script>
@endpush
