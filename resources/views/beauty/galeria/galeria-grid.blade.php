{{-- resources/views/beauty/galeria/galeria-grid.blade.php --}}
<section class="gal-grid-section" id="galeria-trabajos">

    <div class="gal-grid__header">
        <span class="gal-grid__kicker">Trabajos</span>
        <h2 class="gal-grid__title">Nuestra Galería</h2>
    </div>

    {{-- Filtros por categoría --}}
    <div class="gal-grid__filters" role="group" aria-label="Filtrar por categoría">
        <button class="gal-filter-btn is-active" data-filter="all">Todos</button>
        <button class="gal-filter-btn" data-filter="cabello">Cabello</button>
        <button class="gal-filter-btn" data-filter="unas">Uñas</button>
        <button class="gal-filter-btn" data-filter="cejas">Cejas</button>
        <button class="gal-filter-btn" data-filter="faciales">Faciales</button>
        <button class="gal-filter-btn" data-filter="pestanas">Pestañas</button>
        <button class="gal-filter-btn" data-filter="espacio">Espacio</button>
    </div>

    {{-- Grid de imágenes --}}
    <div class="gal-grid__grid" id="galeriaGrid">

        {{-- Cabello --}}
        <div class="gal-grid__item" data-category="cabello">
            <img src="{{ asset('images/servicios/peinado.jpg') }}" alt="Peinado profesional" loading="lazy">
            <div class="gal-grid__overlay"><span>Peinados</span></div>
        </div>

        <div class="gal-grid__item gal-grid__item--wide" data-category="cabello">
            <img src="{{ asset('images/Peinado.png') }}" alt="Estilismo de cabello" loading="lazy">
            <div class="gal-grid__overlay"><span>Cabello</span></div>
        </div>

        <div class="gal-grid__item" data-category="cabello">
            <img src="{{ asset('images/Peinado2.png') }}" alt="Peinado para evento" loading="lazy">
            <div class="gal-grid__overlay"><span>Peinados para Eventos</span></div>
        </div>

        {{-- Uñas --}}
        <div class="gal-grid__item" data-category="unas">
            <img src="{{ asset('images/Uñas2.jpg') }}" alt="Nail art profesional" loading="lazy">
            <div class="gal-grid__overlay"><span>Uñas</span></div>
        </div>

        {{-- Cejas --}}
        <div class="gal-grid__item" data-category="cejas">
            <img src="{{ asset('images/servicios/cejas.jpg') }}" alt="Diseño de cejas profesional" loading="lazy">
            <div class="gal-grid__overlay"><span>Diseño de Cejas</span></div>
        </div>

        {{-- Faciales --}}
        <div class="gal-grid__item" data-category="faciales">
            <img src="{{ asset('images/servicios/faciales.jpg') }}" alt="Tratamiento facial" loading="lazy">
            <div class="gal-grid__overlay"><span>Faciales</span></div>
        </div>

        <div class="gal-grid__item" data-category="faciales">
            <img src="{{ asset('images/Faciales.jpg') }}" alt="Facial revitalizante" loading="lazy">
            <div class="gal-grid__overlay"><span>Faciales</span></div>
        </div>

        {{-- Pestañas --}}
        <div class="gal-grid__item" data-category="pestanas">
            <img src="{{ asset('images/servicios/pestañas.jpg') }}" alt="Extensión de pestañas" loading="lazy">
            <div class="gal-grid__overlay"><span>Pestañas</span></div>
        </div>

        {{-- Espacio --}}
        <div class="gal-grid__item gal-grid__item--tall" data-category="espacio">
            <img src="{{ asset('images/sucursal/1.JPG') }}" alt="Sucursal Beauty Bonita" loading="lazy">
            <div class="gal-grid__overlay"><span>Nuestro Espacio</span></div>
        </div>

        <div class="gal-grid__item" data-category="espacio">
            <img src="{{ asset('images/sucursal/6.JPG') }}" alt="Interior del salón" loading="lazy">
            <div class="gal-grid__overlay"><span>Salón</span></div>
        </div>

        <div class="gal-grid__item" data-category="espacio">
            <img src="{{ asset('images/sucursal/7.JPG') }}" alt="Área de servicios" loading="lazy">
            <div class="gal-grid__overlay"><span>Instalaciones</span></div>
        </div>

        <div class="gal-grid__item" data-category="espacio">
            <img src="{{ asset('images/sucursal/13.JPG') }}" alt="Recepción" loading="lazy">
            <div class="gal-grid__overlay"><span>Recepción</span></div>
        </div>

    </div>

    {{-- CTA al final --}}
    <div class="gal-grid__cta-wrap">
        <a href="{{ route('agendarcita.create') }}" class="gal-grid__cta">
            <i class="bx bx-calendar"></i>
            Agendar mi cita
        </a>
    </div>

