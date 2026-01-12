
@push('styles')
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;&family=Roboto+Mono&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/servicios.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Roboto+Mono&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" />

@endpush
  <div class="servicios-page">

    <section class="sticky-cards">
        <div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Maquillaje">
    </div>
    <div class="card-content">
        <h5>Maquillaje</h5>
        <p>Maquillaje profesional para eventos, sesiones y ocasiones especiales. Acabado duradero, piel luminosa y un look que resalta tus facciones sin perder tu esencia.</p>
    </div>
</div>

<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Peinado">
    </div>
    <div class="card-content">
        <h5>Peinado</h5>
        <p>Peinados elegantes y modernos: ondas, recogidos, trenzas y estilos personalizados. Te ayudamos a elegir el look ideal según tu evento, outfit y tipo de cabello.</p>
    </div>
</div>

<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Cabello">
    </div>
    <div class="card-content">
        <h5>Cabello</h5>
        <p>
            Servicio integral de cabello que combina corte y coloración con asesoría personalizada.
            Analizamos tu tipo de cabello, rostro y estilo para lograr un look armonioso, moderno y fácil de mantener,
            cuidando siempre la salud, el brillo y la textura del cabello.
        </p>
    </div>
</div>


<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Manicura">
    </div>
    <div class="card-content">
        <h5>Manicura</h5>
        <p>Manicura detallada y pulida: limpieza, forma, cutícula y esmaltado perfecto. Ideal para mantener tus manos cuidadas, elegantes y con un acabado impecable.</p>
    </div>
</div>

<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Tratamientos capilares">
    </div>
    <div class="card-content">
        <h5>Tratamientos capilares</h5>
        <p>Recupera brillo, fuerza y suavidad con tratamientos según tu necesidad: hidratación, reparación y control de frizz. Cabello más saludable desde la primera sesión.</p>
    </div>
</div>

<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Pestañas">
    </div>
    <div class="card-content">
        <h5>Pestañas</h5>
        <p>Realce de mirada con técnicas que se adaptan a ti. Logra un efecto más definido y elegante, cuidando la comodidad y la armonía con tus facciones.</p>
    </div>
</div>

<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Pedicura">
    </div>
    <div class="card-content">
        <h5>Pedicura</h5>
        <p>Pedicura completa para pies suaves y cuidados: limpieza, exfoliación y esmaltado. Perfecta para verte bien y sentirte cómoda en cualquier temporada.</p>
    </div>
</div>

<div class="card">
    <div class="card-img">
        <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Faciales">
    </div>
    <div class="card-content">
        <h5>Faciales</h5>
        <p>Faciales personalizados para limpiar, hidratar y revitalizar tu piel. Te ayudamos a mejorar textura y luminosidad con un cuidado profesional y relajante.</p>
    </div>
</div>

    </section>

    <section class="outro">
        <h2 class="outro-title">Servicios Beauty Bonita Studio</h2>

        <a href="#sticky-cards" class="outro-link">
            Ver servicios
        </a>
    </section>

    </div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/lenis@1.3.15/dist/lenis.min.js"></script>
    <script src="{{ asset('js/servicios.js') }}"></script>
@endpush