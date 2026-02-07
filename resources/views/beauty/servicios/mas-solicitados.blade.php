<section class="bb-most" id="mas-solicitados">
  <div class="bb-most__container">
    <header class="bb-most__header">
      <h2 class="bb-most__title">Servicios más solicitados</h2>
    </header>

    <div class="bb-most__grid">
      @forelse($topServicios as $item)
        <article class="bb-card">
          <div class="bb-card__img">
            {{-- Si aún no tienes imagen por servicio, deja la default --}}
            <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}"
                 alt="{{ $item->nombre_servicio }}">
          </div>

          <h3 class="bb-card__name">{{ $item->nombre_servicio }}</h3>

          <a class="bb-card__link"
             href="https://wa.me/5215512345678?text={{ urlencode('Hola, quiero agendar ' . $item->nombre_servicio . ' 😊') }}"
             target="_blank" rel="noopener">
            Agendar
          </a>
        </article>
      @empty
        {{-- Si no hay datos en el mes actual, puedes mostrar tus 4 cards default o un mensaje --}}
        <p style="opacity:.75;">Aún no hay reservas este mes.</p>
      @endforelse
    </div>
  </div>
</section>
