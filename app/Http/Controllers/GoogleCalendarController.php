<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use App\Models\GoogleToken;

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

    public function disconnect()
    {
        try {
            GoogleToken::where('user_id', auth()->id())->delete();
            
            return redirect('/admin/citas')->with('success', 'Cuenta de Google Calendar desconectada exitosamente');
        } catch (\Exception $e) {
            return redirect('/admin/citas')->with('error', 'Error al desconectar: ' . $e->getMessage());
        }
    }

    public function status()
    {
        $isConnected = GoogleToken::where('user_id', auth()->id())->exists();
        
        return response()->json([
            'connected' => $isConnected
        ]);
    }
}