<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empleado;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLogin()
    {
        return view('login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectByRole($user);
        }

        return back()->with('error', 'Correo o contraseña incorrectos');
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'telefono' => 'required|string|max:25',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => 2, // ✅ SIEMPRE EMPLEADO
        ]);

        // Crear empleado con teléfono (y lo demás en defaults)
        $fullName = trim($request->name);
        $parts = preg_split('/\s+/', $fullName);
        $nombre = $parts[0] ?? $fullName;
        $apellido = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        Empleado::create([
            'user_id'  => $user->id,
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $user->email,
            'telefono' => $request->telefono,

            // Defaults (ajusta si tu schema pide otros)
            'puesto'             => 'Sin asignar',
            'departamento'       => 'Sin asignar',
            'fecha_contratacion' => now()->toDateString(),
            'estatus'            => 'activo',
            'informacion_legal'  => null,
        ]);

        return redirect()->route('login.form')
            ->with('success', 'Cuenta creada correctamente. Ingresa con tus datos.');
    }


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // =========================
    // Google (Login general)
    // =========================
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login.form')
                ->with('error', 'No se pudo iniciar sesión con Google. Intenta de nuevo.');
        }

        $email = $googleUser->getEmail();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            $user = new User();
            $user->name = $googleUser->getName() ?? 'Usuario';
            $user->email = $email;
            $user->password = Hash::make(Str::random(32));
            $user->role_id = 2; // 👈 cliente por defecto (tu regla actual)
            $user->google_id = $googleUser->getId();
            $user->email_verified_at = now();
            $user->save();
        } else {
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }

            if (empty($user->name) && $googleUser->getName()) {
                $user->name = $googleUser->getName();
            }

            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    // =========================
    // Google OAuth EMPLEADOS (registro/login)
    // =========================
    public function redirectEmployeeToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt'      => 'consent',
            ])
            ->redirect();
    }

    public function handleEmployeeGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login.form')
                ->with('error', 'No se pudo iniciar sesión con Google (empleado). Intenta de nuevo.');
        }

        $email = $googleUser->getEmail();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            // ✅ Crear nuevo usuario como EMPLEADO
            $user = new User();
            $user->name = $googleUser->getName() ?? 'Empleado';
            $user->email = $email;
            $user->password = Hash::make(Str::random(32));
            $user->role_id = 2; // ✅ EMPLEADO
            $user->google_id = $googleUser->getId();
            $user->email_verified_at = now();
            $user->save();
        } else {
            // Vincular google_id si existía por email
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }

            // No pisar nombre si ya lo tienes
            if (empty($user->name) && $googleUser->getName()) {
                $user->name = $googleUser->getName();
            }

            // ⚠️ Si ya existía con otro rol, no lo cambiamos aquí (tu decisión final)
            $user->save();
        }

        // ✅ Asegurar registro en EMPLEADOS (teléfono queda NULL y lo llenan en su form de empleados)
        $empleado = $user->empleado;

        if (!$empleado) {
            $fullName = trim($user->name ?? 'Empleado');
            $parts = preg_split('/\s+/', $fullName);
            $nombre = $parts[0] ?? $fullName;
            $apellido = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

            $empleado = Empleado::create([
                'user_id' => $user->id,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $user->email,
                'telefono' => null, // ✅ pendiente (lo agrega en editar empleado)

                // Defaults por si tu tabla los requiere (ajusta a tu schema real)
                'puesto' => 'Sin asignar',
                'departamento' => 'Sin asignar',
                'fecha_contratacion' => now()->toDateString(),
                'estatus' => 'activo',
                'informacion_legal' => null,
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        // ✅ Mandarlo directo al form de empleados para agregar teléfono
        // Ajusta el nombre de la ruta si en tu proyecto es diferente.
        return redirect()->intended('/admin/home')
            ->with('success', 'Cuenta creada con Google. Bienvenido al CRM.');

    }

    // =========================
    // Invitación empleado -> conectar Google Calendar (tu flujo actual)
    // =========================
    public function acceptEmployeeInvitation(User $user, Request $request)
    {
        if ((int)$user->role_id !== 2) {
            return redirect()->route('login.form')->with('error', 'Invitación inválida.');
        }

        $request->session()->put('invited_user_id', $user->id);

        return redirect()->route('admin.google.connect');
    }

    // =========================
    // Redirect por rol
    // =========================
    private function redirectByRole(User $user)
    {
        switch ($user->role_id) {
            case 3: return redirect()->intended('/admin/home');  // admin
            case 2: return redirect()->intended('/admin/home');  // ✅ empleado
            case 1: return redirect()->intended('/home');        // cliente
            default:
                Auth::logout();
                return redirect()->route('login.form')
                    ->with('error', 'Rol no válido. Contacta al administrador.');
        }
    }

}
