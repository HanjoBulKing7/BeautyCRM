<section class="find-us" id="find-us">
    <div class="find-us__container">

        {{-- Izquierda: imagen + texto centrado sin fondo --}}
        <div class="find-us__media">
            <img
                src="{{ asset('images/sucursal/c8.webp') }}"
                alt="Sucursal Beauty Bonita Studio"
                class="find-us__img"
                loading="lazy"
            />

            <div class="find-us__label">¿Dónde encontrarnos?</div>
            <div class="find-us__overlay" aria-hidden="true"></div>
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

<link rel="stylesheet" href="{{ asset('css/donde-encontrarnos.css') }}">
