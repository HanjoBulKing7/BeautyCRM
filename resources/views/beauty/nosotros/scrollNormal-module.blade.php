@push('styles')
    <link rel="stylesheet" href="{{ asset('css/nosotros.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.18/dist/lenis.css">
@endpush

{{-- ╔══════════════════════════════════════════╗
     ║  NOSOTROS — HERO  (opens .ns-wrapper)   ║
     ╚══════════════════════════════════════════╝ --}}
<div class="ns-wrapper">

    <section class="ns-hero" id="ns-hero">

        {{-- Parallax image layers --}}
        <div class="ns-hero__parallax">
            <img src="{{ asset('images/sucursal/nosotros.webp') }}"
                 class="ns-hero__sky" alt="" loading="eager" fetchpriority="high">
            <img src="{{ asset('images/sucursal/02.png') }}"
                 class="ns-hero__mountains" alt="" loading="eager">
            <img src="{{ asset('images/edited-photo.png') }}"
                 class="ns-hero__person" alt="" loading="eager">
        </div>

        {{-- Bottom gradient veil --}}
        <div class="ns-hero__overlay"></div>

        {{-- Centered hero content --}}
        <div class="ns-hero__content">
            <p class="ns-hero__label">Beauty Bonita Studio</p>
            <img src="{{ asset('iconos/logo.png') }}"
                 alt="Beauty Bonita Logo"
                 class="ns-hero__logo-img"
                 loading="eager">
            <a href="#ns-sections" class="ns-hero__scroll-link">
                <span>Descubrir</span>
                <span class="ns-hero__scroll-line"></span>
            </a>
        </div>

    </section>
{{-- .ns-wrapper is closed in principal.blade.php --}}
