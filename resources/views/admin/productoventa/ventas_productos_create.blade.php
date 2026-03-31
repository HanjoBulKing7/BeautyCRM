@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nueva Venta de Productos</h1>
    <form action="{{ route('admin.productoventa.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-control">
                <option value="">Selecciona un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Productos</label>
            <div id="productos-lista">
                @foreach($productos as $producto)
                <div class="card mb-2 p-2 d-flex flex-row align-items-center">
                    <img src="{{ $producto->imagen_url ?? asset('images/no-image.png') }}" alt="{{ $producto->nombre }}" style="width: 60px; height: 60px; object-fit: cover; margin-right: 10px;">
                    <div class="flex-grow-1">
                        <strong>{{ $producto->nombre }}</strong><br>
                        <span>${{ number_format($producto->precio, 2) }}</span>
                    </div>
                    <input type="number" name="productos[{{ $producto->id }}][cantidad]" min="0" value="0" class="form-control mx-2" style="width: 80px;">
                    <input type="number" name="productos[{{ $producto->id }}][precio]" min="0" step="0.01" value="{{ $producto->precio }}" class="form-control mx-2" style="width: 100px;">
                </div>
                @endforeach
            </div>
        </div>
        <div class="mb-3">
            <label for="metodo_pago" class="form-label">Método de Pago</label>
            <select name="metodo_pago" id="metodo_pago" class="form-control">
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Transferencia">Transferencia</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Venta</button>
    </form>
</div>
@endsection
