@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        {{-- Badge estado --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                Pago realizado
            </h2>

            <span class="px-4 py-1 text-sm rounded-full bg-green-100 text-green-700 font-medium">
                Confirmado
            </span>
        </div>

        {{-- Icono éxito --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="3"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <p class="text-center text-gray-600 mb-8">
            Tu anticipo fue registrado correctamente.  
            Hemos confirmado tu cita y ya está asegurada.
        </p>

        {{-- Detalles de la cita --}}
        <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-100">

            <div class="grid grid-cols-2 gap-y-4 text-sm">

                <div class="text-gray-500">Número de cita</div>
                <div class="font-medium text-gray-800 text-right">
                    #{{ $cita->id }}
                </div>

                <div class="text-gray-500">Fecha</div>
                <div class="font-medium text-gray-800 text-right">
                    {{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}
                </div>

                <div class="text-gray-500">Hora</div>
                <div class="font-medium text-gray-800 text-right">
                    {{ $cita->hora_cita }}
                </div>

                <div class="text-gray-500">Anticipo pagado</div>
                <div class="font-semibold text-green-700 text-right">
                    ${{ number_format($total, 2) }} MXN
                </div>

            </div>
        </div>

        {{-- Información adicional --}}
        <div class="text-center text-sm text-gray-500 mb-8">
            El resto del pago se liquida el día del servicio en sucursal.
        </div>

        {{-- Botones --}}
        <div class="flex justify-center gap-4">

            <a href="{{ route('admin.dashboard') }}"
               class="bb-btn primary px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                Volver al inicio
            </a>

            <a href="{{ route('agendarcita.create') }}"
               class="px-6 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 transition shadow-sm">
                Agendar otra cita
            </a>

        </div>

    </div>
</div>
@endsection