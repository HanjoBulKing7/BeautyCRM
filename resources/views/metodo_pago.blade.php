@extends('layouts.website')

@section('content')
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')
<div class="min-h-[70vh] flex flex-col items-center justify-start pt-16">

    <div class="w-full max-w-4xl text-center">

        <h1 class="text-2xl font-semibold text-gray-800">
            Selecciona método de pago
        </h1>

        <p class="text-gray-500 mt-1 mb-10">
            Cita #{{ $cita->id_cita }}
        </p>

        <form method="GET" action="{{ route('checkout') }}">
            <input type="hidden" name="id_cita" value="{{ $cita->id_cita }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center">

                {{-- TARJETA --}}
                <label class="payment-card mx-auto">
                    <input type="radio" name="metodo" value="tarjeta" required>
                    <div class="card-content">
                        <img src="https://img.icons8.com/color/96/visa.png" class="h-14 mx-auto mb-4">
                        <h3 class="font-medium text-lg">Tarjeta</h3>
                        <p class="text-sm text-gray-500">Crédito o Débito</p>
                    </div>
                </label>

                

            </div>

            <div class="mt-12">
                <button class="bg-[rgba(201,162,74,.95)] hover:opacity-90 text-white px-8 py-3 rounded-lg shadow">
                    Continuar
                </button>
            </div>

        </form>

    </div>
</div>
<script>
document.querySelectorAll('input[name="metodo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>


<style>
.payment-card {
    cursor: pointer;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    transition: all .25s ease;
    background: white;
}

.payment-card:hover {
    border-color: rgba(201,162,74,.6);
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

.payment-card input {
    display: none;
}

.payment-card input:checked + .card-content {
    border: 2px solid rgba(201,162,74,.9);
    border-radius: 16px;
    padding: 22px;
}
</style>

@include('beauty.partials.footer')
@endsection
