@push('styles')
    @once
        <link rel="stylesheet" href="{{ asset('css/galeriahero.css') }}">
    @endonce
@endpush

<div class="hero-wrapper">
    <div class="hero-wrapper-element">
        <div class="hero-intro">
            <h1 class="bb-title">
                <span class="bb-title__kicker">Salon </span>
                <span class="bb-title__main">Beauty Bonita</span>
            </h1>
            <p> De belleza</p>
        </div>

        <div class="hero-content">
            <section class="hero-section hero-main-section"></section>
        </div>

        <div class="hero-image-container">
        </div>
    </div>
</div>

@push('scripts')
    @once
        {{-- Librerías SOLO una vez aunque incluyas varias vistas --}}
        <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>
    @endonce

    <script src="{{ asset('js/galeriahero.js') }}"></script>
@endpush
