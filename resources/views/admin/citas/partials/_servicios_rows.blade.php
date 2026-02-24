{{-- ======================================
   PARTIAL: Servicios (UI tipo Agendar + rows ocultos para lógica existente)
   Archivo: resources/views/admin/citas/partials/_servicios_rows.blade.php
   Requiere: $mode, $cita, $categorias, $bbField, $bbIconColor
   Depende de tu JS actual en el form (listeners sobre #servicios-wrapper)
====================================== --}}

@php
  // ✅ Normaliza categorías a array de strings (sirve si viene Collection<Model> o array<string>)
  $categoriasList = collect($categorias ?? [])
    ->map(function ($c) {
      if (is_string($c)) return $c;
      return $c->nombre ?? $c->name ?? null;
    })
    ->filter()
    ->values()
    ->all();
@endphp

@php
    // Fallback (si no hay imagen) => SVG inline (no depende de archivos)
    $fallbackImg = "data:image/svg+xml;utf8," . rawurlencode('
      <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="700">
        <defs>
          <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#F4EBDD"/>
            <stop offset="1" stop-color="#FFFFFF"/>
          </linearGradient>
        </defs>
        <rect width="1200" height="700" fill="url(#g)"/>
        <circle cx="980" cy="140" r="120" fill="rgba(184,134,11,0.10)"/>
        <circle cx="240" cy="560" r="180" fill="rgba(184,134,11,0.08)"/>
        <text x="60" y="110" font-family="Inter, Arial" font-size="54" fill="rgba(26,26,26,0.55)" font-weight="800">Servicio</text>
        <text x="60" y="170" font-family="Inter, Arial" font-size="26" fill="rgba(26,26,26,0.45)">Imagen no disponible</text>
      </svg>
    ');

    // Intentar tomar imagen del modelo Servicio sin forzar estructura
    $serviciosUi = collect($servicios ?? [])->map(function ($s) {
        $img =
            $s->imagen
            ?? $s->image
            ?? $s->foto
            ?? $s->imagen_url
            ?? $s->ruta_imagen
            ?? $s->url_imagen
            ?? null;

        return [
            'id'        => $s->id_servicio ?? $s->id ?? null,
            'nombre'    => $s->nombre_servicio ?? $s->nombre ?? $s->name ?? 'Servicio',
            'categoria' => $s->categoria->nombre ?? $s->categoria ?? 'Sin categoría',
            'duracion'  => (int) ($s->duracion_minutos ?? $s->duracion ?? 0),
            'precio'    => (float) ($s->precio ?? 0),
            'imagen'    => $img,
        ];
    })->filter(fn($x) => !empty($x['id']))->values();

    $assetRoot    = rtrim(asset(''), '/');
    $assetStorage = rtrim(asset('storage'), '/');
@endphp

{{-- =========================
   CSS (scoped) para look tipo Agendar
========================= --}}
@push('styles')
<style>
  .bb-admin-services .bb-panel{
    border-radius:22px;
    border:1px solid rgba(0,0,0,0.08);
    box-shadow:0 18px 40px rgba(0,0,0,0.06);
    background:#fff;
    padding:18px;
    margin-top:14px;
  }
  .bb-admin-services .bb-panel__title{
    font-weight:800;
    font-size:14px;
    letter-spacing:.12em;
    text-transform:uppercase;
    margin:0 0 12px;
    color:rgba(26,26,26,0.85);
  }
  .bb-admin-services .bb-badge{
    display:inline-flex;
    font-weight:800;
    font-size:12px;
    letter-spacing:.12em;
    text-transform:uppercase;
    color:#b8860b;
    margin:0 0 8px;
  }
  .bb-admin-services .bb-label{ font-weight:600; font-size:13px; color:rgba(26,26,26,0.80); }
  .bb-admin-services .bb-hint{ font-size:13px; color:rgba(26,26,26,0.62); }
  .bb-admin-services .bb-empty{
    padding:14px 16px;
    border-radius:14px;
    border:1px dashed rgba(0,0,0,0.12);
    background:rgba(244,235,221,0.35);
    color:rgba(26,26,26,0.70);
  }

  /* Categorías (chips) */
  .bb-admin-services .bb-cat__list{ display:flex; flex-wrap:wrap; gap:10px; }
  .bb-admin-services .bb-cat-btn{
    border:1px solid rgba(184,134,11,0.28);
    background:rgba(244,235,221,0.55);
    color:rgba(26,26,26,0.88);
    padding:10px 14px;
    border-radius:999px;
    font-weight:900;
    font-size:12px;
    letter-spacing:.08em;
    text-transform:uppercase;
    cursor:pointer;
    transition:transform 150ms ease, filter 150ms ease;
  }
  .bb-admin-services .bb-cat-btn:hover{ transform:translateY(-1px); filter:brightness(1.03); }
  .bb-admin-services .bb-cat-btn.is-active{
    background:rgba(184,134,11,0.12);
    border-color:rgba(184,134,11,0.55);
    box-shadow:0 10px 22px rgba(184,134,11,0.10);
  }

  /* Cards de servicios (más compactas, en fila como la referencia) */
  .bb-admin-services .bb-svc__cards{
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 320px));
    gap:16px;
    justify-content: start; /* evita que una sola card se estire */
    align-items: stretch;
  }
  @media (max-width:1024px){
    .bb-admin-services .bb-svc__cards{ grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
  }
  @media (max-width:640px){
    .bb-admin-services .bb-svc__cards{ grid-template-columns: 1fr; }
  }

  .bb-admin-services .bb-svc-card{
    border-radius:18px;
    border:1px solid rgba(0,0,0,0.08);
    background:#fff;
    overflow:hidden;
    box-shadow:0 16px 32px rgba(0,0,0,0.06);
    cursor:pointer;
    transition:transform 160ms ease, filter 160ms ease;
    display:flex; flex-direction:column;
  }
  .bb-admin-services .bb-svc-card:hover{ transform:translateY(-2px); filter:brightness(1.02); }
  .bb-admin-services .bb-svc-card.is-disabled{ opacity:.55; cursor:not-allowed; }
  .bb-admin-services .bb-svc-card.is-disabled:hover{ transform:none; filter:none; }

  .bb-admin-services .bb-svc-card__media img{
    width:100%; height:170px; object-fit:cover; display:block; background:#f4ebdd;
  }
  .bb-admin-services .bb-svc-card__body{ padding:12px 12px 12px; display:flex; flex-direction:column; gap:10px; }
  .bb-admin-services .bb-svc-card__name{ font-weight:900; color:rgba(26,26,26,0.90); }
  .bb-admin-services .bb-svc-card__meta{
    display:flex; align-items:center; justify-content:space-between;
    font-size:13px; color:rgba(26,26,26,0.72);
  }
  .bb-admin-services .bb-btn--soft{
    width:100%;
    border-radius:999px;
    padding:10px 14px;
    border:1px solid rgba(184,134,11,0.35);
    background:rgba(244,235,221,0.70);
    color:#b8860b;
    font-weight:900;
    letter-spacing:.06em;
    text-transform:uppercase;
    font-size:12px;
  }
  .bb-admin-services .bb-btn--soft:disabled{ opacity:.7; cursor:not-allowed; }

  /* Selected cards (compactas y en grid como la referencia) */
  .bb-admin-services .bb-selectedList{
    width:100%;
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 340px));
    gap:16px;
    justify-content:start;
    align-items:stretch;
  }

  .bb-admin-services .bb-selectedCard{
    position:relative;
    border-radius:18px;
    border:1px solid rgba(0,0,0,0.08);
    overflow:hidden;
    background:#fff;
    box-shadow:0 16px 32px rgba(0,0,0,0.06);
    display:flex;
    flex-direction:column;
  }

  .bb-admin-services .bb-selectedCard__media{
    position:relative;
    background:#f4ebdd;
  }
  .bb-admin-services .bb-selectedCard__img{
    width:100%;
    height:180px;
    object-fit:cover;
    display:block;
  }

  .bb-admin-services .bb-selectedCard__info{
    padding:12px 12px 14px;
    display:flex;
    flex-direction:column;
    gap:10px;
  }
  .bb-admin-services .bb-selectedCard__name{
    font-weight:900;
    font-size:16px;
    margin:0;
    color:rgba(26,26,26,0.90);
  }

  .bb-admin-services .bb-selectedCard__metaRow{
    display:flex;
    align-items:center;
    justify-content:space-between;
    font-size:13px;
    color:rgba(26,26,26,0.72);
  }

  .bb-admin-services .bb-select{
    width:100%;
    border:1px solid rgba(0,0,0,0.10);
    border-radius:14px;
    padding:12px 14px;
    background:#fff;
    outline:none;
  }

  .bb-admin-services .bb-selectedCard__remove{
    position:absolute;
    top:12px;
    right:12px;
    width:38px;
    height:38px;
    border-radius:999px;
    border:1px solid rgba(184,134,11,0.35);
    background:rgba(255,255,255,0.92);
    color:#b8860b;
    font-size:20px;
    cursor:pointer;
    display:grid;
    place-items:center;
    box-shadow:0 10px 22px rgba(0,0,0,0.10);
  }
  .bb-admin-services .bb-selectedCard__remove:hover{ transform:translateY(-1px); filter:brightness(1.03); }

  @media (max-width:640px){
    .bb-admin-services .bb-selectedList{ grid-template-columns: 1fr; }
    .bb-admin-services .bb-selectedCard__img{ height:220px; }
  }