</section>

<style>
    .gal-grid-section {
        padding: 80px 20px 100px;
        background-color: #fafaf8;
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    }

    .gal-grid__header {
        text-align: center;
        margin-bottom: 40px;
    }

    .gal-grid__kicker {
        display: block;
        font-size: 0.75rem;
        letter-spacing: 4px;
        color: #8e6708;
        text-transform: uppercase;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .gal-grid__title {
        font-size: clamp(2rem, 4vw, 3rem);
        color: #11141c;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 5px;
        margin: 0;
    }

    /* ── Filtros ── */
    .gal-grid__filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        margin-bottom: 48px;
    }

    .gal-filter-btn {
        padding: 10px 24px;
        border: 1px solid #e0e0e0;
        border-radius: 999px;
        background: #fff;
        color: #666;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: background 0.25s, border-color 0.25s, color 0.25s;
        font-family: inherit;
    }

    .gal-filter-btn:hover,
    .gal-filter-btn.is-active {
        background: #11141c;
        border-color: #11141c;
        color: #fff;
    }

    /* ── Grid ── */
    .gal-grid__grid {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .gal-grid__item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 4/5;
        cursor: pointer;
        background: #f0f0f0;
    }

    .gal-grid__item--wide {
        grid-column: span 2;
        aspect-ratio: 16/9;
    }

    .gal-grid__item--tall {
        grid-row: span 2;
    }

    .gal-grid__item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.55s ease;
        display: block;
    }

    .gal-grid__item:hover img {
        transform: scale(1.07);
    }

    .gal-grid__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(17, 20, 28, 0.72) 0%, transparent 55%);
        display: flex;
        align-items: flex-end;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gal-grid__item:hover .gal-grid__overlay {
        opacity: 1;
    }

    .gal-grid__overlay span {
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .gal-grid__item.is-hidden {
        display: none;
    }

    /* ── CTA ── */
    .gal-grid__cta-wrap {
        text-align: center;
        margin-top: 56px;
    }

    .gal-grid__cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 36px;
        border: 1px solid #11141c;
        border-radius: 999px;
        color: #11141c;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        transition: background 0.3s, color 0.3s;
        font-family: inherit;
    }

    .gal-grid__cta:hover {
        background: #11141c;
        color: #fff;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .gal-grid__grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .gal-grid__item--tall {
            grid-row: span 1;
        }
    }

    @media (max-width: 500px) {
        .gal-grid__grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .gal-grid__item--wide {
            grid-column: span 2;
            aspect-ratio: 3/2;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtns = document.querySelectorAll('.gal-filter-btn');
        const items      = document.querySelectorAll('#galeriaGrid .gal-grid__item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const filter = this.dataset.filter;
                filterBtns.forEach(b => b.classList.remove('is-active'));
                this.classList.add('is-active');
                items.forEach(item => {
                    item.classList.toggle('is-hidden', filter !== 'all' && item.dataset.category !== filter);
                });
            });
        });
    });
</script>
