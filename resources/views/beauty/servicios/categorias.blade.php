<section class="salon-menu" id="servicios-categorias">
    <div class="salon-menu__container">
        <div class="salon-menu__header">
            <span class="salon-menu__eyebrow">Descubre</span>
            <h2 class="salon-menu__mainTitle">Nuestros Servicios</h2>
        </div>

        <div class="salon-menu__layout">
            
            <div class="salon-menu__visual">
                <div class="salon-menu__image-wrapper">
                    <img 
                        id="dynamic-service-image" 
                        src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" 
                        alt="Vista previa del servicio"
                    >
                </div>
            </div>

            <div class="salon-menu__accordion-container" id="menu-scroll-area">
                @foreach($categorias as $categoria)
                    @if($categoria->servicios->isNotEmpty())
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                {{ $categoria->nombre }}
                                <span class="accordion-icon">+</span>
                            </button>

                            <div class="accordion-content">
                                <ul class="service-list">
                                    @foreach($categoria->servicios as $servicio)
                                        @php
                                            $imgUrl = $servicio->imagen_url 
                                                ? $servicio->imagen_url 
                                                : asset('images/Beige Blogger Moderna Personal Sitio web.png');
                                        @endphp
                                        
                                        <li class="service-item" data-image="{{ $imgUrl }}" onmouseenter="changeImage(this)" onclick="changeImage(this)">
                                            <div class="service-info">
                                                <h4 class="service-name">{{ $servicio->nombre_servicio }}</h4>
                                                <div class="service-meta">
                                                    <span>{{ (int) $servicio->duracion_minutos }} min</span>
                                                    <span class="meta-divider">|</span>
                                                    <span class="service-price">${{ number_format((float) $servicio->precio, 2) }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="service-action">
                                                <a href="{{ route('agendarcita.create', ['servicio' => $servicio->id_servicio]) }}" class="service-btn">
                                                    Agendar
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>
</section>

<style>
    .salon-menu {
        padding: 80px 20px;
        background-color: #ffffff; /* Fondo blanco impecable */
        /* Usa una fuente elegante como Montserrat si la tienes, si no, usa sans-serif limpia */
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif; 
    }
    .salon-menu__container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    /* Estética de los Títulos idéntica a tu imagen */
    .salon-menu__header {
        text-align: center;
        margin-bottom: 60px;
    }
    .salon-menu__eyebrow {
        display: block;
        font-size: 0.75rem;
        letter-spacing: 4px;
        color: #8e6708; /* Tu dorado */
        text-transform: uppercase;
        margin-bottom: 12px;
        font-weight: 600;
    }
    .salon-menu__mainTitle {
        font-size: 3rem; /* Tamaño grande y llamativo */
        color: #11141c; /* Un azul/negro muy oscuro como en tu foto */
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 5px; /* Separación de letras */
        margin: 0;
    }
    
    /* Layout a dos columnas */
    .salon-menu__layout {
        display: flex;
        gap: 60px;
        align-items: flex-start;
    }

    /* Izquierda: Visor pegajoso */
    .salon-menu__visual {
        flex: 1;
        position: sticky;
        top: 40px; 
    }
    .salon-menu__image-wrapper {
        width: 100%;
        height: 550px; 
        border-radius: 24px; /* Bordes redondeados idénticos a los de tu imagen */
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05); /* Sombra muy sutil */
    }
    .salon-menu__image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.4s ease-in-out; 
    }

    /* Derecha: Acordeón */
    .salon-menu__accordion-container {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .accordion-item {
        border-bottom: 1px solid #f0f0f0; /* Líneas muy tenues y elegantes */
    }
    .accordion-header {
        width: 100%;
        text-align: left;
        padding: 24px 0;
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #11141c;
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.3s ease;
    }
    .accordion-header:hover {
        color: #8e6708; 
    }
    .accordion-icon {
        font-size: 1.8rem;
        font-weight: 300;
        color: #8e6708; 
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Contenido oculto */
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .service-list {
        list-style: none;
        padding: 0 0 20px 0;
        margin: 0;
    }
    .service-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 12px;
        border-radius: 12px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        cursor: pointer;
    }
    .service-item:hover {
        background-color: #faf9f6;
        transform: translateX(5px); /* Pequeño efecto de desplazamiento al pasar el ratón */
    }
    .service-name {
        margin: 0 0 6px 0;
        font-size: 1rem;
        color: #11141c;
        font-weight: 500;
    }
    .service-meta {
        font-size: 0.85rem;
        color: #888;
    }
    .meta-divider {
        margin: 0 8px;
        opacity: 0.3;
    }
    .service-price {
        font-weight: 700;
        color: #8e6708; 
    }
    .service-btn {
        padding: 8px 20px;
        background-color: transparent;
        border: 1px solid #8e6708; 
        color: #8e6708; 
        border-radius: 999px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    .service-item:hover .service-btn {
        background-color: #8e6708; 
        color: #ffffff; 
    }

    /* --- ADAPTACIÓN PARA CELULARES CON SCROLL INTERNO --- */
    @media (max-width: 900px) {
        .salon-menu {
            padding: 50px 15px;
        }
        .salon-menu__mainTitle {
            font-size: 2rem;
        }
        .salon-menu__layout {
            flex-direction: column;
            gap: 30px;
        }
        .salon-menu__visual {
            position: relative;
            top: 0;
            width: 100%;
        }
        .salon-menu__image-wrapper {
            height: 320px; /* Imagen un poco más pequeña en móvil */
            border-radius: 20px;
        }
        
        /* Magia del Scroll Interno para móvil */
        .salon-menu__accordion-container {
            width: 100%;
            max-height: 55vh; /* Ocupa máximo el 55% de la pantalla */
            overflow-y: auto; /* Activa el scroll vertical */
            overscroll-behavior: contain; /* Evita que la página principal haga scroll cuando llegas al final del menú */
            padding-right: 10px; /* Espacio para la barra */
            scroll-behavior: smooth;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Diseño de la barra de scroll (webkit) */
        .salon-menu__accordion-container::-webkit-scrollbar {
            width: 4px;
        }
        .salon-menu__accordion-container::-webkit-scrollbar-track {
            background: #f9f9f9; 
            border-radius: 10px;
        }
        .salon-menu__accordion-container::-webkit-scrollbar-thumb {
            background: rgba(142, 103, 8, 0.4); /* Dorado transparente */
            border-radius: 10px;
        }
        .salon-menu__accordion-container::-webkit-scrollbar-thumb:hover {
            background: #8e6708; /* Dorado sólido al tocarla */
        }
    }
</style>

<script>
    function toggleAccordion(button) {
        const item = button.parentElement;
        const content = item.querySelector('.accordion-content');
        const icon = item.querySelector('.accordion-icon');
        
        if (content.style.maxHeight) {
            content.style.maxHeight = null;
            icon.style.transform = "rotate(0deg)";
        } else {
            // Cerramos los demás
            document.querySelectorAll('.accordion-content').forEach(el => el.style.maxHeight = null);
            document.querySelectorAll('.accordion-icon').forEach(el => el.style.transform = "rotate(0deg)");
            
            // Abrimos el actual
            content.style.maxHeight = content.scrollHeight + "px";
            icon.style.transform = "rotate(45deg)";

            // (Opcional) En móviles, hace un pequeño scroll suave hacia la categoría que abriste
            if (window.innerWidth <= 900) {
                setTimeout(() => {
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 300);
            }
        }
    }

    function changeImage(element) {
        const newImageUrl = element.getAttribute('data-image');
        const mainImage = document.getElementById('dynamic-service-image');
        
        if (mainImage.src !== newImageUrl) {
            mainImage.style.opacity = 0; 
            setTimeout(() => {
                mainImage.src = newImageUrl;
                mainImage.style.opacity = 1; 
            }, 200); // Transición suave acorde a la estética
        }
    }
</script>