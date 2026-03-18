<section class="find-us-section" id="find-us">
    
    {{-- Encabezado con las clases de tu estética --}}
    <div class="gallery__most-requested">
        <span class="gallery__kicker">Ubicación</span>
        <h2 class="gallery__big-title">Visítanos</h2>
    </div>

    <div class="find-us__container">
        {{-- Izquierda: imagen + texto centrado sin fondo --}}
        <div class="find-us__media">
            <img
                src="{{ asset('images/sucursal/c8.webp') }}"
                alt="Sucursal Beauty Bonita Studio"
                class="find-us__img"
                loading="lazy"
            />

            {{-- Capa oscura sutil para que el texto blanco siempre se lea --}}
            <div class="find-us__overlay" aria-hidden="true"></div>
            
            {{-- Texto flotante --}}
            <h3 class="find-us__label">¿Dónde encontrarnos?</h3>
        </div>

        {{-- Derecha: solo mapa --}}
        <div class="find-us__mapCard">
            <iframe
                class="find-us__map"
                title="Mapa de Beauty Bonita"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3701.1655079061807!2d-102.30103272526112!3d21.928187856281365!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8429ef7ebc133657%3A0x9b6df16ae3385e64!2sBEAUTY%20BONITA!5e0!3m2!1ses-419!2smx!4v1768183857594!5m2!1ses-419!2smx"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    </div>
</section>

<style>
    /* =========================================
       SECCIÓN: DÓNDE ENCONTRARNOS (UBICACIÓN)
    ========================================= */

    .find-us-section {
        padding: 80px 0; /* Padding igualado a las otras secciones */
        background-color: #ffffff;
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
        width: 100%;
    }

    /* --- TÍTULOS --- */
    .find-us-section .gallery__most-requested {
        text-align: center;
        margin-bottom: clamp(40px, 6vw, 60px);
        padding: 0 20px;
    }

    /* --- CONTENEDOR PRINCIPAL --- */
    .find-us__container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr; /* Divide exactamente a la mitad en PC */
        gap: 60px; /* Mucho espacio "aire" entre imagen y mapa */
        align-items: stretch; /* Hace que ambas cajas midan exactamente lo mismo de alto */
    }

    /* --- LADO IZQUIERDO: IMAGEN Y TEXTO --- */
    .find-us__media {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        aspect-ratio: 4 / 5; /* Proporción vertical editorial (como de revista) */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); /* Sombra casi invisible */
    }

    .find-us__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    /* Overlay para oscurecer la imagen solo un poco y que el texto resalte */
    .find-us__overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(17, 20, 28, 0.3); /* Tinte oscuro al 30% */
        pointer-events: none; /* Para que no bloquee clics */
        transition: background 0.4s ease;
    }

    .find-us__label {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #ffffff;
        font-size: 2rem;
        font-weight: 300; /* Letra delgada (Lujo) */
        text-transform: uppercase;
        letter-spacing: 4px;
        text-align: center;
        width: 80%;
        margin: 0;
        z-index: 2;
    }

    /* Efecto hover súper sutil en la imagen */
    .find-us__media:hover .find-us__img {
        transform: scale(1.05);
    }
    .find-us__media:hover .find-us__overlay {
        background: rgba(17, 20, 28, 0.4);
    }

    /* --- LADO DERECHO: MAPA --- */
    .find-us__mapCard {
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #f0f0f0; /* Borde muy fino, sin sombras */
        height: 100%;
        min-height: 400px;
    }

    .find-us__map {
        width: 100%;
        height: 100%;
        border: none;
        /* Filtro de Lujo: Vuelve el mapa gris/sobrio para que combine con la web */
        filter: grayscale(100%) contrast(1.1) opacity(0.9);
        transition: filter 0.5s ease;
    }

    /* El mapa recupera todo su color al pasar el cursor encima */
    .find-us__mapCard:hover .find-us__map {
        filter: grayscale(0%) contrast(1) opacity(1);
    }

    /* --- RESPONSIVO --- */

    /* Tablets grandes (hasta 1024px) */
    @media (max-width: 1024px) {
        .find-us__container {
            gap: 30px;
        }
        .find-us__label {
            font-size: 1.5rem;
        }
    }

    /* Celulares y tablets pequeñas (hasta 768px) */
    @media (max-width: 768px) {
        .find-us-section {
            padding: 60px 0;
        }
        
        .find-us__container {
            grid-template-columns: 1fr; /* Se apilan en vertical */
            gap: 40px;
        }

        .find-us__media {
            aspect-ratio: 16 / 9; /* En celular la imagen se hace más horizontal (apaisada) */
        }

        .find-us__mapCard {
            min-height: 350px;
        }

        .find-us__map {
            /* En celulares el mapa suele tener su color normal siempre por usabilidad */
            filter: grayscale(0%) contrast(1); 
        }
    }
</style>