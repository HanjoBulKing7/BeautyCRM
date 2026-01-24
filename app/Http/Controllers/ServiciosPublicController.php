<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;

class ServiciosPublicController extends Controller
{
    public function index()
    {
        $categorias = CategoriaServicio::query()
            ->where('estado', 'activo')
            ->with(['servicios' => function ($q) {
                $q->where('estado', 'activo')
                  ->orderBy('nombre_servicio');
            }])
            ->orderBy('nombre')
            ->get();

        // ✅ Renderiza la vista completa, no el partial
        return view('servicio', compact('categorias'));
    }
}
