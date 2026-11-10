<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Panel de administración: solo empleadas (rol 2) y administradores (rol 3).
class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array((int) Auth::user()?->role_id, [2, 3], true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403);
        }

        return redirect()->route('cliente.home')->with('error', 'No tienes permisos para entrar al panel.');
    }
}
