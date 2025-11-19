<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::latest()->paginate(10);
        return view('admin.servicios.index', compact('servicios')); // ← CAMBIADO
    }

    public function create()
    {
        $servicio = new Servicio();
        return view('admin.servicios.create', compact('servicio')); // ← CAMBIADO
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',
            'descuento' => 'nullable|numeric|min:0',
            'caracteristicas' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Manejar la imagen
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('servicios', 'public');
            $data['imagen'] = $imagePath;
        }

        Servicio::create($data);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio creado correctamente');
    }

    public function show(Servicio $servicio)
    {
        return view('admin.servicios.show', compact('servicio')); // ← CAMBIADO
    }

    public function edit(Servicio $servicio)
    {
        return view('admin.servicios.edit', compact('servicio')); // ← CAMBIADO
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',
            'descuento' => 'nullable|numeric|min:0',
            'caracteristicas' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Manejar la imagen si se sube una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            
            $imagePath = $request->file('imagen')->store('servicios', 'public');
            $data['imagen'] = $imagePath;
        }

        $servicio->update($data);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio actualizado correctamente');
    }

    public function destroy(Servicio $servicio)
    {
        // Eliminar imagen si existe
        if ($servicio->imagen) {
            Storage::disk('public')->delete($servicio->imagen);
        }

        $servicio->delete();

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio eliminado correctamente');
    }
}