<section class="bb-menu" id="menu-servicios">
    <div class="bb-menu__container">
        <h2 class="bb-menu__title">Explora nuestros servicios</h2>

        @php
            // ✅ Nuevo: ahora la página manda $categorias
            // ✅ Fallback: si alguna vista antigua manda $grupos, también funciona
            $categorias = $categorias ?? $grupos ?? collect();
        @endphp

        <nav class="bb-menu__chips" aria-label="Menú de servicios">
            @foreach($categorias as $categoria)

                {{-- ✅ Si NO tiene servicios, no lo mostramos --}}
                @continue($categoria->servicios->isEmpty())

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
