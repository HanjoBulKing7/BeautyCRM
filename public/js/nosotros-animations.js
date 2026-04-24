/* ============================================================
   NOSOTROS — Animations
   Lenis 1.1 + GSAP 3.12 + ScrollTrigger
   ============================================================ */

(function () {
  'use strict';

  // Guard: only run when ns-wrapper is present
  if (!document.querySelector('.ns-wrapper')) return;

  /* ── Register GSAP plugins ─────────────────── */
  gsap.registerPlugin(ScrollTrigger);

  /* ── Lenis smooth scroll ───────────────────── */
  const lenis = new Lenis({
    duration: 1.4,
    easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel: true,
    wheelMultiplier: 0.9,
  });

  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add(time => lenis.raf(time * 1000));
  gsap.ticker.lagSmoothing(0);

  /* ── Header scroll class ───────────────────── */
  const header = document.querySelector('.header');
  const hero   = document.querySelector('.ns-hero');

  if (header && hero) {
    lenis.on('scroll', ({ scroll }) => {
      const threshold = hero.offsetHeight * 0.4;
      header.classList.toggle('on-scroll', scroll >= threshold);
    });
  }

  /* ── Header burger menu ────────────────────── */
  const burger   = header?.querySelector('.burger');
  const backdrop = header?.querySelector('.header-backdrop');
  const closeBtn = header?.querySelector('.close-menu');

  function openMenu() {
    burger?.classList.add('is-active');
    header?.classList.add('menu-is-active');
    document.body.classList.add('overflow-hidden');
    document.body.setAttribute('data-lenis-prevent', '');
  }

  function closeMenu() {
    burger?.classList.remove('is-active');
    header?.classList.remove('menu-is-active');
    document.body.classList.remove('overflow-hidden');
    document.body.removeAttribute('data-lenis-prevent');
  }

  burger?.addEventListener('click',   openMenu);
  backdrop?.addEventListener('click', closeMenu);
  closeBtn?.addEventListener('click', closeMenu);

  /* ── Hero entrance animation ───────────────── */
  gsap.timeline({ defaults: { ease: 'power3.out' } })
    .to('.ns-hero__label',      { opacity: 1, y: 0, duration: 1.0 }, 0.3)
    .to('.ns-hero__logo-img',   { opacity: 1, y: 0, scale: 1, duration: 1.3 }, 0.55)
    .to('.ns-hero__scroll-link',{ opacity: 1, y: 0, duration: 0.9 }, 1.25);

  /* ── Hero parallax on scroll ───────────────── */
  gsap.timeline({
    scrollTrigger: {
      trigger: '.ns-hero',
      start: 'top top',
      end: 'bottom top',
      scrub: 0.8,
      invalidateOnRefresh: true,
    }
  })
    .to('.ns-hero__sky',      { yPercent:  18, ease: 'none' }, 0)
    .to('.ns-hero__mountains',{ yPercent: -12, ease: 'none' }, 0)
    .to('.ns-hero__person',   { yPercent: -18, ease: 'none' }, 0)
    .to('.ns-hero__content',  { yPercent:  28, opacity: 0,  ease: 'none' }, 0);

  /* ── Section reveals ───────────────────────── */
  document.querySelectorAll('.ns-section').forEach((section, idx) => {
    const imgWrap = section.querySelector('.ns-section__img-wrap');
    const img     = section.querySelector('.ns-section__img-wrap img');
    const content = section.querySelector('.ns-section__content');
    const number  = section.querySelector('.ns-section__number');
    const label   = section.querySelector('.ns-section__label');
    const title   = section.querySelector('.ns-section__title');
    const text    = section.querySelector('.ns-section__text');
    const cta     = section.querySelector('.ns-section__cta');

    const isMobile = () => window.innerWidth <= 960;
    const isEven   = idx % 2 !== 0;

    /* Set initial clip-path via GSAP (inline style beats CSS, no !important conflict) */
    const clipStart = isMobile()
      ? 'inset(0 0 100% 0)'
      : isEven ? 'inset(0 0 0 100%)' : 'inset(0 100% 0 0)';
    gsap.set(imgWrap, { clipPath: clipStart });

    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: section,
        start: 'top 78%',
        end:   'top 20%',
        toggleActions: 'play none none reverse',
      }
    });

    /* Ghost number fades in first */
    if (number) {
      tl.to(number, { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out' }, 0);
    }

    /* Image clip reveal — always ends fully visible */
    tl.to(imgWrap, {
      clipPath: 'inset(0 0% 0 0%)',
      duration: 1.3,
      ease: 'power4.inOut',
    }, 0.05)
    .to(img, {
      scale: 1,
      duration: 1.5,
      ease: 'power3.out',
    }, 0.1);

    /* Content block slides up */
    tl.to(content, {
      opacity: 1,
      y: 0,
      duration: 0.9,
      ease: 'power3.out',
    }, 0.35);

    /* Inner elements stagger */
    const innerEls = [label, title, text, cta].filter(Boolean);
    tl.fromTo(innerEls,
      { opacity: 0, y: 28 },
      { opacity: 1, y: 0, stagger: 0.11, duration: 0.7, ease: 'power2.out' },
      0.52
    );
  });

  /* ── Progress bar ──────────────────────────── */
  const progressBar = document.querySelector('.ns-nav__bar');
  if (progressBar) {
    gsap.to(progressBar, {
      height: '100%',
      ease: 'none',
      scrollTrigger: {
        trigger: '.ns-wrapper',
        start: 'top top',
        end: 'bottom bottom',
        scrub: true,
      }
    });
  }

  /* ── Active nav item ───────────────────────── */
  const navItems  = document.querySelectorAll('.ns-nav__item');
  const landmarks = document.querySelectorAll('.ns-hero, .ns-section');

  function setActive(i) {
    navItems.forEach((item, j) => item.classList.toggle('is-active', j === i));
  }

  landmarks.forEach((el, i) => {
    ScrollTrigger.create({
      trigger: el,
      start: 'top 55%',
      end:   'bottom 55%',
      onEnter:     () => setActive(i),
      onEnterBack: () => setActive(i),
    });
  });

  /* ── Smooth anchor clicks ──────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      lenis.scrollTo(target, { offset: 0, duration: 1.6 });
    });
  });

})();
