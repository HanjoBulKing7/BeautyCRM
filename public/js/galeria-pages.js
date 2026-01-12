document.addEventListener("DOMContentLoaded", () => {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;

  gsap.registerPlugin(ScrollTrigger);

  const section = document.querySelector("#bb-pages");
  const wrap = section?.querySelector(".bb-pages__wrap");
  const pages = gsap.utils.toArray(".bb-page");

  if (!section || !wrap || pages.length === 0) return;

  // Coloca páginas apiladas (la primera arriba)
  gsap.set(pages, { zIndex: (i) => pages.length - i });

  // Estado inicial: solo la primera visible, las demás “abajo”
  gsap.set(pages.slice(1), { yPercent: 100 });

  // Timeline: cada scroll “sube” la siguiente página y tapa la anterior
  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: section,
      start: "top top",
      end: `+=${pages.length * 100}%`, // controla cuánto scroll dura
      scrub: true,
      pin: true,
      anticipatePin: 1,
    },
  });

  // Cada transición es un “paso”
  pages.slice(1).forEach((page) => {
    tl.to(page, { yPercent: 0, ease: "none", duration: 1 }, "+=0.15");
  });

  // (Opcional) pequeño “parallax” de imagen por página
  pages.forEach((page) => {
    const img = page.querySelector("img");
    if (!img) return;
    gsap.fromTo(
      img,
      { scale: 1.06 },
      {
        scale: 1.02,
        ease: "none",
        scrollTrigger: {
          trigger: section,
          start: "top top",
          end: `+=${pages.length * 100}%`,
          scrub: true,
        },
      }
    );
  });
});
