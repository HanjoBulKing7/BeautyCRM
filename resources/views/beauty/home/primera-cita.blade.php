{{-- Sección: Primera Cita con 15% de descuento --}}
<section class="bb-primera-cita js-reveal" id="primera-cita">

  {{-- Fondo oscuro con gradiente editorial --}}
  <div class="bb-primera-cita__inner">

    {{-- Badge superior --}}
    <div class="bb-primera-cita__badge">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
        <line x1="7" y1="7" x2="7.01" y2="7"/>
      </svg>
      <span>Oferta exclusiva · Solo en línea</span>
    </div>

    {{-- Contenido principal --}}
    <div class="bb-primera-cita__content">

      {{-- Número grande decorativo --}}
      <div class="bb-primera-cita__percent" aria-hidden="true">15<span>%</span></div>

      <h2 class="bb-primera-cita__title">
        Tu primera cita,<br>tu mejor precio
      </h2>

      <p class="bb-primera-cita__desc">
        Regístrate, agenda en línea y disfruta un <strong>15&nbsp;% de descuento</strong>
        en tu primer servicio con nosotras. El descuento se aplica automáticamente,
        sin códigos ni complicaciones.
      </p>

      {{-- Beneficios --}}
      <ul class="bb-primera-cita__perks">
        <li>
          <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Se aplica automáticamente al reservar
        </li>
        <li>
          <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Válido para cualquier servicio del salón
        </li>
        <li>
          <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Solo para tu primera reserva en línea
        </li>
      </ul>

      {{-- CTA --}}
      <a href="{{ url('/agendar-cita') }}" class="bb-btn-gold bb-primera-cita__cta">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Agendar mi primera cita
      </a>

      <p class="bb-primera-cita__note">
        Solo para reservas en línea &middot; Primera cita &middot; Descuento automático
      </p>

    </div>

    {{-- Tarjeta decorativa flotante --}}
    <div class="bb-primera-cita__card" aria-hidden="true">
      <div class="bb-primera-cita__card-inner">
        <div class="bb-primera-cita__card-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l2.4 4.8 5.3.8-3.8 3.7 1 5.3L12 14l-4.9 2.6 1-5.3L4.3 7.6l5.3-.8L12 2z"/>
          </svg>
        </div>
        <div class="bb-primera-cita__card-label">Primera cita</div>
        <div class="bb-primera-cita__card-discount">-15%</div>
        <div class="bb-primera-cita__card-sub">Descuento exclusivo</div>
        <div class="bb-primera-cita__card-divider"></div>
        <div class="bb-primera-cita__card-footer">
          <span>Reserva en línea</span>
          <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 3.293a1 1 0 011.414 0L10.414 7H3a1 1 0 100 2h7.414l-3.707 3.707a1 1 0 001.414 1.414l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 0z" clip-rule="evenodd"/>
          </svg>
        </div>
      </div>
    </div>

  </div>
</section>

<style>
.bb-primera-cita {
  background: linear-gradient(135deg, rgba(17,24,39,1) 0%, rgba(26,20,40,1) 60%, rgba(17,24,39,.97) 100%);
  position: relative;
  overflow: hidden;
  padding: 5rem 1.5rem;
}

.bb-primera-cita::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse 70% 60% at 80% 50%, rgba(201,162,74,.12) 0%, transparent 70%),
              radial-gradient(ellipse 50% 50% at 10% 80%, rgba(201,162,74,.06) 0%, transparent 60%);
  pointer-events: none;
}

.bb-primera-cita__inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 4rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

/* Badge superior */
.bb-primera-cita__badge {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: .35rem .85rem;
  border-radius: 999px;
  background: rgba(201,162,74,.15);
  border: 1px solid rgba(201,162,74,.3);
  color: rgba(201,162,74,.95);
  font-size: .78rem;
  font-weight: 600;
  letter-spacing: .05em;
  text-transform: uppercase;
  margin-bottom: 1.5rem;
}
.bb-primera-cita__badge svg {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

/* Número decorativo */
.bb-primera-cita__percent {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(5rem, 12vw, 9rem);
  font-weight: 800;
  line-height: 1;
  color: transparent;
  -webkit-text-stroke: 2px rgba(201,162,74,.25);
  position: absolute;
  top: 0;
  left: -1rem;
  pointer-events: none;
  user-select: none;
}
.bb-primera-cita__percent span {
  font-size: 55%;
}

/* Contenido */
.bb-primera-cita__content {
  position: relative;
}

.bb-primera-cita__title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(2rem, 4vw, 3.2rem);
  font-weight: 700;
  line-height: 1.15;
  color: #ffffff;
  margin-bottom: 1.2rem;
}

