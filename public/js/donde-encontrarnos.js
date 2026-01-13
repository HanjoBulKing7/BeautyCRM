document.addEventListener("DOMContentLoaded", () => {
  const mapCard = document.querySelector(".find-us__mapCard");
  const iframe = document.querySelector(".find-us__map");
  if (!mapCard || !iframe) return;

  // Si en 4.5s no cargó, mostramos fallback (evita el cuadro gris feo)
  const timer = setTimeout(() => {
    mapCard.classList.add("is-fallback");
  }, 4500);

  iframe.addEventListener("load", () => {
    clearTimeout(timer);
    // Si cargó, no hacemos nada
  });

  iframe.addEventListener("error", () => {
    clearTimeout(timer);
    mapCard.classList.add("is-fallback");
  });
});
