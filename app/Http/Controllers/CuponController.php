<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CuponController extends Controller
{
    public function index()
    {
        $query = Cupon::latest();

        // Filtrar por búsqueda
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%$search%")
                  ->orWhere('nombre', 'like', "%$search%");
            });
        }

        // Filtrar por estado
        if (request('estado')) {
            $query->where('estado', request('estado'));
        }

        $cupones = $query->paginate(15);
        return view('admin.cupones.index', compact('cupones'));
    }

    public function create()
    {
        return view('admin.cupones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|unique:cupones,codigo|max:50',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:porcentaje,monto',
            'valor_descuento' => 'required|numeric|min:0',
            'descuento_maximo' => 'nullable|numeric|min:0',
            'monto_minimo' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cantidad_usos' => 'nullable|integer|min:1',
            'cantidad_por_cliente' => 'required|integer|min:1',
            'aplica_cumpleaños' => 'boolean',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Cupon::create($data);

        return redirect()->route('admin.cupones.index')
            ->with('success', 'Cupón creado exitosamente.');
    }

    public function edit(Cupon $cupon)
    {
        return view('admin.cupones.edit', compact('cupon'));
    }

    public function show(Cupon $cupon)
    {
        return view('admin.cupones.show', compact('cupon'));
    }

    public function update(Request $request, Cupon $cupon)
    {
        $data = $request->validate([
            'codigo' => [
                'required',
                'string',
                Rule::unique('cupones', 'codigo')->ignore($cupon->id),
                'max:50',
            ],
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:porcentaje,monto',
            'valor_descuento' => 'required|numeric|min:0',
            'descuento_maximo' => 'nullable|numeric|min:0',
            'monto_minimo' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cantidad_usos' => 'nullable|integer|min:1',
            'cantidad_por_cliente' => 'required|integer|min:1',
            'aplica_cumpleaños' => 'boolean',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $cupon->update($data);

        return redirect()->route('admin.cupones.index')
            ->with('success', 'Cupón actualizado exitosamente.');
    }

    public function destroy(Cupon $cupon)
    {
        $cupon->delete();
        return redirect()->route('admin.cupones.index')
            ->with('success', 'Cupón eliminado exitosamente.');
    }

    /**
     * Valida un cupón para un cliente específico.
     */
    public function validar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cliente_id' => 'required|integer',
            'monto' => 'required|numeric|min:0',
        ]);

        $cupon = Cupon::where('codigo', strtoupper($request->codigo))->first();

        if (!$cupon) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Cupón no encontrado.',
            ], 404);
        }

        if (!$cupon->esValido()) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Cupón expirado o inactivo.',
            ], 422);
        }

        $descuento = $cupon->calcularDescuento((float) $request->monto);

        if ($descuento <= 0) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Este cupón no aplica para el monto ingresado.',
            ], 422);
        }

        return response()->json([
            'valido' => true,
            'cupon_id' => $cupon->id,
            'codigo' => $cupon->codigo,
            'descuento' => round($descuento, 2),
            'mensaje' => "Cupón aplicado: {$cupon->nombre}",
        ]);
    }

    /**
     * Obtiene descuento por cumpleaños si aplica.
     */
    public function descuentoCumpleaños(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|integer',
            'monto' => 'required|numeric|min:0',
        ]);

        $cliente = \App\Models\Cliente::find($request->cliente_id);

        if (!$cliente || !$cliente->fecha_nacimiento) {
            return response()->json([
                'aplica' => false,
                'descuento' => 0,
            ]);
        }

        // Verificar si es cumpleaños (misma fecha que hoy)
        $hoy = \Carbon\Carbon::now();
        $cumple = \Carbon\Carbon::parse($cliente->fecha_nacimiento);

        $esCumpleaños = (
            $hoy->month === $cumple->month &&
            $hoy->day === $cumple->day
        );

        if (!$esCumpleaños) {
            return response()->json([
                'aplica' => false,
                'descuento' => 0,
            ]);
        }

        // Buscar cupón de cumpleaños
        $cupon = Cupon::where('aplica_cumpleaños', true)
            ->where('estado', 'activo')
            ->first();

        if (!$cupon) {
            return response()->json([
                'aplica' => false,
                'descuento' => 0,
            ]);
        }

        $descuento = $cupon->calcularDescuento((float) $request->monto);

        return response()->json([
            'aplica' => true,
            'cupon_id' => $cupon->id,
            'descuento' => round($descuento, 2),
            'mensaje' => "¡Descuento de cumpleaños aplicado! ({$cupon->nombre})",
        ]);
    }
}
