<section class="bb-menu" id="menu-servicios">
    <div class="bb-menu__container">
        <h2 class="bb-menu__title">Explora nuestros servicios</h2>

        @php
            // Por si alguna vista incluye este partial sin pasar $grupos
            $grupos = $grupos ?? collect();
        @endphp

        <nav class="bb-menu__chips" aria-label="Menú de servicios">
            @foreach($grupos as $grupo)

                {{-- ✅ Si NO tiene servicios, no lo mostramos --}}
                @continue($grupo->servicios->isEmpty())

                @php
                    $id = \Illuminate\Support\Str::slug($grupo->nombre);
                @endphp

                <a class="bb-chip" href="#{{ $id }}">
                    {{ $grupo->nombre }}
                </a>
            @endforeach
        </nav>
    </div>
</section>
