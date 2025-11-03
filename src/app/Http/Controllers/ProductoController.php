<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Existencia;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = Producto::where('activo', true); // ✅ Solo productos activos

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $productos = $query->orderBy('nombre')->paginate(10);
        return view('productos.index', compact('productos'));
    }
    
    /**
     * Mostrar productos inactivos
     */
    public function inactivos(Request $request)
    {
        $search = $request->query('search');
        
        $query = Producto::where('activo', false);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $productos = $query->orderBy('nombre')->paginate(10);
        return view('productos.inactivos', compact('productos'));
    }

    /**
     * Activar/Desactivar producto
     */
    public function toggle(Producto $producto)
    {
        $producto->update([
            'activo' => !$producto->activo
        ]);

        $status = $producto->activo ? 'activado' : 'desactivado';
        
        // Redirigir a la vista correspondiente
        if ($producto->activo) {
            return redirect()->route('productos.index')
                ->with('success', "Producto {$status} exitosamente.");
        } else {
            return redirect()->route('productos.inactivos')
                ->with('success', "Producto {$status} exitosamente.");
        }
    }


    public function create()
    {
        $producto = new Producto();
        return view('productos.create', compact('producto'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'precio_proveedor' => 'nullable|numeric|min:0',
            'activo' => 'sometimes|boolean'
        ]);

        // Generar SKU provisional (sin ID aún)
        $skuProvisional = $this->generarSKUProvisional($data['nombre']);
        
        $data['sku'] = $skuProvisional;
        $data['activo'] = 1; // ✅ Siempre activo al crear
        
        // Usar transacción para asegurar la consistencia
        DB::beginTransaction();
        
        try {
            $producto = Producto::create($data);
            
            // Ahora que tenemos el ID, actualizamos el SKU con el formato correcto
            $skuDefinitivo = $this->generarSKUDefinitivo($data['nombre'], $producto->id);
            $producto->update(['sku' => $skuDefinitivo]);
            
            // Obtener el usuario autenticado
            $user = Auth::user();
            
            // Validar que el usuario tenga una sucursal asignada
            if (!$user->sucursal_id) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No se puede crear el producto. Usuario no tiene una sucursal asignada.');
            }
            
            $sucursalId = $user->sucursal_id;
            
            // Crear existencia automáticamente
            Existencia::create([
                'producto_id' => $producto->id,
                'sucursal_id' => $sucursalId,
                'stock_actual' => 0,
                'stock_minimo' => 10
            ]);
            
            // Registrar movimiento de inventario
            InventarioMovimiento::create([
                'producto_id' => $producto->id,
                'sucursal_id' => $sucursalId,
                'usuario_id' => $user->id,
                'tipo' => 'entrada',
                'cantidad' => 0,
                'motivo' => 'Creación de producto - Stock inicial',
                'referencia_tipo' => 'producto',
                'referencia_id' => $producto->id
            ]);
            
            DB::commit();
            
            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente. Se ha generado stock inicial automáticamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    // ===============================
    // 🧾 Funciones auxiliares
    // ===============================

    private function generarSKUProvisional($nombre)
    {
        $inicialesNombre = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombre), 0, 3));
        $timestamp = time();
        return $inicialesNombre . substr($timestamp, -3);
    }

    private function generarSKUDefinitivo($nombre, $id)
    {
        $inicialesNombre = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombre), 0, 3));
        $idFormateado = str_pad($id, 3, '0', STR_PAD_LEFT);
        return $inicialesNombre . $idFormateado;
    }

    public function show(Producto $producto)
    {
        // ✅ Verificar que el producto esté activo
        if (!$producto->activo) {
            abort(404, 'Producto no encontrado');
        }
        
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        // ✅ Verificar que el producto esté activo
        if (!$producto->activo) {
            abort(404, 'Producto no encontrado');
        }
        
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        // ✅ Verificar que el producto esté activo
        if (!$producto->activo) {
            abort(404, 'Producto no encontrado');
        }

        $data = $request->validate([
            'sku' => 'required|string|max:100|unique:productos,sku,' . $producto->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'precio_proveedor' => 'nullable|numeric|min:0',
            'activo' => 'sometimes|boolean'
        ]);

        $data['activo'] = $producto->activo; // ✅ Mantener el estado activo
        $producto->update($data);
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        // ✅ En vez de borrar, cambiar estado a inactivo
        $producto->update(['activo' => false]);
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto desactivado exitosamente.');
    }

    // ✅ Opcional: Método para reactivar productos si lo necesitas
    public function activate(Producto $producto)
    {
        $producto->update(['activo' => true]);
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto reactivado exitosamente.');
    }

    // ✅ Opcional: Método para ver productos inactivos
    public function inactive(Request $request)
    {
        $search = $request->query('search');
        
        $query = Producto::where('activo', false);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $productos = $query->orderBy('nombre')->paginate(10);
        return view('productos.inactive', compact('productos'));
    }
}