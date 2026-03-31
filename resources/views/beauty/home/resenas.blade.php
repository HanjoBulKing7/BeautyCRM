<section class="reviews-section" id="resenas">
    {{-- Encabezado --}}
    <div class="gallery__most-requested">
        <span class="gallery__kicker">Testimonios</span>
        <h2 class="gallery__big-title">Nuestras Clientas</h2>
    </div>

    {{-- Resumen de calificación --}}
    <div class="reviews-rating-summary">
        <span class="rating-number">{{ number_format($reviews['average_rating'], 1) }}</span>
        <div class="stars-summary">
            @php
                $fullStars = floor($reviews['average_rating']);
                $halfStar = $reviews['average_rating'] - $fullStars >= 0.5;
            @endphp
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $fullStars)
                    ★
                @elseif ($i == $fullStars + 1 && $halfStar)
                    <span class="half-star">★</span>
                @else
                    ☆
                @endif
            @endfor
        </div>
        <p class="reviews-count">Basado en {{ number_format($reviews['total_ratings']) }} reseñas en Google</p>
    </div>

    {{-- Contenedor flex con scroll --}}
    <div class="reviews-wrapper" id="reviewsWrapper">
        @foreach($reviews['reviews'] as $review)
            <div class="review-card">
                <div class="review-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $review['rating'])
                            ★
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                
                <p class="review-text">"{{ $review['text'] }}"</p>
                
                <div class="review-footer">
                    <span class="review-author">{{ $review['author'] }}</span>
                    <span class="review-time">{{ $review['relativeTime'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Contenedor de puntos (vacío para que el JS los cree) --}}
    <div class="gallery-dots" id="reviewDots"></div>

    {{-- Botón de acción --}}
    <div class="reviews-action">
        <a href="https://maps.app.goo.gl/7eWofigTwTfLXbxX6" target="_blank" class="google-btn">
            Ver todas en Google
        </a>
    </div>
</section>

{{-- SCRIPT PARA QUE FUNCIONEN LOS PUNTITOS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('reviewsWrapper');
        const dotsContainer = document.getElementById('reviewDots');
        const cards = wrapper.querySelectorAll('.review-card');

        // Si no hay tarjetas, no hacemos nada
        if (!wrapper || !dotsContainer || cards.length === 0) return;

        // 1. Crear un puntito por cada reseña
        cards.forEach((card, index) => {
            const dot = document.createElement('button');
            dot.classList.add('gallery-dot');
            if (index === 0) dot.classList.add('active'); // El primero inicia activo
            
            // 2. Al hacer clic en un puntito, hacer scroll hacia esa tarjeta
            dot.addEventListener('click', () => {
                // Calculamos la posición de la tarjeta para centrarla en la pantalla
                const scrollPos = card.offsetLeft - (wrapper.clientWidth / 2) + (card.clientWidth / 2);
                
                wrapper.scrollTo({
                    left: scrollPos,
                    behavior: 'smooth'
                });
            });
            
            dotsContainer.appendChild(dot);
        });

        const dots = dotsContainer.querySelectorAll('.gallery-dot');

        // 3. Detectar el scroll manual y actualizar el puntito activo
        wrapper.addEventListener('scroll', () => {
            let currentIndex = 0;
            let minDistance = Infinity;
            
            // Calculamos el centro actual del contenedor
            const wrapperCenter = wrapper.scrollLeft + (wrapper.clientWidth / 2);

            // Revisamos qué tarjeta está más cerca del centro
            cards.forEach((card, index) => {
                const cardCenter = card.offsetLeft + (card.clientWidth / 2);
                const distance = Math.abs(wrapperCenter - cardCenter);
                
                if (distance < minDistance) {
                    minDistance = distance;
                    currentIndex = index;
                }
            });

            // Actualizamos la clase "active" en los puntitos
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[currentIndex]) {
                dots[currentIndex].classList.add('active');
            }
        });
    });
</script>