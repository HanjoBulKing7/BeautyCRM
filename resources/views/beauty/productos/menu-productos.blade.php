<section class="bb-menu" id="menu-productos">
    <div class="bb-menu__container">
        <h2 class="bb-menu__title">Explora nuestros productos</h2>

        <nav class="bb-menu__chips">
            @foreach($categorias as $categoria)

                @continue($categoria->productos->isEmpty())

                @php
                    $id = \Illuminate\Support\Str::slug($categoria->nombre);
                @endphp

                <a class="bb-chip" href="#{{ $id }}">
                    {{ $categoria->nombre }}
                </a>
            @endforeach
        </nav>
    </div>
</section>
