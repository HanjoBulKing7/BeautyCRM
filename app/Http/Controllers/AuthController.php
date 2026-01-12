<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

    // Mostrar formulario de registro de clientes
    public function showRegister()
    {
        return view('auth.register');
    }

    // Registrar cliente
// Registrar usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'role_id' => 'required|in:1,2,3', // Validar que sea uno de estos valores
        ]);

        // Crear el usuario con el role_id seleccionado
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('login.form')->with('success', 'Cuenta creada correctamente. Ingresa con tus datos.');
    }
    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
        // Redirección a Google (Login)
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    // Callback de Google (Login)
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login.form')
                ->with('error', 'No se pudo iniciar sesión con Google. Intenta de nuevo.');
        }

        $email = $googleUser->getEmail();

        // Buscar por google_id o por email (para vincular si ya existía)
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            // Crear nuevo usuario (default: cliente)
            $user = new User();
            $user->name = $googleUser->getName() ?? 'Usuario';
            $user->email = $email;
            $user->password = Hash::make(Str::random(32));
            $user->role_id = 1; // 👈 cliente por defecto (ajústalo si quieres otra regla)
            $user->google_id = $googleUser->getId();
            $user->email_verified_at = now();
            $user->save();
        } else {
            // Vincular google_id si existía por email
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }

            // Solo llenar name si está vacío (para no pisar tu nombre interno)
            if (empty($user->name) && $googleUser->getName()) {
                $user->name = $googleUser->getName();
            }

            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        // Reusar tu redirección por role_id
        return $this->redirectByRole($user);
    }

    public function acceptEmployeeInvitation(User $user, \Illuminate\Http\Request $request)
    {
        // Solo empleados
        if ((int)$user->role_id !== 2) {
            return redirect()->route('login.form')->with('error', 'Invitación inválida.');
        }

        // Guardamos qué empleado está conectando su Calendar
        $request->session()->put('invited_user_id', $user->id);

        // Mandamos al flujo de Google Calendar (tu connect actual)
        return redirect()->route('admin.google.connect');
    }


    // Método privado para no duplicar lógica
    private function redirectByRole(User $user)
    {
        switch ($user->role_id) {
            case 3: return redirect()->intended('/admin/home');
            case 2: return redirect()->intended('/employee/dashboard');
            case 1: return redirect()->intended('/home');
            default:
                Auth::logout();
                return redirect()->route('login.form')
                    ->with('error', 'Rol no válido. Contacta al administrador.');
        }
    }
}
