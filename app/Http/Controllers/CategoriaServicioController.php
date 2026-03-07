<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriaServicioController extends Controller
{
    public function index()
    {
        $categorias = CategoriaServicio::all();
        return view('admin.categoriaservicios.index', compact('categorias'));
    }

    public function create()
    {
        $categoria = new CategoriaServicio();
        return view('admin.categoriaservicios.create', compact('categoria'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120|unique:categorias_servicios,nombre',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Imagen
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('categorias-servicios', 'public');
        }

        CategoriaServicio::create($validated);

        return redirect()->route('admin.categoriaservicios.index')
            ->with('success', 'Categoría creada correctamente');
    }

    // AJAX: crear categoría desde servicios
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120|unique:categorias_servicios,nombre',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $categoria = CategoriaServicio::create($validated);
        return response()->json([
            'id_categoria' => $categoria->id_categoria,
            'nombre' => $categoria->nombre,
        ]);
    }

    public function show(CategoriaServicio $categoria)
    {
        return view('admin.categoriaservicios.show', compact('categoria'));
    }

    public function edit(CategoriaServicio $categoria)
    {
        return view('admin.categoriaservicios.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaServicio $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120|unique:categorias_servicios,nombre,' . $categoria->id_categoria . ',id_categoria',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Imagen nueva (borra anterior si existe)
        if ($request->hasFile('imagen')) {
            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('categorias-servicios', 'public');
        }

        $categoria->update($validated);

        return redirect()->route('admin.categoriaservicios.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(CategoriaServicio $categoria)
    {
        // Verificar si hay servicios usando esta categoría
        if ($categoria->servicios()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar la categoría porque tiene servicios asociados.');
        }

        // Eliminar imagen si existe
        if ($categoria->imagen) {
            Storage::disk('public')->delete($categoria->imagen);
        }

        $categoria->delete();

        return redirect()->route('admin.categoriaservicios.index')
            ->with('success', 'Categoría eliminada correctamente');
    }

    public function home()
    {
        $categorias = CategoriaServicio::where('estado', 'activo')->get();
        return view('home', compact('categorias'));
    }
}
