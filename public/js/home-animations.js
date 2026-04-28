/* ============================================================
   HOME — Animaciones globales
   Lenis 1.x + GSAP 3.12 + ScrollTrigger
   Cubre: servicios, métricas, reseñas, ubicación
   Hero → homehero.js | People → people.js (no se tocan)
   ============================================================ */

(function () {
  'use strict';

  if (!document.querySelector('.bb-homehero')) return;

  gsap.registerPlugin(ScrollTrigger);

  /* ── Lenis smooth scroll ───────────────────────────────── */
  const lenis = new Lenis({
    duration: 1.3,
    easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel: true,
    wheelMultiplier: 0.9,
  });

  /* Exponer para que hero.js lo tome y actualice el header */
  window.lenis = lenis;

  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add(time => lenis.raf(time * 1000));
  gsap.ticker.lagSmoothing(0);

  /* Smooth anchor clicks */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      lenis.scrollTo(target, { duration: 1.6 });
    });
  });

  /* ── Helper: revela sección header (kicker + título) ───── */
  function revealSectionHeader(section) {
    const kicker = section.querySelector('.gallery__kicker');
    const title  = section.querySelector('.gallery__big-title');
    const els    = [kicker, title].filter(Boolean);
    if (!els.length) return;

    gsap.set(els, { opacity: 0, y: 32 });
    gsap.to(els, {
      opacity: 1, y: 0,
      stagger: 0.14, duration: 0.85, ease: 'power3.out',
      scrollTrigger: {
        trigger: section,
        start: 'top 82%',
        toggleActions: 'play none none reverse',
      },
    });
  }

  /* ============================================================
     1 · SERVICIOS GALLERY (.gallery-section)
     ============================================================ */
  const gallerySection = document.querySelector('.gallery-section');
  if (gallerySection) {
    revealSectionHeader(gallerySection);

    const cards  = gallerySection.querySelectorAll('.gallery-card');
    const footer = gallerySection.querySelector('.gallery-footer');

    gsap.set(cards, { opacity: 0, y: 52, scale: 0.96 });
    gsap.to(cards, {
      opacity: 1, y: 0, scale: 1,
      stagger: 0.09, duration: 0.8, ease: 'power3.out',
      scrollTrigger: {
        trigger: gallerySection.querySelector('.gallery-wrapper'),
        start: 'top 84%',
        toggleActions: 'play none none reverse',
      },
    });

    if (footer) {
      gsap.set(footer, { opacity: 0, y: 20 });
      gsap.to(footer, {
        opacity: 1, y: 0, duration: 0.7, ease: 'power2.out',
        scrollTrigger: {
          trigger: footer,
          start: 'top 92%',
          toggleActions: 'play none none reverse',
        },
      });
    }
  }

  /* ============================================================
     2 · MÉTRICAS (#stats-counter-section)
     ============================================================ */
  const metricsSection = document.getElementById('stats-counter-section');
  if (metricsSection) {
    const items    = metricsSection.querySelectorAll('.grid > div');
    const counters = metricsSection.querySelectorAll('.js-stat-counter');
    let   counted  = false;

    gsap.set(items, { opacity: 0, y: 44 });

    ScrollTrigger.create({
      trigger: metricsSection,
      start: 'top 78%',
      toggleActions: 'play none none reverse',
      onEnter() {
        /* Reveal items */
        gsap.to(items, {
          opacity: 1, y: 0,
          stagger: 0.14, duration: 0.85, ease: 'power3.out',
        });

        /* Counter — solo una vez */
        if (counted) return;
        counted = true;

        counters.forEach(counter => {
          const target = +counter.getAttribute('data-target');
          const obj    = { val: 0 };
          gsap.to(obj, {
            val: target,
            duration: 2.4,
            ease: 'power2.out',
            onUpdate() {
              counter.textContent = Math.round(obj.val).toLocaleString('es-MX');
            },
            onComplete() {
              counter.textContent = target.toLocaleString('es-MX');
            },
          });
        });
      },
      onLeaveBack() {
        gsap.to(items, {
          opacity: 0, y: 44,
          stagger: 0.06, duration: 0.5, ease: 'power2.in',
        });
        /* Reset counters visually so they re-animate on next enter */
        counters.forEach(c => (c.textContent = '0'));
        counted = false;
      },
    });
  }

  /* ============================================================
     3 · RESEÑAS (.reviews-section)
     ============================================================ */
  const reviewsSection = document.querySelector('.reviews-section');
  if (reviewsSection) {
    revealSectionHeader(reviewsSection);

    const ratingSummary = reviewsSection.querySelector('.reviews-rating-summary');
    const cards         = reviewsSection.querySelectorAll('.review-card');
    const actionBtn     = reviewsSection.querySelector('.reviews-action');

    if (ratingSummary) {
      gsap.set(ratingSummary, { opacity: 0, y: 24 });
      gsap.to(ratingSummary, {
        opacity: 1, y: 0, duration: 0.75, ease: 'power2.out',
        scrollTrigger: {
          trigger: reviewsSection,
          start: 'top 80%',
          toggleActions: 'play none none reverse',
        },
      });
    }

    if (cards.length) {
      gsap.set(cards, { opacity: 0, y: 44 });
      gsap.to(cards, {
        opacity: 1, y: 0,
        stagger: 0.08, duration: 0.72, ease: 'power2.out',
        scrollTrigger: {
          trigger: reviewsSection.querySelector('.reviews-wrapper'),
          start: 'top 86%',
          toggleActions: 'play none none reverse',
        },
      });
    }

    if (actionBtn) {
      gsap.set(actionBtn, { opacity: 0, y: 20 });
      gsap.to(actionBtn, {
        opacity: 1, y: 0, duration: 0.65, ease: 'power2.out',
        scrollTrigger: {
          trigger: actionBtn,
          start: 'top 92%',
          toggleActions: 'play none none reverse',
        },
      });
    }
  }

  /* ============================================================
     4 · UBICACIÓN (.find-us-section)
     ============================================================ */
  const locationSection = document.querySelector('.find-us-section');
  if (locationSection) {
    revealSectionHeader(locationSection);

    const container = locationSection.querySelector('.find-us__container');
    const media     = locationSection.querySelector('.find-us__media');
    const mapCard   = locationSection.querySelector('.find-us__mapCard');

    if (media) {
      gsap.set(media, { opacity: 0, x: -56 });
      gsap.to(media, {
        opacity: 1, x: 0, duration: 1.0, ease: 'power3.out',
        scrollTrigger: {
          trigger: container || locationSection,
          start: 'top 78%',
          toggleActions: 'play none none reverse',
        },
      });
    }

    if (mapCard) {
      gsap.set(mapCard, { opacity: 0, x: 56 });
      gsap.to(mapCard, {
        opacity: 1, x: 0, duration: 1.0, delay: 0.16, ease: 'power3.out',
        scrollTrigger: {
          trigger: container || locationSection,
          start: 'top 78%',
          toggleActions: 'play none none reverse',
        },
      });
    }
  }

  /* ============================================================
     5 · PEOPLE section — título propio
     (cards y texto los maneja people.js con GSAP)
     ============================================================ */
  const peopleSection = document.querySelector('.bb-people');
  if (peopleSection) {
    const title = peopleSection.querySelector('.bb-people__title');
    if (title) {
      gsap.set(title, { opacity: 0, y: 36 });
      gsap.to(title, {
        opacity: 1, y: 0, duration: 0.9, ease: 'power3.out',
        scrollTrigger: {
          trigger: peopleSection,
          start: 'top 80%',
          toggleActions: 'play none none reverse',
        },
      });
    }
  }

})();
