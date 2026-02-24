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

                                <div class="bb-svcCard__body" style="text-align:center; padding: 14px 16px;">
                                    <h4 class="bb-svcCard__name" style="margin: 10px 0 6px;">
                                        {{ $servicio->nombre_servicio }}
                                    </h4>

                                    @if(!empty($servicio->descripcion))
                                        <p class="bb-svcCard__desc" style="margin: 0 0 10px; opacity: .85; line-height: 1.5;">
                                            {{ \Illuminate\Support\Str::limit($servicio->descripcion, 140) }}
                                        </p>
                                    @else
                                        <p class="bb-svcCard__desc" style="margin: 0 0 10px; opacity: .65;">
                                            Sin descripción.
                                        </p>
                                    @endif

                                    <div class="bb-svcCard__meta" style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom: 12px;">
                                        <span class="bb-svcCard__metaItem" style="padding: 6px 10px; border-radius: 999px; border: 1px solid rgba(0,0,0,.08);">
                                            <strong>Duración:</strong> {{ (int) $servicio->duracion_minutos }} min
                                        </span>

                                        <span class="bb-svcCard__metaItem" style="padding: 6px 10px; border-radius: 999px; border: 1px solid rgba(0,0,0,.08);">
                                            <strong>Precio:</strong> ${{ number_format((float) $servicio->precio, 2) }}
                                        </span>
                                    </div>

                                    <a class="bb-svcCard__link"
                                       href="{{ route('agendarcita.create', ['servicio' => $servicio->id_servicio]) }}"
                                       style="display:inline-flex; justify-content:center; align-items:center; text-decoration:none;">
                                        Agendar
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

    </div>
</section>
