// ============================
// ✅ 1. BLOQUEO NATIVO (Se ejecuta de inmediato)
// ============================
// Evita que el navegador guarde la posición del scroll
if ("scrollRestoration" in history) {
  history.scrollRestoration = "manual";
}

// ============================
// ✅ 2. EL TRUCO DEL SETTIMEOUT
// ============================
// Retrasamos la inicialización para ganarle al navegador y la carga de imágenes
setTimeout(() => {
  
  // Limpia la memoria de ScrollTrigger antes de que calcule nada
  ScrollTrigger.clearScrollMemory("manual");
  
  // Obliga a la ventana a subir
  window.scrollTo(0, 0);

  // ============================
  // ✅ LENIS + GSAP
  // ============================

  // Initialize Lenis
  const lenis = new Lenis();

  // Fuerza a Lenis a ir al tope inmediatamente sin animación
  lenis.scrollTo(0, { immediate: true });

  // Sync Lenis with ScrollTrigger
  lenis.on("scroll", ScrollTrigger.update);

  // Use Lenis RAF in GSAP ticker
  gsap.ticker.add((time) => {
    lenis.raf(time * 1000);
  });

  // Disable GSAP lag smoothing
  gsap.ticker.lagSmoothing(0);

  // ============================
  // ✅ HERO INTRO ANIMATION
  // ============================
  const heroTitle = document.querySelectorAll(".Normal-hero-title span");
  const heroSubtitle = document.querySelector(".Normal-hero-subtitle");
  const heroAction = document.querySelector(".Normal-hero-action");
  const sliderListItem = document.querySelectorAll(".Normal-slider-list-item");
  const sliderProgress = document.querySelector(".Normal-slider-progress");

  gsap.fromTo(
    [heroSubtitle, heroTitle, heroAction, sliderListItem],
    { autoAlpha: 0, y: 100, stagger: 0.2 },
    { autoAlpha: 1, y: 0, stagger: 0.2 }
  );

  gsap.fromTo(
    sliderProgress,
    { autoAlpha: 0, y: "100" },
    { autoAlpha: 1, y: "0", delay: 1 }
  );

  // ============================
  // ✅ HERO PARALLAX ON SCROLL
  // ============================
  gsap
    .timeline({
      scrollTrigger: {
        trigger: ".Normal-hero-section",
        start: "top top",
        end: "bottom top",
        scrub: 0.5,
        invalidateOnRefresh: true,
      },
    })
    .to(".sky", { y: 1000 }, "0")
    .to(".mountains", { y: -300 }, "0")
    .to(".man-standing", { y: -100 }, "0")
    .to(".Normal-hero-content", { y: 450, autoAlpha: 0 }, "0");

  // ============================
  // ✅ CONTENT SECTIONS ANIMATION
  // ============================
  const contentRows = document.querySelectorAll(".Normal-content-row");

  contentRows.forEach((row) => {
    const imageWrapper = row.querySelector(".Normal-content-image");
    const image = imageWrapper?.querySelector("img");

    const counter = row.querySelector(".counter");
    const subtitle = row.querySelectorAll(".Normal-content-subtitle");
    const title = row.querySelectorAll(".Normal-content-title span");
    const description = row.querySelectorAll(".Normal-content-copy");
    const action = row.querySelectorAll(".Normal-content-action");

    gsap
      .timeline({
        scrollTrigger: {
          trigger: row,
          start: "center-=100 center",
          end: "center top",
          scrub: 0.2,
          pin: row,
          invalidateOnRefresh: true,
        },
      })
      .fromTo(
        [subtitle, title, description, action],
        { autoAlpha: 0, y: 100, stagger: 0.2 },
        { autoAlpha: 1, y: 0, stagger: 0.2 },
        "0"
      )
      .fromTo(counter, { autoAlpha: 0 }, { autoAlpha: 1 }, "0")
      .fromTo(image, { autoAlpha: 0, scale: 1.5 }, { autoAlpha: 1, scale: 1 }, "0");
  });

  // ============================
  // ✅ SLIDER PROGRESS BAR
  // ============================
  gsap.to(".Normal-slider-progress-bar", {
    height: "100%",
    ease: "none",
    scrollTrigger: { scrub: 0.3 },
  });

  // ============================
  // ✅ 3. EL RECALCULO FINAL (VITAL PARA LA GALERÍA Y HOME)
  // ============================
  // Le dice a GSAP que vuelva a medir todo una vez que Lenis y las animaciones cargaron
  ScrollTrigger.refresh();

}, 50); // 50 milisegundos de retraso