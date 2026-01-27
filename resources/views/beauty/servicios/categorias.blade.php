<section class="bb-cats" id="servicios-categorias">
    <div class="bb-cats__container">
        <h2 class="bb-cats__mainTitle">Nuestros servicios</h2>

        @foreach($categorias as $categoria)
            @if($categoria->servicios->isNotEmpty())
                <div class="bb-cat" id="{{ $categoria->slug ?? \Illuminate\Support\Str::slug($categoria->nombre) }}">
                    <h3 class="bb-cat__title">{{ $categoria->nombre }}</h3>

                    <div class="bb-cat__grid">
                        @foreach($categoria->servicios as $servicio)
                            <article class="bb-svcCard">
                                <div class="bb-svcCard__img">
                                    <img
                                        src="{{ $servicio->imagen ? asset('storage/'.$servicio->imagen) : asset('images/Beige Blogger Moderna Personal Sitio web.png') }}"
                                        alt="{{ $servicio->nombre_servicio }}"
                                    >
                                </div>

                                <h4 class="bb-svcCard__name">{{ $servicio->nombre_servicio }}</h4>

                                <a class="bb-svcCard__link"
                                    href="{{ route('agendarcita.create', ['servicio' => $servicio->id_servicio]) }}">
                                        Agendar
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

    </div>
</section>
