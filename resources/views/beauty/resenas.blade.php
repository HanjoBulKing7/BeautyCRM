{{-- resources/views/beauty/resenas.blade.php --}}
<section class="reviews-section">
    <div class="reviews-container">

        <h2 class="reviews-title">Lo que dicen nuestras clientas</h2>

        <div class="reviews-rating">
            <span class="rating-number">{{ number_format($reviews['average_rating'], 1) }}</span>
            <div class="stars">
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

        <div class="reviews-grid">
            @foreach($reviews['reviews'] as $review)
                <div class="review-card">
                    <div class="review-header">
                        <span class="review-author">{{ $review['author'] }}</span>
                        <span class="review-time">{{ $review['relativeTime'] }}</span>
                    </div>
                    <div class="review-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $review['rating'])
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <p class="review-text">{{ $review['text'] }}</p>
                </div>
            @endforeach
        </div>

        <a href="https://maps.app.goo.gl/7eWofigTwTfLXbxX6" target="_blank" class="google-btn">
            Ver todas en Google
        </a>

    </div>
</section>