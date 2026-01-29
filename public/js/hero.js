// public/js/hero.js

document.addEventListener("DOMContentLoaded", () => {
    const header = document.querySelector(".Normal-header");
    if (!header) return;

    // Estado inicial
    header.classList.add("is-visible");
    header.classList.remove("is-hidden");

    let lastScrollY = window.scrollY || 0;
    let ticking = false;

    const MIN_DELTA = 6; // sensibilidad para evitar parpadeos
    const SCROLL_OFFSET = 800; // a partir de aquí aplica .on-scroll

    function apply(scrollY) {
        // 1) on-scroll (para padding compacto y background)
        if (scrollY > SCROLL_OFFSET) header.classList.add("on-scroll");
        else header.classList.remove("on-scroll");

        // 2) Detectar dirección de scroll (con "dead zone" MIN_DELTA)
        const delta = scrollY - lastScrollY;

        if (Math.abs(delta) < MIN_DELTA) {
            lastScrollY = scrollY;
            return;
        }

        // Scroll hacia abajo => ocultar
        if (delta > 0 && scrollY > 120) {
            header.classList.add("is-hidden");
            header.classList.remove("is-visible");
        }

        // Scroll hacia arriba => mostrar
        if (delta < 0) {
            header.classList.add("is-visible");
            header.classList.remove("is-hidden");
        }

        lastScrollY = scrollY;
    }

    // --- Scroll normal (window) ---
    function onScroll() {
        if (ticking) return;
        ticking = true;

        requestAnimationFrame(() => {
            apply(window.scrollY || 0);
            ticking = false;
        });
    }

    window.addEventListener("scroll", onScroll, { passive: true });

    // --- Si estás usando LENIS ---
    // Lenis no siempre dispara window.scroll igual, entonces nos colgamos del callback.
    if (window.lenis && typeof window.lenis.on === "function") {
        window.lenis.on("scroll", ({ scroll }) => {
            // scroll viene como número
            apply(scroll || 0);
        });
    }

    // Estado inicial al cargar
    apply(window.scrollY || 0);
});
// public/js/hero.js

document.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector(".Normal-header");
  if (!header) return;

  const burger = header.querySelector(".Normal-burger");
  const closeBtn = header.querySelector(".Normal-close-menu");
  const menu = header.querySelector(".Normal-menu");
  const backdrop = header.querySelector(".Normal-header-backdrop");
  const menuLinks = header.querySelectorAll(".Normal-menu-link, .Normal-menu-footer-cta");

  // =========================
  // MENU TOGGLE
  // =========================
  const openMenu = () => {
    header.classList.add("Normal-menu-is-active");
    document.body.classList.add("overflow-hidden");

    burger?.setAttribute("aria-expanded", "true");
    menu?.setAttribute("aria-hidden", "false");
  };

  const closeMenu = () => {
    header.classList.remove("Normal-menu-is-active");
    document.body.classList.remove("overflow-hidden");

    burger?.setAttribute("aria-expanded", "false");
    menu?.setAttribute("aria-hidden", "true");
  };

  burger?.addEventListener("click", () => {
    const isOpen = header.classList.contains("Normal-menu-is-active");
    isOpen ? closeMenu() : openMenu();
  });

  closeBtn?.addEventListener("click", closeMenu);
  backdrop?.addEventListener("click", closeMenu);

  // cerrar al click en links (móvil)
  menuLinks.forEach((a) => a.addEventListener("click", closeMenu));

  // cerrar con ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
  });

  // =========================
  // HEADER SCROLL (tu lógica)
  // =========================
  header.classList.add("is-visible");
  header.classList.remove("is-hidden");

  let lastScrollY = window.scrollY || 0;
  let ticking = false;

  const MIN_DELTA = 6;
  const SCROLL_OFFSET = 800;

  function apply(scrollY) {
    // on-scroll background
    if (scrollY > SCROLL_OFFSET) header.classList.add("on-scroll");
    else header.classList.remove("on-scroll");

    // si el menú está abierto, no ocultes header
    if (header.classList.contains("Normal-menu-is-active")) {
      header.classList.add("is-visible");
      header.classList.remove("is-hidden");
      lastScrollY = scrollY;
      return;
    }

    const delta = scrollY - lastScrollY;

    if (Math.abs(delta) < MIN_DELTA) {
      lastScrollY = scrollY;
      return;
    }

    if (delta > 0 && scrollY > 120) {
      header.classList.add("is-hidden");
      header.classList.remove("is-visible");
    }

    if (delta < 0) {
      header.classList.add("is-visible");
      header.classList.remove("is-hidden");
    }

    lastScrollY = scrollY;
  }

  function onScroll() {
    if (ticking) return;
    ticking = true;

    requestAnimationFrame(() => {
      apply(window.scrollY || 0);
      ticking = false;
    });
  }

  window.addEventListener("scroll", onScroll, { passive: true });

  if (window.lenis && typeof window.lenis.on === "function") {
    window.lenis.on("scroll", ({ scroll }) => {
      apply(scroll || 0);
    });
  }

  apply(window.scrollY || 0);
});
