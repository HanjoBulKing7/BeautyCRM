@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Detalle de Venta #{{ $venta->id }}</h1>
            <p class="text-sm text-gray-500">{{ $venta->created_at?->format('d M, Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.productoventa.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold">
            <i class="fas fa-arrow-left text-sm"></i>
            Volver
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-400">Cliente</div>
                <div class="font-semibold">{{ $venta->cliente->nombre ?? 'Venta Mostrador' }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-400">Atendió</div>
                <div class="font-semibold">{{ $venta->user->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-400">Método de Pago</div>
                <div class="font-semibold">{{ $venta->metodo_pago }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4">Productos vendidos</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-3 text-xs uppercase tracking-widest text-gray-400">Producto</th>
                        <th class="pb-3 text-xs uppercase tracking-widest text-gray-400">Cantidad</th>
                        <th class="pb-3 text-xs uppercase tracking-widest text-gray-400">Precio Unit.</th>
                        <th class="pb-3 text-xs uppercase tracking-widest text-gray-400">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($venta->productosVendidos as $item)
                        <tr>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->producto->imagen_url ?? asset('images/no-image.png') }}" alt="{{ $item->producto->nombre ?? 'Producto' }}" class="w-10 h-10 rounded-lg object-cover">
                                    <span class="font-semibold text-gray-700">{{ $item->producto->nombre ?? 'Producto' }}</span>
                                </div>
                            </td>
                            <td class="py-3">{{ $item->cantidad }}</td>
                            <td class="py-3">${{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="py-3">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td colspan="3" class="pt-4 text-right font-bold text-gray-700">Total</td>
                        <td class="pt-4 font-bold text-gray-900">${{ number_format($venta->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
