const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
});

function raf(time) {
  lenis.raf(time);
  ScrollTrigger.update();
  requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

const section_1 = document.getElementById("lenis-vertical");
const col_left = document.querySelector(".lenis-col_left");
const timeln = gsap.timeline({ paused: true });

timeln.fromTo(col_left, { y: 0 }, { y: "170vh", duration: 1, ease: "none" }, 0);

const scroll_1 = ScrollTrigger.create({
  animation: timeln,
  trigger: section_1,
  start: "top top",
  end: "bottom center",
  scrub: true,
});

const section_2 = document.getElementById("lenis-horizontal");
let box_items = gsap.utils.toArray(".lenis-horizontal__item");

gsap.to(box_items, {
  xPercent: -100 * (box_items.length - 1),
  ease: "sine.out",
  scrollTrigger: {
    trigger: section_2,
    pin: true,
    scrub: 3,
    snap: 1 / (box_items.length - 1),
    end: "+=" + section_2.offsetWidth,
  },
});

// Toggle del menú móvil (si existe en tu HTML)
document.addEventListener("DOMContentLoaded", function () {
  const menuBtn = document.getElementById("lenis-menu-btn");
  const mobileMenu = document.querySelector(".lenis-mobile-menu");

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener("click", function () {
      mobileMenu.classList.toggle("active");
    });

    // Cerrar menú al hacer clic fuera de él
    document.addEventListener("click", function (event) {
      if (
        menuBtn &&
        mobileMenu &&
        !menuBtn.contains(event.target) &&
        !mobileMenu.contains(event.target)
      ) {
        mobileMenu.classList.remove("active");
      }
    });
  }
});

console.log("Lenis Scroll Component initialized successfully!");