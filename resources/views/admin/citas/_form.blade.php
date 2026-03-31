@push('styles')
<style>
  /* =================================================================
     VARIABLES Y ESTILOS GLOBALES MINIMALISTAS (Heredados de la vista pública)
  ================================================================== */
  :root {
      --color-primary: #8e6708;
      --color-primary-hover: #705206;
      --color-dark: #11141c;
      --color-light: #ffffff;
      --color-bg-soft: #faf9f6;
      --color-border: #f0f0f0;
  }

  /* LAYOUT A DOS COLUMNAS */
  .bb-admin-layout {
      display: flex;
      gap: 30px;
      align-items: flex-start;
      margin-top: 20px;
  }
  .bb-admin-main {
      flex: 1;
      min-width: 0; 
      display: flex;
      flex-direction: column;
      gap: 25px;
  }
  .bb-admin-sidebar {
      width: 380px;
      flex-shrink: 0;
      position: sticky;
      top: 20px; 
      background: var(--color-light);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.06);
      border: 1px solid var(--color-border);
      max-height: calc(100vh - 40px);
      overflow-y: auto;
  }
  .bb-admin-sidebar::-webkit-scrollbar { width: 4px; }
  .bb-admin-sidebar::-webkit-scrollbar-thumb { background: rgba(142, 103, 8, 0.3); border-radius: 10px; }

  /* =================================================================
     MENÚ DE SERVICIOS (ACORDEÓN ESTILO PÚBLICO)
  ================================================================== */
  .salon-menu { background-color: var(--color-light); border-radius: 12px; border: 1px solid var(--color-border); padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
  .salon-menu__eyebrow { display: block; font-size: 0.75rem; letter-spacing: 2px; color: var(--color-primary); text-transform: uppercase; margin-bottom: 5px; font-weight: 600; }
  .salon-menu__mainTitle { font-size: 1.5rem; color: var(--color-dark); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-top: 0; margin-bottom: 20px; }
  
  .salon-menu__accordion-container { max-height: 400px; overflow-y: auto; padding-right: 10px; }
  .salon-menu__accordion-container::-webkit-scrollbar { width: 4px; }
  .salon-menu__accordion-container::-webkit-scrollbar-thumb { background: rgba(142, 103, 8, 0.3); border-radius: 10px; }

  .accordion-item { border-bottom: 1px solid var(--color-border); }
  .accordion-header {
      width: 100%; text-align: left; padding: 16px 0; font-size: 0.95rem; font-weight: 600; text-transform: uppercase; color: var(--color-dark); background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: color 0.3s ease;
  }
  .accordion-header:hover { color: var(--color-primary); }
  .accordion-icon { font-size: 1.2rem; font-weight: 300; color: var(--color-primary); transition: transform 0.4s ease; }
  .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.5s ease; }
  
  .service-list { list-style: none; padding: 0 0 15px 0; margin: 0; }
  .service-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-radius: 8px; transition: all 0.2s ease; cursor: pointer; border: 1px solid transparent; }
  .service-item:hover { background-color: var(--color-bg-soft); border-color: var(--color-border); }

  .service-left { display: flex; align-items: center; gap: 12px; }
  .service-thumbnail { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; }
  .service-name { margin: 0 0 4px 0; font-size: 0.9rem; color: var(--color-dark); font-weight: 600; }
  .service-meta { font-size: 0.75rem; color: #888; }
  .service-price { font-weight: 700; color: var(--color-primary); }
  
  .service-btn { padding: 4px 12px; background-color: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); border-radius: 999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; transition: all 0.3s ease; cursor: pointer; }
  .service-item:hover .service-btn { background-color: var(--color-primary); color: var(--color-light); }

  /* =================================================================
     TARJETAS DE TU RESERVA (LADO DERECHO)
  ================================================================== */
  .bb-badge { font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--color-dark); margin-bottom: 15px; border-bottom: 1px solid var(--color-border); padding-bottom: 10px; }
  
  .bb-selectedCard { background: var(--color-bg-soft); border-radius: 10px; padding: 12px; margin-bottom: 12px; display: flex; gap: 12px; position: relative; border: 1px solid var(--color-border); }
  .bb-selectedCard__media img { width: 50px; height: 50px; border-radius: 6px; object-fit: cover; }
  .bb-selected__name { font-size: 0.85rem; font-weight: 600; margin: 0 0 4px 0; color: var(--color-dark); }
  .bb-selected__meta { list-style: none; padding: 0; margin: 0; font-size: 0.75rem; color: #666; }
  .bb-selected__meta strong { color: var(--color-primary); }
  
  .bb-selectedCard__remove { position: absolute; top: 8px; right: 8px; background: none; border: none; color: #aaa; cursor: pointer; font-size: 0.9rem; transition: color 0.2s; }
  .bb-selectedCard__remove:hover { color: #d9534f; }

  /* =================================================================
     CALENDARIO MINIMALISTA (El que proporcionaste)
  ================================================================== */
  .bb-admin-datetime .bb-calendar{ margin-top: 8px; }
  .bb-admin-datetime .bb-cal__header{ display:flex; align-items:center; justify-content:space-between; gap:8px; padding:8px 12px; border:1px solid rgba(0,0,0,0.06); border-radius:12px; background: #fafafa; }
  .bb-admin-datetime .bb-cal__title{ font-weight: 700; font-size: 14px; text-transform: capitalize; color: #333; }
  .bb-admin-datetime .bb-cal__nav{ border: 1px solid rgba(201,162,74,0.3); background:#fff; color: rgba(201,162,74,0.95); width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:16px; display:grid; place-items:center; transition: all 0.2s; }
  .bb-admin-datetime .bb-cal__nav:hover{ background: rgba(201,162,74,0.1); }
  .bb-admin-datetime .bb-cal__dow{ display:grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-top: 8px; font-weight: 700; font-size: 11px; color: #888; text-align:center; }
  .bb-admin-datetime .bb-cal__grid{ display:grid; grid-template-columns: repeat(7, 1fr); gap: 6px; margin-top: 6px; }
  .bb-admin-datetime .bb-cal__cell{ position:relative; width: 100%; height: 40px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.06); background: #fff; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size: 13px; transition: all 0.2s; }
  .bb-admin-datetime .bb-cal__cell:hover:not(.is-disabled):not(.is-empty) { border-color: rgba(201,162,74,0.4); }
  .bb-admin-datetime .bb-cal__cell.is-empty{ border: 0; background: transparent; cursor: default; }
  .bb-admin-datetime .bb-cal__cell.is-disabled{ opacity: .4; cursor: not-allowed; background: #f9f9f9; }
  .bb-admin-datetime .bb-cal__cell.is-selected{ border-color: rgba(201,162,74,0.8); background: rgba(201,162,74,0.1); font-weight: bold; color: #000; }
  .bb-admin-datetime .bb-cal__dot{ position:absolute; right: 4px; top: 4px; width: 6px; height: 6px; border-radius: 50%; }
  .bb-admin-datetime .bb-cal__dot.is-gold{ background: rgba(201,162,74,0.95); }
  .bb-admin-datetime .bb-cal__dot.is-muted{ background: #ccc; }
  .bb-admin-datetime .bb-timesPanel{ border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); background: #fafafa; padding: 12px; }
  .bb-admin-datetime .bb-timesGrid{ margin-top: 8px; display:grid; gap: 6px; grid-template-columns: repeat(4, minmax(0,1fr)); }
  @media (max-width: 640px){ .bb-admin-datetime .bb-timesGrid{ grid-template-columns: repeat(3, minmax(0,1fr)); } }
  .bb-admin-datetime .bb-timeBtn{ border-radius: 8px; border: 1px solid rgba(0,0,0,0.08); background:#fff; padding: 6px 4px; font-weight: 600; font-size: 12px; cursor:pointer; transition: all 0.2s; text-align:center; }
  .bb-admin-datetime .bb-timeBtn:hover{ border-color: rgba(201,162,74,0.4); }
  .bb-admin-datetime .bb-timeBtn.is-selected{ border-color: rgba(201,162,74,0.8); background: rgba(201,162,74,0.1); color: #000; }

  /* RESPONSIVE MÓVIL */
  @media (max-width: 1024px) {
      .bb-admin-layout { flex-direction: column; }
      .bb-admin-sidebar { width: 100%; position: relative; top: 0; max-height: none; }
  }
</style>
@endpush

@push('scripts')
<script>
  function toggleAccordion(button) {
      const item = button.parentElement;
      const content = item.querySelector('.accordion-content');
      const icon = item.querySelector('.accordion-icon');
      
      if (content.style.maxHeight) {
          content.style.maxHeight = null;
          icon.style.transform = "rotate(0deg)";
      } else {
          document.querySelectorAll('.accordion-content').forEach(el => el.style.maxHeight = null);
          document.querySelectorAll('.accordion-icon').forEach(el => el.style.transform = "rotate(0deg)");
          
          setTimeout(() => {
              content.style.maxHeight = content.scrollHeight + "px";
          }, 10);
          icon.style.transform = "rotate(45deg)";
      }
  }
</script>
@endpush

@php
    $mode = $mode ?? 'create';
    $cita = $cita ?? null;

    $selectedClienteId = old('cliente_id', $cita->cliente_id ?? '');
    $selectedCliente   = $selectedClienteId ? ($clientes->firstWhere('id', (int) $selectedClienteId) ?? null) : null;
    $clienteLabel = trim(($selectedCliente->nombre ?? '') . (($selectedCliente && !empty($selectedCliente->email)) ? ' - ' . $selectedCliente->email : ''));

    $bbField = "w-full border border-gray-200 rounded-md px-3 py-2 text-sm text-gray-800 bg-white transition focus:outline-none focus:ring-1 focus:ring-[rgba(201,162,74,.5)] focus:border-[rgba(201,162,74,.5)]";
    $bbIconColor = "color: rgba(201,162,74,.92)";
    $horaGuardada = substr((string) old('hora_cita', $cita->hora_cita ?? ''), 0, 5); 
    
    // Fallback Image
    $fallbackImg = asset('images/Beige Blogger Moderna Personal Sitio web.png');
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    {{-- BARRA SUPERIOR: BUSCADOR DE CLIENTE (Importante para admin) --}}
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm mb-2 relative">
        <label for="cliente_search" class="block text-xs font-bold text-gray-600 mb-1 uppercase">
            <i class="fas fa-user mr-1" style="{{ $bbIconColor }}"></i> Cliente <span class="text-red-500">*</span>
        </label>
        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $selectedClienteId }}" required>
        <div class="relative">
            <input type="text" id="cliente_search" autocomplete="off" placeholder="Buscar cliente..." value="{{ old('cliente_label', $clienteLabel) }}" class="{{ $bbField }} pr-8" />
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400"><i class="fas fa-search text-xs"></i></div>
        </div>
        <div id="cliente_dropdown" class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden hidden">
            <div id="cliente_results" class="max-h-48 overflow-auto text-sm"></div>
        </div>
        @error('cliente_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ESTRUCTURA DE DOS COLUMNAS --}}
    <div class="bb-admin-layout">

        {{-- COLUMNA IZQUIERDA: SERVICIOS Y CALENDARIO --}}
        <div class="bb-admin-main">
            
            {{-- SECCIÓN: MENÚ DE SERVICIOS --}}
            <section class="salon-menu">
                <span class="salon-menu__eyebrow">Catálogo</span>
                <h2 class="salon-menu__mainTitle">Agregar Servicios</h2>
        
                <div class="salon-menu__accordion-container" id="menu-scroll-area">
                    @foreach($categorias as $categoria)
                        @php
                            if (is_string($categoria)) {
                                $catId = $categoria;
                                $catNombre = $categoria;
                                $catImgUrl = $fallbackImg ?? '';
                                $serviciosDeCat = collect($servicios ?? [])->filter(function($s) use ($catNombre) {
                                    $cName = is_object($s) ? ($s->categoria->nombre ?? 'Sin categoría') : ($s['categoria']['nombre'] ?? 'Sin categoría');
                                    return $cName === $catNombre;
                                });
                            } else {
                                $catId = is_object($categoria) ? ($categoria->id_categoria ?? $categoria->id) : ($categoria['id_categoria'] ?? $categoria['id']);
                                $catNombre = is_object($categoria) ? $categoria->nombre : $categoria['nombre'];
                                $catImgUrl = (is_object($categoria) ? $categoria->imagen_url : $categoria['imagen_url']) ?: $fallbackImg;
                                $serviciosDeCat = collect($servicios ?? [])->filter(fn($s) => (is_object($s) ? $s->id_categoria : $s['id_categoria']) == $catId);
                            }
                        @endphp

                        @if($serviciosDeCat->count() > 0)
                            <div class="accordion-item">
                                <button type="button" class="accordion-header" onclick="toggleAccordion(this)">
                                    <div class="header-left">
                                        <span>{{ $catNombre }}</span>
                                    </div>
                                    <span class="accordion-icon">+</span>
                                </button>
    
                                <div class="accordion-content">
                                    <ul class="service-list mt-2">
                                        @foreach($serviciosDeCat as $servicio)
                                            @php
                                                $sImgUrl = (is_object($servicio) ? $servicio->imagen_url : $servicio['imagen_url']) ?: $fallbackImg;
                                                $sNombre = is_object($servicio) ? $servicio->nombre_servicio : $servicio['nombre_servicio'];
                                                $sDuracion = is_object($servicio) ? $servicio->duracion_minutos : $servicio['duracion_minutos'];
                                                $sPrecio = is_object($servicio) ? $servicio->precio : $servicio['precio'];
                                                $sId = is_object($servicio) ? $servicio->id_servicio : $servicio['id_servicio'];
                                            @endphp
                                            <li class="service-item">
                                                <div class="service-left">
                                                    <img src="{{ $sImgUrl }}" alt="{{ $sNombre }}" class="service-thumbnail">
                                                    <div class="service-info">
                                                        <h4 class="service-name">{{ $sNombre }}</h4>
                                                        <div class="service-meta">
                                                            <span>{{ (int) $sDuracion }} min</span> | 
                                                            <span class="service-price">${{ number_format((float) $sPrecio, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="service-action">
                                                    <button type="button" class="service-btn js-add-service-btn" data-service-id="{{ $sId }}">Agregar</button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>

      @php
        $svcIdToCat = collect($servicios ?? [])->mapWithKeys(function ($s) {
          $id = $s->id_servicio ?? $s->id ?? null;
          $cat = null;
          try {
            $cat = $s->categoria->nombre ?? null;
          } catch (\Throwable $e) {
            $cat = null;
          }
          return $id ? [ (string) $id => (string) ($cat ?? '') ] : [];
        });

        $hiddenRows = [];
        $oldRows = old('servicios');

        if (is_array($oldRows) && count($oldRows)) {
          foreach ($oldRows as $row) {
            $sid = $row['id_servicio'] ?? null;
            $hiddenRows[] = [
              'id_servicio'       => $sid,
              'id_empleado'       => $row['id_empleado'] ?? null,
              'precio_snapshot'   => $row['precio_snapshot'] ?? null,
              'duracion_snapshot' => $row['duracion_snapshot'] ?? null,
              'categoria'         => $row['categoria'] ?? ($sid ? $svcIdToCat->get((string) $sid, '') : ''),
            ];
          }
        } elseif (($mode ?? 'create') === 'edit' && !empty($cita) && $cita->servicios && $cita->servicios->count()) {
          foreach ($cita->servicios as $svc) {
            $catName = '';
            try {
              $catName = (string) ($svc->categoria->nombre ?? '');
            } catch (\Throwable $e) {
              $catName = '';
            }

            $hiddenRows[] = [
              'id_servicio'       => $svc->id_servicio ?? $svc->id ?? null,
              'id_empleado'       => $svc->pivot->id_empleado ?? null,
              'precio_snapshot'   => $svc->pivot->precio_snapshot ?? null,
              'duracion_snapshot' => $svc->pivot->duracion_snapshot ?? null,
              'categoria'         => $catName ?: ($svcIdToCat->get((string) ($svc->id_servicio ?? $svc->id ?? ''), '') ?? ''),
            ];
          }
        } else {
          $hiddenRows[] = [
            'id_servicio'       => null,
            'id_empleado'       => null,
            'precio_snapshot'   => null,
            'duracion_snapshot' => null,
            'categoria'         => null,
          ];
        }
      @endphp

      {{-- CONTENEDOR OCULTO DE SERVICIOS (NECESARIO PARA JS) --}}
      <div id="servicios-wrapper" style="display:none;">
        @foreach($hiddenRows as $i => $row)
          <div class="servicio-row" data-bb-uid="bb_{{ $i + 1 }}">

            {{-- CATEGORÍA --}}
            <select data-role="categoria">
              <option value="">Seleccionar categoría</option>
              @foreach($categorias as $categoria)
                @php $catVal = is_object($categoria) ? $categoria->nombre : $categoria; @endphp
                <option value="{{ $catVal }}" @selected((string) $catVal === (string) ($row['categoria'] ?? ''))>
                  {{ $catVal }}
                </option>
              @endforeach
            </select>

            {{-- SERVICIO --}}
            <select
              name="servicios[{{ $i }}][id_servicio]"
              data-role="servicio"
              data-selected="{{ $row['id_servicio'] ? (string) $row['id_servicio'] : '' }}"
            >
              <option value="">Selecciona primero una categoría</option>
            </select>

            {{-- EMPLEADO --}}
            <select
              name="servicios[{{ $i }}][id_empleado]"
              data-role="empleado"
              data-preselect="{{ $row['id_empleado'] ?? '' }}"
              disabled
            >
              <option value="">Selecciona un servicio primero</option>
            </select>

            {{-- SNAPSHOTS (CLAVE PARA CÁLCULOS) --}}
            <input
              type="hidden"
              name="servicios[{{ $i }}][precio_snapshot]"
              data-role="precio_snapshot"
              value="{{ $row['precio_snapshot'] ?? '' }}"
            >
            <input
              type="hidden"
              name="servicios[{{ $i }}][duracion_snapshot]"
              data-role="duracion_snapshot"
              value="{{ $row['duracion_snapshot'] ?? '' }}"
            >

            {{-- BOTÓN REMOVE (requerido por JS) --}}
            <button type="button" class="btn-remove-servicio" style="display:none;">X</button>
          </div>
        @endforeach
      </div>

            {{-- SECCIÓN: CALENDARIO Y FECHA --}}
            @php
                $fechaInit = old('fecha_cita', !empty($cita?->fecha_cita) ? \Carbon\Carbon::parse($cita->fecha_cita)->format('Y-m-d') : ($fechaPrefill ?? ''));
                $horaInitRaw = old('hora_cita', $cita->hora_cita ?? '');
                $horaInit = $horaInitRaw ? substr((string)$horaInitRaw, 0, 5) : '';
            @endphp
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <label class="block text-xs font-bold text-gray-600 mb-4 uppercase tracking-wider">
                    <i class="fas fa-calendar-alt mr-1" style="{{ $bbIconColor }}"></i> Fecha y Hora de la Cita <span class="text-red-500">*</span>
                </label>
                
                <div class="bb-admin-datetime">
                    <input type="hidden" name="fecha_cita" id="bbDateInput" value="{{ $fechaInit }}">
                    <input type="hidden" name="hora_cita"  id="bbHourInput" value="{{ $horaInit }}">

                    <div id="bbDatetimeLock" class="text-xs px-3 py-2 rounded-md bg-amber-50 text-amber-800 border border-amber-100 mb-3" style="display:none;">
                        Selecciona los servicios e indica el empleado para ver disponibilidad.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        <div>
                            <div id="bbAdminCalendar" class="bb-calendar"></div>
                            @error('fecha_cita') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <div class="bb-timesPanel">
                                <div class="text-xs font-bold text-gray-700 mb-1" id="bbTimesTitle">Horas disponibles</div>
                                <div id="bbTimesGrid" class="bb-timesGrid"></div>
                                <p id="bbTimesEmpty" class="text-xs text-gray-400 mt-2" style="display:none;">Sin horarios para esta fecha.</p>
                                @error('hora_cita') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: SIDEBAR (Tu Reserva / Resumen Admin) --}}
        <div class="bb-admin-sidebar">
            <p class="bb-badge">Resumen de Cita</p>

            {{-- Aquí se inyectarán las tarjetas de servicio seleccionadas mediante JS --}}
            {{-- Debes asegurarte que tu JS (admin.citas.js) renderice los items seleccionados aquí usando el formato .bb-selectedCard --}}
            <div id="resumen_servicios_seleccionados" class="mb-4 min-h-[60px] flex flex-col gap-2">
                <div class="text-center text-gray-400 text-sm py-4 italic">
                    No hay servicios seleccionados.
                </div>
            </div>

            {{-- TOTALES GLOBALES --}}
            <div class="flex gap-2 mb-6">
                <div class="flex-1 bg-gray-50 border border-gray-100 rounded-md p-2 flex flex-col justify-center items-center">
                    <span class="text-[10px] uppercase font-bold text-gray-500">Duración</span>
                    <input id="duracion_total" type="text" class="bg-transparent border-none text-center font-bold text-sm w-full p-0 focus:ring-0" readonly value="0 min">
                </div>
                <div class="flex-1 bg-green-50 border border-green-100 rounded-md p-2 flex flex-col justify-center items-center">
                    <span class="text-[10px] uppercase font-bold text-green-700">Total</span>
                    <input id="total_servicios" name="total_servicios" type="text" class="bg-transparent border-none text-center font-bold text-green-700 text-sm w-full p-0 focus:ring-0" readonly value="$0.00">
                </div>
            </div>

            {{-- CAMPOS ADMINISTRATIVOS EXTRA --}}
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <div>
                    <label for="estado_cita" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Estado <span class="text-red-500">*</span></label>
                    @php $estadoSelected = old('estado_cita', $cita->estado_cita ?? 'pendiente'); @endphp
                    <select id="estado_cita" name="estado_cita" class="{{ $bbField }}" required>
                        <option value="pendiente"  @selected($estadoSelected === 'pendiente')>Pendiente</option>
                        <option value="confirmada" @selected($estadoSelected === 'confirmada')>Confirmada</option>
                        <option value="cancelada"  @selected($estadoSelected === 'cancelada')>Cancelada</option>
                        <option value="completada" @selected($estadoSelected === 'completada')>Completada</option>
                    </select>
                </div>

                <div id="metodo_pago_wrap">
                    <label for="metodo_pago" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Método Pago</label>
                    @php $metodoPagoSelected = old('metodo_pago', $cita->metodo_pago ?? ''); @endphp
                    <select id="metodo_pago" name="metodo_pago" class="{{ $bbField }}">
                        <option value="">Seleccionar...</option>
                        <option value="efectivo" @selected($metodoPagoSelected === 'efectivo')>Efectivo</option>
                        <option value="tarjeta_credito" @selected($metodoPagoSelected === 'tarjeta_credito')>Tarjeta (Crédito)</option>
                        <option value="tarjeta_debito" @selected($metodoPagoSelected === 'tarjeta_debito')>Tarjeta (Débito)</option>
                        <option value="transferencia" @selected($metodoPagoSelected === 'transferencia')>Transferencia</option>
                    </select>
                </div>

                <div>
                    <label for="descuento" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Descuento ($)</label>
                    <input type="number" step="0.01" min="0" name="descuento" id="descuento" value="{{ old('descuento', $cita->descuento ?? 0) }}" class="{{ $bbField }}" placeholder="0.00" />
                </div>

                <div>
                    <label for="observaciones" class="block text-xs font-bold text-gray-600 mb-1 uppercase">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2" class="{{ $bbField }} resize-none" placeholder="Notas internas...">{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>
                </div>
            </div>

            {{-- BOTÓN GUARDAR --}}
            <button type="submit"
                    class="w-full mt-6 px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition"
                    style="background: linear-gradient(135deg, var(--bb-gold, #c9a24a), var(--bb-gold-2, #b08d40)); border: 1px solid rgba(201,162,74,.35); box-shadow: 0 10px 22px rgba(201,162,74,.18); color: #fff;"
                    onmouseover="this.style.boxShadow='0 14px 28px rgba(201,162,74,.25)'"
                    onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'">
                <i class="fas fa-save"></i>
                {{ $mode === 'edit' ? 'Actualizar Cita' : 'Guardar Reserva' }}
            </button>
            <a href="{{ route('admin.citas.index') }}" class="block text-center mt-3 text-xs text-gray-500 hover:text-gray-800 underline">Cancelar</a>
            
        </div>
    </div>
</form>
@push('scripts')
@php
    $clientesForJs = $clientes->map(function ($c) {
        return [
            'id'     => $c->id,
            'label'  => trim((($c->nombre ?? $c->name ?? '') . ' - ' . ($c->email ?? ''))),
            'nombre' => ($c->nombre ?? $c->name ?? ''),
            'email'  => ($c->email ?? ''),
        ];
    })->values();

    $serviciosForJs = $servicios->map(function ($s) {
        return [
            'id'       => $s->id_servicio,
            'nombre'   => $s->nombre_servicio,
            'categoria'=> $s->categoria->nombre ?? 'Sin categoría',
            'duracion' => $s->duracion_minutos,
            'precio'   => $s->precio,
        ];
    })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
  const MODE = @json($mode);
  const CITA_ID = @json($cita->id ?? null);

  const serviciosWrapper = document.getElementById('servicios-wrapper');
  const btnAddServicio   = document.getElementById('btn-add-servicio');

  const elCalendar   = document.getElementById('bbAdminCalendar');
  const elTimesGrid  = document.getElementById('bbTimesGrid');
  const elTimesTitle = document.getElementById('bbTimesTitle');
  const elTimesHint  = document.getElementById('bbTimesHint');
  const elTimesEmpty = document.getElementById('bbTimesEmpty');
  const elLock       = document.getElementById('bbDatetimeLock');

  const resumenWrap  = document.getElementById('resumen_servicios_seleccionados');

  const elDateInput  = document.getElementById('bbDateInput');
  const elHourInput  = document.getElementById('bbHourInput');

  const URL_EMPLEADOS = @json(route('admin.citas.empleadosPorServicio'));
  const URL_HORAS     = @json(route('admin.citas.horasDisponibles'));

  const dtState = {
    calendarMonth: new Date(),
    selectedDate: (elDateInput?.value || '').trim() || null,
    selectedHour: (elHourInput?.value || '').trim() || null,
    locked: true,
    // ✅ Solo para el primer render: que el mes se alinee a la fecha precargada (edit/old())
    snapToSelectedMonth: true,
  };

  // ===========================
  // ✅ Disponibilidad por día (cache por mes + selección actual)
  // ===========================
  const availState = {
    // key: `${itemsHash}|${YYYY-MM}` => Set(['YYYY-MM-DD', ...])
    cache: new Map(),
    loadingKey: null,
  };

  function monthKeyFromDate(d){
    return `${d.getFullYear()}-${pad2(d.getMonth()+1)}`;
  }

  function hashItems(items){
    // hash estable por selección de servicio/empleado
    return items
      .map(it => `${it.id_servicio}:${it.id_empleado}:${it.orden}`)
      .join('|');
  }

  async function preloadMonthAvailability() {
    if (dtState.locked || !isReadyForAvailability()) return;

    const items = getItemsFromRows();
    const itemsHash = hashItems(items);

    const mk = monthKeyFromDate(dtState.calendarMonth);
    const key = `${itemsHash}|${mk}`;

    if (availState.cache.has(key)) return; // ya cargado

    availState.loadingKey = key;

    const [yy, mm] = mk.split('-').map(n => parseInt(n, 10));
    const daysInMonth = new Date(yy, mm, 0).getDate(); // mm ya viene 1-12
    const todayYMD = toYMD(new Date());
    const disablePast = (MODE === 'create');

    const available = new Set();

    // ✅ Guarda selección actual para no afectarla mientras se precarga el mes
    const restoreDate = dtState.selectedDate;

    // ⚠️ 31 requests máx. (rápido de implementar sin endpoint nuevo)
    // Si luego quieres optimizar, lo cambiamos por un endpoint "fechasDisponibles".
    const tasks = [];
    for (let day = 1; day <= daysInMonth; day++) {
      const ymd = `${yy}-${pad2(mm)}-${pad2(day)}`;
      const isPast = disablePast ? (ymd < todayYMD) : false;
      if (isPast) continue;

      const qs = new URLSearchParams();
      dtState.selectedDate = ymd; // temporal para buildAvailabilityParams
      buildAvailabilityParams(qs, items);
      dtState.selectedDate = restoreDate; // restaura

      const url = URL_HORAS + '?' + qs.toString();

      tasks.push(
        fetch(url, { headers: { 'Accept': 'application/json' }})
          .then(r => r.json())
          .then(data => {
            let list = [];
            if (Array.isArray(data)) list = data;
            else if (Array.isArray(data?.horas)) list = data.horas;
            else list = [];
            if (list.length) available.add(ymd);
          })
          .catch(() => {})
      );
    }

    await Promise.all(tasks);

    // si mientras cargaba cambió la selección/mes, no guardes
    if (availState.loadingKey !== key) return;

    availState.cache.set(key, available);
  }

  function getAvailableSetForCurrentMonth(){
    const items = getItemsFromRows();
    const itemsHash = hashItems(items);
    const mk = monthKeyFromDate(dtState.calendarMonth);
    const key = `${itemsHash}|${mk}`;
    return availState.cache.get(key) || null;
  }

  function pad2(n){ return String(n).padStart(2,'0'); }
  function toYMD(d){ return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`; }
  function ymdToDate(ymd){
    const [y,m,d] = String(ymd).split('-').map(x => parseInt(x,10));
    if (!y || !m || !d) return null;
    return new Date(y, m-1, d);
  }
  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // ✅ IMPORTANTE: ignora filas vacías (sin servicio)
  function getItemsFromRows() {
    if (!serviciosWrapper) return [];

    const rows = [...serviciosWrapper.querySelectorAll('.servicio-row')];

    const raw = rows.map((row) => {
      const svcSel = row.querySelector('select[data-role="servicio"]');
      const empSel = row.querySelector('select[data-role="empleado"]');

      return {
        id_servicio: (svcSel?.value || '').trim(),
        id_empleado: (empSel?.value || '').trim(),
      };
    }).filter(it => it.id_servicio);

    return raw.map((it, idx) => ({ ...it, orden: idx + 1 }));
  }

  function isReadyForAvailability() {
    const items = getItemsFromRows();
    if (!items.length) return false;
    return items.every(it => String(it.id_servicio).length > 0 && String(it.id_empleado).length > 0);
  }

  function setLockUI() {
    const locked = !isReadyForAvailability();
    dtState.locked = locked;
    if (elLock) elLock.style.display = locked ? '' : 'none';

    if (locked) {
      dtState.selectedDate = null;
      dtState.selectedHour = null;
      if (elDateInput) elDateInput.value = '';
      if (elHourInput) elHourInput.value = '';
      if (elTimesGrid) elTimesGrid.innerHTML = '';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';
      if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
      if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';
    }
  }

  function renderCalendar() {
    if (!elCalendar) return;

    if (dtState.snapToSelectedMonth && dtState.selectedDate) {
      const dSel = ymdToDate(dtState.selectedDate);
      if (dSel) dtState.calendarMonth = new Date(dSel.getFullYear(), dSel.getMonth(), 1);
      dtState.snapToSelectedMonth = false;
    }

    const d = dtState.calendarMonth;
    const y = d.getFullYear();
    const m = d.getMonth();

    const first = new Date(y, m, 1);
    const startDow = (first.getDay() + 6) % 7; // lunes=0
    const daysInMonth = new Date(y, m + 1, 0).getDate();

    const monthName = first.toLocaleString('es-MX', { month: 'long', year: 'numeric' });

    const grid = [];
    for (let i = 0; i < startDow; i++) grid.push(null);
    for (let day = 1; day <= daysInMonth; day++) grid.push(new Date(y, m, day));
    while (grid.length % 7 !== 0) grid.push(null);

    const todayYMD = toYMD(new Date());
    const disablePast = (MODE === 'create');

    elCalendar.innerHTML = `
      <div class="bb-cal__header">
        <button type="button" class="bb-cal__nav" data-nav="-1">‹</button>
        <div class="bb-cal__title">${escapeHtml(monthName)}</div>
        <button type="button" class="bb-cal__nav" data-nav="1">›</button>
      </div>

      <div class="bb-cal__dow">
        <div>L</div><div>M</div><div>M</div><div>J</div><div>V</div><div>S</div><div>D</div>
      </div>

      <div class="bb-cal__grid">
        ${grid.map(cell => {
          if (!cell) return `<div class="bb-cal__cell is-empty"></div>`;

          const ymd = toYMD(cell);
          const isPast = disablePast ? (ymd < todayYMD) : false;
          const isSelected = dtState.selectedDate === ymd;

          const availSet = getAvailableSetForCurrentMonth();
          const availabilityKnown = !!availSet;
          const isAvailable = availabilityKnown ? availSet.has(ymd) : false;

          // ✅ Mientras se precarga la disponibilidad del mes, deshabilita para evitar selección incorrecta
          const disabled = dtState.locked || isPast || (!availabilityKnown ? true : !isAvailable);

          // Punto dorado SOLO si está disponible (si no se conoce aún, queda muted)
          const dotClass = (dtState.locked || !availabilityKnown || !isAvailable) ? 'is-muted' : 'is-gold';

          return `
            <button type="button"
              class="bb-cal__cell ${isSelected ? 'is-selected' : ''} ${disabled ? 'is-disabled' : ''}"
              data-date="${ymd}"
              ${disabled ? 'disabled' : ''}>
              <span class="bb-cal__day">${cell.getDate()}</span>
              <span class="bb-cal__dot ${dotClass}" aria-hidden="true"></span>
            </button>
          `;
        }).join('')}
      </div>
    `;

    elCalendar.querySelectorAll('.bb-cal__nav').forEach(btn => {
      btn.addEventListener('click', async () => {
        const delta = Number(btn.getAttribute('data-nav'));
        dtState.calendarMonth = new Date(y, m + delta, 1);
        // ✅ al navegar meses NO queremos volver a forzar el mes al seleccionado
        dtState.snapToSelectedMonth = false;
        await syncCalendarAvailability({ clearInvalidSelection: false });
      });
    });

    elCalendar.querySelectorAll('.bb-cal__cell[data-date]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const ymd = btn.getAttribute('data-date');
        dtState.selectedDate = ymd;
        dtState.snapToSelectedMonth = false;
        if (elDateInput) elDateInput.value = ymd;

        dtState.selectedHour = null;
        if (elHourInput) elHourInput.value = '';

        // Si por alguna razón aún no está precargada la disponibilidad, precárgala
        if (!getAvailableSetForCurrentMonth()) {
          await syncCalendarAvailability({ clearInvalidSelection: false });
        } else {
          renderCalendar();
        }
        await refreshHorasDisponibles();
      });
    });
  }

  // ===========================
  // ✅ Render + precarga disponibilidad del mes y bloquea días no disponibles
  // ===========================
  async function syncCalendarAvailability({ clearInvalidSelection = true } = {}) {
    // Render inmediato (por UX)
    renderCalendar();

    if (dtState.locked) return;

    try {
      await preloadMonthAvailability();

      const availSet = getAvailableSetForCurrentMonth();

      // Si cambian servicios/empleados, la fecha seleccionada puede dejar de ser válida
      if (
        clearInvalidSelection &&
        MODE === 'create' &&
        dtState.selectedDate &&
        availSet &&
        !availSet.has(dtState.selectedDate)
      ) {
        dtState.selectedDate = null;
        dtState.selectedHour = null;

        if (elDateInput) elDateInput.value = '';
        if (elHourInput) elHourInput.value = '';

        if (elTimesGrid) elTimesGrid.innerHTML = '';
        if (elTimesEmpty) elTimesEmpty.style.display = 'none';
        if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
        if (elTimesHint) elTimesHint.textContent = 'Selecciona una fecha para ver horarios.';
      }
    } finally {
      // Render final con disponibilidad aplicada
      renderCalendar();
    }
  }

  function buildAvailabilityParams(qs, items) {
    if (dtState.selectedDate) {
      qs.set('date', dtState.selectedDate);
      qs.set('fecha', dtState.selectedDate);
    }

    if (CITA_ID) qs.set('cita_id', String(CITA_ID));

    [...qs.keys()].forEach(k => {
      if (k === 'servicios[]' || k === 'empleados[]' || k.startsWith('items[')) qs.delete(k);
    });

    items.forEach((it, i) => {
      qs.append('servicios[]', String(it.id_servicio));
      qs.append('empleados[]', String(it.id_empleado));

      qs.set(`items[${i}][id_servicio]`, String(it.id_servicio));
      qs.set(`items[${i}][id_empleado]`, String(it.id_empleado));
      qs.set(`items[${i}][orden]`, String(it.orden));
    });
  }

  function renderTimesButtons(list) {
    if (!elTimesGrid) return;

    elTimesGrid.innerHTML = '';

    if (!list.length) {
      if (elTimesEmpty) elTimesEmpty.style.display = '';
      return;
    }

    if (elTimesEmpty) elTimesEmpty.style.display = 'none';

    elTimesGrid.innerHTML = list.map(it => {
      const value = String(it.value ?? it);
      const label = String(it.label ?? it);
      const selected = (dtState.selectedHour && String(dtState.selectedHour) === value);

      return `
        <button type="button" class="bb-timeBtn ${selected ? 'is-selected' : ''}"
                data-hour="${escapeHtml(value)}">
          ${escapeHtml(label)}
        </button>
      `;
    }).join('');

    elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(btn => {
      btn.addEventListener('click', () => {
        const h = btn.getAttribute('data-hour');
        dtState.selectedHour = h;
        if (elHourInput) elHourInput.value = h;

        elTimesGrid.querySelectorAll('.bb-timeBtn').forEach(x => x.classList.remove('is-selected'));
        btn.classList.add('is-selected');
      });
    });
  }

  async function refreshHorasDisponibles() {
    if (!elTimesGrid) return;

    if (dtState.locked || !dtState.selectedDate) {
      if (elTimesTitle) elTimesTitle.textContent = 'Horas disponibles';
      if (elTimesHint) elTimesHint.textContent = dtState.locked
        ? 'Selecciona servicio(s) y empleado(s) para ver disponibilidad.'
        : 'Selecciona una fecha para ver horarios.';
      if (elTimesEmpty) elTimesEmpty.style.display = 'none';
      elTimesGrid.innerHTML = '';
      return;
    }

    if (elTimesHint) elTimesHint.textContent = 'Cargando horarios...';
    if (elTimesEmpty) elTimesEmpty.style.display = 'none';
    elTimesGrid.innerHTML = '';

    try {
      const items = getItemsFromRows();
      const qs = new URLSearchParams();
      buildAvailabilityParams(qs, items);

      const url = URL_HORAS + '?' + qs.toString();
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      let list = [];
      if (Array.isArray(data)) list = data;
      else if (Array.isArray(data?.horas)) list = data.horas.map(h => ({ value: h, label: h }));
      else if (Array.isArray(data?.items)) list = data.items;
      else list = [];

      if (elTimesTitle) elTimesTitle.textContent = `Horas disponibles (${dtState.selectedDate})`;
      if (!list.length) {
        if (elTimesHint) elTimesHint.textContent = 'No hay horas disponibles, prueba otro día o cambia empleado.';
        renderTimesButtons([]);
        return;
      }

      if (elTimesHint) elTimesHint.textContent = 'Selecciona una hora para confirmar.';

      const current = dtState.selectedHour;
      renderTimesButtons(list);

      if (current) {
        const exists = list.some(x => String(x.value ?? x) === String(current));
        if (!exists) {
          dtState.selectedHour = current;
          if (elHourInput) elHourInput.value = current;
        }
      }

    } catch (e) {
      console.error(e);
      if (elTimesHint) elTimesHint.textContent = 'Error cargando horarios.';
      renderTimesButtons([]);
    }
  }

  // ===========================
  // Multi-servicio (rows ocultos) + empleados + snapshots
  // ===========================
  if (serviciosWrapper) {
    const serviciosAll = @json($serviciosForJs);
    const norm = (v) => (v ?? '').toString().trim().toLowerCase();

    if (!window.__bbAdminRowUid) window.__bbAdminRowUid = 1;

    function ensureRowUids() {
      const seen = new Set();
      serviciosWrapper.querySelectorAll('.servicio-row').forEach(row => {
        let uid = row.dataset.bbUid;
        if (!uid || seen.has(uid)) {
          uid = `bb_${window.__bbAdminRowUid++}`;
          row.dataset.bbUid = uid;
        }
        seen.add(uid);
      });
    }

    function getSelectedServiceIds() {
      return getItemsFromRows().map(it => String(it.id_servicio));
    }

    function updateCatalogButtons() {
      const selected = new Set(getSelectedServiceIds());
      document.querySelectorAll('.js-add-service-btn').forEach(btn => {
        const id = String(btn.dataset.serviceId || btn.getAttribute('data-service-id') || '');
        const isSelected = id && selected.has(id);
        btn.disabled = isSelected;
        btn.textContent = isSelected ? 'Agregado' : 'Agregar';
        btn.classList.toggle('opacity-50', isSelected);
        btn.classList.toggle('cursor-not-allowed', isSelected);
      });
    }

    function renderResumenServicios() {
      if (!resumenWrap) return;

      ensureRowUids();

      const rows = [...serviciosWrapper.querySelectorAll('.servicio-row')]
        .map(row => {
          const svcSel = row.querySelector('select[data-role="servicio"]');
          const svcId = (svcSel?.value || svcSel?.dataset.selected || '').trim();
          if (!svcId) return null;

          const svc = serviciosAll.find(s => String(s.id) === String(svcId));
          const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
          const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');

          const precio = (precioInp?.value ?? '') !== '' ? Number(precioInp.value) : Number(svc?.precio ?? 0);
          const dur    = (durInp?.value ?? '') !== '' ? Number(durInp.value) : Number(svc?.duracion ?? 0);

          return {
            uid: row.dataset.bbUid,
            svcId,
            nombre: svc?.nombre || `Servicio #${svcId}`,
            precio,
            dur,
          };
        })
        .filter(Boolean);

      if (!rows.length) {
        resumenWrap.innerHTML = `
          <div class="text-center text-gray-400 text-sm py-4 italic">
            No hay servicios seleccionados.
          </div>
        `;
        return;
      }

      resumenWrap.innerHTML = rows.map(r => {
        const precio = Number(r.precio || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
        return `
          <div class="bb-selectedCard" data-uid="${escapeHtml(r.uid)}">
            <div class="bb-selectedCard__media">
              <img src="{{ $fallbackImg }}" alt="${escapeHtml(r.nombre)}">
            </div>
            <div class="bb-selectedCard__info" style="flex:1">
              <h3 class="bb-selected__name">${escapeHtml(r.nombre)}</h3>
              <ul class="bb-selected__meta">
                <li><strong>${escapeHtml(String(r.dur || 0))}</strong> min</li>
                <li><strong>$${escapeHtml(precio)}</strong></li>
              </ul>
            </div>
            <button type="button" class="bb-selectedCard__remove" data-remove="${escapeHtml(r.uid)}" title="Quitar">✕</button>
          </div>
        `;
      }).join('');
    }

    function syncServiceUi() {
      renderResumenServicios();
      updateCatalogButtons();
    }

    function buildOptionsForServiceSelect(selectEl, categoria, selectedId = "") {
      selectEl.innerHTML = "";

      if (!categoria) {
        const opt = document.createElement("option");
        opt.value = "";
        opt.textContent = "Selecciona primero una categoría";
        selectEl.appendChild(opt);
        return;
      }

      const opt0 = document.createElement("option");
      opt0.value = "";
      opt0.textContent = "Seleccionar servicio";
      selectEl.appendChild(opt0);

      const catN = norm(categoria);

      serviciosAll
        .filter(s => norm(s.categoria) === catN)
        .forEach(s => {
          const opt = document.createElement("option");
          opt.value = s.id;

          const precio = Number(s.precio ?? 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
          opt.textContent = `${s.nombre} - $${precio} (${s.duracion} min)`;

          opt.dataset.duracion = s.duracion ?? 0;
          opt.dataset.precio   = s.precio ?? 0;

          if (String(selectedId) === String(s.id)) opt.selected = true;
          selectEl.appendChild(opt);
        });
    }

    async function loadEmpleadosForRow(rowEl, servicioId, preselectId = null) {
      const empleadoSelect = rowEl.querySelector('select[data-role="empleado"]');
      if (!empleadoSelect) return;

      empleadoSelect.innerHTML = `<option value="">Cargando...</option>`;
      empleadoSelect.disabled = true;

      if (!servicioId) {
        empleadoSelect.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
        return;
      }

      const url = URL_EMPLEADOS + `?servicio_id=${encodeURIComponent(servicioId)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      empleadoSelect.innerHTML =
        `<option value="">Selecciona un empleado</option>` +
        (data || []).map(e => `<option value="${e.id}">${e.label}</option>`).join('');

      empleadoSelect.disabled = false;

      if (preselectId) {
        empleadoSelect.value = String(preselectId);
      } else if (Array.isArray(data) && data.length === 1) {
        empleadoSelect.value = String(data[0].id);
      }
    }

    function recalcTotalDuracion() {
      let total = 0;
      serviciosWrapper.querySelectorAll('input[data-role="duracion_snapshot"]').forEach(inp => {
        const v = parseInt(inp.value || '0', 10);
        total += isNaN(v) ? 0 : v;
      });

      const totalInput = document.getElementById('duracion_total');
      if (totalInput) totalInput.value = total;
    }

    function recalcTotalMonto() {
      let total = 0;
      serviciosWrapper.querySelectorAll('input[data-role="precio_snapshot"]').forEach(inp => {
        const v = parseFloat(inp.value || '0');
        total += isNaN(v) ? 0 : v;
      });

      const totalInput = document.getElementById('total_servicios');
      if (totalInput) totalInput.value = total.toFixed(2);
    }

    function recalcAll() { recalcTotalDuracion(); recalcTotalMonto(); }

    function reindexRows() {
      const rows = serviciosWrapper.querySelectorAll('.servicio-row');

      rows.forEach((row, i) => {
        const svc    = row.querySelector('select[data-role="servicio"]');
        const emp    = row.querySelector('select[data-role="empleado"]');
        const precio = row.querySelector('input[data-role="precio_snapshot"]');
        const dur    = row.querySelector('input[data-role="duracion_snapshot"]');

        if (svc)    svc.name    = `servicios[${i}][id_servicio]`;
        if (emp)    emp.name    = `servicios[${i}][id_empleado]`;
        if (precio) precio.name = `servicios[${i}][precio_snapshot]`;
        if (dur)    dur.name    = `servicios[${i}][duracion_snapshot]`;
      });

      const canRemove = rows.length > 1;
      rows.forEach(row => {
        const btn = row.querySelector('.btn-remove-servicio');
        if (btn) btn.disabled = !canRemove;
      });
    }

    serviciosWrapper.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-remove-servicio');
      if (!btn) return;

      const row = btn.closest('.servicio-row');
      if (!row) return;

      const rows = serviciosWrapper.querySelectorAll('.servicio-row');
      if (rows.length <= 1) return;

      row.remove();
      reindexRows();
      recalcAll();

      setLockUI();
      await syncCalendarAvailability();
      await refreshHorasDisponibles();

      syncServiceUi();
    });

    async function addRow() {
      const base = serviciosWrapper.querySelector('.servicio-row');
      if (!base) return;

      const clone = base.cloneNode(true);
      // ✅ importante: evitar que el uid se copie al clonar
      clone.removeAttribute('data-bb-uid');
      if (clone.dataset) delete clone.dataset.bbUid;

      clone.querySelectorAll('input').forEach(inp => inp.value = '');

      const catSel = clone.querySelector('select[data-role="categoria"]');
      const svcSel = clone.querySelector('select[data-role="servicio"]');
      const empSel = clone.querySelector('select[data-role="empleado"]');

      if (catSel) catSel.selectedIndex = 0;

      if (svcSel) {
        svcSel.innerHTML = `<option value="">Selecciona primero una categoría</option>`;
        svcSel.removeAttribute('data-selected');
      }

      if (empSel) {
        empSel.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
        empSel.disabled = true;
        empSel.value = '';
        empSel.removeAttribute('data-preselect');
      }

      clone.querySelector('#servicio_main')?.removeAttribute('id');
      clone.querySelector('#categoria_main')?.removeAttribute('id');

      serviciosWrapper.appendChild(clone);

      reindexRows();
      recalcAll();

      setLockUI();
      await syncCalendarAvailability();
      await refreshHorasDisponibles();

      syncServiceUi();

      return clone;
    }

    if (btnAddServicio) btnAddServicio.addEventListener('click', addRow);

    function getEmptyRow() {
      const rows = [...serviciosWrapper.querySelectorAll('.servicio-row')];
      return rows.find(row => {
        const svcSel = row.querySelector('select[data-role="servicio"]');
        const val = (svcSel?.value || svcSel?.dataset.selected || '').trim();
        return !val;
      }) || null;
    }

    async function ensureEmptyRow() {
      const existing = getEmptyRow();
      if (existing) return existing;

      const clone = await addRow();
      if (clone) return clone;

      const rows = serviciosWrapper.querySelectorAll('.servicio-row');
      return rows.length ? rows[rows.length - 1] : null;
    }

    function selectServiceInRow(row, serviceId) {
      const svc = serviciosAll.find(s => String(s.id) === String(serviceId));
      if (!svc) return;

      const catSel = row.querySelector('select[data-role="categoria"]');
      const svcSel = row.querySelector('select[data-role="servicio"]');
      if (!svcSel) return;

      if (catSel) {
        catSel.value = svc.categoria || '';
      }

      buildOptionsForServiceSelect(svcSel, svc.categoria, String(serviceId));
      svcSel.value = String(serviceId);
      svcSel.dataset.selected = String(serviceId);
      svcSel.dispatchEvent(new Event('change', { bubbles: true }));

      syncServiceUi();
    }

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.js-add-service-btn');
      if (!btn) return;

      const serviceId = btn.dataset.serviceId || btn.getAttribute('data-service-id');
      if (!serviceId) return;

      const row = await ensureEmptyRow();
      if (!row) return;

      selectServiceInRow(row, serviceId);
    });

    if (resumenWrap) {
      resumenWrap.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-remove]');
        if (!btn) return;

        const uid = btn.getAttribute('data-remove');
        const row = serviciosWrapper.querySelector(`.servicio-row[data-bb-uid="${CSS.escape(uid)}"]`);
        if (!row) return;

        const removeBtn = row.querySelector('.btn-remove-servicio');
        if (removeBtn) removeBtn.click();
        else row.remove();

        syncServiceUi();
      });
    }

    (async () => {
      const rows = serviciosWrapper.querySelectorAll('.servicio-row');
      if (!rows.length) return;

      for (const row of rows) {
        const catSel = row.querySelector('select[data-role="categoria"]');
        const svcSel = row.querySelector('select[data-role="servicio"]');
        if (!catSel || !svcSel) continue;

        const selectedId = svcSel.dataset.selected || svcSel.value || "";
        buildOptionsForServiceSelect(svcSel, catSel.value, selectedId);
        svcSel.dataset.selected = selectedId;

        const empSel = row.querySelector('select[data-role="empleado"]');
        const preEmp = empSel?.dataset.preselect || null;

        const currentServiceId = selectedId || svcSel.value || null;
        if (currentServiceId) {
          await loadEmpleadosForRow(row, currentServiceId, preEmp);
        }
      }

      reindexRows();
      recalcAll();

      setLockUI();

      if (dtState.selectedDate) {
        const d = ymdToDate(dtState.selectedDate);
        if (d) dtState.calendarMonth = new Date(d.getFullYear(), d.getMonth(), 1);
      }

      await syncCalendarAvailability({ clearInvalidSelection: false });
      await refreshHorasDisponibles();

      syncServiceUi();
    })();

    serviciosWrapper.addEventListener('change', async (e) => {
      const catSel = e.target.closest('select[data-role="categoria"]');
      if (catSel) {
        const row = catSel.closest('.servicio-row');
        const svcSel = row?.querySelector('select[data-role="servicio"]');
        if (!row || !svcSel) return;

        buildOptionsForServiceSelect(svcSel, catSel.value, "");

        const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
        const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');
        if (precioInp) precioInp.value = '';
        if (durInp) durInp.value = '';

        const empSel = row.querySelector('select[data-role="empleado"]');
        if (empSel) {
          empSel.innerHTML = `<option value="">Selecciona un servicio primero</option>`;
          empSel.disabled = true;
          empSel.value = '';
          empSel.removeAttribute('data-preselect');
        }

        recalcAll();

  syncServiceUi();

        setLockUI();
        await syncCalendarAvailability();
        await refreshHorasDisponibles();
        return;
      }

      const svcSel = e.target.closest('select[data-role="servicio"]');
      if (svcSel) {
        const row = svcSel.closest('.servicio-row');
        const opt = svcSel.options[svcSel.selectedIndex];
        if (!row || !opt) return;

        const precioInp = row.querySelector('input[data-role="precio_snapshot"]');
        const durInp    = row.querySelector('input[data-role="duracion_snapshot"]');

        const precio   = opt.dataset.precio ?? '';
        const duracion = opt.dataset.duracion ?? '';

        if (precioInp && (precioInp.value === '' || precioInp.value == 0)) precioInp.value = precio;
        if (durInp && (durInp.value === '' || durInp.value == 0)) durInp.value = duracion;

        await loadEmpleadosForRow(row, svcSel.value || null);

        recalcAll();

        setLockUI();
        await syncCalendarAvailability();
        await refreshHorasDisponibles();
        return;
      }

      const empSel = e.target.closest('select[data-role="empleado"]');
      if (empSel) {
        setLockUI();
        await syncCalendarAvailability();
        await refreshHorasDisponibles();
        return;
      }
    });

    serviciosWrapper.addEventListener('input', (e) => {
      if (
        e.target.matches('input[data-role="duracion_snapshot"]') ||
        e.target.matches('input[data-role="precio_snapshot"]')
      ) {
        recalcAll();
      }
    });
  }

  // Método de pago (solo completada)
  const estadoSelect = document.getElementById('estado_cita');
  const metodoWrap   = document.getElementById('metodo_pago_wrap');
  const metodoSelect = document.getElementById('metodo_pago');

  function toggleMetodoPago() {
    const show = (estadoSelect?.value === 'completada');
    if (metodoWrap) metodoWrap.style.display = show ? '' : 'none';
    if (metodoSelect) {
      metodoSelect.required = show;
      if (!show) metodoSelect.value = '';
    }
  }
  if (estadoSelect) estadoSelect.addEventListener('change', toggleMetodoPago);
  toggleMetodoPago();

  // Buscador de clientes
  const CLIENTES = @json($clientesForJs);
  const input    = document.getElementById('cliente_search');
  const dropdown = document.getElementById('cliente_dropdown');
  const results  = document.getElementById('cliente_results');
  const hidden   = document.getElementById('cliente_id');

  function hideResults() {
    dropdown.classList.add('hidden');
    results.innerHTML = '';
  }

  function showResults(items) {
    if (!items.length) {
      results.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500">Sin resultados</div>`;
      dropdown.classList.remove('hidden');
      return;
    }

    results.innerHTML = items.map(c => `
      <button type="button"
        class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm"
        data-id="${c.id}"
        data-label="${escapeHtml(c.label || '')}">
        <div class="font-medium text-gray-800">${escapeHtml(c.nombre || 'Sin nombre')}</div>
        ${c.email ? `<div class="text-gray-500">${escapeHtml(c.email)}</div>` : ''}
      </button>
    `).join('');

    dropdown.classList.remove('hidden');
  }

  if (input) {
    input.addEventListener('input', () => {
      const q = input.value.trim().toLowerCase();

      if (!q) {
        if (hidden) hidden.value = '';
        hideResults();
        return;
      }

      const filtered = CLIENTES.filter(c =>
        (c.nombre || '').toLowerCase().includes(q) ||
        (c.email  || '').toLowerCase().includes(q)
      ).slice(0, 8);

      showResults(filtered);
    });
  }

  if (results) {
    results.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-id]');
      if (!btn) return;

      if (hidden) hidden.value = btn.dataset.id;
      if (input)  input.value  = btn.dataset.label || '';
      hideResults();
    });
  }

  document.addEventListener('click', (e) => {
    if (!e.target.closest('#cliente_search') && !e.target.closest('#cliente_dropdown')) {
      hideResults();
    }
  });
});
</script>
@endpush