document.addEventListener("DOMContentLoaded", () => {
  const section = document.querySelector("#about-salon");
  if (!section) return;

  const observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        section.classList.add("is-visible");
        observer.disconnect();
      }
    },
    { threshold: 0.2 }
  );

  observer.observe(section);
});
