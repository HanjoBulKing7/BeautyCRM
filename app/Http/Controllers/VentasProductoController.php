<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VentasProducto;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoVentaProducto;
use Illuminate\Support\Facades\DB;

class VentasProductoController extends Controller
{
    /**
     * Listado de ventas.
     */
    public function index()
    {
        $ventas = VentasProducto::with(['cliente', 'user'])
            ->orderByDesc('created_at')
            ->get();
            
        return view('admin.productoventa.index', compact('ventas'));
    }

    /**
     * Formulario para nueva venta.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();
        $selectedProducts = [];
        
        return view('admin.productoventa.create', compact('clientes', 'productos', 'selectedProducts'));
    }

    /**
     * Guardar nueva venta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'metodo_pago' => 'required|string',
            'productos' => 'required|array',
        ]);

        // Filtrar productos que tengan cantidad mayor a 0
        $productosSeleccionados = collect($request->input('productos'))->filter(function ($item) {
            return isset($item['cantidad']) && $item['cantidad'] > 0;
        });

        if ($productosSeleccionados->isEmpty()) {
            return back()->withErrors(['productos' => 'Debes seleccionar al menos un producto con cantidad mayor a 0.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Calcular total
            $total = $productosSeleccionados->reduce(function ($carry, $item) {
                return $carry + ($item['cantidad'] * ($item['precio'] ?? 0));
            }, 0);

            // Crear la cabecera de la venta
            $venta = VentasProducto::create([
                'cliente_id' => $request->input('cliente_id'),
                'user_id' => auth()->id(),
                'total' => $total,
                'metodo_pago' => $request->input('metodo_pago'),
            ]);

            // Crear los detalles
            foreach ($productosSeleccionados as $productoId => $item) {
                ProductoVentaProducto::create([
                    'venta_producto_id' => $venta->id,
                    'producto_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.productoventa.index')->with('success', 'Venta registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la venta: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        // Cargamos la venta con sus productos asociados
        $venta = VentasProducto::with('productosVendidos')->findOrFail($id);
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        // Mapeamos los productos ya guardados para que el formulario los reconozca
        $selectedProducts = $venta->productosVendidos->mapWithKeys(function ($item) {
            return [
                $item->producto_id => [
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                ],
            ];
        })->toArray();

        return view('admin.productoventa.edit', compact('venta', 'clientes', 'productos', 'selectedProducts'));
    }

    /**
     * Actualizar la venta.
     */
    public function update(Request $request, $id)
    {
        $venta = VentasProducto::findOrFail($id);

        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'metodo_pago' => 'required|string',
            'productos' => 'required|array',
        ]);

        $productosSeleccionados = collect($request->input('productos'))->filter(function ($item) {
            return isset($item['cantidad']) && $item['cantidad'] > 0;
        });

        if ($productosSeleccionados->isEmpty()) {
            return back()->withErrors(['productos' => 'Debes seleccionar al menos un producto.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Calcular nuevo total
            $total = $productosSeleccionados->reduce(function ($carry, $item) {
                return $carry + ($item['cantidad'] * ($item['precio'] ?? 0));
            }, 0);

            // Actualizar cabecera
            $venta->update([
                'cliente_id' => $request->input('cliente_id'),
                'total' => $total,
                'metodo_pago' => $request->input('metodo_pago'),
            ]);

            // Eliminar detalles anteriores y crear los nuevos
            $venta->productosVendidos()->delete();

            foreach ($productosSeleccionados as $productoId => $item) {
                ProductoVentaProducto::create([
                    'venta_producto_id' => $venta->id,
                    'producto_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.productoventa.index')->with('success', 'Venta actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la venta: ' . $e->getMessage())->withInput();
        }
    }


/**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Cargamos la venta con todas sus relaciones necesarias:
        // - cliente: para saber a quién se le vendió
        // - user: para saber qué empleado realizó la venta
        // - productosVendidos.producto: para traer el nombre e imagen de los productos dentro del detalle
        $venta = VentasProducto::with(['cliente', 'user', 'productosVendidos.producto'])
            ->findOrFail($id);

        return view('admin.productoventa.show', compact('venta'));
    }

    /**
     * Eliminar la venta.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $venta = VentasProducto::findOrFail($id);
            
            // Eliminar detalles primero (si no tienes onCascadeDelete en la BD)
            $venta->productosVendidos()->delete();
            
            // Eliminar cabecera
            $venta->delete();

            DB::commit();
            return redirect()->route('admin.productoventa.index')->with('success', 'Venta eliminada con éxito.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo eliminar la venta.');
        }
    }
}