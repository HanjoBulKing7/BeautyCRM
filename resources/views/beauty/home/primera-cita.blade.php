{{-- Sección: Primera Cita - Minimalista con AOS --}}
<section class="bb-promo-white" id="primera-cita">
  <div class="bb-promo-white__container">
    
    {{-- Lado Izquierdo: Visual & Decorativo --}}
    <div class="bb-promo-white__visual" data-aos="fade-right" data-aos-duration="1000">
      {{-- Número gigante de fondo --}}
      <div class="bb-promo-white__bg-number" data-aos="zoom-in" data-aos-delay="200" data-aos-duration="1200">15</div>
      
      <div class="bb-promo-white__circle-deco" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="1000"></div>
      
      {{-- Tarjeta flotante --}}
      <div class="bb-promo-white__card" data-aos="fade-up" data-aos-delay="400" data-aos-duration="800">
        <span class="bb-promo-white__card-tag">Welcome Gift</span>
        <div class="bb-promo-white__card-value">15<span>%</span></div>
        <p class="bb-promo-white__card-txt">Off en tu primer servicio</p>
      </div>
    </div>

    {{-- Lado Derecho: Contenido --}}
    <div class="bb-promo-white__content">
      
      {{-- Encabezado usando tus clases de galería --}}
      <div class="bb-promo-white__header" data-aos="fade-up">
        <span class="gallery__kicker">OFERTA EXCLUSIVA</span>
       <h2 class="bb-promo-white__title">
        Tu primera experiencia <br><span>con un beneficio exclusivo</span>
      </h2>
      
      <p class="bb-promo-white__desc">
        Queremos que conozcas nuestro estándar de belleza. Reserva tu primera cita a través de nuestra web y recibe un <strong>15% de descuento automático</strong>.
      </p>
      
    
      <ul class="bb-promo-white__list">
        <li data-aos="fade-up" data-aos-delay="200">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Se aplica automáticamente al reservar
        </li>
        <li data-aos="fade-up" data-aos-delay="300">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Válido para cualquier servicio del salón
        </li>
        <li data-aos="fade-up" data-aos-delay="400">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Solo para tu primera reserva en línea
        </li>
      </ul>

      {{-- Botón usando tu clase gallery-link --}}
      <div class="bb-promo-white__actions" data-aos="fade-up" data-aos-delay="500">
        <a href="{{ url('/agendar-cita') }}" class="gallery-link">
          AGENDAR MI PRIMERA CITA
        </a>
      </div>
      
      <p class="bb-promo-white__footer-note" data-aos="fade-up" data-aos-delay="600">
        Válido únicamente para nuevas clientas y reservas vía web.
      </p>
    </div>

  </div>
</section>

<style>
/* =========================================
   SECCIÓN: PRIMERA CITA (BLANCA & MINIMALISTA)
========================================= */

.bb-promo-white {
  background-color: #ffffff;
  padding: 100px 20px;
  overflow: hidden;
  font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
}

.bb-promo-white__container {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1.1fr;
  align-items: center;
  gap: 80px;
}

/* --- LADO IZQUIERDO: DECORACIÓN --- */
.bb-promo-white__visual {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 400px;
}

.bb-promo-white__bg-number {
  position: absolute;
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 20rem;
  font-weight: 900;
  color: #f7f7f7; /* Gris extra claro para que apenas se note */
  z-index: 1;
  line-height: 1;
  user-select: none;
}

.bb-promo-white__circle-deco {
  position: absolute;
  width: 300px;
  height: 300px;
  border: 1px solid #eaeaea;
  border-radius: 50%;
  z-index: 2;
}

.bb-promo-white__card {
  position: relative;
  z-index: 3;
  background: #ffffff;
  padding: 40px 30px;
  border-radius: 16px; /* Borde redondeado a juego con las tarjetas de servicios */
  box-shadow: 0 20px 40px rgba(17, 20, 28, 0.05); /* Sombra súper sutil */
  text-align: center;
  width: 220px;
  border-top: 3px solid #d4af37; /* Acento dorado */
}

.bb-promo-white__card-tag {
  display: block;
  font-size: 0.65rem;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #888;
  margin-bottom: 10px;
  font-weight: 600;
}

.bb-promo-white__card-value {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 4.5rem;
  font-weight: 700;
  color: #11141c; /* Color oscuro institucional */
  line-height: 1;
}

.bb-promo-white__card-value span {
  font-size: 2rem;
  color: #d4af37;
}

.bb-promo-white__card-txt {
  font-size: 0.85rem;
  color: #555;
  margin-top: 10px;
}

/* --- LADO DERECHO: TEXTO --- */
.bb-promo-white__header {
  margin-bottom: 10px;
}

/* Ajustes para tus clases existentes por si acaso (Kicker y Title) */
.gallery__kicker {
  color: #8e6708;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 3px;
  text-transform: uppercase;
  display: block;
}

.gallery__big-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(2.2rem, 4vw, 3rem);
  font-weight: 700;
  color: #11141c;
  line-height: 1.1;
  margin: 0;
}

.bb-promo-white__desc {
  font-size: 1.05rem;
  color: #555;
  line-height: 1.7;
  margin-bottom: 30px;
}

.bb-promo-white__desc strong {
  color: #11141c;
  font-weight: 600;
}

.bb-promo-white__list {
  list-style: none;
  padding: 0;
  margin-bottom: 40px;
}

.bb-promo-white__list li {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.95rem;
  color: #444;
  margin-bottom: 12px;
}

.bb-promo-white__list svg {
  width: 18px;
  height: 18px;
  color: #d4af37;
  flex-shrink: 0;
}

.bb-promo-white__footer-note {
  margin-top: 20px;
  font-size: 0.75rem;
  color: #aaa;
}

/* Reutilizando tu botón exactamente igual */
.gallery-link {
  display: inline-block;
  padding: 14px 36px;
  background-color: transparent;
  border: 1px solid #11141c;
  color: #11141c;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border-radius: 999px;
  font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
}

.gallery-link:hover {
  background-color: #11141c;
  color: #ffffff;
}

/* --- RESPONSIVO --- */
@media (max-width: 991px) {
  .bb-promo-white__container {
    grid-template-columns: 1fr;
    gap: 60px;
  }
  
  .bb-promo-white__visual {
    order: 2; /* Pasa la imagen abajo en móviles */
    height: 300px;
  }

  .bb-promo-white__header,
  .bb-promo-white__desc,
  .bb-promo-white__actions,
  .bb-promo-white__footer-note {
    text-align: center;
  }
  
  .gallery__big-title {
    text-align: center !important;
  }

  .bb-promo-white__list li {
    justify-content: center;
  }

  .bb-promo-white__bg-number {
    font-size: 14rem;
  }
}
</style>