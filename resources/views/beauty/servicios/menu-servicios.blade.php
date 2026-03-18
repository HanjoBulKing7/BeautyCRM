<section class="bb-menu" id="menu-servicios">
    <div class="bb-menu__container">
        
        <header class="bb-menu__header" data-aos="fade-up" data-aos-duration="800">
            <span class="bb-menu__eyebrow">Categorías</span>
            <h2 class="salon-menu__mainTitle">Explora nuestros servicios</h2>
        </header>

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

                <a class="bb-chip" href="#{{ $id }}" data-aos="fade-up" data-aos-duration="600" data-aos-delay="{{ $loop->index * 100 }}">
                    {{ $categoria->nombre }}
                </a>
            @endforeach
        </nav>
    </div>
</section>

<style>
    .bb-menu {
        padding: 80px 20px;
        background-color: #ffffff; 
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    }

    /* --- MÁRGENES IGUALADOS A LA PRIMERA SECCIÓN --- */
    .bb-menu__container {
        max-width: 1200px; /* Igualado a los 1200px de salon-popular */
        margin: 0 auto;
    }

    /* --- TÍTULOS Y CABECERAS EXACTOS --- */
    .bb-menu__header {
        text-align: center;
        margin-bottom: 60px; /* Igualado a los 60px de salon-popular */
    }
    
    .bb-menu__eyebrow {
        display: block;
        font-size: 0.75rem;
        letter-spacing: 4px;
        color: #8e6708;
        text-transform: uppercase;
        margin-bottom: 12px;
        font-weight: 600;
    }
    
    .bb-menu__title {
        font-size: 3rem; /* Igualado a 3rem */
        color: #11141c;
        font-weight: 700; /* Igualado al grosor 700 */
        text-transform: uppercase;
        letter-spacing: 5px;
        margin: 0;
    }

    /* --- CONTENEDOR DE LOS BOTONES --- */
    .bb-menu__chips {
        display: flex;
        flex-wrap: wrap;
        justify-content: center; 
        gap: 12px; /* Espaciado un poco más sutil */
    }

    /* --- BOTONES MINIMALISTAS (CHIPS) --- */
    .bb-chip {
        display: inline-block;
        padding: 10px 24px;
        background-color: transparent; 
        border: 1px solid #e0e0e0; /* Borde gris muy suave inicial para no saturar visualmente */
        color: #666666; /* Texto oscuro/gris para mantener el minimalismo */
        border-radius: 999px; 
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500; /* Bajamos el grosor de 600 a 500 para un look más delicado */
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    /* Efecto hover minimalista */
    .bb-chip:hover {
        border-color: #8e6708; /* El borde y texto se pintan de dorado al pasar el cursor */
        color: #8e6708;
        background-color: transparent;
        /* Se eliminaron las sombras (box-shadow) y el movimiento (translateY) para una estética más moderna y plana */
    }

    /* --- AJUSTES PARA MÓVILES --- */
    @media (max-width: 900px) { /* Breakpoint igualado a 900px */
        .bb-menu { padding: 50px 15px; }
        .bb-menu__title { font-size: 2rem; } /* Igualado a 2rem en móviles */
        .bb-chip { 
            padding: 8px 20px; 
            font-size: 0.8rem; 
        }
    }
</style>