</style>
@endpush

<div class="md:col-span-2 bb-admin-services">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        <i class="fas fa-scissors mr-1" style="{{ $bbIconColor }}"></i>
        Servicios <span class="text-red-500">*</span>
    </label>

    {{-- =========================
       1) Servicios seleccionados (UI)
    ========================== --}}
    <section class="bb-panel">
        <p class="bb-badge">Servicios seleccionados</p>

        <div id="bbSelectedListAdmin" class="bb-selectedList">
            <div class="bb-empty">Selecciona un servicio para iniciar la cita.</div>
        </div>

        <p class="bb-hint" style="margin-top:.75rem;">
            Puedes cambiar el empleado por servicio. Al seleccionar empleados, se habilita la disponibilidad de fecha/hora.
        </p>
    </section>

    {{-- =========================
       2) Agregar otro servicio (categorías + cards)
    ========================== --}}
    <section class="bb-panel">
        <h3 class="bb-panel__title">Agregar otro servicio</h3>

        <div class="bb-label" style="margin-bottom:.5rem;">Categorías</div>
        <div id="bbCategoryListAdmin" class="bb-cat__list"></div>

        <p class="bb-hint" style="margin-top:.5rem;">
            Selecciona una categoría para ver servicios.
        </p>

        <div style="margin-top: 1rem;">
            <div id="bbServiceCardsAdmin" class="bb-svc__cards"></div>
        </div>
    </section>

    {{-- =========================
       3) Rows originales (ocultos) => tu lógica actual sigue funcionando
    ========================== --}}
    <div id="bbRowsHiddenAdmin" class="hidden">
        <div id="servicios-wrapper" class="space-y-3">

            {{-- =========================
                EDIT: filas precargadas
            ========================== --}}
            @if($mode === 'edit' && $cita && $cita->servicios && $cita->servicios->count())
                @foreach($cita->servicios as $i => $svc)
                    <div
                        class="servicio-row bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center"
                        data-index="{{ $i }}"
                    >
                        {{-- CATEGORÍA --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                            <select class="categoria-select {{ $bbField }}" data-role="categoria">
                                <option value="">Seleccionar categoría</option>
                                @foreach($categoriasList as $cat)
                                    <option value="{{ $cat }}" @selected($cat === ($svc->categoria ?? ''))>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SERVICIO --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Servicio <span class="text-red-500">*</span></label>
                            <select
                                name="servicios[{{ $i }}][id_servicio]"
                                data-role="servicio"
                                data-selected="{{ $svc->id_servicio }}"
                                class="servicio-select {{ $bbField }}"
                                required
                            >
                                <option value="">Cargando servicios…</option>
                            </select>
                        </div>

                        {{-- EMPLEADO (por servicio) --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                            <select
                                name="servicios[{{ $i }}][id_empleado]"
                                data-role="empleado"
                                data-preselect="{{ old("servicios.$i.id_empleado", $svc->pivot->id_empleado ?? '') }}"
                                class="{{ $bbField }}"
                                disabled
                            >
                                <option value="">Selecciona un servicio primero</option>
                            </select>
                        </div>

                        {{-- PRECIO --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="servicios[{{ $i }}][precio_snapshot]"
                                value="{{ old("servicios.$i.precio_snapshot", $svc->pivot->precio_snapshot ?? '') }}"
                                data-role="precio_snapshot"
                                class="precio-input {{ $bbField }}"
                            >
                        </div>

                        {{-- DURACIÓN --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                            <input
                                type="number"
                                step="1"
                                min="0"
                                name="servicios[{{ $i }}][duracion_snapshot]"
                                value="{{ old("servicios.$i.duracion_snapshot", $svc->pivot->duracion_snapshot ?? '') }}"
                                data-role="duracion_snapshot"
                                class="duracion-input {{ $bbField }}"
                            >
                        </div>

                        {{-- QUITAR --}}
                        <div class="md:col-span-1 flex items-center justify-center mt-7">
                            <button
                                type="button"
                                class="btn-remove-servicio w-12 h-12 rounded-lg bg-red-500 text-white hover:bg-red-600 transition"
                                title="Quitar servicio"
                            >
                                <i class="fas fa-times text-lg leading-none"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- =========================
                CREATE: row base (clonable)
            ========================== --}}
            <div
                class="servicio-row bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center"
                data-index="0"
            >
                {{-- CATEGORÍA --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select id="categoria_main" class="categoria-select {{ $bbField }}" data-role="categoria">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categoriasList as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SERVICIO --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Servicio <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="servicio_main"
                        name="servicios[0][id_servicio]"
                        data-role="servicio"
                        class="servicio-select {{ $bbField }}"
                        required
                    >
                        <option value="">Selecciona primero una categoría</option>
                    </select>
                </div>

                {{-- EMPLEADO --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select
                        name="servicios[0][id_empleado]"
                        data-role="empleado"
                        class="{{ $bbField }}"
                        disabled
                    >
                        <option value="">Selecciona un servicio primero</option>
                    </select>
                </div>

                {{-- PRECIO --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                    <div class="relative">
                        <span class="absolute left-3 top-0 bottom-0 flex items-center text-gray-500">$</span>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="servicios[0][precio_snapshot]"
                            class="precio-input {{ $bbField }} pl-9"
                            placeholder="0.00"
                            data-role="precio_snapshot"
                        >
                    </div>
                </div>

                {{-- DURACIÓN --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                    <input
                        type="number"
                        step="1"
                        min="0"
                        name="servicios[0][duracion_snapshot]"
                        class="duracion-input {{ $bbField }}"
                        placeholder="min"
                        data-role="duracion_snapshot"
                    >
                </div>

                {{-- QUITAR --}}
                <div class="md:col-span-1 flex items-center justify-center mt-7">
                    <button
                        type="button"
                        class="btn-remove-servicio remove-servicio w-12 h-12 inline-flex items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600 transition"
                        title="Quitar servicio"
                    >
                        <i class="fas fa-times text-lg leading-none"></i>
                    </button>
                </div>
            </div>

            <button
                type="button"
                id="btn-add-servicio"
                class="mt-2 inline-flex items-center text-sm font-semibold transition"
                style="color: rgba(201,162,74,.95)"
            >
                <i class="fas fa-plus-circle mr-2"></i>
                Agregar otro servicio
            </button>

            @error('servicios')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- =========================
   JS puente (solo UI) => sincroniza UI con rows ocultos
========================= --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.getElementById('servicios-wrapper');
  const btnAdd  = document.getElementById('btn-add-servicio');

  const elCats   = document.getElementById('bbCategoryListAdmin');
  const elCards  = document.getElementById('bbServiceCardsAdmin');
  const elSel    = document.getElementById('bbSelectedListAdmin');

  if (!wrapper || !elCats || !elCards || !elSel) return;

  // Data desde PHP
  const CATEGORIAS = @json($categoriasList);
  const SERVICIOS  = @json($serviciosUi);
  const ASSET_ROOT = @json($assetRoot);
  const ASSET_STO  = @json($assetStorage);
  const FALLBACK   = @json($fallbackImg);

  // uid estable por row
  if (!window.__bbAdminRowUid) window.__bbAdminRowUid = 1;

  const norm = (v) => String(v ?? '').trim().toLowerCase();
  const money = (n) => Number(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }
  function joinUrl(base, path) {
    const b = String(base || '').replace(/\/+$/, '');
    const p = String(path || '').replace(/^\/+/, '');
    return b ? `${b}/${p}` : p;
  }
  function resolveImg(img) {
    if (!img) return FALLBACK;
    if (/^https?:\/\//i.test(img)) return img;

    const s = String(img);
    if (s.startsWith('images/') || s.startsWith('/images/')) return joinUrl(ASSET_ROOT, s);
    return joinUrl(ASSET_STO, s);
  }

  function ensureRowUids() {
  const seen = new Set();

  wrapper.querySelectorAll('.servicio-row').forEach(row => {
    let uid = row.dataset.bbUid;

    // ✅ si no hay uid o está repetido, genera uno nuevo
    if (!uid || seen.has(uid)) {
      uid = 'bb_' + (window.__bbAdminRowUid++);
      row.dataset.bbUid = uid; // => data-bb-uid
    }

    seen.add(uid);
  });
}


  function rows() {
    ensureRowUids();
    return [...wrapper.querySelectorAll('.servicio-row')];
  }

  function serviceById(id) {
    return SERVICIOS.find(s => String(s.id) === String(id)) || null;
  }

  function selectedServiceIds() {
    return rows()
      .map(r => r.querySelector('select[data-role="servicio"]')?.value || '')
      .filter(Boolean)
      .map(String);
  }

  // Encuentra una row vacía; si no hay, crea una usando el botón oculto
  function ensureEmptyRow() {
    let r = rows().find(row => !(row.querySelector('select[data-role="servicio"]')?.value || '').trim());
    if (r) return r;

    if (btnAdd) btnAdd.click(); // dispara tu clon y reindex
    r = rows().find(row => !(row.querySelector('select[data-role="servicio"]')?.value || '').trim());
    return r || null;
  }

  function setCategoriaValue(select, categoria) {
    if (!select) return;
    const target = norm(categoria);
    const opt = [...select.options].find(o => norm(o.value) === target);
    if (opt) select.value = opt.value;
    else select.value = categoria;
  }

  // Estado UI
  let activeCat = CATEGORIAS?.[0] || null;

  function renderCats() {
    if (!CATEGORIAS.length) {
      elCats.innerHTML = `<div class="bb-empty">No hay categorías.</div>`;
      return;
    }
    if (!activeCat) activeCat = CATEGORIAS[0];

    elCats.innerHTML = CATEGORIAS.map(c => {
      const active = norm(c) === norm(activeCat);
      return `<button type="button" class="bb-cat-btn ${active ? 'is-active' : ''}" data-cat="${escapeHtml(c)}">${escapeHtml(c)}</button>`;
    }).join('');

    elCats.querySelectorAll('.bb-cat-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        activeCat = btn.getAttribute('data-cat');
        renderCats();
        renderServiceCards();
      });
    });
  }

  function renderServiceCards() {
    if (!activeCat) {
      elCards.innerHTML = `<div class="bb-empty">Selecciona una categoría para ver servicios.</div>`;
      return;
    }

    const selected = new Set(selectedServiceIds());
    const list = SERVICIOS.filter(s => norm(s.categoria) === norm(activeCat));

    if (!list.length) {
      elCards.innerHTML = `<div class="bb-empty">No hay servicios en esta categoría.</div>`;
      return;
    }

    elCards.innerHTML = list.map(s => {
      const already = selected.has(String(s.id));
      const imgSrc = resolveImg(s.imagen);
      return `
        <article class="bb-svc-card ${already ? 'is-disabled' : ''}" data-svc="${escapeHtml(s.id)}">
          <div class="bb-svc-card__media">
            <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(s.nombre)}" loading="lazy">
          </div>
          <div class="bb-svc-card__body">
            <div class="bb-svc-card__name">${escapeHtml(s.nombre)}</div>
            <div class="bb-svc-card__meta">
              <span>${Number(s.duracion || 0)} min</span>
              <span><strong>$${money(s.precio)}</strong></span>
            </div>
            <button type="button" class="bb-btn--soft" ${already ? 'disabled' : ''}>
              ${already ? 'Agregado' : 'Agregar'}
            </button>
          </div>
        </article>
      `;
    }).join('');

    elCards.querySelectorAll('.bb-svc-card').forEach(card => {
      card.addEventListener('click', () => {
        const id = card.getAttribute('data-svc');
        if (!id) return;

        if (selectedServiceIds().includes(String(id))) return;

        const svc = serviceById(id);
        if (!svc) return;

        const row = ensureEmptyRow();
        if (!row) return;

        const catSel = row.querySelector('select[data-role="categoria"]');
        const svcSel = row.querySelector('select[data-role="servicio"]');

        // 1) set categoría (dispara tu buildOptions)
        if (catSel) {
          setCategoriaValue(catSel, svc.categoria);
          catSel.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // 2) set servicio (dispara tu load empleados + snapshots + recalcs)
        setTimeout(() => {
          if (svcSel) {
            svcSel.value = String(id);
            svcSel.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }, 0);

        setTimeout(() => {
          renderSelectedCards();
          renderServiceCards();
        }, 50);
      });
    });
  }

  function buildEmpleadoSelectFromHidden(hiddenSelect, uid) {
    if (!hiddenSelect) {
      return `<select class="bb-select" disabled><option value="">Sin empleados</option></select>`;
    }
    const disabled = hiddenSelect.disabled ? 'disabled' : '';
    const opts = [...hiddenSelect.options].map(o => {
      const sel = o.selected ? 'selected' : '';
      return `<option value="${escapeHtml(o.value)}" ${sel}>${escapeHtml(o.textContent)}</option>`;
    }).join('');
    return `<select class="bb-select" data-emp-uid="${escapeHtml(uid)}" ${disabled}>${opts}</select>`;
  }

  function renderSelectedCards() {
    const allRows = rows();

    const selectedRows = allRows
      .filter(r => (r.querySelector('select[data-role="servicio"]')?.value || '').trim())
      .map(r => {
        const uid = r.dataset.bbUid;
        const svcId = r.querySelector('select[data-role="servicio"]')?.value || '';
        const svc = serviceById(svcId);

        const precioSnap = r.querySelector('input[data-role="precio_snapshot"]')?.value;
        const durSnap    = r.querySelector('input[data-role="duracion_snapshot"]')?.value;

        const precio = (precioSnap !== '' && precioSnap != null) ? Number(precioSnap) : Number(svc?.precio || 0);
        const dur    = (durSnap !== '' && durSnap != null) ? Number(durSnap) : Number(svc?.duracion || 0);

        const empSelHidden = r.querySelector('select[data-role="empleado"]');

        return { row: r, uid, svcId, svc, precio, dur, empSelHidden };
      });

    if (!selectedRows.length) {
      elSel.innerHTML = `<div class="bb-empty">Selecciona un servicio para iniciar la cita.</div>`;
      return;
    }

    elSel.innerHTML = selectedRows.map(({ uid, svc, svcId, precio, dur, empSelHidden }) => {
      const name = svc?.nombre || `Servicio #${svcId}`;
      const img  = resolveImg(svc?.imagen);
      const empHtml = buildEmpleadoSelectFromHidden(empSelHidden, uid);

      return `
        <article class="bb-selectedCard" data-uid="${escapeHtml(uid)}">
          <div class="bb-selectedCard__media">
            <img src="${escapeHtml(img)}" class="bb-selectedCard__img" alt="${escapeHtml(name)}" loading="lazy" />
            <button type="button" class="bb-selectedCard__remove" data-remove="${escapeHtml(uid)}" title="Quitar">✕</button>
          </div>

          <div class="bb-selectedCard__info">
            <h3 class="bb-selectedCard__name">${escapeHtml(name)}</h3>

            <div class="bb-selectedCard__metaRow">
              <span>${Number(dur || 0)} min</span>
              <span><strong>$${money(precio)}</strong></span>
            </div>

            <div>
              <label class="bb-label" style="margin-bottom:.35rem;">Empleado</label>
              ${empHtml}
            </div>
          </div>
        </article>
      `;
    }).join('');

    // Remove => click al botón real (para que tu JS haga remove + reindex + recalcs + horas)
    elSel.querySelectorAll('.bb-selectedCard__remove').forEach(btn => {
      btn.addEventListener('click', () => {
        const uid = btn.getAttribute('data-remove');
        const row = wrapper.querySelector(`.servicio-row[data-bb-uid="${CSS.escape(uid)}"]`);
        if (!row) return;

        const realRemove = row.querySelector('.btn-remove-servicio');
        if (realRemove) realRemove.click();
        else row.remove();

        setTimeout(() => {
          renderSelectedCards();
          renderServiceCards();
        }, 30);
      });
    });

    // Empleado change (UI) => sincroniza hidden select y dispara change
    elSel.querySelectorAll('select[data-emp-uid]').forEach(sel => {
      sel.addEventListener('change', () => {
        const uid = sel.getAttribute('data-emp-uid');
        const row = wrapper.querySelector(`.servicio-row[data-bb-uid="${CSS.escape(uid)}"]`);
        const hidden = row?.querySelector('select[data-role="empleado"]');
        if (!hidden) return;

        hidden.value = sel.value;
        hidden.dispatchEvent(new Event('change', { bubbles: true }));

        setTimeout(() => renderSelectedCards(), 20);
      });
    });
  }

  // Re-render cuando cambien rows ocultos (tu JS carga empleados async)
  wrapper.addEventListener('change', () => {
    setTimeout(() => {
      renderSelectedCards();
      renderServiceCards();
    }, 10);
  });

  // MutationObserver: cuando tu JS actual rellena selects (empleados) o agrega/borra rows
  const mo = new MutationObserver(() => {
    renderSelectedCards();
    renderServiceCards();
  });
  mo.observe(wrapper, { childList: true, subtree: true });

  // Init
  renderCats();
  renderServiceCards();
  renderSelectedCards();
});
</script>
@endpush
