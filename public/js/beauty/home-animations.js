/* ============================================================
   HOME — Smooth scroll (Lenis) + Scroll-triggered animations
   Lenis 1.1 + GSAP 3.12 + ScrollTrigger
   Reemplaza AOS para que nada anime antes de que llegues
   ============================================================ */

(function () {
  'use strict';

  if (!window.gsap || !window.ScrollTrigger) {
    console.warn('[home-animations] GSAP / ScrollTrigger no encontrado.');
    return;
  }
  if (typeof Lenis === 'undefined') {
    console.warn('[home-animations] Lenis no encontrado.');
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  /* ── Lenis smooth scroll ─────────────────────────────── */
  const lenis = new Lenis({
    duration       : 1.25,
    easing         : t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel    : true,
    wheelMultiplier: 0.85,
  });

  // Conectar con GSAP ticker para que ScrollTrigger conozca la posición real
  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add(time => lenis.raf(time * 1000));
  gsap.ticker.lagSmoothing(0);

  // Exponer para que otros scripts puedan usarlo
  window.__lenis = lenis;

  /* ── Smooth anchor clicks ────────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      lenis.scrollTo(target, { offset: -80, duration: 1.6 });
    });
  });

  /* ── Helper: reveal de una sola vez al entrar en viewport ── */
  function revealFrom(targets, fromVars, toVars, triggerEl, startPos) {
    gsap.set(targets, fromVars);
    gsap.to(targets, {
      ...toVars,
      scrollTrigger: {
        trigger  : triggerEl,
        start    : startPos || 'top 82%',
        toggleActions: 'play none none none',  // solo se reproduce una vez
      },
    });
  }

  function revealStagger(targets, fromVars, toVars, triggerEl, startPos, staggerVal) {
    gsap.set(targets, fromVars);
    gsap.to(targets, {
      ...toVars,
      stagger: staggerVal || 0.10,
      scrollTrigger: {
        trigger  : triggerEl,
        start    : startPos || 'top 82%',
        toggleActions: 'play none none none',
      },
    });
  }

  /* ── SECCIÓN: people ─────────────────────────────────── */
  const peopleSec = document.querySelector('.bb-people');
  if (peopleSec) {
    // Texto izquierda
    const textEl = peopleSec.querySelector('.bb-people__text');
    if (textEl) revealFrom(textEl, { opacity: 0, x: -40 }, { opacity: 1, x: 0, duration: 0.9, ease: 'power3.out' }, peopleSec, 'top 80%');

    // Card derecha (agendar)
    const promoEl = peopleSec.querySelector('.bb-people__top-promoted');
    if (promoEl) revealFrom(promoEl, { opacity: 0, x: 40 }, { opacity: 1, x: 0, duration: 0.9, ease: 'power3.out' }, peopleSec, 'top 75%');

    // Cards bottom — stagger
    const cards = peopleSec.querySelectorAll('.js-card');
    if (cards.length) {
      revealStagger(cards, { opacity: 0, y: 36 }, { opacity: 1, y: 0, duration: 0.75, ease: 'power3.out' }, peopleSec, 'top 70%', 0.12);
    }
  }

  /* ── SECCIÓN: servicios home (gallery-section) ────────── */
  const serviciosSec = document.querySelector('.gallery-section');
  if (serviciosSec) {
    // Heading
    const heading = serviciosSec.querySelector('.gallery__most-requested');
    if (heading) revealFrom(heading, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }, serviciosSec, 'top 85%');

    // Tarjetas de servicio — stagger
    const gCards = serviciosSec.querySelectorAll('.gallery-card');
    if (gCards.length) {
      revealStagger(gCards, { opacity: 0, y: 40, scale: 0.97 }, { opacity: 1, y: 0, scale: 1, duration: 0.7, ease: 'power3.out' }, serviciosSec, 'top 80%', 0.09);
    }

    // Footer link
    const footer = serviciosSec.querySelector('.gallery-footer');
    if (footer) revealFrom(footer, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.7, ease: 'power2.out' }, serviciosSec, 'top 60%');
  }

  /* ── SECCIÓN: stats / métricas ────────────────────────── */
  const statsSec = document.getElementById('stats-counter-section');
  if (statsSec) {
    const statItems = statsSec.querySelectorAll('.grid > div');
    if (statItems.length) {
      revealStagger(statItems, { opacity: 0, y: 32 }, { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }, statsSec, 'top 82%', 0.14);
    }
  }

  /* ── SECCIÓN: reseñas ─────────────────────────────────── */
  const resenasSec = document.getElementById('resenas');
  if (resenasSec) {
    const rHead = resenasSec.querySelector('.gallery__most-requested');
    const rSummary = resenasSec.querySelector('.reviews-rating-summary');
    if (rHead)    revealFrom(rHead, { opacity: 0, y: 28 }, { opacity: 1, y: 0, duration: 0.75, ease: 'power3.out' }, resenasSec, 'top 85%');
    if (rSummary) revealFrom(rSummary, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out', delay: 0.12 }, resenasSec, 'top 82%');

    const reviewCards = resenasSec.querySelectorAll('.review-card');
    if (reviewCards.length) {
      revealStagger(reviewCards, { opacity: 0, y: 28 }, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out' }, resenasSec, 'top 78%', 0.06);
    }
  }

  /* ── SECCIÓN: ubicación ───────────────────────────────── */
  const findUsSec = document.getElementById('find-us');
  if (findUsSec) {
    const infoBar = findUsSec.querySelector('.find-us__info-bar');
    const media   = findUsSec.querySelector('.find-us__media');
    const mapCard = findUsSec.querySelector('.find-us__mapCard');
    const heading = findUsSec.querySelector('.gallery__most-requested');

    if (heading)  revealFrom(heading, { opacity: 0, y: 28 }, { opacity: 1, y: 0, duration: 0.75, ease: 'power3.out' }, findUsSec, 'top 85%');
    if (infoBar)  revealFrom(infoBar, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out' }, findUsSec, 'top 82%');

    const sides = [media, mapCard].filter(Boolean);
    if (sides.length) {
      revealStagger(sides, { opacity: 0, y: 36 }, { opacity: 1, y: 0, duration: 0.85, ease: 'power3.out' }, findUsSec, 'top 78%', 0.15);
    }
  }

  /* ── Refresh por resize ───────────────────────────────── */
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => ScrollTrigger.refresh(), 200);
  });

})();
