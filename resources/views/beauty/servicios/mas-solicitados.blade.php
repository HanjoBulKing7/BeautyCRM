<section class="bb-most" id="mas-solicitados">
  <div class="bb-most__container">
    <header class="bb-most__header">
      <h2 class="bb-most__title">Servicios más solicitados</h2>
    </header>

    <div class="bb-cat__grid">
      @forelse(($topServicios ?? collect()) as $item)
        @php
          $precioFinal = max(0, (float)($item->precio ?? 0) - (float)($item->descuento ?? 0));

          $imgSrc = asset('images/Beige Blogger Moderna Personal Sitio web.png');
          $imgCandidate = $item->imagen_url ?? null;

          if (!$imgCandidate) {
            $img = (string)($item->imagen ?? '');

            if ($img !== '') {
              if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
                $imgCandidate = $img;
              } elseif (\Illuminate\Support\Str::startsWith($img, ['images/', '/images/'])) {
                $imgCandidate = asset(ltrim($img, '/'));
              } else {
                $path = ltrim($img, '/');
                if (\Illuminate\Support\Str::startsWith($path, 'storage/')) {
                  $path = substr($path, 8);
                }

                $publicStoragePath = public_path('storage');
                if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
                  $imgCandidate = asset('storage/' . $path);
                } else {
                  $imgCandidate = route('media.public', ['path' => $path]);
                }
              }
            }
          }

          if (!empty($imgCandidate)) {
            $imgSrc = $imgCandidate;
          }
        @endphp

        <article class="bb-svcCard">
          <div class="bb-svcCard__img">
            <img src="{{ $imgSrc }}" alt="{{ $item->nombre_servicio }}" loading="lazy">
          </div>

          <div class="bb-svcCard__body">
            <h4 class="bb-svcCard__name">{{ $item->nombre_servicio }}</h4>

            <p class="bb-svcCard__desc">
              {{ \Illuminate\Support\Str::limit($item->descripcion ?? 'Sin descripción.', 120) }}
            </p>

            <div class="bb-svcCard__meta">
              <span class="bb-svcCard__pill">
                <strong>Duración:</strong> {{ (int)($item->duracion_minutos ?? 0) }} min
              </span>

              <span class="bb-svcCard__pill">
                <strong>Precio:</strong> ${{ number_format($precioFinal, 2) }}
              </span>
            </div>

            <a class="bb-svcCard__link"
               href="{{ route('agendarcita.create', ['servicio' => $item->id_servicio]) }}">
              Agendar
            </a>
          </div>
        </article>
      @empty
    <div style="display: flex; justify-content: center; width: 100%;">
        <p class="bb-most__empty">Aún no hay servicios para mostrar.</p>
    </div>
@endforelse
    </div>
  </div>
</section>
