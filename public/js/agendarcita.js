document.addEventListener("DOMContentLoaded", () => {
  const select = document.querySelector("#extraService");
  const addBtn = document.querySelector("#addServiceBtn");
  const list = document.querySelector("#selectedServicesList");

  const submitBtn = document.querySelector("#submitBooking");
  const note = document.querySelector("#bookingNote");

  if (!select || !addBtn || !list) return;

  // Imagen temporal para todos
  const defaultImg = "/images/Beige%20Blogger%20Moderna%20Personal%20Sitio%20web.png";

  // Evitar duplicados (incluye el principal)
  const selected = new Set(
    Array.from(list.querySelectorAll("[data-service]")).map((el) => el.dataset.service)
  );

  function createCard(serviceName) {
    const article = document.createElement("article");
    article.className = "bb-selectedCard";
    article.dataset.service = serviceName;

    // Info básica (por ahora). Luego lo conectas con tus datos reales.
    const meta = {
      duration: "60 min",
      price: "$___",
      includes: "Servicio profesional con atención personalizada",
    };

    article.innerHTML = `
      <div class="bb-selectedCard__media">
        <img class="bb-selectedCard__img" src="${defaultImg}" alt="${serviceName}">
      </div>

      <div class="bb-selectedCard__info">
        <h2 class="bb-selectedCard__name">${serviceName}</h2>
        <ul class="bb-selectedCard__meta">
          <li><strong>Duración:</strong> ${meta.duration}</li>
          <li><strong>Desde:</strong> ${meta.price}</li>
          <li><strong>Incluye:</strong> ${meta.includes}</li>
        </ul>
      </div>

      <button type="button" class="bb-selectedCard__remove" title="Quitar ${serviceName}">×</button>
    `;

    article.querySelector(".bb-selectedCard__remove").addEventListener("click", () => {
      selected.delete(serviceName);
      article.remove();
    });

    return article;
  }

  addBtn.addEventListener("click", () => {
    const value = select.value.trim();
    if (!value) return;

    // No duplicar
    if (selected.has(value)) {
      select.value = "";
      return;
    }

    selected.add(value);
    list.appendChild(createCard(value));
    select.value = "";
  });

  // Mensaje final
  if (submitBtn && note) {
    submitBtn.addEventListener("click", () => {
      note.hidden = false;
      note.scrollIntoView({ behavior: "smooth", block: "center" });
    });
  }
});
