@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hero.css') }}">
@endpush

<div class="hero-wrapper">
    <div class="hero-wrapper-element">
        <div class="hero-intro">
            <h1>BeautyBonita</h1>
            <p>Salon de belleza</p>
        </div>
        <div class="hero-content">
            <section class="hero-section hero-main-section"></section>
            <section class="hero-about-section">
                <p>
                    sdhsadhkjasbdkjas 
                    jsasjbdjkasbdjkasdnkj 
                    basdjhasdkbaskjd 
                    mmabsdghiasubiucvuwe 
                    kjbdscuewib
                </p>
            </section>
        </div>

        <div class="hero-image-container">
            <img  
                alt="foreground-leaf-image"
            />
        </div>
    </div>
</div>

@push('scripts')
    {{-- GSAP CDN --}}
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>
    <script src="{{ asset('js/hero.js') }}"></script>
@endpush