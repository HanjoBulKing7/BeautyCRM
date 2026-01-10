<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use App\Models\GoogleToken;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class GoogleCalendarController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    public function connect()
    {
        $authUrl = $this->googleCalendar->getAuthUrl();
        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->get('code');

            if (!$code) {
                return redirect('/login')->with('error', 'Error en la autenticación con Google');
            }

            // ✅ Si viene desde invitación (guardado en sesión)
            $invitedUserId = $request->session()->pull('invited_user_id');

            if ($invitedUserId) {
                $user = User::find($invitedUserId);

                if (!$user || (int)$user->role_id !== 2) {
                    return redirect('/login')->with('error', 'Invitación inválida o expirada.');
                }

                // Guardar tokens para el empleado invitado
                $this->googleCalendar->handleCallback($code, (int)$user->id);

                // Loguearlo
                Auth::login($user, true);
                $request->session()->regenerate();

                return redirect('/employee/dashboard')->with('success', 'Google Calendar conectado. ¡Listo!');
            }

            // ✅ Flujo normal (admin ya logueado)
            $this->googleCalendar->handleCallback($code);

            return redirect('/admin/citas')->with('success', 'Cuenta de Google Calendar conectada exitosamente');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function status()
    {
        $isConnected = GoogleToken::where('user_id', auth()->id())->exists();
        
        return response()->json([
            'connected' => $isConnected
        ]);
    }

    public function disconnect(Request $request)
    {
        GoogleToken::where('user_id', Auth::id())->delete();

        return redirect()
            ->back()
            ->with('success', 'Se ha desconectado tu cuenta de Google correctamente. Puedes volver a conectarla cuando quieras.');
    }

    /**
     * (Opcional) Fuerza una reconexión: borra el token y manda al flujo de OAuth.
     */
    public function reconnect(Request $request)
    {
        GoogleToken::where('user_id', Auth::id())->delete();

        // Asumo que ya tienes esta ruta para iniciar el OAuth:
        return redirect()->route('google.redirect');
    }
}