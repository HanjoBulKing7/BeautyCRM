<section class="bb-people" id="people" style="overflow-x: hidden;">
  <div class="bb-people__wrap bb-people__container">

    <div class="bb-people__top">
      <div class="bb-people__text js-reveal">
           
        <h2 class="bb-people__title">Beauty Bonita Studio</h2>

        <p class="bb-people__lead">
          Descubre un espacio donde la <strong>belleza y la exclusividad</strong> se unen. Diseñamos cada detalle para ofrecerte una experiencia de lujo, confort y relajación desde el primer momento en que cruzas nuestras puertas.
        </p>

        <p class="bb-people__lead">
          Somos expertos en resaltar tu mejor versión. Ofrecemos <strong>peinados de alta costura, maquillaje profesional, faciales revitalizantes</strong>, cortes vanguardistas y servicios de uñas con las últimas tendencias internacionales. Déjate consentir por nuestros especialistas, porque mereces lo mejor.
        </p>
      </div>

      <div class="bb-people__top-promoted js-reveal">
           
        <a href="{{ url('/agendar-cita') }}" class="bb-people__nav-card bb-card--promoted js-card">
          <div class="bb-people__img-wrap">
            <img src="{{ asset('images/agendarcitass.webp') }}" alt="Agendar Cita" loading="lazy">
          </div>
          <div class="bb-people__card-info">
            <span class="bb-people__card-title">Agendar Cita</span>
            <span class="bb-people__card-arrow">&rarr;</span>
          </div>
        </a>
      </div>
    </div>

    <div class="bb-people__bottom">
      <a href="{{ url('/servicio') }}" class="bb-people__nav-card bb-item--servicios js-card">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/servicios.webp') }}" alt="Nuestros Servicios" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Servicios</span>
          <span class="bb-people__card-arrow">&rarr;</span>
        </div>
      </a>
      
      <a href="{{ url('/nosotros') }}" class="bb-people__nav-card bb-item--nosotros js-card">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/nosotros.webp') }}" alt="Conócenos" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Nosotros</span>
          <span class="bb-people__card-arrow">&rarr;</span>
        </div>
      </a>


      <a href="{{ url('/galeria') }}" class="bb-people__nav-card bb-item--galeria js-card">
        <div class="bb-people__img-wrap">
          <img src="{{ asset('images/galeria.webp') }}" alt="Galería de trabajos" loading="lazy">
        </div>
        <div class="bb-people__card-info">
          <span class="bb-people__card-title">Galería</span>
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

  /* --- PARTE SUPERIOR (Flex) --- */
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

  .bb-people__top-promoted {
    flex: 1 1 400px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    background: #fff;
  }

  .bb-card--promoted.bb-people__nav-card {
    height: 100%;
    margin-bottom: 0;
  }

  .bb-card--promoted .bb-people__img-wrap {
    margin-bottom: 0;
    border-radius: 0;
    aspect-ratio: 16 / 10;
  }
  
  .bb-card--promoted .bb-people__card-info {
    padding: 20px;
  }

  /* --- PARTE INFERIOR (Grid 3 columnas Escritorio) --- */
  .bb-people__bottom {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
  }

  /* --- ESTILOS BASE DE TARJETAS --- */
  .bb-people__nav-card {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    background: #fff;
    transition: transform 0.3s ease;
    height: 100%; /* Forzamos a que llene la celda */
  }

  .bb-people__img-wrap {
    width: 100%;
    flex: 1 1 auto; /* Permite que el contenedor crezca y ocupe el espacio */
    aspect-ratio: 4 / 3;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    position: relative;
  }

  .bb-people__img-wrap img {
    position: absolute;
    top: 0;
    left: 0;
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
    flex-shrink: 0; /* Evita que el texto se apachurre */
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
  .bb-people__nav-card:hover .bb-people__img-wrap img { transform: scale(1.08); }
  .bb-people__nav-card:hover .bb-people__card-title { color: #8e6708; }
  .bb-people__nav-card:hover .bb-people__card-arrow { transform: translateX(8px); }

  /* --- RESPONSIVO --- */
  @media (max-width: 992px) {
    .bb-people__bottom { gap: 20px; }
  }

  @media (max-width: 768px) {
    .bb-people { padding: 50px 15px; }
    .bb-people__title { font-size: 2.2rem; }
    .bb-people__top { gap: 30px; margin-bottom: 40px; }

    /* En móvil, la de Cita arriba es más cuadradita */
    .bb-card--promoted .bb-people__img-wrap { aspect-ratio: 4 / 3; }

    /* COLLAGE PARA CELULARES (Las 3 de abajo) */
    .bb-people__bottom {
      grid-template-columns: repeat(2, 1fr); /* 2 Columnas */
      grid-auto-rows: 150px; /* Altura de la celda */
      gap: 15px;
    }

    .bb-item--nosotros {
      grid-column: 1 / 2;
      grid-row: 1 / 3; /* Ocupa el lado izquierdo completo (alta) */
    }

    .bb-item--servicios {
      grid-column: 2 / 3;
      grid-row: 1 / 2; /* Ocupa arriba a la derecha */
    }

    .bb-item--galeria {
      grid-column: 2 / 3;
      grid-row: 2 / 3; /* Ocupa abajo a la derecha */
    }

    /* FIX DE LAS IMÁGENES: Quitamos el aspect ratio pero obligamos a llenar el 100% */
    .bb-people__bottom .bb-people__img-wrap {
      aspect-ratio: unset; /* En lugar de auto, lo anulamos */
      height: 100%; /* Toma todo el espacio sobrante de la celda */
      margin-bottom: 8px;
    }
  }

  @media (max-width: 480px) {
    .bb-people__title { font-size: 1.8rem; letter-spacing: 3px; }
    
    /* Hacemos el collage un poco más compacto en pantallas muy chicas */
    .bb-people__bottom {
      grid-auto-rows: 130px;
      gap: 10px;
    }
    
    /* Reducimos la letra en el collage para que quepa bien */
    .bb-people__card-title { font-size: 0.75rem; letter-spacing: 1px; }
    .bb-people__card-arrow { font-size: 1.2rem; }
  }
</style>