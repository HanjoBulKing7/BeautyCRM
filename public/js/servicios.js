document.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const cards = gsap.utils.toArray(".sticky-cards .card");
  const rotations = [-12, -10, -5, 5, -5, -2];

  // orden visual (la última arriba)
  cards.forEach((card, index) => {
    gsap.set(card, {
      zIndex: index + 1,
      transformOrigin: "50% 50%",
      rotate: rotations[index] ?? 0,
      x: 0,
      y: window.innerHeight,
    });
  });

  const HOLD_SEGMENTS = 1;
  const segments = cards.length + HOLD_SEGMENTS;

  ScrollTrigger.create({
    trigger: ".sticky-cards",
    start: "top top",
    end: () => `+=${window.innerHeight * segments}`,
    pin: true,
    pinSpacing: true,
    scrub: 1,
    markers: false,
    invalidateOnRefresh: true,

    onUpdate: (self) => {
      const progress = self.progress; // 0..1
      const progressPerCard = 1 / segments;

      cards.forEach((card, index) => {
        const cardStart = index * progressPerCard;

        let cardProgress = (progress - cardStart) / progressPerCard;
        cardProgress = Math.min(Math.max(cardProgress, 0), 1);

        let yPos = window.innerHeight * (1 - cardProgress);
        let xPos = 0;

        if (cardProgress === 1 && index < cards.length - 1) {
          const afterThisCard = cardStart + progressPerCard;
          const remainingProgress = (progress - afterThisCard) / (1 - afterThisCard);

          if (remainingProgress > 0) {
            const distanceMultiplier = 1 - index * 0.15;
            xPos = -window.innerWidth * 0.35 * distanceMultiplier * remainingProgress;
            yPos = -window.innerHeight * 0.30 * distanceMultiplier * remainingProgress;
          }
        }

        gsap.set(card, { x: xPos, y: yPos });
      });
    },
  });

  window.addEventListener("load", () => ScrollTrigger.refresh());
});
