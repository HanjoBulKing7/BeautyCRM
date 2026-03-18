<section class="gallery-section">
    <div class="gallery__most-requested">
        <span class="gallery__kicker">DESCUBRE</span>
        <h2 class="gallery__big-title">Lo más solicitado</h2>
    </div>

    <div class="gallery-wrapper">
        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/Peinado2.png') }}" alt="Cabello">
            <div class="gallery-overlay">
                <h3 class="gallery-title">Cabello</h3>
            </div>
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/download-2025-12-16T00_07_16.jpg') }}" alt="Uñas">
            <div class="gallery-overlay">
                <h3 class="gallery-title">Uñas</h3>
            </div>
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/servicios/cejas.jpg') }}" alt="Cejas">
            <div class="gallery-overlay">
                <h3 class="gallery-title">Cejas</h3>
            </div>
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/servicios/faciales.jpg') }}" alt="Faciales">
            <div class="gallery-overlay">
                <h3 class="gallery-title">Faciales</h3>
            </div>
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/servicios/peinado.jpg') }}" alt="Extensión de cabello">
            <div class="gallery-overlay">
                <h3 class="gallery-title">Extensión de cabello</h3>
            </div>
        </a>
    </div>

   <div class="flex justify-center mt-12 mb-8 gallery-footer">
        <a href="{{ url('/servicio') }}" class="gallery-link">
            VER TODOS LOS SERVICIOS
        </a>
    </div>
</section>

<style>
    /* No tocaremos .gallery-wrapper para respetar tus proporciones originales.
       Solo añadimos CSS para colocar el texto elegantemente sobre las imágenes.
    */

    .gallery-card {
        position: relative; /* Necesario para que el texto se superponga */
        overflow: hidden; /* Evita que el zoom de la imagen se salga de la tarjeta */
        border-radius: 16px; /* Bordes redondeados como en tu imagen de referencia */
    }

    .gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    /* Degradado oscuro en la base para que el texto blanco sea legible */
    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: linear-gradient(to top, rgba(17, 20, 28, 0.85) 0%, rgba(17, 20, 28, 0) 100%);
        padding: 40px 15px 15px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        pointer-events: none; /* Para que el clic pase directo al enlace */
    }

    .gallery-title {
        color: #ffffff;
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 0;
        text-align: center;
        line-height: 1.3;
        transition: color 0.3s ease;
    }

    /* Animación sutil al pasar el cursor */
    .gallery-card:hover img {
        transform: scale(1.08);
    }

    .gallery-card:hover .gallery-title {
        color: #d4af37; /* El texto se hace dorado */
    }

    /* --- BOTÓN INFERIOR --- */
    .gallery-footer {
        margin-top: 50px;
        text-align: center;
    }

    .gallery-link {
        display: inline-block;
        padding: 14px 36px;
        background-color: transparent;
        border: 1px solid #11141c;
        color: #11141c;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 999px; /* Botón redondeado */
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    }

    .gallery-link:hover {
        background-color: #11141c;
        color: #ffffff;
    }
</style>