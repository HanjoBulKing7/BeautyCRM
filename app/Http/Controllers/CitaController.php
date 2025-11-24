<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index()
    {
        return view('admin.citas.index');
    }
}