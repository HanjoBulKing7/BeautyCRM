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
                <h2>Peinados Profesionales</h2>
                <p class="horizontal-item_p">
                  Creaciones únicas para cada ocasión: desde recogidos elegantes para eventos formales 
                  hasta looks sueltos con ondas perfectas para el día a día. Nuestros estilistas 
                  diseñan peinados que resaltan tu personalidad y te hacen brillar en cada momento 
                  especial.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/peinado.mp4') }}"
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
                <h2>Masajes Relajantes</h2>
                <p class="horizontal-item_p">
                  Liberá tensiones y renová tu energía con nuestros masajes corporales. 
                  Técnicas de relajación profunda, descontracturantes y drenaje linfático 
                  que alivian el estrés, mejoran la circulación y restauran el equilibrio 
                  de tu cuerpo en un ambiente de calma y tranquilidad.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/masajes.mp4') }}"
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
                <h2>Perforaciones</h2>
                <p class="horizontal-item_p">
                  Expresá tu estilo con perforaciones realizadas por profesionales 
                  con los más altos estándares de higiene y seguridad. Ofrecemos una amplia 
                  variedad de opciones para orejas, nariz y más, utilizando materiales 
                  hipoalergénicos de primera calidad para cuidar tu piel.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/perforaciones.mp4') }}"
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
                <h2>Masajes Corporales Avanzados</h2>
                <p class="horizontal-item_p">
                  Experimentá una nueva dimensión del relax con nuestros masajes 
                  que combinan técnicas manuales con avanzada tecnología. Ideal para 
                  tratar contracturas profundas, mejorar la circulación y lograr 
                  una renovación completa de tu cuerpo. Resultados visibles desde 
                  la primera sesión.
                </p>
                <a href="/agendar-cita" class="horizontal-cta">Agendar cita</a>
              </div>
              <video
                src="{{ asset('videos/masaje2.mp4') }}"
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
    <script src="{{ asset('js/galeriahorizontal.js') }}"></script>
@endpush