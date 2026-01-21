@push('styles')
    <link rel="stylesheet" href="{{ asset('css/horizontal.css') }}">
@endpush

    <section class="horizontal-main-wrapper">
      <div class="horizontal-section">
        <div class="horizontal-container-medium">
          <div class="horizontal-padding-vertical">
            <div class="horizontal-max-width-large">
              <h1 class="horizontal-heading">Beauty Bonita Salon De Belleza</h1>
            </div>
          </div>
        </div>
      </div>

      <div class="horizontal-scroll-section horizontal-vertical-section horizontal-section">
        <div class="horizontal-wrapper">
          <div role="list" class="horizontal-list">
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2 class="horizontal-item_number">1</h2>
                <h2>Tratamientos para el Cabello: Nutre, Repara y Realza tu Belleza</h2>
                <p class="horizontal-item_p">
                  Descubre tratamientos profesionales diseñados para revitalizar, fortalecer
                  y devolverle el brillo natural a tu cabello, dejándolo suave, saludable
                  y lleno de vida.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/3214448/3214448-uhd_2560_1440_25fps.mp4"
                loading="lazy"
                autoplay
                muted
                loop
                class="horizontal-item_media"
              ></video>
            </div>
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2 class="horizontal-item_number">2</h2>
                <h2>Color de Cabello: Transforma tu Estilo con Tonos Perfectos</h2>
                <p class="horizontal-item_p">
                  Renueva tu look con técnicas de coloración profesional que aportan
                  brillo, dimensión y personalidad. Somos especialistas en crear tonos
                  que realzan tu belleza y te hacen sentir completamente renovada.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/3214448/3214448-uhd_2560_1440_25fps.mp4"
                loading="lazy"
                autoplay
                muted
                loop
                class="horizontal-item_media"
              ></video>
            </div>
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2 class="horizontal-item_number">3</h2>
                <h2>Maquillajes Profesionales: Resalta tu Belleza Natural</h2>
                <p class="horizontal-item_p">
                  Nuestros especialistas crean looks que realzan tus rasgos con armonía,
                  desde acabados naturales hasta estilos sofisticados para tus eventos
                  formales, haciéndote lucir impecable en cada ocasión.
              </div>
              <video
                src="https://videos.pexels.com/video-files/4328514/4328514-uhd_2560_1440_30fps.mp4"
                loading="lazy"
                autoplay
                muted
                loop
                class="horizontal-item_media"
              ></video>
            </div>
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2 class="horizontal-item_number">4</h2>
                <h2>Peinados Profesionales: Estilo que Enmarca tu Belleza</h2>
                <p class="horizontal-item_p">
                  Descubre peinados creados con técnica y precisión, diseñados para resaltar
                  tu estilo y hacerte lucir espectacular en cada evento, ocasión especial o
                  momento inolvidable.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/2871916/2871916-hd_1920_1080_30fps.mp4"
                loading="lazy"
                autoplay
                muted
                loop
                class="horizontal-item_media"
              ></video>
            </div>
          </div>
        </div>
      </div>

     

    
@push('scripts')
    {{-- GSAP CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/horizontal.js') }}"></script>
@endpush