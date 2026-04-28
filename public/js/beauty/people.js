/* ============================================================
   PEOPLE SECTION — Animaciones
   Sin dependencia de AOS: usa GSAP fromTo
   ============================================================ */

(() => {
  const section = document.querySelector('.bb-people');
  if (!section) return;

  if (!window.gsap || !window.ScrollTrigger) {
    console.warn('[people] Falta GSAP/ScrollTrigger.');
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  /* ── Bloque de texto + tarjeta promo (fila superior) ─── */
  const reveals = section.querySelectorAll('.js-reveal');
  if (reveals.length) {
    gsap.set(reveals, { opacity: 0, y: 50 });
    gsap.to(reveals, {
      opacity: 1, y: 0,
      stagger: 0.14, duration: 0.9, ease: 'power3.out',
      scrollTrigger: {
        trigger: section,
        start: 'top 75%',
        toggleActions: 'play none none reverse',
      },
    });
  }

  /* ── Tarjetas de navegación (fila inferior) ──────────── */
  const cards = section.querySelectorAll('.js-card');
  gsap.set(cards, { opacity: 0, y: 50 });

  cards.forEach(card => {
    gsap.to(card, {
      opacity: 1, y: 0,
      duration: 0.9, ease: 'power3.out',
      scrollTrigger: {
        trigger: card,
        start: 'top 86%',
        toggleActions: 'play none none reverse',
      },
    });

    /* Parallax sutil en cada imagen */
    const img = card.querySelector('img');
    if (img) {
      gsap.fromTo(img,
        { y: 12 },
        {
          y: -12, ease: 'none',
          scrollTrigger: {
            trigger: card,
            start: 'top bottom',
            end: 'bottom top',
            scrub: true,
          },
        }
      );
    }
  });

  /* Asegurar que ScrollTrigger recalcule posiciones */
  requestAnimationFrame(() => ScrollTrigger.refresh());
})();
