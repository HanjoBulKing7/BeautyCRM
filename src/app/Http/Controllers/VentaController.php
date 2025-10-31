<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\InventarioMovimiento;
use App\Models\Existencia;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{

    private function getLogoPath($nombreSucursal)
    {
        $sucursalLower = strtolower($nombreSucursal);
        
        $mappings = [
            'fresh' => 'logo_freshboys.png',
            'hype' => 'logo_freshhype.png',
            'society' => 'logo_society.png',
            'pilar' => 'logo_freshhype2.png',
        ];

        foreach($mappings as $key => $logo){
            if (str_contains($sucursalLower, $key)) {
                return storage_path("../public/logos/{$logo}");
            }
        }

        return storage_path("../public/logos/logo_freshboys.png");
    }

    private function getQrPath($nombreSucursal)
    {
        $sucursalLower = strtolower($nombreSucursal);
        
        if (!str_contains($sucursalLower, 'sbhype')) {//Cualquier otra sucursal 
            return storage_path('../public/logos/QR_sbhype.jpeg');
        } else {//La sucursal es SBHype
            return storage_path('../public/qr/QR.jpeg');
        }
    }

    public function ticket(Venta $venta)
    {
        // Verificar permisos
        if ($venta->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()->route('ventas.index')
                ->with('error', 'No tienes permisos para ver este ticket.');
        }

        // ✅ CARGAR TODAS LAS RELACIONES NECESARIAS
        $venta->load([
            'detalles.producto', 
            'pagos', 
            'sucursal', 
            'usuario', 
            'cliente'
        ]);
        
        // Obtener datos dinámicos de la sucursal
        $sucursal = $venta->sucursal;
        
        $data = [
            'venta' => $venta,
            'sucursal' => $sucursal,
            'config' => [
                'logo' => $this->getLogoPath($sucursal->nombre),
                'telefono' => $sucursal->telefono ?? 'N/A',
                'direccion' => $sucursal->direccion ?? 'Dirección no especificada',
                'qr_whatsapp' => $this->getQrPath($sucursal->nombre)
            ]
        ];

        // ✅ USAR RUTA ABSOLUTA CORRECTA
        $pdf = Pdf::loadView('ventas.ticket-pdf', $data);
        
        // ✅ CONFIGURAR TAMAÑO DE PAPEL PARA TICKET (72mm = 204 puntos)
        $pdf->setPaper([0, 0, 300, 1000], 'portrait');
        
        // ✅ MOSTRAR PDF EN EL NAVEGADOR
        return $pdf->stream('ticket-venta-' . $venta->id . '.pdf');
    }       

        public function index(Request $request)
        {
            $user = auth()->user();

            // Filtro por fecha específica (del input)
            $fecha = $request->query('fecha');

            $ventas = \App\Models\Venta::with(['cliente', 'usuario', 'sucursal', 'pagos'])
                // Solo las ventas de la sucursal del usuario logueado
                ->where('sucursal_id', $user->sucursal_id)
                // Filtrar por fecha si se selecciona una
                ->when($fecha, fn($q) => $q->whereDate('fecha', $fecha))
                // 🔽 Orden descendente por ID (más recientes primero)
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();

            // Lista de clientes (opcional)
            $clientes = \App\Models\Cliente::orderBy('nombre')->get();

            return view('ventas.index', compact('ventas', 'fecha', 'clientes'));
        }



    public function create()
    {
        $venta = new Venta();
        $clientes = Cliente::orderBy('nombre')->get();
        
        // Obtener solo productos que tengan existencia en la sucursal del usuario
        $sucursal_id = Auth::user()->sucursal_id;
        $productos = Producto::whereHas('existencias', function($query) use ($sucursal_id) {
            $query->where('sucursal_id', $sucursal_id)
                ->where('stock_actual', '>', 0);
        })->orderBy('nombre')->get();
        
        return view('ventas.create', compact('venta', 'clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->request->remove('usuario_id');
        
        // Validar datos principales de la venta
        $data = $request->validate([
            'cliente_id' => 'required|integer|min:0',
            'fecha' => 'required|date',
            'subtotal' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'impuestos' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,multipago',
            'referencia_pago' => 'nullable|string|max:100',
            'destinatario_transferencia' => 'nullable|in:Karen,Ethan',
            'notas' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.total_linea' => 'required|numeric|min:0',
            'pagos' => 'nullable|array',
            'pagos.*.metodo' => 'required|in:efectivo,transferencia,tarjeta',
            'pagos.*.monto' => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:100',
            'pagos.*.destinatario' => 'nullable|in:Karen,Ethan',
        ]);

        // Validar destinatario si el método es transferencia
        if ($data['metodo_pago'] === 'transferencia' && empty($data['destinatario_transferencia'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Debe seleccionar un destinatario para transferencias.');
        }

        // Validar pagos múltiples
        if ($data['metodo_pago'] === 'multipago') {
            if (empty($data['pagos']) || count($data['pagos']) < 2) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Para pago múltiple debe agregar al menos 2 métodos de pago.');
            }

            $totalPagos = array_sum(array_column($data['pagos'], 'monto'));
            if (abs($totalPagos - $data['total']) > 0.01) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'La suma de los pagos ($' . number_format($totalPagos, 2) . ') debe ser igual al total de la venta ($' . number_format($data['total'], 2) . ').');
            }
        }

        // Obtener la sucursal del usuario autenticado
        $sucursal_id = Auth::user()->sucursal_id;
        $usuario_id = Auth::user()->id;

        // Validar que el cliente exista si no es anónimo
        if ($data['cliente_id'] != 0) {
            $cliente = Cliente::find($data['cliente_id']);
            if (!$cliente) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'El cliente seleccionado no existe.');
            }
        }

        // Iniciar transacción para asegurar la integridad de los datos
        DB::beginTransaction();

        try {
            // Crear la venta - usar null si es cliente anónimo (0)
            $venta = Venta::create([
                'sucursal_id' => $sucursal_id,
                'usuario_id' => $usuario_id,
                'cliente_id' => $data['cliente_id'] == 0 ? null : $data['cliente_id'],
                'fecha' => $data['fecha'],
                'subtotal' => $data['subtotal'],
                'descuento' => $data['descuento'] ?? 0,
                'impuestos' => $data['impuestos'] ?? 0,
                'total' => $data['total'],
                'metodo_pago' => $data['metodo_pago'],
                'referencia_pago' => $data['referencia_pago'] ?? null,
                'notas' => $data['notas'] ?? null,
            ]);

            // CREAR PAGOS - NUEVA LÓGICA
            if ($data['metodo_pago'] === 'multipago') {
                // Crear múltiples pagos
                foreach ($data['pagos'] as $pagoData) {
                    $pago = [
                        'venta_id' => $venta->id,
                        'metodo_pago' => $pagoData['metodo'],
                        'monto' => $pagoData['monto'],
                        'referencia_pago' => $pagoData['referencia'] ?? null,
                        'estado' => 'completado',
                        'fecha_pago' => now(),
                    ];

                    // Agregar destinatario solo para transferencias
                    if ($pagoData['metodo'] === 'transferencia' && !empty($pagoData['destinatario'])) {
                        $pago['destinatario_transferencia'] = $pagoData['destinatario'];
                    }

                    Pago::create($pago);
                }
            } else {
                // Pago único (comportamiento tradicional)
                $pagoData = [
                    'venta_id' => $venta->id,
                    'metodo_pago' => $data['metodo_pago'],
                    'monto' => $data['total'],
                    'referencia_pago' => $data['referencia_pago'] ?? null,
                    'estado' => 'completado',
                    'fecha_pago' => now(),
                ];

                // Solo agregar destinatario si el método es transferencia
                if ($data['metodo_pago'] === 'transferencia') {
                    $pagoData['destinatario_transferencia'] = $data['destinatario_transferencia'];
                }

                Pago::create($pagoData);
            }
            
            // Crear los detalles de la venta y actualizar existencias
            foreach ($data['productos'] as $index => $productoData) {
                $producto_id = $productoData['producto_id'];
                $cantidad = $productoData['cantidad'];

                // Verificar si hay suficiente stock
                $existencia = Existencia::where('producto_id', $producto_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->first();

                if (!$existencia) {
                    throw new \Exception("No existe inventario para el producto ID: {$producto_id}");
                }

                if ($existencia->stock_actual < $cantidad) {
                    throw new \Exception("Stock insuficiente para el producto ID: {$producto_id}. Stock actual: {$existencia->stock_actual}, solicitado: {$cantidad}");
                }

                // Crear detalle de venta
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $productoData['precio_unitario'],
                    'total_linea' => $productoData['total_linea'],
                ]);

                // Actualizar existencia (descontar stock)
                $existencia->decrement('stock_actual', $cantidad);

                // Registrar movimiento de inventario (tipo 'venta')
                InventarioMovimiento::create([
                    'producto_id' => $producto_id,
                    'sucursal_id' => $sucursal_id,
                    'usuario_id' => $usuario_id,
                    'tipo' => 'venta',
                    'cantidad' => $cantidad,
                    'motivo' => 'Venta realizada - ID: ' . $venta->id,
                    'referencia_tipo' => 'venta',
                    'referencia_id' => $venta->id
                ]);
            }

            DB::commit();

            // Si la solicitud es AJAX/JSON, regresamos la URL del ticket
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('ventas.ticket', $venta->id),
                ]);
            }


        return redirect()
            ->route('ventas.ticket', $venta)  // Redirige al ticket PDF
            ->with('success', 'Venta creada correctamente. Stock actualizado y pago registrado.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'sucursal', 'detalles.producto', 'pagos']);
        
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        // Verificar que la venta pertenezca a la sucursal del usuario
        if ($venta->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('ventas.index')
                ->with('error', 'No tienes permisos para editar esta venta.');
        }

        $venta->load('detalles.producto', 'pagos');
        $clientes = Cliente::orderBy('nombre')->get();
        
        // Obtener productos con existencia + los productos ya incluidos en la venta (aunque ya no tengan stock)
        $sucursal_id = Auth::user()->sucursal_id;
        $productosIdsEnVenta = $venta->detalles->pluck('producto_id')->toArray();
        
        $productos = Producto::where(function($query) use ($sucursal_id, $productosIdsEnVenta) {
            $query->whereHas('existencias', function($q) use ($sucursal_id) {
                $q->where('sucursal_id', $sucursal_id)
                ->where('stock_actual', '>', 0);
            })->orWhereIn('id', $productosIdsEnVenta);
        })->orderBy('nombre')->get();
        
        return view('ventas.edit', compact('venta', 'clientes', 'productos'));
    }

    public function update(Request $request, Venta $venta)
    {
        // Verificar que la venta pertenezca a la sucursal del usuario
        if ($venta->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('ventas.index')
                ->with('error', 'No tienes permisos para editar esta venta.');
        }

        // Validar datos
        $data = $request->validate([
            'cliente_id' => 'required|integer|min:0',
            'fecha' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'impuestos' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,multipago',
            'referencia_pago' => 'nullable|string|max:100',
            'destinatario_transferencia' => 'nullable|in:Karen,Ethan',
            'notas' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'nullable|exists:venta_detalles,id',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.total_linea' => 'required|numeric|min:0',
            'pagos' => 'nullable|array',
            'pagos.*.metodo' => 'required|in:efectivo,transferencia,tarjeta',
            'pagos.*.monto' => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:100',
            'pagos.*.destinatario' => 'nullable|in:Karen,Ethan',
        ]);

        // Validaciones para multipago
        if ($data['metodo_pago'] === 'multipago') {
            if (empty($data['pagos']) || count($data['pagos']) < 2) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Para pago múltiple debe agregar al menos 2 métodos de pago.');
            }

            $totalPagos = array_sum(array_column($data['pagos'], 'monto'));
            if (abs($totalPagos - $data['total']) > 0.01) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'La suma de los pagos debe ser igual al total de la venta.');
            }
        }

        $sucursal_id = Auth::user()->sucursal_id;
        $usuario_id = Auth::id();

        DB::beginTransaction();

        try {
            // Primero, revertir el stock de los detalles originales
            $detallesOriginales = VentaDetalle::where('venta_id', $venta->id)->get();
            
            foreach ($detallesOriginales as $detalle) {
                $existencia = Existencia::where('producto_id', $detalle->producto_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->first();

                if ($existencia) {
                    // Revertir el stock (aumentar)
                    $existencia->increment('stock_actual', $detalle->cantidad);

                    // Registrar movimiento de reversión
                    InventarioMovimiento::create([
                        'producto_id' => $detalle->producto_id,
                        'sucursal_id' => $sucursal_id,
                        'usuario_id' => $usuario_id,
                        'tipo' => 'entrada',
                        'cantidad' => $detalle->cantidad,
                        'motivo' => 'Reversión por edición de venta ID: ' . $venta->id,
                        'referencia_tipo' => 'venta',
                        'referencia_id' => $venta->id
                    ]);
                }
            }

            // Validar que el cliente exista si no es anónimo
            if ($data['cliente_id'] != 0) {
                $cliente = Cliente::find($data['cliente_id']);
                if (!$cliente) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'El cliente seleccionado no existe.');
                }
            }

            // Actualizar la venta
            $venta->update([
                'cliente_id' => $data['cliente_id'] == 0 ? null : $data['cliente_id'],
                'fecha' => $data['fecha'],
                'subtotal' => $data['subtotal'],
                'descuento' => $data['descuento'],
                'impuestos' => $data['impuestos'],
                'total' => $data['total'],
                'metodo_pago' => $data['metodo_pago'],
                'referencia_pago' => $data['referencia_pago'] ?? null,
                'notas' => $data['notas'] ?? null,
            ]);

            // Eliminar pagos existentes y crear nuevos
            Pago::where('venta_id', $venta->id)->delete();

            // Crear nuevos pagos según el método
            if ($data['metodo_pago'] === 'multipago') {
                foreach ($data['pagos'] as $pagoData) {
                    $pago = [
                        'venta_id' => $venta->id,
                        'metodo_pago' => $pagoData['metodo'],
                        'monto' => $pagoData['monto'],
                        'referencia_pago' => $pagoData['referencia'] ?? null,
                        'estado' => 'completado',
                        'fecha_pago' => now(),
                    ];

                    if ($pagoData['metodo'] === 'transferencia' && !empty($pagoData['destinatario'])) {
                        $pago['destinatario_transferencia'] = $pagoData['destinatario'];
                    }

                    Pago::create($pago);
                }
            } else {
                // Pago único
                $pagoData = [
                    'venta_id' => $venta->id,
                    'metodo_pago' => $data['metodo_pago'],
                    'monto' => $data['total'],
                    'referencia_pago' => $data['referencia_pago'] ?? null,
                    'estado' => 'completado',
                    'fecha_pago' => now(),
                ];

                if ($data['metodo_pago'] === 'transferencia') {
                    $pagoData['destinatario_transferencia'] = $data['destinatario_transferencia'];
                }

                Pago::create($pagoData);
            }

            // Procesar los nuevos detalles
            $detallesIds = [];
            
            foreach ($data['productos'] as $productoData) {
                $producto_id = $productoData['producto_id'];
                $cantidad = $productoData['cantidad'];

                // Verificar stock para los nuevos productos
                $existencia = Existencia::where('producto_id', $producto_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->first();

                if (!$existencia) {
                    throw new \Exception("No existe inventario para el producto ID: {$producto_id}");
                }

                if ($existencia->stock_actual < $cantidad) {
                    throw new \Exception("Stock insuficiente para el producto ID: {$producto_id}. Stock actual: {$existencia->stock_actual}, solicitado: {$cantidad}");
                }

                if (isset($productoData['id'])) {
                    // Actualizar detalle existente
                    $detalle = VentaDetalle::find($productoData['id']);
                    if ($detalle && $detalle->venta_id === $venta->id) {
                        $detalle->update([
                            'producto_id' => $producto_id,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $productoData['precio_unitario'],
                            'total_linea' => $productoData['total_linea'],
                        ]);
                        $detallesIds[] = $detalle->id;
                    }
                } else {
                    // Crear nuevo detalle
                    $detalle = VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto_id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $productoData['precio_unitario'],
                        'total_linea' => $productoData['total_linea'],
                    ]);
                    $detallesIds[] = $detalle->id;
                }

                // Descontar stock
                $existencia->decrement('stock_actual', $cantidad);

                // Registrar movimiento de inventario (tipo 'venta')
                InventarioMovimiento::create([
                    'producto_id' => $producto_id,
                    'sucursal_id' => $sucursal_id,
                    'usuario_id' => $usuario_id,
                    'tipo' => 'venta',
                    'cantidad' => $cantidad,
                    'motivo' => 'Venta actualizada - ID: ' . $venta->id,
                    'referencia_tipo' => 'venta',
                    'referencia_id' => $venta->id
                ]);
            }

            // Eliminar detalles que ya no están en la solicitud
            VentaDetalle::where('venta_id', $venta->id)
                ->whereNotIn('id', $detallesIds)
                ->delete();

            DB::commit();
            

            return redirect()
                ->route('ventas.index')
                ->with('ok', 'Venta actualizada correctamente. Stock ajustado y pago actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    public function destroy(Venta $venta)
    {
        // Verificar que la venta pertenezca a la sucursal del usuario
        if ($venta->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('ventas.index')
                ->with('error', 'No tienes permisos para eliminar esta venta.');
        }

        $sucursal_id = Auth::user()->sucursal_id;
        $usuario_id = Auth::id();

        DB::beginTransaction();

        try {
            // Revertir el stock de todos los productos de la venta
            $detalles = VentaDetalle::where('venta_id', $venta->id)->get();
            
            foreach ($detalles as $detalle) {
                $existencia = Existencia::where('producto_id', $detalle->producto_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->first();

                if ($existencia) {
                    // Revertir el stock (aumentar)
                    $existencia->increment('stock_actual', $detalle->cantidad);

                    // Registrar movimiento de reversión
                    InventarioMovimiento::create([
                        'producto_id' => $detalle->producto_id,
                        'sucursal_id' => $sucursal_id,
                        'usuario_id' => $usuario_id,
                        'tipo' => 'entrada',
                        'cantidad' => $detalle->cantidad,
                        'motivo' => 'Reversión por eliminación de venta ID: ' . $venta->id,
                        'referencia_tipo' => 'venta',
                        'referencia_id' => $venta->id
                    ]);
                }
            }

            // Eliminar el pago asociado
            Pago::where('venta_id', $venta->id)->delete();

            // Eliminar detalles
            VentaDetalle::where('venta_id', $venta->id)->delete();
            
            // Eliminar la venta
            $venta->delete();

            DB::commit();

            return redirect()
                ->route('ventas.index')
                ->with('ok', 'Venta eliminada correctamente. Stock revertido y pago eliminado.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('ventas.index')
                ->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }
}