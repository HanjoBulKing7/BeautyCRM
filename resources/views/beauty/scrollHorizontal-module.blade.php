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
                <h2>Cejas Perfectas</h2>
                <p class="horizontal-item_p">
                  Diseño y perfilado de cejas personalizado según la forma de tu rostro. 
                  Técnicas de depilación profesional con hilo, cera o pinza, y opciones 
                  de micropigmentación para un acabado natural y armonioso que realza 
                  la expresión de tus ojos.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/cejas.mp4') }}"
                loading="lazy"
                autoplay
                muted
                loop
                playsinline
                class="horizontal-item_media"
              ></video>
            </div>
            
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2>Uñas</h2>
                <p class="horizontal-item_p">
                  Manicura y pedicura de lujo con las últimas tendencias en diseños y colores. 
                  Servicio de uñas esculpidas en gel o acrílico, kapping, y esmaltado semipermanente 
                  de larga duración. Cuidado profesional para manos y pies impecables.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/unas.mp4') }}"
                loading="lazy"
                autoplay
                muted
                loop
                playsinline
                class="horizontal-item_media"
              ></video>
            </div>
            
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2>Extensiones de Cabello</h2>
                <p class="horizontal-item_p">
                  Transformá tu look con extensiones de cabello natural de alta calidad. 
                  Técnicas de aplicación invisible que aportan largo, volumen y densidad 
                  sin dañar tu cabello. Asesoramiento personalizado para que luzcas 
                  el estilo que siempre soñaste.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/extensiones.mp4') }}"
                loading="lazy"
                autoplay
                muted
                loop
                playsinline
                class="horizontal-item_media"
              ></video>
            </div>
            
            <div role="listitem" class="horizontal-item">
              <div class="horizontal-item_content">
                <h2>Pestañas</h2>
                <p class="horizontal-item_p">
                  Realzá tu mirada con nuestras técnicas de pestañas pelo a pelo, 
                  volumen ruso o lifting de pestañas. Logramos un efecto natural 
                  o dramático según tu preferencia, con productos de alta calidad 
                  que cuidan tus pestañas naturales.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/pestanas.mp4') }}"
                loading="lazy"
                autoplay
                muted
                loop
                playsinline
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
    <script src="{{ asset('js/horizontal.js') }}"></script>
@endpush