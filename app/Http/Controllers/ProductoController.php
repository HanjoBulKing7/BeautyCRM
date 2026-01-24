<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->latest()->paginate(10);
        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        $producto = new Producto();
        $categorias = CategoriaServicio::orderBy('nombre')->get();

        return view('admin.productos.create', compact('producto', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:120',
            'precio'       => 'required|numeric|min:0|max:999999.99',
            'descripcion'  => 'nullable|string',
            'id_categoria' => 'required|exists:categorias_servicios,id_categoria',
            'estado'       => 'required|in:activo,inactivo',
        ]);

        Producto::create($request->only([
            'nombre',
            'precio',
            'descripcion',
            'id_categoria',
            'estado',
        ]));

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return view('admin.productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = CategoriaServicio::orderBy('nombre')->get();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'       => 'required|string|max:120',
            'precio'       => 'required|numeric|min:0|max:999999.99',
            'descripcion'  => 'nullable|string',
            'id_categoria' => 'required|exists:categorias_servicios,id_categoria',
            'estado'       => 'required|in:activo,inactivo',
        ]);

        $producto->update($request->only([
            'nombre',
            'precio',
            'descripcion',
            'id_categoria',
            'estado',
        ]));

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}
