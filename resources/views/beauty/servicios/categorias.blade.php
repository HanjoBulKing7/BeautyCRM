<section class="bb-cats" id="servicios-categorias">
    <div class="bb-cats__container">
        <h2 class="bb-cats__mainTitle">Nuestros servicios</h2>

        @foreach($grupos as $grupo)
            @if($grupo->servicios->isNotEmpty())
                <div class="bb-cat" id="{{ \Illuminate\Support\Str::slug($grupo->nombre) }}">
                    <h3 class="bb-cat__title">{{ $grupo->nombre }}</h3>

                    <div class="bb-cat__grid">
                        @foreach($grupo->servicios as $servicio)
                            <article class="bb-svcCard">
                                <div class="bb-svcCard__img">
                                    <img
                                        src="{{ $servicio->imagen ? asset($servicio->imagen) : asset('images/Beige Blogger Moderna Personal Sitio web.png') }}"
                                        alt="{{ $servicio->nombre_servicio }}"
                                    >

                                </div>

                                <h4 class="bb-svcCard__name">{{ $servicio->nombre_servicio }}</h4>

                                {{-- Seguro aunque tu ruta no acepte params: se va por querystring --}}
                                <a class="bb-svcCard__link"
                                   href="{{ route('citas.create', ['servicio' => $servicio->id_servicio]) }}">
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
