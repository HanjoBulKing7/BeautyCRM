@push('styles')
    <link rel="stylesheet" href="{{ asset('css/galeriahorizontal.css') }}">
@endpush

    <section class="horizontal-main-wrapper">
      <div class="horizontal-section">
        <div class="horizontal-container-medium">
          <div class="horizontal-padding-vertical">
            <div class="horizontal-max-width-large">
              <h1 class="horizontal-heading"></h1>
            </div>
          </div>
        </div>
      </div>
     <div class="horizontal-section">
        <div class="horizontal-container-medium">
          <div class="horizontal-padding-vertical">
            <div class="horizontal-max-width-large">
              <h1 class="horizontal-heading">Beauty Bonita Salon De Belleza</h1>
            </div>
          </div>
        </div>
      </div>
      

      <div class="horizontal-scroll-section horizontal-horizontal-section horizontal-section">
        <div class="horizontal-wrapper">
          <div role="list" class="horizontal-list">
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2 class="horizontal-item_number">1</h2>
                <h2>Pestañas en Acción: Realza tu Mirada Cada Día</h2>
                <p class="horizontal-item_p">
                  Descubre la belleza de unas pestañas perfectas con técnicas profesionales
                  que resaltan tu mirada, aportan volumen y transforman tu estilo de manera
                  natural y elegante.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/10178127/10178127-uhd_2560_1440_30fps.mp4"
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
                <h2>Nature's Symphony: The Sounds That Heal the Soul</h2>
                <p class="horizontal-item_p">
                  Immerse yourself in the soothing sounds of chirping birds,
                  rustling leaves, and flowing streams – nature's music for
                  peace and tranquility.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/15708463/15708463-uhd_2560_1440_24fps.mp4"
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
                <h2>
                  Cejas en Armonía: El Arte que Realza tu Expresión
                </h2>
                <p class="horizontal-item_p">
                  Sumérgete en la belleza de unas cejas perfectamente diseñadas, donde cada
                  trazo aporta forma, equilibrio y definición para lograr una mirada más
                  expresiva y natural.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/15708462/15708462-uhd_2560_1440_24fps.mp4"
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
                <h2>Belleza Renaciente: Tratamientos Faciales que Transforman tu Piel</h2>
                <p class="horizontal-item_p">
                  Descubre el poder de rituales faciales que revitalizan, hidratan y renuevan
                  tu piel, revelando un brillo natural que refleja su verdadera belleza.
                </p>
              </div>
              <video
                src="https://videos.pexels.com/video-files/5788966/5788966-hd_1920_1080_25fps.mp4"
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

     
    </section>
    
@push('scripts')
    {{-- GSAP CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/galeriahorizontal.js') }}"></script>
@endpush