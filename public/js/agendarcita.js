document.addEventListener("DOMContentLoaded", () => {
  const services = window.__SERVICIOS__ || {};

  const select = document.querySelector("#extraService");
  const addBtn = document.querySelector("#addServiceBtn");
  const list = document.querySelector("#selectedServicesList");
  const form = document.querySelector("#bookingForm");

  const submitBtn = document.querySelector("#submitBooking");
  const note = document.querySelector("#bookingNote");

  if (!select || !addBtn || !list || !form) return;

  // Fallback (public/images) - mismo que ya usabas
  const fallbackImg = "/images/Beige%20Blogger%20Moderna%20Personal%20Sitio%20web.png";

  // -----------------------------
  // Helpers
  // -----------------------------
  const resolveImg = (img) => {
    if (!img) return fallbackImg;

    // Si viene "images/..." (public/images)
    if (img.startsWith("images/")) return "/" + img;

    // Si viene del storage public: "servicios/xxx.png"
    return "/storage/" + img.replace(/^\/+/, "");
  };

  const parseFeatures = (raw) => {
    if (!raw) return [];
    try {
      const v = JSON.parse(raw);
      return Array.isArray(v) ? v : [];
    } catch {
      return [];
    }
  };

  const getSelectedIds = () => {
    return Array.from(list.querySelectorAll("[data-service-id]"))
      .map((el) => Number(el.getAttribute("data-service-id")))
      .filter(Boolean);
  };

  const addHiddenExtra = (id) => {
    // evita duplicar hidden
    if (form.querySelector(`input[data-extra="${id}"]`)) return;

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "id_servicios[]";
    input.value = String(id);
    input.dataset.extra = String(id);
    form.appendChild(input);
  };

  const removeHiddenExtra = (id) => {
    const input = form.querySelector(`input[data-extra="${id}"]`);
    if (input) input.remove();
  };

  function createCardFromService(svc) {
    const article = document.createElement("article");
    article.className = "bb-selectedCard";
    article.dataset.serviceId = String(svc.id);

    const features = parseFeatures(svc.caracteristicas);
    const priceFinal = Math.max(0, (svc.precio || 0) - (svc.descuento || 0));

    article.innerHTML = `
      <div class="bb-selectedCard__media">
        <img
          class="bb-selectedCard__img"
          src="${resolveImg(svc.imagen)}"
          alt="${svc.nombre}"
          loading="lazy"
        >
      </div>

      <div class="bb-selectedCard__info">
        <h2 class="bb-selectedCard__name">${svc.nombre}</h2>
        <ul class="bb-selectedCard__meta">
          <li><strong>Duración:</strong> ${svc.duracion} min</li>
          <li><strong>Desde:</strong> $${priceFinal.toFixed(2)}</li>
          <li><strong>Incluye:</strong> ${features[0] || "Servicio profesional con atención personalizada"}</li>
        </ul>
      </div>

      <button type="button" class="bb-selectedCard__remove" title="Quitar ${svc.nombre}">×</button>
    `;

    article.querySelector(".bb-selectedCard__remove").addEventListener("click", () => {
      const id = Number(article.dataset.serviceId);
      article.remove();
      removeHiddenExtra(id);

      // (Opcional) re-habilitar opción en select
      const opt = select.querySelector(`option[value="${id}"]`);
      if (opt) opt.disabled = false;
    });

    return article;
  }

  // -----------------------------
  // Al cargar: registrar principal si existe
  // (principal viene en HTML como data-service-id)
  // -----------------------------
  const initiallySelected = new Set(getSelectedIds());

  // -----------------------------
  // Agregar extra
  // -----------------------------
  addBtn.addEventListener("click", () => {
    const id = Number(select.value);
    if (!id) return;

    const svc = services[id];
    if (!svc) return;

    // No duplicar
    if (initiallySelected.has(id)) {
      select.value = "";
      return;
    }

    // Agregar card
    list.appendChild(createCardFromService(svc));

    // Hidden input para backend (extras)
    addHiddenExtra(id);

    // bloquear esa opción para evitar duplicados por UI
    const opt = select.querySelector(`option[value="${id}"]`);
    if (opt) opt.disabled = true;

    initiallySelected.add(id);
    select.value = "";
  });

  // -----------------------------
  // Mensaje final (como tu versión vieja)
  // -----------------------------
  if (submitBtn && note) {
    submitBtn.addEventListener("click", () => {
      note.hidden = false;
      note.scrollIntoView({ behavior: "smooth", block: "center" });
    });
  }
});
