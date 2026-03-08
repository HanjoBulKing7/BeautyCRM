@push('styles')
    <link rel="stylesheet" href="{{ asset('css/galeriahorizontal.css') }}">
@endpush

    <section class="horizontal-main-wrapper">
      
     <div class="horizontal-section">
        <div class="horizontal-container-medium">
          <div class="horizontal-padding-vertical">
            <div class="horizontal-max-width-large">
              <h1 class="horizontal-heading">Cuidado y Belleza para Ti</h1>
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
                <h2>Tratamientos Capilares: Brillo, Fuerza y Reparación</h2>
                <p class="horizontal-item_p">
                  Devuélvele vida a tu cabello con hidrataciones profundas, 
                  reparación y nutrición profesional. Ideal para controlar frizz, 
                  mejorar textura y lograr un acabado suave y luminoso.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/pestañas.mp4') }}"
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
                <h2>Pestañas: Mirada Definida Todo el Día</h2>
                <p class="horizontal-item_p">
                  Realza tu mirada con técnicas que aportan longitud, 
                  volumen y curvatura con un resultado elegante y natural. 
                  Perfecto para verte increíble sin esfuerzo.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/pestañas.mp4') }}"
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
                  Pedicura: Pies Suaves y Presentables
                </h2>
                <p class="horizontal-item_p">
                  Relájate con un cuidado completo: limpieza, exfoliación 
                  e hidratación para unos pies suaves y lindos. 
                  Un toque perfecto para sentirte fresca y segura.
                </p>
                <a href="/agendarcita" class="horizontal-cta">Agendar cita</a>
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
                <h2>Faciales: Piel Radiante y Renovada</h2>
                <p class="horizontal-item_p">
                  Rituales faciales que limpian, hidratan y revitalizan tu piel. 
                  Logra un glow natural, textura más uniforme y 
                  una sensación de frescura desde la primera sesión.
                </p>
                <a href="/agendarcita" class="horizontal-cta">Agendar cita</a>
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