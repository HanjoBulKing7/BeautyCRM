<section class="gallery-section">
    <div class="gallery__most-requested">
        <span class="gallery__kicker">DESCUBRE</span>
        <h2 class="gallery__big-title">Lo más solicitado</h2>
    </div>

    <div class="gallery-wrapper">
        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/Peinado2.png') }}" alt="Peinados de moda">
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/download-2025-12-16T00_07_16.jpg') }}" alt="Tendencias en maquillaje">
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/servicios/cejas.jpg') }}" alt="Diseño de cejas">
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/servicios/faciales.jpg') }}" alt="Tratamientos faciales">
        </a>

        <a href="{{ url('/servicio') }}" class="gallery-card block">
            <img src="{{ asset('images/servicios/peinado.jpg') }}" alt="Cortes y peinados">
        </a>
    </div>

    <div class="flex justify-center mt-12 mb-8">
        <a href="{{ url('/servicio') }}" class="gallery-link">
            VER TODOS LOS SERVICIOS
        </a>
    </div>
</section>