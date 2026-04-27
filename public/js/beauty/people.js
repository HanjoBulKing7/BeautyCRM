(() => {
  const section = document.querySelector('.bb-people');
  if (!section) return;

  if (!window.gsap || !window.ScrollTrigger) {
    console.warn('[people] Falta GSAP/ScrollTrigger.');
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  // Texto + card de agendar: los maneja home-animations.js con fromTo
  // Aquí sólo añadimos el parallax suave en las imágenes

  section.querySelectorAll('.js-card').forEach(card => {
    const img = card.querySelector('img');
    if (img) {
      gsap.fromTo(
        img,
        { y: 10 },
        {
          y: -10,
          ease: 'none',
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
})();
