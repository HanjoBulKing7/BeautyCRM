function refreshAOS() {
  if (!window.AOS) return;
  if (typeof window.AOS.refreshHard === "function") window.AOS.refreshHard();
  else if (typeof window.AOS.refresh === "function") window.AOS.refresh();
}

(() => {
  // Si no existe la sección, no hacemos nada
  const section = document.querySelector(".bb-people");
  if (!section) return;

  // Requiere GSAP + ScrollTrigger (CDN o bundle)
  if (!window.gsap || !window.ScrollTrigger) {
    console.warn("[people] Falta GSAP/ScrollTrigger.");
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  // Reveal del bloque superior
  gsap.to(section.querySelectorAll(".js-reveal"), {
    opacity: 1,
    y: 0,
    duration: 0.9,
    ease: "power3.out",
    stagger: 0.12,
    scrollTrigger: {
      trigger: section,
      start: "top 75%",
      toggleActions: "play none none reverse",
    },
    onStart: () => {
      // Por si AOS ya está inicializado, refrescamos cálculos
      refreshAOS();
    },
  });

  // Cards: entrada + mini-parallax suave
  section.querySelectorAll(".js-card").forEach((card) => {
    gsap.to(card, {
      opacity: 1,
      y: 0,
      duration: 0.9,
      ease: "power3.out",
      scrollTrigger: {
        trigger: card,
        start: "top 85%",
        toggleActions: "play none none reverse",
      },
      onStart: () => {
        refreshAOS();
      },
    });

    // Parallax: mueve la imagen ligeramente mientras scrolleas
    const img = card.querySelector("img");
    if (img) {
      gsap.fromTo(
        img,
        { y: 10 },
        {
          y: -10,
          ease: "none",
          scrollTrigger: {
            trigger: card,
            start: "top bottom",
            end: "bottom top",
            scrub: true,
          },
        }
      );
    }
  });

  // Refresh inicial por si el layout cambió
  requestAnimationFrame(() => refreshAOS());
})();
