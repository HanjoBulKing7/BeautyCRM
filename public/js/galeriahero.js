(() => {
  // Evita doble init si por algún motivo se carga 2 veces
  if (window.__BB_HERO_INIT__) return;
  window.__BB_HERO_INIT__ = true;

  gsap.registerPlugin(ScrollTrigger);

  window.addEventListener("load", () => {
    const root = document.querySelector(".hero-wrapper-element");
    if (!root) return;

    // Si existía un trigger previo por hot reload / navegación, lo matamos
    const prev = ScrollTrigger.getById("bb-hero");
    if (prev) prev.kill(true);

    const tl = gsap.timeline({
      scrollTrigger: {
        id: "bb-hero",
        trigger: root,
        start: "top top",
        end: "+=70%",
        pin: true,
        scrub: true,
        invalidateOnRefresh: true,
        anticipatePin: 1,
        // markers: true,
      },
    });

    const heroImg = document.querySelector(".hero-image-container img");
    if (heroImg) {
      tl.to(heroImg, {
        scale: 2,
        z: 250,
        transformOrigin: "center center",
      });
    }

    tl.to(
      ".hero-section.hero-main-section",
      {
        scale: 1.4,
        boxShadow: "10000px 0 0 0 rgba(0,0,0,0.5) inset",
        transformOrigin: "center center",
      },
      "<"
    )
      .to(".hero-image-container", { autoAlpha: 0 })
      // ✅ En vez de cambiar height (causa refresh/layout shift), desvanecemos
      .to(".hero-intro", { autoAlpha: 0 }, "<");

    // Asegura cálculos correctos cuando hay varios pins
    ScrollTrigger.refresh();
  });
})();
