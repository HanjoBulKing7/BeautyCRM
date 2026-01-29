(() => {
  const hero = document.querySelector(".bb-homehero");
  if (!hero) return;

  if (!window.gsap || !window.ScrollTrigger) {
    console.warn("[homehero] Falta GSAP/ScrollTrigger");
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  const bg = hero.querySelector(".bb-homehero__bg");
  const overlay = hero.querySelector(".bb-homehero__overlay");
  const text = hero.querySelector(".bb-homehero__text");

  // Timeline con scrub para que siga el scroll
  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: hero,
      start: "top top",
      end: "+=140%",      // controla cuánto dura el efecto
      scrub: true,
      pin: true,          // fija el hero mientras ocurre el efecto
      anticipatePin: 1,
    }
  });

  // 1) zoom imagen + oscurecer overlay
  tl.to(bg, {
    scale: 1.22,
    ease: "none",
  }, 0);

  tl.to(overlay, {
    backgroundColor: "rgba(0,0,0,0.72)", // “se pone negra”
    ease: "none",
  }, 0);

  // 2) texto hace zoom
  tl.to(text, {
    scale: 1.15,
    ease: "none",
  }, 0);

  // 3) al final el texto desaparece
  tl.to(text, {
    opacity: 0,
    ease: "none",
  }, 0.55); // empieza a desvanecer a la mitad
})();
