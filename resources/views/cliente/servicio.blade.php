@extends('layouts.app') {{-- AGREGAR ESTA LÍNEA --}}
@section('title', 'Inicio - Cliente')
@section('content')
  <!-- Hero Section dinámico -->
  <section class="relative py-20 bg-gradient-to-r from-amber-800 to-amber-600">
    <div class="max-w-6xl mx-auto px-6 lg:px-12 text-center text-white">
      <h1 class="text-4xl md:text-6xl font-montas mb-6">
        {{ $categoria->nombre ?? 'Nuestros Servicios' }}
      </h1>
      <p class="text-xl md:text-2xl max-w-3xl mx-auto">
        {{ $categoria->descripcion ?? 'Descubre todos nuestros servicios profesionales' }}
      </p>
    </div>
  </section>

  <!-- Servicios de la categoría seleccionada -->
  <section class="py-20 bg-beige" id="servicios">
    <div class="max-w-7xl mx-auto px-4">
      @if(isset($categoria) && $servicios->count() > 0)
        <h2 class="text-3xl text-center font-montas mb-12 text-white">Servicios de {{ $categoria->nombre }}</h2>
      @elseif($servicios->count() > 0)
        <h2 class="text-3xl text-center font-montas mb-12 text-white">Todos Nuestros Servicios</h2>
      @endif
      
      <div class="servicios-container">
        @forelse($servicios as $servicio)
        <div class="service-card">
          @if($servicio->descuento > 0)
            <div class="discount-badge">
              -{{ $servicio->descuento }}%
            </div>
          @endif
          
          <div class="service-image-container">
            @if($servicio->imagen)
              <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="{{ $servicio->nombre_servicio }}" class="service-image">
            @else
              <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            @endif
          </div>
          
          <div class="service-content">
            <h3 class="service-title">{{ $servicio->nombre_servicio }}</h3>
            
            @if($servicio->descripcion)
              <p class="service-description">{{ $servicio->descripcion }}</p>
            @endif
            
            <div class="service-details">
              <div class="detail-item">
                <span class="detail-label">
                  <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Precio:
                </span>
                <span class="service-price">
                  @if($servicio->descuento > 0)
                    <span class="original-price">${{ number_format($servicio->precio, 2) }}</span>
                    ${{ number_format($servicio->precio * (1 - $servicio->descuento / 100), 2) }}
                  @else
                    ${{ number_format($servicio->precio, 2) }}
                  @endif
                </span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">
                  <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Duración:
                </span>
                <span class="font-semibold text-gray-700">{{ $servicio->duracion_minutos }} min</span>
              </div>
            </div>
            
            <a href="{{ url('reserva?servicio=' . $servicio->id_servicio) }}" class="reserve-button">
              Reservar ahora
            </a>
          </div>
        </div>
        @empty
        <div class="no-services">
          <svg class="no-services-icon mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay servicios disponibles</h3>
          <p class="text-gray-500">Próximamente agregaremos más servicios.</p>
        </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Scroll suave a la sección de servicios
      @if(request()->has('categoria'))
        setTimeout(() => {
          document.getElementById('servicios').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
          });
        }, 300);
      @endif
      
      // Ajustar altura mínima de las cards para consistencia
      const adjustCardHeights = () => {
        const cards = document.querySelectorAll('.service-card');
        let maxHeight = 0;
        
        // Primero resetear todas las alturas
        cards.forEach(card => {
          card.style.minHeight = 'auto';
        });
        
        // Encontrar la card más alta
        cards.forEach(card => {
          if (card.offsetHeight > maxHeight) {
            maxHeight = card.offsetHeight;
          }
        });
        
        // Aplicar la altura mínima a todas las cards
        cards.forEach(card => {
          card.style.minHeight = maxHeight + 'px';
        });
      };
      
      // Ajustar alturas después de que la página cargue
      setTimeout(adjustCardHeights, 100);
      
      // Reajustar cuando la ventana cambie de tamaño
      window.addEventListener('resize', adjustCardHeights);
    });
  </script>
@endsection