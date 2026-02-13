(() => {
  function initEmpleadoServicios(root = document) {
    root.querySelectorAll('[data-empleado-servicios]').forEach(wrapper => {
      if (wrapper.dataset.bound === '1') return;
      wrapper.dataset.bound = '1';

      const select   = wrapper.querySelector('.js-servicio-selector');
      const cont     = wrapper.querySelector('.js-servicios-seleccionados');
      const countEl  = wrapper.querySelector('.js-servicios-count');
      const emptyEl  = wrapper.querySelector('.js-servicios-empty');

      if (!select || !cont) return;

      const refreshUI = () => {
        const count = cont.querySelectorAll('[data-id]').length;
        if (countEl) countEl.textContent = String(count);
        if (emptyEl) emptyEl.classList.toggle('hidden', count > 0);
      };

      const exists = (id) => !!cont.querySelector(`[data-id="${CSS.escape(String(id))}"]`);

      const addTag = (id, label) => {
        const v = String(id || '').trim();
        if (!v) return;
        if (exists(v)) { refreshUI(); return; }

        const pill = document.createElement('span');
        pill.className = 'flex items-center gap-2 px-3 py-1 rounded-full text-sm border';
        pill.style.background = 'rgba(201,162,74,.12)';
        pill.style.borderColor = 'rgba(201,162,74,.35)';
        pill.dataset.id = v;

        pill.innerHTML = `
          <span class="max-w-[240px] truncate">${label || ('Servicio #' + v)}</span>
          <button type="button" class="text-gray-500 hover:text-red-600 js-remove-servicio" title="Quitar">✕</button>
          <input type="hidden" name="servicios[]" value="${v}">
        `;

        cont.appendChild(pill);
        refreshUI();
      };

      // Quitar servicio
      cont.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-remove-servicio');
        if (!btn) return;
        const pill = btn.closest('[data-id]');
        if (pill) pill.remove();
        refreshUI();
      });

      // Agregar por change (sin TomSelect)
      const handleChange = () => {
        const v = select.value;
        if (!v) return;
        const label = select.options[select.selectedIndex]?.textContent?.trim() || v;
        addTag(v, label);
        select.value = '';
      };
      select.addEventListener('change', handleChange);

      // Si tienes TomSelect cargado globalmente, lo montamos aquí también
      if (window.TomSelect && !select.tomselect) {
        new TomSelect(select, {
          create: false,
          maxItems: 1,
          placeholder: 'Selecciona un servicio',
          closeAfterSelect: true,
          onItemAdd(value) {
            const opt = this.getOption(value);
            const label = opt ? opt.textContent.trim() : value;
            addTag(value, label);
            this.clear(true);
          }
        });
      }

      refreshUI();
    });
  }

  // ✅ Inicializar normal
  const boot = () => initEmpleadoServicios(document);

  // ✅ MUTATION OBSERVER para modales/AJAX: detecta cuando se inyecta el form
  const observe = () => {
    let scheduled = false;
    const schedule = () => {
      if (scheduled) return;
      scheduled = true;
      requestAnimationFrame(() => {
        scheduled = false;
        initEmpleadoServicios(document);
      });
    };

    const obs = new MutationObserver(schedule);
    obs.observe(document.documentElement, { childList: true, subtree: true });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { boot(); observe(); });
  } else {
    boot(); observe();
  }

  // por si quieres llamarlo manual desde consola
  window.initEmpleadoServicios = initEmpleadoServicios;
})();
