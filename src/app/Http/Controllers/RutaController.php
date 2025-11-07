<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\RutaDetalle;
use App\Models\Producto;
use App\Models\User;
use App\Models\Existencia;
use App\Models\InventarioMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RutaController extends Controller
{
    /**
     * 📋 Mostrar listado general de rutas
     */
    public function index()
    {
        $rutas = Ruta::with(['empleado', 'detalles.producto'])
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        // ✅ Mantener cierre automático pero sin cambiar fecha
        foreach ($rutas as $ruta) {
            if ($ruta->fecha < now()->toDateString()) {
                $this->cerrarDiaAutomatico($ruta);
            }
        }

        return view('rutas.index', compact('rutas'));
    }

    /**
     * 🧾 Formulario para crear una nueva ruta
     */
    public function create()
    {
        $empleados = User::where('activo', 1)
            ->whereIn('rol', ['vendedor', 'gerente'])
            ->orderBy('nombre')
            ->get();

        // Generar nombre automático: Ruta 1, Ruta 2, etc.
        $contador = Ruta::count() + 1;
        $nombreSugerido = "Ruta {$contador}";

        return view('rutas.create', compact('empleados', 'nombreSugerido'));
    }

    /**
     * 💾 Guardar nueva ruta
     */
    public function store(Request $request)
    {
        \Log::info('=== CREANDO NUEVA RUTA ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            $data = $request->validate([
                'nombre' => 'nullable|string|max:255',
                'fecha' => 'required|date',
                'empleado_id' => 'required|exists:users,id',
            ]);

            \Log::info('Datos validados:', $data);

            // Si no se define nombre, se genera automáticamente
            if (empty($data['nombre'])) {
                $data['nombre'] = 'Ruta ' . (Ruta::count() + 1);
            }

            $ruta = Ruta::create($data);

            \Log::info('Ruta creada exitosamente:', $ruta->toArray());

            return redirect()
                ->route('rutas.show', $ruta)
                ->with('success', 'Ruta creada correctamente. Ahora puedes agregar productos.');

        } catch (\Exception $e) {
            \Log::error('Error al crear ruta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()
                ->with('error', 'Error al crear la ruta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 👁️ Mostrar detalle de una ruta (productos, totales, etc.)
     */
    public function show(Ruta $ruta)
    {
        \Log::info('Mostrando ruta:', ['ruta_id' => $ruta->id]);

        // ✅ Mantener cierre automático pero sin cambiar fecha
        if ($ruta->fecha < now()->toDateString()) {
            $this->cerrarDiaAutomatico($ruta);
        }

        $ruta->load(['empleado', 'detalles.producto']);
        $productos = Producto::where('activo', 1)
            ->with(['existencias' => function($query) {
                $query->where('sucursal_id', Auth::user()->sucursal_id);
            }])
            ->orderBy('nombre')
            ->get();

        // Debug: verificar detalles cargados
        \Log::info('Detalles de la ruta:', [
            'count' => $ruta->detalles->count(),
            'detalles' => $ruta->detalles->pluck('id')->toArray()
        ]);

        // Totales globales
        $totalVentas = $ruta->detalles->sum('total');
        $totalUnidades = $ruta->detalles->sum('ventas');

        return view('rutas.show', compact('ruta', 'productos', 'totalVentas', 'totalUnidades'));
    }

    /**
     * ✏️ Editar una ruta (formulario básico)
     */
    public function edit(Ruta $ruta)
    {
        $empleados = User::where('activo', 1)
            ->whereIn('rol', ['vendedor', 'gerente'])
            ->orderBy('nombre')
            ->get();

        return view('rutas.edit', compact('ruta', 'empleados'));
    }

    /**
     * 🔄 Actualizar una ruta (datos básicos)
     */
    public function update(Request $request, Ruta $ruta)
    {
        \Log::info('=== ACTUALIZANDO RUTA BÁSICA ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            $data = $request->validate([
                'nombre' => 'nullable|string|max:255',
                'fecha' => 'required|date',
                'empleado_id' => 'required|exists:users,id',
            ]);

            \Log::info('Datos validados:', $data);

            // Si no se define nombre, se genera automáticamente
            if (empty($data['nombre'])) {
                $data['nombre'] = 'Ruta ' . $ruta->id;
            }

            $ruta->update($data);

            \Log::info('Ruta actualizada exitosamente:', $ruta->toArray());

            return redirect()
                ->route('rutas.show', $ruta)
                ->with('success', 'Ruta actualizada correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar ruta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()
                ->with('error', 'Error al actualizar la ruta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * ➕ Agregar un producto a la ruta (método individual - mantener por compatibilidad)
     */
    public function addProducto(Request $request, Ruta $ruta)
    {
        \Log::info('=== AGREGANDO PRODUCTO A RUTA ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            $data = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'carga_inicial' => 'required|integer|min:0',
            ]);

            // Verificar si el producto ya está en la ruta
            $existe = RutaDetalle::where('ruta_id', $ruta->id)
                ->where('producto_id', $data['producto_id'])
                ->first();

            if ($existe) {
                return back()->with('error', 'Este producto ya está asignado a la ruta.');
            }

            $producto = Producto::findOrFail($data['producto_id']);
            $user = Auth::user();
            $sucursalId = $user->sucursal_id;

            DB::transaction(function () use ($ruta, $data, $producto, $user, $sucursalId) {
                // 🔻 Descontar CARGA INICIAL del INVENTARIO GENERAL
                $existencia = Existencia::where('producto_id', $data['producto_id'])
                    ->where('sucursal_id', $sucursalId)
                    ->first();

                if ($existencia) {
                    // Verificar que haya suficiente stock en inventario general
                    if ($existencia->stock_actual < $data['carga_inicial']) {
                        throw new \Exception("Stock insuficiente en inventario general. Stock actual: {$existencia->stock_actual}, Carga inicial solicitada: {$data['carga_inicial']}");
                    }

                    $nuevoStock = $existencia->stock_actual - $data['carga_inicial'];
                    $existencia->update(['stock_actual' => $nuevoStock]);

                    \Log::info('Carga inicial descontada del inventario general:', [
                        'producto_id' => $data['producto_id'],
                        'carga_inicial' => $data['carga_inicial'],
                        'stock_anterior' => $existencia->stock_actual,
                        'stock_nuevo' => $nuevoStock
                    ]);

                    // 🧾 Registrar movimiento de SALIDA en inventario general
                    InventarioMovimiento::create([
                        'producto_id' => $data['producto_id'],
                        'sucursal_id' => $sucursalId,
                        'usuario_id' => $user->id,
                        'tipo' => 'salida',
                        'cantidad' => $data['carga_inicial'],
                        'motivo' => "Carga inicial para ruta {$ruta->nombre}",
                        'referencia_tipo' => 'ruta',
                        'referencia_id' => $ruta->id,
                    ]);
                }

                // Crear el detalle de la ruta
                $detalle = RutaDetalle::create([
                    'ruta_id' => $ruta->id,
                    'producto_id' => $data['producto_id'],
                    'carga_inicial' => $data['carga_inicial'], // Este será nuestro stock de ruta
                    'recargas' => 0,
                    'devoluciones' => 0,
                    'ventas' => 0,
                    'precio_unitario' => $producto->precio,
                    'total' => 0,
                ]);

                \Log::info('Producto agregado a ruta:', $detalle->toArray());
            });

            return back()->with('success', 'Producto agregado correctamente a la ruta.');

        } catch (\Exception $e) {
            \Log::error('Error al agregar producto a ruta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error al agregar producto: ' . $e->getMessage());
        }
    }

    /**
     * ➕ Agregar múltiples productos a la ruta (modal)
     */
    public function bulkAddProductos(Request $request, Ruta $ruta)
    {
        \Log::info('=== AGREGANDO MÚLTIPLES PRODUCTOS A RUTA ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            $productosSeleccionados = $request->input('productos_seleccionados', []);
            $cargasIniciales = $request->input('carga_inicial', []);

        \Log::info('Productos seleccionados:', $productosSeleccionados);
        \Log::info('Cargas iniciales:', $cargasIniciales);

        $user = Auth::user();
        $sucursalId = $user->sucursal_id;

        DB::transaction(function () use ($ruta, $productosSeleccionados, $cargasIniciales, $user, $sucursalId) {
            foreach ($productosSeleccionados as $productoId) {
                $cargaInicial = $cargasIniciales[$productoId] ?? 0;

                if ($cargaInicial > 0) {
                    // Verificar si el producto ya está en la ruta
                    $existe = RutaDetalle::where('ruta_id', $ruta->id)
                        ->where('producto_id', $productoId)
                        ->first();

                    if ($existe) {
                        continue; // Saltar si ya existe
                    }

                    $producto = Producto::findOrFail($productoId);

                    // 🔻 Descontar CARGA INICIAL del INVENTARIO GENERAL
                    $existencia = Existencia::where('producto_id', $productoId)
                        ->where('sucursal_id', $sucursalId)
                        ->first();

                    if ($existencia) {
                        // Verificar que haya suficiente stock en inventario general
                        if ($existencia->stock_actual < $cargaInicial) {
                            throw new \Exception("Stock insuficiente para {$producto->nombre}. Stock actual: {$existencia->stock_actual}, Carga inicial solicitada: {$cargaInicial}");
                        }

                        $nuevoStock = $existencia->stock_actual - $cargaInicial;
                        $existencia->update(['stock_actual' => $nuevoStock]);

                        // 🧾 Registrar movimiento de SALIDA en inventario general
                        InventarioMovimiento::create([
                            'producto_id' => $productoId,
                            'sucursal_id' => $sucursalId,
                            'usuario_id' => $user->id,
                            'tipo' => 'salida',
                            'cantidad' => $cargaInicial,
                            'motivo' => "Carga inicial para ruta {$ruta->nombre}",
                            'referencia_tipo' => 'ruta',
                            'referencia_id' => $ruta->id,
                        ]);
                    }

                    // Crear el detalle de la ruta
                    RutaDetalle::create([
                        'ruta_id' => $ruta->id,
                        'producto_id' => $productoId,
                        'carga_inicial' => $cargaInicial,
                        'recargas' => 0,
                        'devoluciones' => 0,
                        'ventas' => 0,
                        'precio_unitario' => $producto->precio,
                        'total' => 0,
                    ]);

                    \Log::info('Producto agregado a ruta:', [
                        'producto_id' => $productoId,
                        'carga_inicial' => $cargaInicial
                    ]);
                }
            }
        });

        return back()->with('success', 'Productos agregados correctamente a la ruta.');

        } catch (\Exception $e) {
            \Log::error('Error al agregar productos a ruta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error al agregar productos: ' . $e->getMessage());
        }
    }

    /**
     * 🔄 Actualizar múltiples productos de la ruta (bulk update)
     */
    public function bulkUpdate(Request $request, Ruta $ruta)
    {
        \Log::info('=== ACTUALIZANDO MÚLTIPLES PRODUCTOS DE RUTA ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            $ventas = $request->input('ventas', []);
            $recargas = $request->input('recargas', []);
            $devoluciones = $request->input('devoluciones', []);

            $user = Auth::user();
            $sucursalId = $user->sucursal_id;

            DB::transaction(function () use ($ruta, $ventas, $recargas, $devoluciones, $user, $sucursalId) {
                $totalGeneral = 0;

                foreach ($ventas as $detalleId => $nuevasVentas) {
                    $detalle = RutaDetalle::with('producto')->find($detalleId);
                    if (!$detalle || $detalle->ruta_id != $ruta->id) {
                        continue;
                    }

                    $nuevasRecargas = $recargas[$detalleId] ?? 0;
                    $nuevasDevoluciones = $devoluciones[$detalleId] ?? 0;

                    // 🔻 Calcular NUEVO STOCK DE RUTA (CORREGIDO)
                    $stockActualRuta = $detalle->carga_inicial;
                    $nuevoStockRuta = $stockActualRuta 
                        + $nuevasRecargas     // Agregar recargas
                        - $nuevasVentas       // Restar ventas
                        - $nuevasDevoluciones; // RESTAR devoluciones (NO sumar)

                    // Verificar que no haya stock negativo en la ruta
                    if ($nuevoStockRuta < 0) {
                        throw new \Exception("No hay suficiente stock en la ruta para {$detalle->producto->nombre}. Stock actual: {$stockActualRuta}, Ventas: {$nuevasVentas}, Devoluciones: {$nuevasDevoluciones}");
                    }

                    // 🔻 Manejar RECARGAS (descontar del INVENTARIO GENERAL)
                    $recargasAnteriores = $detalle->recargas;
                    $diferenciaRecargas = $nuevasRecargas - $recargasAnteriores;

                    if ($diferenciaRecargas > 0) {
                        $existencia = Existencia::where('producto_id', $detalle->producto_id)
                            ->where('sucursal_id', $sucursalId)
                            ->first();

                        if ($existencia) {
                            // Verificar stock suficiente en inventario general para recargas
                            if ($existencia->stock_actual < $diferenciaRecargas) {
                                throw new \Exception("Stock insuficiente en inventario general para recargas de {$detalle->producto->nombre}. Stock actual: {$existencia->stock_actual}, Recargas solicitadas: {$diferenciaRecargas}");
                            }

                            // Descontar recargas del inventario general
                            $nuevoStockGeneral = $existencia->stock_actual - $diferenciaRecargas;
                            $existencia->update(['stock_actual' => $nuevoStockGeneral]);

                            // 🧾 Registrar movimiento de SALIDA por recargas
                            InventarioMovimiento::create([
                                'producto_id' => $detalle->producto_id,
                                'sucursal_id' => $sucursalId,
                                'usuario_id' => $user->id,
                                'tipo' => 'salida',
                                'cantidad' => $diferenciaRecargas,
                                'motivo' => "Recarga para ruta {$ruta->nombre}",
                                'referencia_tipo' => 'ruta',
                                'referencia_id' => $ruta->id,
                            ]);
                        }
                    }

                    // 🔻 Manejar DEVOLUCIONES (MERMA - NO regresan al inventario general)
                    $devolucionesAnteriores = $detalle->devoluciones;
                    $diferenciaDevoluciones = $nuevasDevoluciones - $devolucionesAnteriores;

                    if ($diferenciaDevoluciones > 0) {
                        // 🧾 Registrar movimiento de MERMA por devoluciones
                        InventarioMovimiento::create([
                            'producto_id' => $detalle->producto_id,
                            'sucursal_id' => $sucursalId,
                            'usuario_id' => $user->id,
                            'tipo' => 'ajuste',
                            'cantidad' => $diferenciaDevoluciones,
                            'motivo' => "Devolución/Merma de ruta {$ruta->nombre}",
                            'referencia_tipo' => 'ruta',
                            'referencia_id' => $ruta->id,
                        ]);

                        \Log::info('Devoluciones registradas como merma:', [
                            'producto_id' => $detalle->producto_id,
                            'devoluciones_adicionales' => $diferenciaDevoluciones
                        ]);
                    }

                    // Calcular total de ventas (las devoluciones NO afectan las ventas)
                    $total = $nuevasVentas * $detalle->precio_unitario;
                    $totalGeneral += $total;

                    // 🔥 ACTUALIZAR EL DETALLE con el NUEVO STOCK DE RUTA
                    $detalle->update([
                        'carga_inicial' => $nuevoStockRuta, // Actualizar el stock de la ruta
                        'recargas' => $nuevasRecargas,
                        'devoluciones' => $nuevasDevoluciones,
                        'ventas' => $nuevasVentas,
                        'total' => $total,
                    ]);

                    \Log::info('Detalle actualizado - Stock de ruta:', [
                        'detalle_id' => $detalle->id,
                        'stock_ruta_anterior' => $stockActualRuta,
                        'stock_ruta_nuevo' => $nuevoStockRuta,
                        'ventas' => $nuevasVentas,
                        'recargas' => $nuevasRecargas,
                        'devoluciones' => $nuevasDevoluciones
                    ]);
                }

                // Actualizar total global de la ruta
                $ruta->update([
                    'total_venta' => $totalGeneral
                ]);

                \Log::info('Total general actualizado:', ['total_venta' => $totalGeneral]);
            });

            return back()->with('success', 'Datos actualizados correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar productos de ruta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error al actualizar productos: ' . $e->getMessage());
        }
    }

    /**
     * 🔄 Actualizar cantidades y ventas de un producto específico (método individual - mantener por compatibilidad)
     */
    public function updateDetalle(Request $request, RutaDetalle $detalle)
    {
        \Log::info('=== ACTUALIZANDO DETALLE DE RUTA ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            $data = $request->validate([
                'recargas' => 'required|integer|min:0',
                'devoluciones' => 'required|integer|min:0',
                'ventas' => 'required|integer|min:0',
            ]);

            \Log::info('Datos validados:', $data);

            $user = Auth::user();
            $sucursalId = $user->sucursal_id;

            DB::transaction(function () use ($detalle, $data, $user, $sucursalId) {
                // 🔻 Obtener el producto y precio DENTRO de la transacción
                $producto = $detalle->producto;
                $precio = $producto->precio;
                
                $existencia = Existencia::where('producto_id', $producto->id)
                    ->where('sucursal_id', $sucursalId)
                    ->first();

                // 🔻 Calcular NUEVO STOCK DE RUTA (CORREGIDO)
                $stockActualRuta = $detalle->carga_inicial;
                $nuevoStockRuta = $stockActualRuta 
                    + $data['recargas']     // Agregar recargas
                    - $data['ventas']       // Restar ventas
                    - $data['devoluciones']; // RESTAR devoluciones (NO sumar)

                // Verificar que no haya stock negativo en la ruta
                if ($nuevoStockRuta < 0) {
                    throw new \Exception("No hay suficiente stock en la ruta. Stock actual: {$stockActualRuta}, Ventas: {$data['ventas']}, Devoluciones: {$data['devoluciones']}");
                }

                // 🔻 Manejar RECARGAS (descontar del INVENTARIO GENERAL)
                $recargasAnteriores = $detalle->recargas;
                $nuevasRecargas = $data['recargas'];
                $diferenciaRecargas = $nuevasRecargas - $recargasAnteriores;

                if ($diferenciaRecargas > 0 && $existencia) {
                    // Verificar stock suficiente en inventario general para recargas
                    if ($existencia->stock_actual < $diferenciaRecargas) {
                        throw new \Exception("Stock insuficiente en inventario general para recargas. Stock actual: {$existencia->stock_actual}, Recargas solicitadas: {$diferenciaRecargas}");
                    }

                    // Descontar recargas del inventario general
                    $nuevoStockGeneral = $existencia->stock_actual - $diferenciaRecargas;
                    $existencia->update(['stock_actual' => $nuevoStockGeneral]);

                    \Log::info('Recargas descontadas del inventario general:', [
                        'producto_id' => $producto->id,
                        'recargas_adicionales' => $diferenciaRecargas,
                        'stock_anterior_general' => $existencia->stock_actual,
                        'stock_nuevo_general' => $nuevoStockGeneral
                    ]);

                    // 🧾 Registrar movimiento de SALIDA por recargas
                    InventarioMovimiento::create([
                        'producto_id' => $producto->id,
                        'sucursal_id' => $sucursalId,
                        'usuario_id' => $user->id,
                        'tipo' => 'salida',
                        'cantidad' => $diferenciaRecargas,
                        'motivo' => "Recarga para ruta {$detalle->ruta->nombre}",
                        'referencia_tipo' => 'ruta',
                        'referencia_id' => $detalle->ruta->id,
                    ]);
                }

                // 🔻 Manejar DEVOLUCIONES (MERMA - NO regresan al inventario general)
                $devolucionesAnteriores = $detalle->devoluciones;
                $nuevasDevoluciones = $data['devoluciones'];
                $diferenciaDevoluciones = $nuevasDevoluciones - $devolucionesAnteriores;

                if ($diferenciaDevoluciones > 0) {
                    // 🧾 Registrar movimiento de MERMA por devoluciones
                    InventarioMovimiento::create([
                        'producto_id' => $producto->id,
                        'sucursal_id' => $sucursalId,
                        'usuario_id' => $user->id,
                        'tipo' => 'ajuste',
                        'cantidad' => $diferenciaDevoluciones,
                        'motivo' => "Devolución/Merma de ruta {$detalle->ruta->nombre}",
                        'referencia_tipo' => 'ruta',
                        'referencia_id' => $detalle->ruta->id,
                    ]);

                    \Log::info('Devoluciones registradas como merma:', [
                        'producto_id' => $producto->id,
                        'devoluciones_adicionales' => $diferenciaDevoluciones
                    ]);
                }

                // Calcular total de ventas (las devoluciones NO afectan las ventas)
                $total = $data['ventas'] * $precio;

                // 🔥 ACTUALIZAR EL DETALLE con el NUEVO STOCK DE RUTA
                $detalle->update([
                    'carga_inicial' => $nuevoStockRuta,
                    'recargas' => $data['recargas'],
                    'devoluciones' => $data['devoluciones'],
                    'ventas' => $data['ventas'],
                    'precio_unitario' => $precio,
                    'total' => $total,
                ]);

                // Actualizar total global de la ruta
                $detalle->ruta->update([
                    'total_venta' => $detalle->ruta->detalles()->sum('total')
                ]);

                \Log::info('Detalle actualizado - Stock de ruta:', [
                    'detalle_id' => $detalle->id,
                    'stock_ruta_anterior' => $stockActualRuta,
                    'stock_ruta_nuevo' => $nuevoStockRuta,
                    'ventas' => $data['ventas'],
                    'recargas' => $data['recargas'],
                    'devoluciones' => $data['devoluciones']
                ]);
            });

            return back()->with('success', 'Datos del producto actualizados correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar detalle de ruta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error al actualizar producto: ' . $e->getMessage());
        }
    }

    /**
     * 🗑️ Eliminar un producto de la ruta
     */
    public function deleteDetalle(RutaDetalle $detalle)
    {
        \Log::info('=== ELIMINANDO DETALLE DE RUTA ===', ['detalle_id' => $detalle->id]);

        $ruta = $detalle->ruta;
        
        DB::transaction(function () use ($detalle, $ruta) {
            $user = Auth::user();
            $sucursalId = $user->sucursal_id;
            
            // 🔼 REGRESAR al inventario: carga_inicial + recargas - ventas
            // NO regresar devoluciones porque son merma
            $producto = $detalle->producto;
            $totalParaRegresar = $detalle->carga_inicial + $detalle->recargas - $detalle->ventas;
            
            if ($totalParaRegresar > 0) {
                $existencia = Existencia::where('producto_id', $producto->id)
                    ->where('sucursal_id', $sucursalId)
                    ->first();

                if ($existencia) {
                    $nuevoStock = $existencia->stock_actual + $totalParaRegresar;
                    $existencia->update(['stock_actual' => $nuevoStock]);

                    \Log::info('Stock regresado al eliminar producto de ruta:', [
                        'producto_id' => $producto->id,
                        'stock_regresado' => $totalParaRegresar,
                        'stock_anterior' => $existencia->stock_actual,
                        'stock_nuevo' => $nuevoStock
                    ]);

                    // 🧾 Registrar movimiento de ENTRADA por eliminación
                    InventarioMovimiento::create([
                        'producto_id' => $producto->id,
                        'sucursal_id' => $sucursalId,
                        'usuario_id' => $user->id,
                        'tipo' => 'entrada',
                        'cantidad' => $totalParaRegresar,
                        'motivo' => "Producto eliminado de ruta {$ruta->nombre}",
                        'referencia_tipo' => 'ruta',
                        'referencia_id' => $ruta->id,
                    ]);
                }
            }

            $detalle->delete();

            // Recalcular total global
            $ruta->update([
                'total_venta' => $ruta->detalles()->sum('total')
            ]);

            \Log::info('Detalle eliminado, nuevo total:', ['total_venta' => $ruta->total_venta]);
        });

        return back()->with('success', 'Producto eliminado correctamente de la ruta.');
    }

    /**
     * 🧾 Eliminar una ruta completa
     */
    public function destroy(Ruta $ruta)
    {
        \Log::info('=== ELIMINANDO RUTA COMPLETA ===', ['ruta_id' => $ruta->id]);

        DB::transaction(function () use ($ruta) {
            $user = Auth::user();
            $sucursalId = $user->sucursal_id;
            
            // 🔼 REGRESAR al inventario todos los productos de la ruta
            foreach ($ruta->detalles as $detalle) {
                $producto = $detalle->producto;
                $totalParaRegresar = $detalle->carga_inicial + $detalle->recargas - $detalle->ventas;
                
                if ($totalParaRegresar > 0) {
                    $existencia = Existencia::where('producto_id', $producto->id)
                        ->where('sucursal_id', $sucursalId)
                        ->first();

                    if ($existencia) {
                        $nuevoStock = $existencia->stock_actual + $totalParaRegresar;
                        $existencia->update(['stock_actual' => $nuevoStock]);

                        // 🧾 Registrar movimiento de ENTRADA por eliminación de ruta
                        InventarioMovimiento::create([
                            'producto_id' => $producto->id,
                            'sucursal_id' => $sucursalId,
                            'usuario_id' => $user->id,
                            'tipo' => 'entrada',
                            'cantidad' => $totalParaRegresar,
                            'motivo' => "Ruta {$ruta->nombre} eliminada",
                            'referencia_tipo' => 'ruta',
                            'referencia_id' => $ruta->id,
                        ]);
                    }
                }
            }
            
            $ruta->detalles()->delete();
            $ruta->delete();
        });

        return redirect()
            ->route('rutas.index')
            ->with('success', 'Ruta eliminada correctamente.');
    }

    /**
     * ⚙️ Función interna → cerrar el día automáticamente (SIN CAMBIAR LA FECHA)
     */
    private function cerrarDiaAutomatico(Ruta $ruta)
    {
        \Log::info('=== CERRANDO DÍA AUTOMÁTICAMENTE ===', ['ruta_id' => $ruta->id]);

        $ruta->load(['detalles.producto']);
        $user = Auth::user();
        $sucursalId = $user->sucursal_id;

        DB::transaction(function () use ($ruta, $user, $sucursalId) {
            foreach ($ruta->detalles as $detalle) {
                $producto = $detalle->producto;
                $ventas = $detalle->ventas;
                $devoluciones = $detalle->devoluciones;

                // 🔻 Las DEVOLUCIONES son MERMA - NO regresan al inventario
                if ($devoluciones > 0) {
                    // 🧾 Registrar movimiento de MERMA por devoluciones
                    InventarioMovimiento::create([
                        'producto_id' => $producto->id,
                        'sucursal_id' => $sucursalId,
                        'usuario_id' => $user->id,
                        'tipo' => 'ajuste',
                        'cantidad' => $devoluciones,
                        'motivo' => "Devolución/Merma de ruta {$ruta->nombre}",
                        'referencia_tipo' => 'ruta',
                        'referencia_id' => $ruta->id,
                    ]);
                    
                    \Log::info('Devoluciones registradas como merma al cerrar día:', [
                        'producto_id' => $producto->id,
                        'devoluciones' => $devoluciones
                    ]);
                }

                \Log::info('Detalles mantenidos para el día:', [
                    'detalle_id' => $detalle->id,
                    'ventas' => $ventas,
                    'devoluciones' => $devoluciones,
                    'recargas' => $detalle->recargas
                ]);
            }

            // ✅ MANTENER LA FECHA ORIGINAL - NO ACTUALIZAR
            \Log::info('Ruta procesada para cierre de día (fecha mantenida):', [
                'ruta_id' => $ruta->id,
                'fecha_original' => $ruta->fecha
            ]);
        });
    }
}