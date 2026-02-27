@props([
    // Color dorado (ajústalo al dorado que uses en tu web)
    'gold' => '#C8A64A',

    // Stats (opcional por si luego quieres cambiar desde la vista)
    'stats' => null,
])

@php
    $stats = $stats ?? [
        ['icon' => 'sparkle', 'value' => '+1,200', 'label' => 'Servicios Realizados'],
        ['icon' => 'heart',   'value' => '+850',   'label' => 'Clientes Satisfechos'],
        ['icon' => 'badge',   'value' => '+5',     'label' => 'Años de Experiencia'],
    ];
@endphp

<section class="relative bg-white py-20 md:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 gap-y-12 gap-x-10 sm:grid-cols-3 md:gap-x-14">
            @foreach($stats as $s)
                <div class="text-center">
                    {{-- Icono grande, dorado, con fondo suave --}}
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-black/5 shadow-sm"
                         style="background: color-mix(in srgb, {{ $gold }} 12%, white); color: {{ $gold }};">
                        @switch($s['icon'])
                            @case('sparkle')
                                {{-- Sparkle / calidad --}}
                                <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none"
                                     stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2l1.2 4.2L17.5 8l-4.3 1.8L12 14l-1.2-4.2L6.5 8l4.3-1.8L12 2z"/>
                                    <path d="M19 13l.7 2.3L22 16l-2.3.7L19 19l-.7-2.3L16 16l2.3-.7L19 13z"/>
                                    <path d="M4.5 13l.8 2.6L8 16l-2.7.4L4.5 19l-.8-2.6L1 16l2.7-.4L4.5 13z"/>
                                </svg>
                                @break

                            @case('heart')
                                {{-- Clientes satisfechos --}}
                                <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none"
                                     stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                                </svg>
                                @break

                            @case('badge')
                            @default
                                {{-- Años de experiencia / premio --}}
                                <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none"
                                     stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2l2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5L4.8 7.2l5-.7L12 2z"/>
                                    <path d="M8 14.8V22l4-2 4 2v-7.2"/>
                                </svg>
                                @break
                        @endswitch
                    </div>

                    <div class="text-3xl md:text-4xl font-semibold tracking-tight text-gray-900">
                        {{ $s['value'] }}
                    </div>
                    <div class="mt-2 text-sm md:text-base text-gray-500">
                        {{ $s['label'] }}
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
