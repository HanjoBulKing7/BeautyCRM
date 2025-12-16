@push('styles')
    <link rel="stylesheet" href="{{ asset('css/lenis.css') }}">
@endpush

<section class="lenis-main-wrapper">
      <section id="lenis-vertical">
        <div class="lenis-container">
          <div class="lenis-vertical__content">
            <div class="lenis-col lenis-col_left">
              <h2 class="lenis-vertical__heading">
                <span>Beauty</span><span>Bonita</span>
              </h2>
            </div>
            <div class="lenis-col lenis-col_right">
              <div class="lenis-vertical__item">
                <h3>EXPERIENCIA TRANSFORMADORA</h3>
                <p>
                  En nuestro santuario de belleza, cada tratamiento es una obra de
                  arte meticulosamente diseñada. Nuestros expertos combinan
                  técnicas ancestrales con innovación de vanguardia para ofrecer
                  resultados que trascienden lo ordinario.
                </p>
              </div>
              <div class="lenis-vertical__item">
                <h3>MAESTRÍA ARTESANAL</h3>
                <p>
                  Cada detalle es perfeccionado con precisión quirúrgica y
                  sensibilidad artística. Nuestros especialistas, formados en las
                  mejores escuelas internacionales, transforman el cuidado
                  personal en una experiencia sensorial única.
                </p>
              </div>
              <div class="lenis-vertical__item">
                <h3>PRIVACIDAD ABSOLUTA</h3>
                <p>
                  Diseñamos espacios íntimos donde la discreción es primordial. Su
                  comodidad y privacidad son nuestra máxima prioridad, creando un
                  refugio exclusivo lejos del mundo exterior.
                </p>
              </div>
              <div class="lenis-vertical__item">
                <h3>EXCELENCIA SUSTENTABLE</h3>
                <p>
                  Seleccionamos ingredientes de origen ético y productos de lujo
                  que respetan tanto su piel como el medio ambiente. La
                  sostenibilidad y la calidad excepcional convergen en cada
                  servicio.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="lenis-horizontal">
        <div class="lenis-container">
          <div class="lenis-horizontal__content">
            <div class="lenis-horizontal__item">
              <div class="lenis-horizontal__num">1</div>
              <div class="lenis-card-content">
                <div class="lenis-image-container">
                  <img src="{{ asset('images/download-2025-12-16T00_07_16.jpg') }}" alt="Manicura" />
                </div>
                <h3 class="lenis-card-title">Manicura</h3>
                <p class="lenis-card-description">
                  Cuida y embellece tus manos con acabados impecables y diseños
                  únicos que realzan tu estilo personal.
                </p>
                <a href="#" class="lenis-card-button">Explorar servicios</a>
              </div>
            </div>
            <div class="lenis-horizontal__item">
              <div class="lenis-horizontal__num">2</div>
              <div class="lenis-card-content">
                <div class="lenis-image-container">
                  <img src="imagenes/Faciales.jpg" alt="Faciales" />
                </div>
                <h3 class="lenis-card-title">Faciales</h3>
                <p class="lenis-card-description">
                  Tratamientos faciales personalizados para rejuvenecer, hidratar
                  y revitalizar tu piel con ingredientes naturales.
                </p>
                <a href="#" class="lenis-card-button">Explorar servicios</a>
              </div>
            </div>
            <div class="lenis-horizontal__item">
              <div class="lenis-horizontal__num">3</div>
              <div class="lenis-card-content">
                <div class="lenis-image-container">
                  <img src="imagenes/corte.jpg" alt="Cortes" />
                </div>
                <h3 class="lenis-card-title">Cortes</h3>
                <p class="lenis-card-description">
                  Estilos modernos y clásicos que se adaptan a tu rostro y
                  personalidad, realizados por nuestros expertos estilistas.
                </p>
                <a href="#" class="lenis-card-button">Explorar servicios</a>
              </div>
            </div>
            <div class="lenis-horizontal__item">
              <div class="lenis-horizontal__num">4</div>
              <div class="lenis-card-content">
                <div class="lenis-image-container">
                  <img src="imagenes/color.jpg" alt="Coloración" />
                </div>
                <h3 class="lenis-card-title">Coloración</h3>
                <p class="lenis-card-description">
                  Desde tonos naturales hasta colores vibrantes, encuentra el look
                  perfecto que exprese tu verdadero yo.
                </p>
                <a href="#" class="lenis-card-button">Explorar servicios</a>
              </div>
            </div>
            <div class="lenis-horizontal__item">
              <div class="lenis-horizontal__num">5</div>
              <div class="lenis-card-content">
                <div class="lenis-image-container">
                  <img src="imagenes/Tratamientos.jpg" alt="Tratamientos" />
                </div>
                <h3 class="lenis-card-title">Tratamientos</h3>
                <p class="lenis-card-description">
                  Sesiones relajantes que combinan técnicas avanzadas para
                  equilibrar mente, cuerpo y espíritu.
                </p>
                <a href="#" class="lenis-card-button">Explorar servicios</a>
              </div>
            </div>
            <div class="lenis-horizontal__item">
              <div class="lenis-horizontal__num">6</div>
              <div class="lenis-card-content">
                <div class="lenis-image-container">
                  <img src="imagenes/Pestañas2.png" alt="Pestañas" />
                </div>
                <h3 class="lenis-card-title">Pestañas</h3>
                <p class="lenis-card-description">
                  Realza tu mirada con extensiones y tratamientos especializados
                  para unas pestañas impactantes.
                </p>
                <a href="#" class="lenis-card-button">Explorar servicios</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>

@push('scripts')
    {{-- GSAP CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"></script>
    <script src="https://assets.codepen.io/16327/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@latest/bundled/lenis.js"></script>
    <script src="{{ asset('js/lenis.js') }}"></script>
    @vite('resources/js/lenis.js')
@endpush