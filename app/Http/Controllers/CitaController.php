<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Empleado;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CitaController; // ← AGREGAR
use App\Http\Controllers\GoogleCalendarController; // ← AGREGAR

class CitaController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    /**
     * Mostrar todas las citas
     */
    public function index()
    {
        $citas = Cita::with(['cliente', 'servicio', 'empleado'])
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->paginate(15);
        
        return view('admin.citas.index', compact('citas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $clientes = User::where('role_id', 1)->get(); // Asume que role_id 1 es cliente
        $servicios = Servicio::where('estado', 'activo')->get();
        $empleados = Empleado::all();
        
        return view('admin.citas.create', compact('clientes', 'servicios', 'empleados'));
    }

    /**
     * Crear nueva cita
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:users,id',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_empleado' => 'nullable|exists:empleados,id_empleado',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'estado_cita' => 'required|in:pendiente,confirmada,cancelada,completada',
            'observaciones' => 'nullable|string',
        ]);

        // Crear la cita en tu BD
        $cita = Cita::create($validated);

        // Sincronizar con Google Calendar
        try {
            if ($this->googleCalendar->isConnected()) {
                $this->syncCitaToGoogle($cita);
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar con Google Calendar: ' . $e->getMessage());
        }

        return redirect()->route('admin.citas.index')
            ->with('success', 'Cita creada exitosamente');
    }

    /**
     * Mostrar una cita específica
     */
    public function show(Cita $cita)
    {
        $cita->load(['cliente', 'servicio', 'empleado']);
        return view('admin.citas.show', compact('cita'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Cita $cita)
    {
        $clientes = User::where('role_id', 1)->get();
        $servicios = Servicio::where('estado', 'activo')->get();
        $empleados = Empleado::all();
        
        return view('admin.citas.edit', compact('cita', 'clientes', 'servicios', 'empleados'));
    }

    /**
     * Actualizar cita existente
     */
    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:users,id',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_empleado' => 'nullable|exists:empleados,id_empleado',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'estado_cita' => 'required|in:pendiente,confirmada,cancelada,completada',
            'observaciones' => 'nullable|string',
        ]);

        $cita->update($validated);

        // Actualizar en Google Calendar
        try {
            if ($this->googleCalendar->isConnected() && $cita->google_event_id) {
                $this->syncCitaToGoogle($cita);
            }
        } catch (\Exception $e) {
            Log::error('Error al actualizar en Google Calendar: ' . $e->getMessage());
        }

        return redirect()->route('admin.citas.index')
            ->with('success', 'Cita actualizada exitosamente');
    }

    /**
     * Eliminar cita
     */
    public function destroy(Cita $cita)
    {
        // Eliminar de Google Calendar primero
        try {
            if ($this->googleCalendar->isConnected() && $cita->google_event_id) {
                $this->googleCalendar->deleteEvent($cita->google_event_id);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar de Google Calendar: ' . $e->getMessage());
        }

        $cita->delete();

        return redirect()->route('admin.citas.index')
            ->with('success', 'Cita eliminada exitosamente');
    }

    /**
     * Sincronizar cita con Google Calendar
     */
    protected function syncCitaToGoogle(Cita $cita)
    {
        // Cargar relaciones necesarias
        $cita->load('cliente', 'servicio', 'empleado');

        // Preparar datos
        $summary = "Cita: {$cita->cliente->name} - {$cita->servicio->nombre_servicio}";
        
        $description = "Cliente: {$cita->cliente->name}\n";
        $description .= "Email: {$cita->cliente->email}\n";
        $description .= "Servicio: {$cita->servicio->nombre_servicio}\n";
        $description .= "Precio: $" . number_format($cita->servicio->precio, 2) . "\n";
        
        if ($cita->empleado) {
            $description .= "Empleado: {$cita->empleado->nombre}\n";
        }
        
        if ($cita->observaciones) {
            $description .= "\nObservaciones: {$cita->observaciones}";
        }

        // Calcular fecha/hora de inicio y fin usando la duración del servicio
        $startDateTime = Carbon::parse($cita->fecha_cita->format('Y-m-d') . ' ' . $cita->hora_cita);
        $endDateTime = $startDateTime->copy()->addMinutes($cita->servicio->duracion_minutos);

        $attendeeEmail = $cita->cliente->email;

        // Si ya existe en Google, actualizar; si no, crear
        if ($cita->google_event_id) {
            // Actualizar evento existente
            $eventId = $this->googleCalendar->updateEvent(
                $cita->google_event_id,
                $summary,
                $description,
                $startDateTime,
                $endDateTime,
                $attendeeEmail
            );
        } else {
            // Crear nuevo evento
            $eventId = $this->googleCalendar->createEvent(
                $summary,
                $description,
                $startDateTime,
                $endDateTime,
                $attendeeEmail
            );

            // Guardar el ID del evento
            $cita->update([
                'google_event_id' => $eventId,
                'synced_with_google' => true,
            ]);
        }
    }
}