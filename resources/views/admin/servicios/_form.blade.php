@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Fila 1: Nombre del Servicio y Categoría -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-tag mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre del Servicio <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre_servicio"
            value="{{ old('nombre_servicio', $servicio->nombre_servicio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: Corte de Cabello, Manicure"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-layer-group mr-2" style="color: rgba(201,162,74,.92)"></i>
            Categoría
        </label>

        <select
            name="id_categoria"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
        >
            <option value="">— Sin categoría —</option>

            @foreach(($categorias ?? []) as $cat)
                <option value="{{ $cat->id_categoria }}"
                    {{ (string)old('id_categoria', $servicio->id_categoria ?? '') === (string)$cat->id_categoria ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>

        @error('id_categoria')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>


    <!-- Fila 2: Precio y Duración -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-dollar-sign mr-2" style="color: rgba(201,162,74,.92)"></i>
            Precio <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="precio"
            step="0.01"
            min="0"
            value="{{ old('precio', $servicio->precio ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="0.00"
        >
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-clock mr-2" style="color: rgba(201,162,74,.92)"></i>
            Duración (minutos) <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="duracion_minutos"
            min="1"
            value="{{ old('duracion_minutos', $servicio->duracion_minutos ?? '') }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            required
            placeholder="Ej: 60"
        >
    </div>

    <!-- Fila 3: Descuento y Estado 
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-percent mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descuento
        </label>
        <input
            type="number"
            name="descuento"
            step="0.01"
            min="0"
            value="{{ old('descuento', $servicio->descuento ?? 0) }}"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="0.00"
        >
    </div>
    -->

    @if(!empty($showEstado))
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <label class="block text-sm font-medium mb-2 text-gray-700">
                <i class="fas fa-toggle-on mr-2" style="color: rgba(201,162,74,.92)"></i>
                Estado <span class="text-red-500">*</span>
            </label>

            <select
                name="estado"
                class="w-full border border-gray-300 rounded-lg p-3 transition
                       focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
                required
            >
                <option value="activo" {{ old('estado', $servicio->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $servicio->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    @endif

    <!-- Fila 4: Descripción -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-align-left mr-2" style="color: rgba(201,162,74,.92)"></i>
            Descripción
        </label>
        <textarea
            name="descripcion"
            rows="3"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
            placeholder="Descripción detallada del servicio"
        >{{ old('descripcion', $servicio->descripcion ?? '') }}</textarea>
    </div>

    {{-- Imagen del servicio --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-image mr-2" style="color: rgba(201,162,74,.92)"></i>
            Foto del Servicio
        </label>

        <input
            type="file"
            name="imagen"
            id="imagenInput"
            accept="image/*"
            class="w-full border border-gray-300 rounded-lg p-3 transition
                   focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
        >

        @error('imagen')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

        <p class="text-xs text-gray-500 mt-2">
            Formatos recomendados: JPG/PNG/WEBP (máx 2MB).
        </p>

        {{-- Preview --}}
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Vista previa</p>

            <img
                id="imagenPreview"
                src="{{ isset($servicio) && $servicio->imagen ? asset('storage/' . ltrim($servicio->imagen, '/')) : '' }}"
                alt="Preview"
                class="hidden w-full max-w-md rounded-xl border border-gray-200 shadow-sm object-cover"
                style="aspect-ratio: 16 / 9;"
            >
        </div>
    </div>

 {{-- =========================
    HORARIOS DEL SERVICIO (GRID 30 min)
    ========================= --}}
    @php
    $gridStart = '07:00';
    $gridEnd   = '21:00';
    $slotMin   = 30;

    $daysMap = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    // Columnas de tiempo cada 30 min
    $times = [];
    $t = \Carbon\Carbon::createFromFormat('H:i', $gridStart);
    $end = \Carbon\Carbon::createFromFormat('H:i', $gridEnd);
    while ($t->lt($end)) {
        $times[] = $t->format('H:i');
        $t->addMinutes($slotMin);
    }

    // Prefill para edit desde servicio_horarios
    $existingRanges = [];
    if(isset($servicio) && $servicio && $servicio->exists) {
        foreach(($servicio->horarios ?? []) as $h) {
        $existingRanges[$h->dia_semana][] = [
            'inicio' => substr((string)$h->hora_inicio, 0, 5),
            'fin'    => substr((string)$h->hora_fin, 0, 5),
        ];
        }
    }
    @endphp

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
    <div class="flex items-start justify-between gap-3">
        <div>
        <label class="block text-sm font-medium text-gray-700">
            <i class="fas fa-calendar-alt mr-2" style="color: rgba(201,162,74,.92)"></i>
            Horarios del Servicio
        </label>
        <p class="text-xs text-gray-500 mt-1">
            Selecciona bloques de <b>30 min</b>. Tip: clic y arrastra para pintar.
        </p>
        </div>

        <div class="flex gap-2">
        <button type="button" id="bbGridClearAll" class="text-sm px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50">
            Limpiar
        </button>
        <button type="button" id="bbGridCopyMonday" class="text-sm px toggle px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50">
            Copiar Lunes
        </button>
        </div>
    </div>

    {{-- ✅ JSON (para rehidratar/depurar) --}}
    <input type="hidden" name="horarios_grid" id="horarios_grid" value="{{ old('horarios_grid') }}">

    {{-- ✅ inputs reales para el backend (horarios[d][i][hora_inicio|hora_fin]) --}}
    <div id="horariosFields" class="hidden"></div>


    <div id="bbScheduleGrid"
        class="mt-4 overflow-auto border border-gray-200 rounded-xl bg-white"
        data-slot-minutes="{{ $slotMin }}"
        data-start="{{ $gridStart }}"
        data-end="{{ $gridEnd }}"
        data-existing='@json($existingRanges)'>
        <table class="min-w-max w-full border-separate" style="border-spacing:0;">
        <thead>
            <tr>
            <th class="sticky top-0 left-0 z-20 bg-gray-50 text-xs text-gray-700 border-b border-r border-gray-200 p-2 text-left min-w-[130px]">
                Día
            </th>
            @foreach($times as $time)
                <th class="sticky top-0 z-10 bg-gray-50 text-xs text-gray-700 border-b border-r border-gray-200 p-2">
                {{ $time }}
                </th>
            @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($daysMap as $d => $label)
            <tr>
                <th class="sticky left-0 z-10 bg-white text-xs text-gray-700 border-b border-r border-gray-200 p-2 text-left min-w-[130px]">
                {{ $label }}
                </th>
                @foreach($times as $time)
                <td class="border-b border-r border-gray-200 p-2 text-center">
                    <button type="button"
                            class="bb-slot"
                            data-day="{{ $d }}"
                            data-time="{{ $time }}"
                            aria-pressed="false"></button>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>

    {{-- errores por si backend valida horarios_grid o horarios --}}
    @error('horarios_grid')
        <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
    @enderror
    @error('horarios')
        <p class="text-red-500 text-sm mt-3">{{ is_array($message) ? implode(', ', $message) : $message }}</p>
    @enderror
    </div>

    <style>
    :root { --bb-gold:#C9A24A; --bb-border:#E5E7EB; }
    .bb-slot{
        width:18px; height:18px; border-radius:6px;
        border:1px solid var(--bb-border);
        background:#fff; cursor:pointer; display:inline-block;
    }
    .bb-slot.is-on{
        background:var(--bb-gold);
        border-color:var(--bb-gold);
        box-shadow:0 0 0 2px rgba(201,162,74,.20) inset;
    }
    </style>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('bbScheduleGrid');
  if (!root) return;

  const hiddenGrid = document.getElementById('horarios_grid');
  const hiddenFieldsWrap = document.getElementById('horariosFields');

  const slotMin = parseInt(root.dataset.slotMinutes || '30', 10);
  const start = root.dataset.start || '07:00';
  const end   = root.dataset.end   || '21:00';

  let existing = {};
  try { existing = JSON.parse(root.dataset.existing || '{}') || {}; } catch(e){ existing = {}; }

  const slots = Array.from(root.querySelectorAll('.bb-slot'));

  const setOn = (btn, on) => {
    btn.classList.toggle('is-on', !!on);
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
  };
  const isOn = (btn) => btn.classList.contains('is-on');

  const toMinutes = (hhmm) => {
    const [h,m] = String(hhmm).slice(0,5).split(':').map(Number);
    return h*60 + m;
  };
  const fromMinutes = (mins) => {
    const h = String(Math.floor(mins/60)).padStart(2,'0');
    const m = String(mins%60).padStart(2,'0');
    return `${h}:${m}`;
  };

  const clearAll = () => {
    slots.forEach(b => setOn(b,false));
    syncHiddenAndFields();
  };

  // ===== 1) Prefill desde DB (existing ranges) =====
  Object.keys(existing || {}).forEach(day => {
    (existing[day] || []).forEach(r => {
      const ini = (r.inicio || '').slice(0,5);
      const fin = (r.fin || '').slice(0,5);
      if (!ini || !fin) return;

      let cur = toMinutes(ini);
      const stop = toMinutes(fin);
      while (cur < stop) {
        const t = fromMinutes(cur);
        const btn = root.querySelector(`.bb-slot[data-day="${day}"][data-time="${t}"]`);
        if (btn) setOn(btn, true);
        cur += slotMin;
      }
    });
  });

  // ===== 2) Si viene old('horarios_grid'), rehidrata selección (override) =====
  const tryHydrateFromHiddenGrid = () => {
    const raw = (hiddenGrid?.value || '').trim();
    if (!raw) return;
    try {
      const obj = JSON.parse(raw);
      if (!obj || !obj.days) return;

      // limpiamos y repintamos
      slots.forEach(b => setOn(b,false));

      Object.keys(obj.days).forEach(day => {
        (obj.days[day] || []).forEach(t => {
          const btn = root.querySelector(`.bb-slot[data-day="${day}"][data-time="${String(t).slice(0,5)}"]`);
          if (btn) setOn(btn, true);
        });
      });
    } catch(e) {}
  };

  tryHydrateFromHiddenGrid();

  // ===== Helpers: convertir slots -> rangos =====
  const timesToRanges = (timesSorted) => {
    if (!timesSorted.length) return [];
    const mins = timesSorted.map(toMinutes).sort((a,b)=>a-b);

    const ranges = [];
    let startM = mins[0];
    let prevM  = mins[0];

    for (let i=1;i<mins.length;i++){
      const m = mins[i];
      if (m === prevM + slotMin) {
        prevM = m;
      } else {
        ranges.push({ inicio: fromMinutes(startM), fin: fromMinutes(prevM + slotMin) });
        startM = m;
        prevM  = m;
      }
    }
    ranges.push({ inicio: fromMinutes(startM), fin: fromMinutes(prevM + slotMin) });

    return ranges;
  };

  // ===== Sync: JSON + hidden inputs horarios[...] =====
  const syncHiddenAndFields = () => {
    const days = {};
    for (let d=0; d<=6; d++) days[d] = [];

    slots.forEach(btn => {
      if (!isOn(btn)) return;
      const d = btn.dataset.day;
      const t = btn.dataset.time;
      days[d].push(String(t).slice(0,5));
    });

    Object.keys(days).forEach(d => {
      const arr = Array.from(new Set(days[d]));
      arr.sort((a,b)=>toMinutes(a)-toMinutes(b));
      days[d] = arr;
    });

    // 1) JSON (debug / rehidratar)
    if (hiddenGrid) {
      hiddenGrid.value = JSON.stringify({ slot_minutes: slotMin, start, end, days });
    }

    // 2) Inputs reales para backend: horarios[d][i][hora_inicio|hora_fin]
    // IMPORTANT: siempre mandamos horarios para que en EDIT reemplace (y pueda limpiar)
    const html = [];
    html.push(`<input type="hidden" name="horarios[_present]" value="1">`);

    Object.keys(days).forEach((d) => {
      const ranges = timesToRanges(days[d]);
      ranges.forEach((r, idx) => {
        html.push(`<input type="hidden" name="horarios[${d}][${idx}][hora_inicio]" value="${r.inicio}">`);
        html.push(`<input type="hidden" name="horarios[${d}][${idx}][hora_fin]" value="${r.fin}">`);
      });
    });

    if (hiddenFieldsWrap) hiddenFieldsWrap.innerHTML = html.join('');
  };

  // inicial
  syncHiddenAndFields();

  // ===== Interacción (clic + arrastrar) =====
  let down = false;
  let paintMode = null;
  let lastPointerToggleAt = 0;

  const beginPaint = (btn) => {
    down = true;
    paintMode = !isOn(btn);
    setOn(btn, paintMode);
    lastPointerToggleAt = Date.now();
    syncHiddenAndFields();
  };

  const paintOver = (btn) => {
    if (!down || paintMode === null) return;
    setOn(btn, paintMode);
    syncHiddenAndFields();
  };

  slots.forEach(btn => {
    btn.style.touchAction = 'none';

    btn.addEventListener('pointerdown', (e) => {
      e.preventDefault();
      beginPaint(btn);
    });

    btn.addEventListener('pointerenter', () => paintOver(btn));

    // Click fallback (evita doble toggle después de pointerdown)
    btn.addEventListener('click', (e) => {
      if (Date.now() - lastPointerToggleAt < 250) { e.preventDefault(); return; }
      e.preventDefault();
      setOn(btn, !isOn(btn));
      syncHiddenAndFields();
    });
  });

  window.addEventListener('pointerup', () => {
    down = false;
    paintMode = null;
  });

  // ===== Acciones =====
  const copyMonday = () => {
    const mondayOn = new Set(
      slots.filter(b=>b.dataset.day==="1" && isOn(b)).map(b=>b.dataset.time)
    );
    if (mondayOn.size === 0) return;

    for (let d=0; d<=6; d++){
      if (d===1) continue;
      const daySlots = slots.filter(b=>b.dataset.day===String(d));
      const anyOn = daySlots.some(isOn);
      if (anyOn) continue; // no pisar si ya tiene algo
      daySlots.forEach(b => setOn(b, mondayOn.has(b.dataset.time)));
    }
    syncHiddenAndFields();
  };

  document.getElementById('bbGridClearAll')?.addEventListener('click', clearAll);
  document.getElementById('bbGridCopyMonday')?.addEventListener('click', copyMonday);

  root.closest('form')?.addEventListener('submit', syncHiddenAndFields);
});
</script>
