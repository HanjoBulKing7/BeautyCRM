<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categorias = CategoriaServicio::where('estado', 'activa')->get();
        return view('cliente.home', compact('categorias'));
    }
}