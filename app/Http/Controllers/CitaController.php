<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use App\Services\GoogleCalendarService;

class CitaController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    public function index()
    {
        $citas = Cita::with(['cliente', 'servicio', 'empleado'])
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->get();

        $clientes = User::where('role_id', 1)->get();
        $servicios = Servicio::all();
        $empleados = User::where('role_id', 2)->get();
        
        // Verificar conexión con Google Calendar de forma segura
        try {
            $isConnected = class_exists('App\Models\GoogleToken') && 
                        \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
        } catch (\Exception $e) {
            $isConnected = false;
        }

        return view('admin.citas.index', compact('citas', 'clientes', 'servicios', 'empleados', 'isConnected'));
    }

    public function create()
    {
        $clientes = User::all();
        $servicios = Servicio::all();
        $empleados = User::all();

        return view('admin.citas.create', compact('clientes', 'servicios', 'empleados'));
    }

    // En el método store, después de $cita = Cita::create($validated);
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:users,id',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_empleado' => 'nullable|exists:users,id',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required|date_format:H:i',
            'estado_cita' => 'required|in:pendiente,confirmada,cancelada,completada',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $cita = Cita::create($validated);

            // ✅ NUEVO: Si se crea como completada, crear venta
            if ($cita->estado_cita === 'completada') {
                $this->crearVentaDesdeCita($cita);
            }

            // Intentar sincronizar con Google Calendar si está conectado
            try {
                $tokenExists = \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
                if ($tokenExists) {
                    $event = $this->googleCalendar->createEventFromCita($cita);
                    
                    $cita->update([
                        'google_event_id' => $event->getId(),
                        'synced_with_google' => true,
                        'last_sync_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the cita creation
                \Log::error('Error syncing cita with Google Calendar: ' . $e->getMessage());
            }

            return redirect()->route('admin.citas.index')
                ->with('success', 'Cita creada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la cita: ' . $e->getMessage())
                ->withInput();
        }
    }

    // En CitaController.php - método show
    public function show(Cita $cita)
    {
        // Cargar todas las relaciones necesarias
        $cita->load(['cliente', 'servicio', 'empleado', 'venta']);
        
        return view('admin.citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        $clientes = User::where('role_id', 1)->get();
        $servicios = Servicio::all();
        $empleados = User::where('role_id', 2)->get();

        return view('admin.citas.edit', compact('cita', 'clientes', 'servicios', 'empleados'));
    }

    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:users,id',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_empleado' => 'nullable|exists:users,id',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required|date_format:H:i',
            'estado_cita' => 'required|in:pendiente,confirmada,cancelada,completada',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $oldEstado = $cita->estado_cita;
            $newEstado = $validated['estado_cita'];
            
            $cita->update($validated);

            // ✅ NUEVO: Crear venta automática cuando se marca como completada
            if ($oldEstado !== 'completada' && $newEstado === 'completada') {
                $this->crearVentaDesdeCita($cita);
            }

            // Sincronizar con Google Calendar si está conectado
            try {
                $tokenExists = \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
                if ($tokenExists) {
                    if ($cita->estado_cita === 'cancelada') {
                        $this->googleCalendar->deleteEventFromCita($cita);
                        $cita->update([
                            'synced_with_google' => false,
                            'google_event_id' => null,
                        ]);
                    } else {
                        $event = $this->googleCalendar->updateEventFromCita($cita);
                        $cita->update([
                            'google_event_id' => $event->getId(),
                            'synced_with_google' => true,
                            'last_sync_at' => now(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error syncing cita update with Google Calendar: ' . $e->getMessage());
            }

            return redirect()->route('admin.citas.index')
                ->with('success', 'Cita actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la cita: ' . $e->getMessage())
                ->withInput();
        }
    }


    private function crearVentaDesdeCita(Cita $cita)
    {
        try {
            // Verificar si ya existe una venta para esta cita
            if ($cita->venta) {
                return $cita->venta;
            }

            // Obtener el precio del servicio
            $servicio = $cita->servicio;
            $total = $servicio->precio ?? 0;
            
            // Calcular comisión (ejemplo: 15% del servicio)
            $comision_empleado = $total * 0.15;

            // Crear la venta con la estructura CORRECTA de tu tabla ventas
            $venta = \App\Models\Venta::create([
                'id_cita' => $cita->id_cita,
                'total' => $total,
                'forma_pago' => 'efectivo', // Valor por defecto
                'comision_empleado' => $comision_empleado,
                'notas' => 'Venta automática generada al completar cita #' . $cita->id_cita
            ]);

            \Log::info('Venta creada automáticamente para cita #' . $cita->id_cita . ', Venta #' . $venta->id_venta);
            
            return $venta;
            
        } catch (\Exception $e) {
            \Log::error('Error al crear venta desde cita: ' . $e->getMessage());
            return null;
        }
    }

    public function destroy(Cita $cita)
    {
        try {
            // Eliminar de Google Calendar si existe
            try {
                $tokenExists = \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
                if ($tokenExists && $cita->google_event_id) {
                    $this->googleCalendar->deleteEventFromCita($cita);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting cita from Google Calendar: ' . $e->getMessage());
            }

            $cita->delete();

            return redirect()->route('admin.citas.index')
                ->with('success', 'Cita eliminada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la cita: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar cita específica con Google Calendar
     */
// Agrega estos métodos al CitaController
    public function syncWithGoogle(Cita $cita)
    {
        try {
            $event = $this->googleCalendar->createEventFromCita($cita);
            
            $cita->update([
                'google_event_id' => $event->getId(),
                'synced_with_google' => true,
                'last_sync_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Cita sincronizada con Google Calendar exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al sincronizar con Google Calendar: ' . $e->getMessage());
        }
    }

    public function syncAllWithGoogle()
    {
        try {
            $results = $this->googleCalendar->syncPendingCitas();

            $message = "Sincronización completada: {$results['success']} citas sincronizadas.";
            if (!empty($results['errors'])) {
                $message .= " Errores: " . implode(', ', $results['errors']);
            }

            return redirect()->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error en la sincronización: ' . $e->getMessage());
        }
    }
}