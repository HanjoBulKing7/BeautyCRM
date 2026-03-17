<section class="bb-cats" id="productos-categorias">
    <div class="bb-cats__container">
        <h2 class="bb-cats__mainTitle">Nuestros productos</h2>

        @foreach($categorias as $categoria)
            @if($categoria->productos->isNotEmpty())
                <div class="bb-cat" id="{{ \Illuminate\Support\Str::slug($categoria->nombre) }}">
                    <h3 class="bb-cat__title">{{ $categoria->nombre }}</h3>

                    <div class="bb-cat__grid">
                        @foreach($categoria->productos as $producto)
                            <article class="bb-svcCard">

                                <div class="bb-svcCard__img">
                                    <img
                                        src="{{ $producto->imagen_url
                                            ? $producto->imagen_url
                                            : asset('images/Beige Blogger Moderna Personal Sitio web.png') }}"
                                        alt="{{ $producto->nombre }}"
                                    >
                                </div>

                                <h4 class="bb-svcCard__name">
                                    {{ $producto->nombre }}
                                </h4>

                                <p class="bb-svcCard__price">
                                    ${{ number_format($producto->precio, 2) }}
                                </p>

                                @if($producto->descripcion)
                                    <p class="bb-svcCard__desc">
                                        {{ $producto->descripcion }}
                                    </p>
                                @endif

                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</section>
