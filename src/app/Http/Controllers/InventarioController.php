<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Existencia;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    /**
     * Mostrar el listado del inventario con reportes.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $estado_stock = $request->query('estado_stock');
        $stock_min = $request->query('stock_min');
        $stock_max = $request->query('stock_max');
        $sucursalId = Auth::user()->sucursal_id;

        // =============================
        // 📦 CONSULTA PRINCIPAL DE INVENTARIO (SOLO PRODUCTOS ACTIVOS)
        // =============================
        $query = Existencia::with(['producto'])
            ->where('sucursal_id', $sucursalId)
            ->whereHas('producto', function ($q) {
                $q->where('activo', true); // Solo productos activos
            });

        // 🔍 Filtro por búsqueda (SKU / nombre / descripción)
        if ($search) {
            $query->whereHas('producto', function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // 📊 Filtro por estado de stock
        if ($estado_stock) {
            $query->where(function($q) use ($estado_stock) {
                if ($estado_stock == 'bajo') {
                    $q->whereRaw('stock_actual <= stock_minimo');
                } elseif ($estado_stock == 'medio') {
                    $q->whereRaw('stock_actual > stock_minimo AND stock_actual <= stock_minimo * 1.5');
                } elseif ($estado_stock == 'alto') {
                    $q->whereRaw('stock_actual > stock_minimo * 1.5');
                }
            });
        }

        // 🔢 Filtro por rango de stock
        if ($stock_min) {
            $query->where('stock_actual', '>=', $stock_min);
        }

        if ($stock_max) {
            $query->where('stock_actual', '<=', $stock_max);
        }

        // 📊 Ordenar por stock actual
        $existencias = $query->orderBy('stock_actual', 'desc')->paginate(10);

        // =============================
        // 📊 DATOS PARA REPORTES (PANEL DERECHO)
        // =============================
        $ventasDia = InventarioMovimiento::where('tipo', 'salida')
            ->whereDate('created_at', now())
            ->where('sucursal_id', $sucursalId)
            ->sum('cantidad');

        $mermaDia = InventarioMovimiento::where('tipo', 'merma')
            ->whereDate('created_at', now())
            ->where('sucursal_id', $sucursalId)
            ->sum('cantidad');

        $stockAgregado = InventarioMovimiento::where('tipo', 'entrada')
            ->whereDate('created_at', now())
            ->where('sucursal_id', $sucursalId)
            ->sum('cantidad');

        $acumuladoSemana = InventarioMovimiento::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('sucursal_id', $sucursalId)
            ->sum('cantidad');

        // 📊 Contador de productos activos
        $totalActivos = Existencia::where('sucursal_id', $sucursalId)
            ->whereHas('producto', function ($q) {
                $q->where('activo', true);
            })->count();

        // =============================
        // 📦 DEVOLVER VISTA
        // =============================
        return view('inventario.index', compact(
            'existencias',
            'ventasDia',
            'mermaDia',
            'stockAgregado',
            'acumuladoSemana',
            'totalActivos'
        ));
    }

    /**
     * Mostrar detalle de una existencia específica.
     */
    public function show(Existencia $existencia)
    {
        if ($existencia->sucursal_id != Auth::user()->sucursal_id) {
            abort(403, 'No tienes acceso a este inventario');
        }

        return view('inventario.show', compact('existencia'));
    }

    /**
     * Editar una existencia específica.
     */
    public function edit(Existencia $existencia)
    {
        if ($existencia->sucursal_id != Auth::user()->sucursal_id) {
            abort(403, 'No tienes acceso a este inventario');
        }

        return view('inventario.edit', compact('existencia'));
    }

    /**
     * Actualizar el stock (entradas / salidas).
     */
    public function update(Request $request, Existencia $existencia)
    {
        // =============================
        // 🔒 VALIDACIÓN DE PERMISOS
        // =============================
        if ($existencia->sucursal_id != Auth::user()->sucursal_id) {
            abort(403, 'No tienes acceso a este inventario');
        }

        // =============================
        // 🧾 VALIDAR CAMPOS DEL FORMULARIO
        // =============================
        $data = $request->validate([
            'entrada' => 'nullable|integer|min:0',
            'salida' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $entradas = $data['entrada'] ?? 0;
        $salidas = $data['salida'] ?? 0;

        // =============================
        // 📊 CALCULAR NUEVO STOCK
        // =============================
        $nuevoStock = $existencia->stock_actual + $entradas - $salidas;
        if ($nuevoStock < 0) $nuevoStock = 0;

        $existencia->update(['stock_actual' => $nuevoStock]);

        // =============================
        // 🧾 REGISTRAR MOVIMIENTOS EN HISTORIAL
        // =============================
        if ($entradas > 0) {
            InventarioMovimiento::create([
                'producto_id' => $existencia->producto_id,
                'sucursal_id' => $existencia->sucursal_id,
                'usuario_id' => $user->id,
                'tipo' => 'entrada',
                'cantidad' => $entradas,
                'motivo' => 'Entrada manual desde inventario',
                'referencia_tipo' => 'existencia',
                'referencia_id' => $existencia->id,
            ]);
        }

        if ($salidas > 0) {
            InventarioMovimiento::create([
                'producto_id' => $existencia->producto_id,
                'sucursal_id' => $existencia->sucursal_id,
                'usuario_id' => $user->id,
                'tipo' => 'salida',
                'cantidad' => $salidas,
                'motivo' => 'Salida manual desde inventario',
                'referencia_tipo' => 'existencia',
                'referencia_id' => $existencia->id,
            ]);
        }

        // =============================
        // ✅ RESPUESTA
        // =============================
        return redirect()->route('inventario.index')->with('success', 'Inventario actualizado exitosamente.');
    }

    /**
     * 🕓 Mostrar historial de movimientos por producto.
     */
    public function movimientos($productoId)
    {
        $producto = Producto::with(['movimientos' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($productoId);

        return view('inventario.movimientos', compact('producto'));
    }

    /**
     * Actualizar múltiples existencias a la vez
     */
    public function bulkUpdate(Request $request)
    {
        $sucursalId = Auth::user()->sucursal_id;
        $user = Auth::user();
        
        $entradas = $request->input('entradas', []);
        $salidas = $request->input('salidas', []);
        
        DB::beginTransaction();
        
        try {
            foreach ($entradas as $existenciaId => $cantidadEntrada) {
                $cantidadSalida = $salidas[$existenciaId] ?? 0;
                
                if ($cantidadEntrada > 0 || $cantidadSalida > 0) {
                    $existencia = Existencia::where('id', $existenciaId)
                        ->where('sucursal_id', $sucursalId)
                        ->first();
                    
                    if ($existencia) {
                        // Calcular nuevo stock
                        $nuevoStock = $existencia->stock_actual + $cantidadEntrada - $cantidadSalida;
                        if ($nuevoStock < 0) $nuevoStock = 0;
                        
                        $existencia->update(['stock_actual' => $nuevoStock]);
                        
                        // Registrar movimientos
                        if ($cantidadEntrada > 0) {
                            InventarioMovimiento::create([
                                'producto_id' => $existencia->producto_id,
                                'sucursal_id' => $existencia->sucursal_id,
                                'usuario_id' => $user->id,
                                'tipo' => 'entrada',
                                'cantidad' => $cantidadEntrada,
                                'motivo' => 'Entrada masiva desde inventario',
                                'referencia_tipo' => 'existencia',
                                'referencia_id' => $existencia->id,
                            ]);
                        }
                        
                        if ($cantidadSalida > 0) {
                            InventarioMovimiento::create([
                                'producto_id' => $existencia->producto_id,
                                'sucursal_id' => $existencia->sucursal_id,
                                'usuario_id' => $user->id,
                                'tipo' => 'salida',
                                'cantidad' => $cantidadSalida,
                                'motivo' => 'Salida masiva desde inventario',
                                'referencia_tipo' => 'existencia',
                                'referencia_id' => $existencia->id,
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('inventario.index')
                ->with('success', 'Inventario actualizado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('inventario.index')
                ->with('error', 'Error al actualizar el inventario: ' . $e->getMessage());
        }
    }
}