<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ✅ NUEVO

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
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ✅ NUEVO
        ]);

        $data = $request->only([
            'nombre',
            'precio',
            'descripcion',
            'id_categoria',
            'estado',
        ]);

        // ✅ Guardar imagen
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($data);

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
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ✅ NUEVO
        ]);

        $data = $request->only([
            'nombre',
            'precio',
            'descripcion',
            'id_categoria',
            'estado',
        ]);

        // ✅ Reemplazar imagen (borrar anterior y guardar nueva)
        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        // ✅ Borrar imagen del storage si existe
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}
