@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border"
                    style="
                        background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75));
                        border-color: rgba(201,162,74,.22);
                        box-shadow: 0 10px 22px rgba(201,162,74,.12);
                        color: rgba(17,24,39,.90);
                    "
                >
                    <i class="fas fa-user"></i>
                </span>
                {{ $cliente->nombre ?? 'Cliente' }}
            </h1>
            <p class="text-gray-600 mt-1">
                Visualiza la información del cliente
                @if(!empty($cliente->id))
                    <span class="text-gray-400">• ID: #{{ $cliente->id }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            @php
            $bbBackBtn = "inline-flex items-center gap-2 px-4 py-2 rounded-xl
                        border border-[rgba(201,162,74,.35)]
                        bg-[rgba(255,255,255,.70)] hover:bg-white
                        text-gray-800 font-semibold shadow-sm hover:shadow transition";
            @endphp

            @if(request('modal'))
            <button type="button"
                    onclick="document.getElementById('bb-modal-close')?.click()"
                    class="{{ $bbBackBtn }}">
                <i class="fas fa-arrow-left" style="color: rgba(201,162,74,.95)"></i>
                Volver
            </button>
            @else
            <a href="{{ route('admin.clientes.index') }}" class="{{ $bbBackBtn }}">
                <i class="fas fa-arrow-left" style="color: rgba(201,162,74,.95)"></i>
                Volver
            </a>
            @endif

            <a href="{{ route('admin.clientes.edit', $cliente->id) }}"
            data-bb-open="modal"
            data-bb-title="Editar Cliente"
            data-bb-url="{{ route('admin.clientes.edit', $cliente->id) }}?modal=1"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition"
            style="
                    background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                    border: 1px solid rgba(201,162,74,.35);
                    box-shadow: 0 10px 22px rgba(201,162,74,.18);
                    color: #111827;
            "
            onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
            onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
            >
            <i class="fas fa-pen" style="color: rgba(17,24,39,.90)"></i>
            Editar
            </a>
        </div>
    </div>

    <!-- Card principal -->
    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">

        <!-- Top bar glass dorado -->
        <div
            class="px-6 py-4"
            style="
                background: linear-gradient(135deg, rgba(201,162,74,.14), rgba(255,255,255,.78));
                border-bottom: 1px solid rgba(201,162,74,.18);
            "
        >
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-id-card" style="color: rgba(201,162,74,.92)"></i>
                Detalles del cliente
            </h2>
            <p class="text-sm text-gray-600 mt-1">Información de contacto y datos generales</p>
        </div>

        <div class="p-6 space-y-6">

            <!-- Grid info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Email -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        <i class="fas fa-envelope mr-2" style="color: rgba(201,162,74,.92)"></i>
                        Email
                    </p>
                    <p class="text-gray-800 font-medium break-all">
                        {{ $cliente->email ?? '—' }}
                    </p>
                </div>

                <!-- Teléfono -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        <i class="fas fa-phone mr-2" style="color: rgba(201,162,74,.92)"></i>
                        Teléfono
                    </p>
                    <p class="text-gray-800 font-medium">
                        {{ $cliente->telefono ?? '—' }}
                    </p>
                </div>

                <!-- Dirección -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        <i class="fas fa-map-marker-alt mr-2" style="color: rgba(201,162,74,.92)"></i>
                        Dirección
                    </p>
                    <p class="text-gray-800 font-medium whitespace-pre-line">
                        {{ $cliente->direccion ?? '—' }}
                    </p>
                </div>

            </div>

            <!-- Meta (opcional, solo si existen timestamps) -->
            @if(!empty($cliente->created_at) || !empty($cliente->updated_at))
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    <i class="fas fa-clock mr-2" style="color: rgba(201,162,74,.92)"></i>
                    Actividad
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Registrado</p>
                        <p class="text-gray-800 font-medium">
                            {{ $cliente->created_at ? $cliente->created_at->format('d/m/Y H:i') : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Última actualización</p>
                        <p class="text-gray-800 font-medium">
                            {{ $cliente->updated_at ? $cliente->updated_at->format('d/m/Y H:i') : '—' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Acciones abajo -->
                <a href="{{ route('admin.clientes.edit', $cliente->id) }}"
                data-bb-open="modal"
                data-bb-title="Editar Cliente"
                data-bb-url="{{ route('admin.clientes.edit', $cliente->id) }}?modal=1"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold transition focus:outline-none"
                style="
                        background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                        border: 1px solid rgba(201,162,74,.35);
                        box-shadow: 0 10px 22px rgba(201,162,74,.18);
                        color: #111827;
                "
                onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
                onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
                >
                <i class="fas fa-pen" style="color: rgba(17,24,39,.90)"></i>
                Editar Cliente
                </a>


                @php
                $bbListBtn = "inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg
                            bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                            shadow-sm hover:shadow transition";
                @endphp

                @if(request('modal'))
                <button type="button"
                        onclick="document.getElementById('bb-modal-close')?.click()"
                        class="{{ $bbListBtn }}">
                    <i class="fas fa-list" style="color: rgba(17,24,39,.70)"></i>
                    Volver al listado
                </button>
                @else
                <a href="{{ route('admin.clientes.index') }}" class="{{ $bbListBtn }}">
                    <i class="fas fa-list" style="color: rgba(17,24,39,.70)"></i>
                    Volver al listado
                </a>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection