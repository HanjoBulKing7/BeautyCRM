<section class="salon-popular" id="mas-solicitados">
    @if(isset($destacados) && $destacados['servicios']->isNotEmpty())
    <div class="salon-popular__container">
        
        <header class="salon-popular__header">
            <span class="salon-popular__eyebrow">Favoritos</span>
            <h2 class="salon-popular__title">{{ $destacados['titulo'] }}</h2>
        </header>

        <div class="salon-popular__grid">
            @foreach($destacados['servicios'] as $servicio)
                <article class="salon-popular__card">
                    
                    <div class="salon-popular__img-wrapper">
                        @php
                            // Verificamos 'imagen_url' y si no existe, ponemos tu imagen local por defecto.
                            $imgUrl = !empty($servicio->imagen_url) 
                                ? $servicio->imagen_url 
                                : asset('images/Beige Blogger Moderna Personal Sitio web.png');
                        @endphp
                        
                        <img src="{{ $imgUrl }}" alt="{{ $servicio->nombre_servicio }}" loading="lazy">
                    </div>

                    <div class="salon-popular__body">
                        <h4 class="salon-popular__name">{{ $servicio->nombre_servicio }}</h4>

                        <p class="salon-popular__desc">
                            {{ \Illuminate\Support\Str::limit($servicio->descripcion ?? 'Servicio profesional de belleza y cuidado personal.', 80) }}
                        </p>

                        <div class="salon-popular__meta">
                            <span class="salon-popular__duration">
                                ⏱ {{ (int)($servicio->duracion_minutos ?? 0) }} min
                            </span>
                            <span class="salon-popular__price">
                                ${{ number_format($servicio->precio, 2) }}
                            </span>
                        </div>

                        <a class="salon-popular__btn" href="{{ route('agendarcita.create', ['servicio' => $servicio->id_servicio]) }}">
                            Agendar Cita
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    @endif
</section>

<style>
    .salon-popular {
        padding: 80px 20px;
        background-color: #ffffff; 
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    }

    .salon-popular__container {
        max-width: 1200px; 
        margin: 0 auto;
    }

    .salon-popular__header {
        text-align: center;
        margin-bottom: 60px; 
    }
    .salon-popular__eyebrow {
        display: block;
        font-size: 0.75rem;
        letter-spacing: 4px;
        color: #8e6708; 
        text-transform: uppercase;
        margin-bottom: 12px;
        font-weight: 600;
    }
    .salon-popular__title {
        font-size: 3rem; 
        color: #11141c; 
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 5px; 
        margin: 0;
    }

    .salon-popular__grid {
        display: grid;
        /* Modificado aquí: 250px permite que quepan 4 tarjetas en los 1200px del contenedor */
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .salon-popular__card {
        background-color: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06); 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 1px solid #f8f8f8; 
    }
    .salon-popular__card:hover {
        transform: translateY(-8px); 
        box-shadow: 0 15px 40px rgba(0,0,0,0.1); 
    }

    .salon-popular__img-wrapper {
        width: 100%;
        height: 220px; 
        overflow: hidden;
    }
    .salon-popular__img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .salon-popular__card:hover .salon-popular__img-wrapper img {
        transform: scale(1.05); 
    }

    .salon-popular__body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1; 
    }

    .salon-popular__name {
        margin: 0 0 10px 0;
        font-size: 1.1rem;
        color: #11141c;
        font-weight: 600;
        line-height: 1.3;
    }

    .salon-popular__desc {
        font-size: 0.85rem;
        color: #666;
        line-height: 1.5;
        margin: 0 0 20px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .salon-popular__meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0; 
        margin-top: auto; 
        margin-bottom: 20px;
    }

    .salon-popular__duration {
        font-size: 0.85rem;
        color: #888;
        font-weight: 500;
    }

    .salon-popular__price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #8e6708; 
    }

    .salon-popular__btn {
        display: block;
        width: 100%;
        text-align: center;
        padding: 12px 0;
        background-color: transparent;
        border: 1px solid #8e6708;
        color: #8e6708;
        border-radius: 999px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    .salon-popular__btn:hover {
        background-color: #8e6708;
        color: #ffffff;
    }

    .salon-popular__empty {
        grid-column: 1 / -1; 
        text-align: center;
        padding: 40px;
        color: #888;
        font-size: 1.1rem;
    }

    @media (max-width: 900px) {
        .salon-popular { 
            padding: 50px 15px; 
        }
        .salon-popular__title { 
            font-size: 2rem; 
        }
    }
</style>