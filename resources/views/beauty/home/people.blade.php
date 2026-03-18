<section class="bb-people" id="people" style="overflow-x: hidden;">
  <div class="bb-people__wrap bb-people__container">

    <div class="bb-people__top">
      <div class="bb-people__text js-reveal"
           data-aos="fade-right"
           data-aos-duration="1000"
           data-aos-offset="200"
           data-aos-once="true">
           
        <h2 class="bb-people__title">Beauty Bonita Studio</h2>

        <p class="bb-people__lead">
          Descubre un espacio donde la <strong>belleza y la exclusividad</strong> se unen. Diseñamos cada detalle para ofrecerte una experiencia de lujo, confort y relajación desde el primer momento en que cruzas nuestras puertas.
        </p>

        <p class="bb-people__lead">
          Somos expertos en resaltar tu mejor versión. Ofrecemos <strong>peinados de alta costura, maquillaje profesional, faciales revitalizantes</strong>, cortes vanguardistas y servicios de uñas con las últimas tendencias internacionales. Déjate consentir por nuestros especialistas, porque mereces lo mejor.
        </p>
      </div>

      <div class="bb-people__hero js-reveal"
           data-aos="fade-left"
           data-aos-duration="1000"
           data-aos-offset="200"
           data-aos-once="true">
        <img
          src="{{ asset('images/download-2025-12-16T00_07_16.jpg') }}"
          alt="Experiencia en Beauty Bonita Studio"
          loading="lazy"
        >
      </div>
    </div>

    <div class="bb-people__bottom">
      
      <a href="{{ url('/nosotros') }}" class="bb-people__nav-card js-card"
         data-aos="fade-up" data-aos-duration="800" data-aos-offset="150" data-aos-once="true">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/Peinado.png') }}" alt="Conócenos" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Nosotros</span>
          <span class="bb-people__card-arrow">&rarr;</span>
        </div>
      </a>

      <a href="{{ url('/servicio') }}" class="bb-people__nav-card js-card"
         data-aos="fade-up" data-aos-duration="800" data-aos-delay="100" data-aos-offset="150" data-aos-once="true">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/Uñas2.jpg') }}" alt="Nuestros Servicios" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Servicios</span>
          <span class="bb-people__card-arrow">&rarr;</span>
        </div>
      </a>

      <a href="{{ url('/galeria') }}" class="bb-people__nav-card js-card"
         data-aos="fade-up" data-aos-duration="800" data-aos-delay="200" data-aos-offset="150" data-aos-once="true">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/servicios/faciales.jpg') }}" alt="Galería de trabajos" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Galería</span>
          <span class="bb-people__card-arrow">&rarr;</span>
        </div>
      </a>

      <a href="{{ url('/agendarcita') }}" class="bb-people__nav-card js-card"
         data-aos="fade-up" data-aos-duration="800" data-aos-delay="300" data-aos-offset="150" data-aos-once="true">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/Peinado2.png') }}" alt="Agendar Cita" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Agendar Cita</span>
          <span class="bb-people__card-arrow">&rarr;</span>
        </div>
      </a>
      
    </div>

  </div>
</section>

<style>
  .bb-people {
    padding: 80px 20px;
    background-color: #ffffff;
    font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
  }

  .bb-people__container {
    max-width: 1200px;
    margin: 0 auto;
  }

  /* --- PARTE SUPERIOR --- */
  .bb-people__top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 50px;
    margin-bottom: 80px;
  }

  .bb-people__text {
    flex: 1 1 500px;
  }

  .bb-people__title {
    font-size: 3rem;
    color: #11141c;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 5px;
    margin-bottom: 30px;
  }

  .bb-people__lead {
    font-size: 1rem;
    color: #555555;
    line-height: 1.8;
    margin-bottom: 20px;
  }

  .bb-people__lead strong {
    color: #11141c;
    font-weight: 600;
  }

  .bb-people__hero {
    flex: 1 1 400px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
  }

  .bb-people__hero img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
  }

  /* --- PARTE INFERIOR --- */
  .bb-people__bottom {
    display: grid;
    /* En escritorio forzamos 4 columnas en 1 sola fila */
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
  }

  .bb-people__nav-card {
    display: flex;
    flex-direction: column;
    text-decoration: none;
  }

  .bb-people__img-wrap {
    width: 100%;
    aspect-ratio: 4 / 5;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  }

  .bb-people__img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
  }

  .bb-people__card-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 5px;
  }

  .bb-people__card-title {
    color: #11141c;
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: color 0.3s ease;
  }

  .bb-people__card-arrow {
    color: #8e6708;
    font-size: 1.5rem;
    line-height: 1;
    transition: transform 0.3s ease;
  }

  /* --- EFECTOS HOVER --- */
  .bb-people__nav-card:hover .bb-people__img-wrap img {
    transform: scale(1.08);
  }

  .bb-people__nav-card:hover .bb-people__card-title {
    color: #8e6708;
  }

  .bb-people__nav-card:hover .bb-people__card-arrow {
    transform: translateX(8px);
  }

  /* --- RESPONSIVO --- */
  @media (max-width: 900px) {
    .bb-people { padding: 50px 15px; }
    .bb-people__title { font-size: 2rem; }
    
    /* Cambiamos el comportamiento a un carrusel deslizable en lugar de apilar hacia abajo */
    .bb-people__bottom {
      display: flex;
      flex-wrap: nowrap;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      padding-bottom: 15px;
      gap: 15px;
    }

    .bb-people__nav-card {
      flex: 0 0 45%; /* En tablet ocupa casi la mitad */
      scroll-snap-align: start;
    }
  }

  @media (max-width: 600px) {
    .bb-people__nav-card {
      flex: 0 0 70%; /* En celular se ve una entera y un pedazo de la siguiente para invitar al scroll */
    }
    
    .bb-people__card-title {
      font-size: 0.85rem;
    }
  }
</style>