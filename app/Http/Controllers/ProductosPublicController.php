<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;

class ProductosPublicController extends Controller
{
    public function index()
    {
        $categorias = CategoriaServicio::query()
            ->where('estado', 'activo')
            ->with(['productos' => function ($q) {
                $q->where('estado', 'activo')
                  ->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return view('productos', compact('categorias'));
    }
}
