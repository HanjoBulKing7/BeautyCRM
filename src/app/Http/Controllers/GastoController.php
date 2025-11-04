<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gasto;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    // Categorías disponibles (EXACTAMENTE como están en el ENUM de la BD)
    const CATEGORIAS = [
        'servicios' => 'Servicios',
        'renta' => 'Renta', 
        'insumos' => 'Insumos',
        'nomina' => 'Nómina',
        'mantenimiento' => 'Mantenimiento',
        'otros' => 'Otros'
    ];

    // Métodos de pago disponibles (EXACTAMENTE como están en el ENUM de la BD)
    const METODOS_PAGO = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia', 
        'tarjeta' => 'Tarjeta'
    ];

    public function index(Request $request)
    {
        $query = Gasto::with(['sucursal', 'ruta', 'ruta.empleado'])
            ->where('sucursal_id', auth()->user()->sucursal_id)
            ->latest();

        // Filtro por categoría
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria', $request->categoria);
        }

        // Filtro por fecha
        $fecha = $request->fecha ?? now()->toDateString();
        if ($fecha) {
            $query->whereDate('fecha', $fecha);
        }

        // Filtro por tipo (gasto general o de ruta)
        if ($request->has('tipo') && $request->tipo != '') {
            if ($request->tipo === 'ruta') {
                $query->whereNotNull('ruta_id');
            } elseif ($request->tipo === 'general') {
                $query->whereNull('ruta_id');
            }
        }

        // Filtro por ruta específica
        if ($request->has('ruta_id') && $request->ruta_id != '') {
            $query->where('ruta_id', $request->ruta_id);
        }

        $gastos = $query->paginate(20);

        // Calcular total del día
        $totalDia = Gasto::where('sucursal_id', auth()->user()->sucursal_id)
            ->whereDate('fecha', $fecha)
            ->sum('monto');

        // Obtener rutas para el filtro
        $rutas = Ruta::where('sucursal_id', auth()->user()->sucursal_id)
            ->orderBy('nombre')
            ->get();

        return view('gastos.index', [
            'gastos' => $gastos,
            'totalDia' => $totalDia,
            'fecha' => $fecha,
            'rutas' => $rutas,
            'categorias' => self::CATEGORIAS
        ]);
    }

    public function create(Request $request)
    {
        $rutaId = $request->get('ruta_id');
        $ruta = null;
        
        if ($rutaId) {
            $ruta = Ruta::with('empleado')->find($rutaId);
        }

        return view('gastos.create', [
            'gasto' => new Gasto(),
            'categorias' => self::CATEGORIAS,
            'metodosPago' => self::METODOS_PAGO,
            'ruta' => $ruta
        ]);
    }

    public function store(Request $request)
    {
        // Validar datos con las categorías permitidas (EXACTAMENTE como en el ENUM)
        $data = $request->validate([
            'fecha' => 'required|date',
            'categoria' => 'required|in:servicios,renta,insumos,nomina,mantenimiento,otros',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'ruta_id' => 'nullable|exists:rutas,id',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Obtener la sucursal y usuario automáticamente del usuario autenticado
        $sucursal_id = Auth::user()->sucursal_id;
        $usuario_id = Auth::user()->id;

        // Verificar que el usuario tenga una sucursal asignada
        if (!$sucursal_id) {
            return redirect()->back()
                ->with('error', 'No tienes una sucursal asignada. Contacta al administrador.')
                ->withInput();
        }

        // Procesar comprobante si se subió
        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('comprobantes', 'public');
        }

        // Crear el gasto
        $gasto = Gasto::create([
            'sucursal_id' => $sucursal_id,
            'usuario_id' => $usuario_id,
            'ruta_id' => $data['ruta_id'] ?? null,
            'fecha' => $data['fecha'],
            'categoria' => $data['categoria'],
            'descripcion' => $data['descripcion'],
            'monto' => $data['monto'],
            'metodo_pago' => $data['metodo_pago'],
            'comprobante_url' => $comprobanteUrl,
        ]);

        // Redirigir dependiendo de si es gasto de ruta o general
        if ($data['ruta_id']) {
            return redirect()->route('rutas.show', $data['ruta_id'])
                ->with('success', 'Gasto de ruta registrado correctamente');
        }

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function show(Gasto $gasto)
    {
        // Verificar que el gasto pertenezca a la sucursal del usuario
        if ($gasto->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('gastos.index')
                ->with('error', 'No tienes permisos para ver este gasto.');
        }

        $gasto->load(['sucursal', 'usuario', 'ruta', 'ruta.empleado']);
        
        return view('gastos.show', compact('gasto'));
    }

    public function edit(Gasto $gasto)
    {
        // Verificar que el gasto pertenezca a la sucursal del usuario
        if ($gasto->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('gastos.index')
                ->with('error', 'No tienes permisos para editar este gasto.');
        }

        return view('gastos.edit', [
            'gasto' => $gasto,
            'categorias' => self::CATEGORIAS,
            'metodosPago' => self::METODOS_PAGO
        ]);
    }

    public function update(Request $request, Gasto $gasto)
    {
        // Verificar que el gasto pertenezca a la sucursal del usuario
        if ($gasto->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('gastos.index')
                ->with('error', 'No tienes permisos para editar este gasto.');
        }

        // Validar datos con las categorías permitidas (EXACTAMENTE como en el ENUM)
        $data = $request->validate([
            'fecha' => 'required|date',
            'categoria' => 'required|in:servicios,renta,insumos,nomina,mantenimiento,otros',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Procesar comprobante si se subió uno nuevo
        if ($request->hasFile('comprobante')) {
            // Eliminar comprobante anterior si existe
            if ($gasto->comprobante_url) {
                Storage::disk('public')->delete($gasto->comprobante_url);
            }
            
            $comprobanteUrl = $request->file('comprobante')->store('comprobantes', 'public');
            $data['comprobante_url'] = $comprobanteUrl;
        }

        // Actualizar el gasto
        $gasto->update($data);

        // Redirigir dependiendo de si es gasto de ruta o general
        if ($gasto->ruta_id) {
            return redirect()->route('rutas.show', $gasto->ruta_id)
                ->with('success', 'Gasto de ruta actualizado correctamente');
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Gasto $gasto)
    {
        // Verificar que el gasto pertenezca a la sucursal del usuario
        if ($gasto->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('gastos.index')
                ->with('error', 'No tienes permisos para eliminar este gasto.');
        }

        $rutaId = $gasto->ruta_id;

        // Eliminar comprobante si existe
        if ($gasto->comprobante_url) {
            Storage::disk('public')->delete($gasto->comprobante_url);
        }

        // Eliminar el gasto
        $gasto->delete();

        // Redirigir dependiendo de si es gasto de ruta o general
        if ($rutaId) {
            return redirect()->route('rutas.show', $rutaId)
                ->with('success', 'Gasto de ruta eliminado correctamente');
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto eliminado correctamente.');
    }

    public function downloadComprobante(Gasto $gasto)
    {
        // Verificar que el gasto pertenezca a la sucursal del usuario
        if ($gasto->sucursal_id !== Auth::user()->sucursal_id) {
            return redirect()
                ->route('gastos.index')
                ->with('error', 'No tienes permisos para acceder a este comprobante.');
        }

        if (!$gasto->comprobante_url) {
            return redirect()
                ->route('gastos.show', $gasto)
                ->with('error', 'No hay comprobante disponible para este gasto.');
        }

        if (!Storage::disk('public')->exists($gasto->comprobante_url)) {
            return redirect()
                ->route('gastos.show', $gasto)
                ->with('error', 'El archivo del comprobante no existe.');
        }

       return response()->download(Storage::disk('public')->path($gasto->comprobante_url));
    }
}