{{-- Floating WhatsApp button (Bottom-Right) --}}
@php
    $phone = $phone ?? '5215512345678';
    $msg = $msg ?? 'Hola, quiero agendar una cita por WhatsApp.';

    // ✅ Dorado más elegante (champán)
    $gold = '#C8A24A';
@endphp

<a
    href="https://wa.me/{{ $phone }}?text={{ urlencode($msg) }}"
    target="_blank"
    rel="noopener"
    aria-label="Agendar cita por WhatsApp"
    style="
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 9999;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        user-select: none;
    "
>
    {{-- Leyenda primero (con flecha hacia el círculo a la derecha) --}}
    <span
        style="
            position: relative;
            background: {{ $gold }};
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            line-height: 1;
            padding: 14px 18px 14px 16px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,.18);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            white-space: nowrap;
        "
        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 14px 30px rgba(0,0,0,.22)'; this.style.filter='brightness(1.02)';"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,.18)'; this.style.filter='none';"
    >
        {{-- Flecha hacia la derecha (pegada al círculo) --}}
        <span
            style="
                position: absolute;
                right: -10px;
                top: 50%;
                transform: translateY(-50%);
                width: 0;
                height: 0;
                border-top: 10px solid transparent;
                border-bottom: 10px solid transparent;
                border-left: 10px solid {{ $gold }};
            "
            aria-hidden="true"
        ></span>

        Agendar cita
    </span>

    {{-- Icono en círculo al final (pegado al borde de pantalla) --}}
    <span
        style="
            width: 52px;
            height: 52px;
            border-radius: 999px;
            background: {{ $gold }};
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,.18);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        "
        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 14px 30px rgba(0,0,0,.22)'; this.style.filter='brightness(1.02)';"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,.18)'; this.style.filter='none';"
    >
        {{-- WhatsApp Icon (SVG) --}}
        <svg width="24" height="24" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <path fill="#fff" d="M19.11 17.52c-.26-.13-1.53-.75-1.77-.84-.24-.09-.41-.13-.58.13-.17.26-.67.84-.82 1.01-.15.17-.31.2-.57.07-.26-.13-1.11-.41-2.12-1.3-.78-.7-1.31-1.56-1.47-1.82-.15-.26-.02-.4.11-.53.12-.12.26-.31.39-.46.13-.15.17-.26.26-.44.09-.17.04-.33-.02-.46-.07-.13-.58-1.4-.79-1.91-.21-.51-.43-.44-.58-.44h-.5c-.17 0-.44.07-.67.33-.24.26-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.3 3.78.6.26 1.07.41 1.44.52.61.19 1.17.16 1.61.1.49-.07 1.53-.63 1.74-1.23.21-.6.21-1.12.15-1.23-.06-.11-.24-.17-.5-.3Z"/>
            <path fill="#fff" d="M16.02 3C8.85 3 3.02 8.72 3.02 15.77c0 2.27.61 4.48 1.78 6.41L3 29l6.98-1.77c1.86 1.02 3.97 1.55 6.12 1.55 7.17 0 13-5.72 13-12.77C29.02 8.72 23.19 3 16.02 3Zm0 23.37c-2.01 0-3.97-.56-5.67-1.63l-.41-.25-4.15 1.05 1.11-3.99-.27-.4a10.9 10.9 0 0 1-1.78-5.98c0-6.06 5.05-10.99 11.17-10.99 6.12 0 11.17 4.93 11.17 10.99 0 6.06-5.05 10.99-11.17 10.99Z"/>
        </svg>
    </span>
</a>
