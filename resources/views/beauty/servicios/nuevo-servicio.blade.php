<section class="bb-new-section" id="nuevo-servicio">
    @if($ultimoServicio)

    <div class="bb-new__container">

        {{-- Lado izquierdo: Textos y Datos del Servicio --}}
        <div class="bb-new__content">
            <div class="bb-new__text-group">
                <span class="bb-new__subtitle">Nuevo Servicio</span>
                <h3 class="bb-new__title">{{ $ultimoServicio->nombre_servicio }}</h3>
                <p class="bb-new__description">
                    {{ $ultimoServicio->descripcion }}
                </p>
            </div>

            {{-- Bloque de Precio, Duración y Botón --}}
            <div class="bb-new__service-info">
                <div class="bb-new__meta">
                    <div class="bb-new__meta-item">
                        <span class="bb-new__meta-label">Duración</span>
                        <span class="bb-new__meta-value">{{ (int) $ultimoServicio->duracion_minutos }} min</span>
                    </div>
                    <div class="bb-new__meta-divider"></div>
                    <div class="bb-new__meta-item">
                        <span class="bb-new__meta-label">Inversión</span>
                        <span class="bb-new__meta-value">${{ number_format($ultimoServicio->precio, 2) }}</span>
                    </div>
                </div>

                {{-- Botón con la clase correcta y alineación para tu SVG --}}
                <a href="{{ route('agendarcita.create', ['servicio' => $ultimoServicio->id_servicio]) }}" class="bb-new__btn" style="display: inline-flex; align-items: center; gap: 8px;">
                    Agendar Ahora
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.16669 10H15.8334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.8333 5L15.8333 10L10.8333 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Lado derecho: Imagen del Servicio --}}
        <div class="bb-new__media">
            @php
                // Verificamos 'imagen_url' y si no existe, ponemos tu imagen local por defecto.
                $imgUrl = !empty($ultimoServicio->imagen_url) 
                    ? $ultimoServicio->imagen_url 
                    : asset('images/Beige Blogger Moderna Personal Sitio web.png');
            @endphp
            
            <img src="{{ $imgUrl }}" alt="Imagen de {{ $ultimoServicio->nombre_servicio }}" class="bb-new__img" loading="lazy">
        </div>

    </div>
    @endif
</section>