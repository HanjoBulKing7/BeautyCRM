<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sucursal;

class AuthController extends Controller
{
    // === LOGIN ===
    public function showLogin()
    {
        return view('auth.login'); // resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nombre'   => 'required|string',
            'password' => 'required|string',
        ]);

        // Solo usuarios activos
        if (Auth::attempt([
            'nombre'   => $credentials['nombre'],
            'password' => $credentials['password'],
            'activo'   => 1
        ])) {
            $request->session()->regenerate();
                 return redirect()->route('ventas.dashboard');
        }

        return back()->withErrors([
            'nombre' => 'Nombre o contraseña incorrectos o usuario inactivo.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // === REGISTER ===
    public function showRegister()
    {
        $sucursales = Sucursal::all();
        return view('auth.register', compact('sucursales')); 
        // resources/views/auth/register.blade.php
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255|unique:users,nombre',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'rol'          => 'required|in:admin,vendedor,gerente',
            'sucursal_id'  => 'required|exists:sucursales,id',
        ]);

        $user = User::create([
            'nombre'      => $data['nombre'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'rol'         => $data['rol'],
            'sucursal_id' => $data['sucursal_id'],
            'activo'      => true,
        ]);

        Auth::login($user);

        return redirect()->route('ventas.dashboard')->with('success', 'Usuario registrado exitosamente.');
    }
}
