<section class="bb-most" id="mas-solicitados">
  <div class="bb-most__container">
    <header class="bb-most__header">
      <h2 class="bb-most__title">Servicios más solicitados</h2>
    </header>

    {{-- ✅ MISMO grid/clases que categorías --}}
    <div class="bb-cat__grid">
      @forelse(($topServicios ?? collect()) as $item)
        @php
          $precioFinal = max(0, (float)($item->precio ?? 0) - (float)($item->descuento ?? 0));

          // Imagen robusta (igual criterio que en categorías)
          $img = $item->imagen ?? null;
          if ($img) {
              if (\Illuminate\Support\Str::startsWith($img, ['images/', '/images/'])) {
                  $imgSrc = asset(ltrim($img, '/'));
              } else {
                  $imgSrc = asset('storage/' . ltrim($img, '/'));
              }
          } else {
              $imgSrc = asset('images/Beige Blogger Moderna Personal Sitio web.png');
          }
        @endphp

        <article class="bb-svcCard">
          <div class="bb-svcCard__img">
            <img src="{{ $imgSrc }}" alt="{{ $item->nombre_servicio }}" loading="lazy">
          </div>

          {{-- ✅ Body centrado con toda la info --}}
          <div class="bb-svcCard__body" style="text-align:center; padding: 14px 16px;">
            <h4 class="bb-svcCard__name" style="margin: 10px 0 6px;">
              {{ $item->nombre_servicio }}
            </h4>

            <p class="bb-svcCard__desc" style="margin: 0 0 12px; opacity: .85; line-height: 1.5;">
              {{ \Illuminate\Support\Str::limit($item->descripcion ?? 'Sin descripción.', 120) }}
            </p>

            <div class="bb-svcCard__meta" style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom: 12px;">
              <span class="bb-svcCard__pill" style="padding: 6px 10px; border-radius: 999px; border: 1px solid rgba(0,0,0,.08);">
                <strong>Duración:</strong> {{ (int)($item->duracion_minutos ?? 0) }} min
              </span>

              <span class="bb-svcCard__pill" style="padding: 6px 10px; border-radius: 999px; border: 1px solid rgba(0,0,0,.08);">
                <strong>Precio:</strong> ${{ number_format($precioFinal, 2) }}
              </span>
            </div>

            <a class="bb-svcCard__link"
               href="{{ route('agendarcita.create', ['servicio' => $item->id_servicio]) }}"
               style="display:inline-flex; justify-content:center; align-items:center; text-decoration:none;">
              Agendar
            </a>
          </div>
        </article>
      @empty
        <p style="opacity:.75; text-align:center; width:100%;">Aún no hay servicios para mostrar.</p>
      @endforelse
    </div>
  </div>
</section>
