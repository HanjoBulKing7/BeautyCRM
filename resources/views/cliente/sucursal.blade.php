@extends('layouts.app') {{-- AGREGAR ESTA LÍNEA --}}
@section('title', 'Inicio - Cliente')
@section('content')
  <!-- Galería -->
  <section id="galeria" class="mt-8 text-center">
    <header>
      <h1>Bienvenida a nuestra sucursal</h1>
      <p>Explora cada rincón a través de nuestra galería visual</p>
    </header>

    <div class="carousel-wrapper mx-auto mt-6" id="carouselWrapper">
      <img id="carouselImage" src="" alt="Sucursal" onclick="abrirFullscreen(this.src)" />
    </div>
  </section>

  <!-- Sección Ubicación -->
  <section id="ubicacion" class="section mt-12">
    <div class="section mx-auto p-6 bg-white rounded-xl shadow-md max-w-3xl">
      <h2>¿Dónde estamos?</h2>
      <p>Nos encontramos en una ubicación privilegiada, con fácil acceso y estacionamiento. Nuestro equipo te espera con una sonrisa y la mejor atención.</p>
      <p>Dirección: Av. Ejemplo #123, Ciudad, Estado, CP 00000</p>
    </div>
  </section>

  <!-- Sección Qué ofrecemos -->
  <section id="ofrecemos" class="section mt-12">
    <div class="section mx-auto p-6 bg-white rounded-xl shadow-md max-w-3xl">
      <h2>¿Qué ofrecemos?</h2>
      <ul class="list-disc list-inside">
        <li>Atención personalizada</li>
        <li>Ambientes limpios y modernos</li>
        <li>Zona de espera cómoda</li>
        <li>Experiencia profesional en cada servicio</li>
      </ul>
    </div>
  </section>

  <!-- Pantalla completa -->
  <div id="fullscreen-overlay">
    <span class="close-btn">&times;</span>
    <img id="fullscreen-img" src="" alt="Foto en grande" />
  </div>
@endsection
@section('scripts')
  <!-- JS personalizado -->
  <script src="{{ asset('js/sucursal.js') }}" defer></script>
@endsection