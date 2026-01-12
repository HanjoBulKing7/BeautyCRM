<section class="bb-most" id="mas-solicitados">
    <div class="bb-most__container">
        <header class="bb-most__header">
            <h2 class="bb-most__title">Servicios más solicitados</h2>
        </header>

        <div class="bb-most__grid">
            {{-- Card 1 --}}
            <article class="bb-card">
                <div class="bb-card__img">
                    <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Maquillaje">
                </div>
                <h3 class="bb-card__name">Maquillaje</h3>
                <a class="bb-card__link"
                   href="https://wa.me/5215512345678?text={{ urlencode('Hola, quiero agendar Maquillaje 😊') }}"
                   target="_blank" rel="noopener">
                    Agendar
                </a>
            </article>

            {{-- Card 2 --}}
            <article class="bb-card">
                <div class="bb-card__img">
                    <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Peinado">
                </div>
                <h3 class="bb-card__name">Peinado</h3>
                <a class="bb-card__link"
                   href="https://wa.me/5215512345678?text={{ urlencode('Hola, quiero agendar Peinado 😊') }}"
                   target="_blank" rel="noopener">
                    Agendar
                </a>
            </article>

            {{-- Card 3 --}}
            <article class="bb-card">
                <div class="bb-card__img">
                    <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Cabello">
                </div>
                <h3 class="bb-card__name">Cabello</h3>
                <a class="bb-card__link"
                   href="https://wa.me/5215512345678?text={{ urlencode('Hola, quiero agendar Cabello (corte y color) 😊') }}"
                   target="_blank" rel="noopener">
                    Agendar
                </a>
            </article>

            {{-- Card 4 --}}
            <article class="bb-card">
                <div class="bb-card__img">
                    <img src="{{ asset('images/Beige Blogger Moderna Personal Sitio web.png') }}" alt="Faciales">
                </div>
                <h3 class="bb-card__name">Faciales</h3>
                <a class="bb-card__link"
                   href="https://wa.me/5215512345678?text={{ urlencode('Hola, quiero agendar Facial 😊') }}"
                   target="_blank" rel="noopener">
                    Agendar
                </a>
            </article>
        </div>
    </div>
</section>
