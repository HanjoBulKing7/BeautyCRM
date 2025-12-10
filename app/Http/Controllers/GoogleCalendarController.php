<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use App\Models\GoogleToken;
use Illuminate\Support\Facades\Auth;


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
                return redirect('/admin/citas')->with('error', 'Error en la autenticación con Google');
            }

            $this->googleCalendar->handleCallback($code);

            return redirect('/admin/citas')->with('success', 'Cuenta de Google Calendar conectada exitosamente');
        } catch (\Exception $e) {
            return redirect('/admin/citas')->with('error', 'Error: ' . $e->getMessage());
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