.bb-primera-cita__desc {
  font-size: 1rem;
  line-height: 1.7;
  color: rgba(255,255,255,.65);
  max-width: 500px;
  margin-bottom: 1.8rem;
}
.bb-primera-cita__desc strong {
  color: rgba(201,162,74,.95);
  font-weight: 600;
}

/* Lista de beneficios */
.bb-primera-cita__perks {
  list-style: none;
  padding: 0;
  margin: 0 0 2rem 0;
  display: flex;
  flex-direction: column;
  gap: .65rem;
}
.bb-primera-cita__perks li {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-size: .9rem;
  color: rgba(255,255,255,.75);
}
.bb-primera-cita__perks svg {
  width: 16px;
  height: 16px;
  color: rgba(201,162,74,.9);
  flex-shrink: 0;
}

/* CTA */
.bb-primera-cita__cta {
  font-size: .95rem;
  padding: .85rem 1.6rem;
  border-radius: 1rem;
  font-weight: 700;
}
.bb-primera-cita__cta svg {
  width: 18px;
  height: 18px;
}

/* Nota legal */
.bb-primera-cita__note {
  margin-top: 1rem;
  font-size: .75rem;
  color: rgba(255,255,255,.35);
  letter-spacing: .025em;
}

/* Tarjeta decorativa */
.bb-primera-cita__card {
  flex-shrink: 0;
}
.bb-primera-cita__card-inner {
  background: rgba(255,255,255,.06);
  backdrop-filter: blur(16px) saturate(120%);
  -webkit-backdrop-filter: blur(16px) saturate(120%);
  border: 1px solid rgba(201,162,74,.22);
  border-radius: 1.5rem;
  padding: 2rem 1.75rem;
  width: 220px;
  text-align: center;
  box-shadow: 0 30px 60px rgba(0,0,0,.35), 0 0 0 1px rgba(201,162,74,.08) inset;
}
.bb-primera-cita__card-icon {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: rgba(201,162,74,.15);
  border: 1px solid rgba(201,162,74,.3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  color: rgba(201,162,74,.95);
}
.bb-primera-cita__card-icon svg {
  width: 26px;
  height: 26px;
}
.bb-primera-cita__card-label {
  font-size: .78rem;
  color: rgba(255,255,255,.5);
  letter-spacing: .05em;
  text-transform: uppercase;
  margin-bottom: .4rem;
}
.bb-primera-cita__card-discount {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 3.5rem;
  font-weight: 800;
  color: rgba(201,162,74,.95);
  line-height: 1;
  margin-bottom: .3rem;
  background: linear-gradient(135deg, rgba(201,162,74,1), rgba(231,215,161,1));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.bb-primera-cita__card-sub {
  font-size: .8rem;
  color: rgba(255,255,255,.55);
  margin-bottom: 1.2rem;
}
.bb-primera-cita__card-divider {
  height: 1px;
  background: rgba(201,162,74,.18);
  margin-bottom: 1rem;
}
.bb-primera-cita__card-footer {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .4rem;
  font-size: .78rem;
  color: rgba(201,162,74,.8);
  font-weight: 600;
}
.bb-primera-cita__card-footer svg {
  width: 14px;
  height: 14px;
}

/* Responsivo */
@media (max-width: 768px) {
  .bb-primera-cita {
    padding: 4rem 1.25rem;
  }
  .bb-primera-cita__inner {
    grid-template-columns: 1fr;
    gap: 2.5rem;
  }
  .bb-primera-cita__card {
    display: none;
  }
  .bb-primera-cita__percent {
    font-size: 6rem;
    opacity: .6;
  }
}
</style>
