<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beauty - {{ $categoria->nombre ?? 'Servicios' }}</title>

  {{-- Tailwind CSS CDN --}}
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />

  {{-- Tu hoja de estilos personalizada --}}
  <link rel="stylesheet" href="{{ asset('css/servicio.css') }}">
  
  <style>
    .servicios-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 2rem;
      padding: 0 1rem;
    }
    
    .service-card {
      width: 350px;
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }
    
    .service-image-container {
      width: 100%;
      height: 250px;
      overflow: hidden;
      position: relative;
    }
    
    .service-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .service-card:hover .service-image {
      transform: scale(1.05);
    }
    
    .discount-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: #ef4444;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-size: 0.875rem;
      font-weight: 600;
      z-index: 10;
    }
    
    .service-content {
      padding: 1.5rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .service-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.5rem;
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 0.75rem;
      line-height: 1.3;
    }
    
    .service-description {
      color: #718096;
      margin-bottom: 1.5rem;
      line-height: 1.5;
      flex: 1;
    }
    
    .service-details {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
    }
    
    .detail-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
    }
    
    .detail-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 500;
      color: #4a5568;
    }
    
    .detail-icon {
      width: 20px;
      height: 20px;
      color: #a18a7c;
    }
    
    .service-price {
      font-size: 1.5rem;
      font-weight: bold;
      color: #a18a7c;
      text-align: right;
    }
    
    .original-price {
      text-decoration: line-through;
      color: #a0aec0;
      font-size: 1rem;
      margin-right: 0.5rem;
    }
    
    .reserve-button {
      background: #999189;
      color: white;
      padding: 1rem 2rem;
      border-radius: 50px;
      text-align: center;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
      margin-top: auto;
    }
    
    .reserve-button:hover {
      background: #7a6f67;
      transform: translateY(-2px);
    }
    
    .no-services {
      text-align: center;
      padding: 3rem 1rem;
      grid-column: 1 / -1;
    }
    
    .no-services-icon {
      width: 80px;
      height: 80px;
      color: #cbd5e0;
      margin-bottom: 1rem;
    }
    
    /* Centrado responsivo */
    @media (max-width: 768px) {
      .servicios-container {
        justify-content: center;
      }
      
      .service-card {
        width: 100%;
        max-width: 400px;
      }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
      .service-card {
        width: calc(50% - 2rem);
      }
    }
    
    @media (min-width: 1025px) {
      .service-card {
        width: calc(33.333% - 2rem);
      }
    }
  </style>
</head>
<body class="font-sans bg-beige">
  <!-- Header -->
  <header class="header">
    <nav class="nav">
      <!-- Logo -->
      <div class="logo">
        <img src="{{ asset('iconos/logo blanco.png') }}" alt="Beauty Logo" class="logo-img">
      </div>

      <!-- Botón Móvil -->
      <button id="menu-btn" class="rounded-full p-2" style="background-color: #f9f4ef52;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
          <ellipse cx="12" cy="20" rx="5" ry="1.2" fill="rgba(0,0,0,0.08)" />
          <rect x="10.5" y="3" width="3" height="6" rx="0.8" fill="#C17B7B" stroke="#5A3E36" stroke-width="0.8" />
          <rect x="10.5" y="9" width="3" height="5" fill="#EED1CC" stroke="#5A3E36" stroke-width="0.8" />
          <rect x="9.8" y="14" width="4.4" height="4" rx="0.8" fill="#9C7C6D" stroke="#5A3E36" stroke-width="0.8" />
        </svg>
      </button>

      <!-- Menú Escritorio -->
      <div class="desktop-menu">
        <a href="{{ url('/interfaz') }}">Inicio</a>
        <a href="#servicios">Servicios</a>
        <a href="{{ url('sucursal') }}">Sucursal</a>
        <a href="{{ url('reserva') }}">Reserva</a>
        
        @auth
          <!-- Si el usuario está autenticado -->
          <div class="relative user-menu-container">
            <button id="user-menu-button" class="flex items-center focus:outline-none">
              <span class="mr-1">{{ Auth::user()->name }}</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div id="user-menu" class="absolute hidden right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
              <a href="{{ url('perfil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ver perfil</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
              </form>
            </div>
          </div>
        @else
          <!-- Si el usuario no está autenticado -->
          <a href="{{ route('login') }}">Iniciar sesión</a>
        @endauth
      </div>
    </nav>

    <!-- Menú Móvil -->
    <div id="mobile-menu" class="mobile-menu">
      <div class="mobile-menu-content">
        <a href="{{ url('/interfaz') }}">Inicio</a>
        <a href="#servicios">Servicios</a>
        <a href="{{ url('sucursal') }}">Sucursal</a>
        
        @auth
          <!-- Si el usuario está autenticado (versión móvil) -->
          <a href="{{ url('perfil') }}">Mi perfil</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-left py-2 text-gray-700 border-b border-gray-200">Cerrar sesión</button>
          </form>
        @else
          <!-- Si el usuario no está autenticado (versión móvil) -->
          <a href="{{ route('login') }}">Iniciar sesión</a>
        @endauth

        <div class="highlight-section">
          <p class="highlight-text">Resalta tu belleza</p>
          <a href="{{ url('reserva') }}" class="cta-button">Agenda tu cita</a>
          <p class="natural-text">Natural</p>
        </div>
      </div>
    </div>
  </header>

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

  {{-- JS para funcionalidad adicional --}}
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
</body>
</html>