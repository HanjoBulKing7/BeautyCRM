<section class="reviews-section section">
    <div class="reviews-container container">

        {{-- Títulos con tus clases existentes centrados --}}
        <div class="reviews-header" style="text-align: center;">
            <span class="gallery__kicker">TESTIMONIOS</span>
            <h2 class="gallery__big-title">nuestras clientas</h2>
        </div>

        {{-- Resumen de calificación --}}
        <div class="reviews-rating-summary" style="text-align: center;">
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

        {{-- Grid de Tarjetas Minimalistas --}}
        <div class="reviews-grid">
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

        {{-- Botón de acción --}}
        <div class="reviews-action" style="text-align: center;">
            <a href="https://maps.app.goo.gl/7eWofigTwTfLXbxX6" target="_blank" class="google-btn">
                Ver todas en Google
            </a>
        </div>

    </div>
</section>