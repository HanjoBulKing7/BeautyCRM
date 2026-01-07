<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaConfirmadaMail;
use Illuminate\Support\Facades\Log;
use App\Models\GoogleToken;


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

        // Crear arreglo de eventos para el calendario
        $calendarEvents = $citas->map(function ($cita) {
            // formatear fecha y hora a ISO 8601
            $start = \Carbon\Carbon::parse($cita->fecha_cita->format('Y-m-d') . ' ' . $cita->hora_cita);

            return [
                'id'    => $cita->id_cita,
                'title' => $cita->servicio->nombre ?? 'Cita',
                'start' => $start->format('Y-m-d\TH:i:s'),
                // Opcional: si quieres duración fija de 1h
                'end'   => $start->copy()->addHour()->format('Y-m-d\TH:i:s'),
            ];
        })->values();

        $clientes = User::where('role_id', 1)->get();
        $servicios = Servicio::all();
        $empleados = User::where('role_id', 2)->get();
        $isGoogleConnected = GoogleToken::where('user_id', auth()->id())->exists();
        
        // Verificar conexión con Google Calendar de forma segura
        try {
            $isConnected = class_exists('App\Models\GoogleToken') && 
                        \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
        } catch (\Exception $e) {
            $isConnected = false;
        }

        return view('admin.citas.index', compact('citas', 'clientes', 'servicios', 'empleados', 'isConnected','isGoogleConnected', 'calendarEvents'));
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
            'id_cliente'    => 'required|exists:users,id',
            'id_servicio'   => 'required|exists:servicios,id_servicio',
            'id_servicios'  => 'nullable|array',
            'id_servicios.*'=> 'nullable|distinct|exists:servicios,id_servicio',
            'duracion_total_minutos' => 'nullable|integer|min:1|max:600',
            'id_empleado'   => 'nullable|exists:users,id',
            'fecha_cita'    => 'required|date',
            'hora_cita'     => 'required|date_format:H:i',
            'estado_cita'   => 'required|in:pendiente,confirmada,cancelada,completada',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            // 1) Crear la cita
            $cita = Cita::create($validated);
            Log::info('DEBUG: Cita creada', ['cita_id' => $cita->id_cita ?? null]);


            // 1.1) Guardar servicios (multi-servicio) en tabla pivote (sin romper compatibilidad)
            $extraServicios = (array) $request->input('id_servicios', []);
            $idsServicios = collect(array_merge([$validated['id_servicio']], $extraServicios))
                ->filter(fn($v) => !is_null($v) && $v !== '')
                ->map(fn($v) => (int) $v)
                ->unique()
                ->values();

            // Snapshots (precio/duración) para mantener histórico aunque cambie el servicio después
            $serviciosSeleccionados = Servicio::whereIn('id_servicio', $idsServicios)
                ->get(['id_servicio', 'precio', 'duracion_minutos']);

            // Duración total: por defecto suma de duraciones de servicios seleccionados.
            // (Usamos duracion_minutos del catálogo; snapshot se guarda en la pivote.)
            $duracionAuto = (int) $serviciosSeleccionados->sum(function ($s) {
                return (int) ($s->duracion_minutos ?? 0);
            });

            // Si el usuario ajustó manualmente la duración en el formulario, respetarla.
            $duracionManual = $request->input('duracion_total_minutos');
            $duracionFinal = (!is_null($duracionManual) && $duracionManual !== '')
                ? (int) $duracionManual
                : $duracionAuto;

            if ($duracionFinal > 0) {
                $cita->duracion_total_minutos = $duracionFinal;
                $cita->save();
            }


            $pivotData = [];
            foreach ($serviciosSeleccionados as $s) {
                $pivotData[$s->id_servicio] = [
                    'precio_snapshot'   => $s->precio,
                    'duracion_snapshot' => $s->duracion_minutos ?? 60,
                ];
            }

            // Guarda el servicio principal + extras
            $cita->servicios()->sync($pivotData);

            Log::info('DEBUG: Servicios vinculados a cita', [
                'cita_id'    => $cita->id_cita ?? null,
                'servicios'  => $idsServicios->all(),
                'pivot_rows' => count($pivotData),
            ]);

            // 2) Si se crea como completada, crear venta
            if ($cita->estado_cita === 'completada') {
                $this->crearVentaDesdeCita($cita);
                Log::info('DEBUG: Venta creada desde cita', ['cita_id' => $cita->id_cita ?? null]);
            }

            // 3) Sincronizar con Google Calendar (si hay token)
            $googleEventLink = null;

            try {
                $tokenExists = GoogleToken::where('user_id', auth()->id())->exists();

                if ($tokenExists) {
                    $event = $this->googleCalendar->createEventFromCita($cita);

                    $cita->update([
                        'google_event_id'    => $event->getId(),
                        'synced_with_google' => true,
                        'last_sync_at'       => now(),
                    ]);

                    // htmlLink del evento
                    if (method_exists($event, 'getHtmlLink')) {
                        $googleEventLink = $event->getHtmlLink();
                    } elseif (property_exists($event, 'htmlLink')) {
                        $googleEventLink = $event->htmlLink;
                    }

                    Log::info('DEBUG: Evento Google creado', [
                        'cita_id' => $cita->id_cita ?? null,
                        'event_id' => $event->getId(),
                        'html_link' => $googleEventLink,
                    ]);
                } else {
                    Log::info('DEBUG: Usuario sin GoogleToken, no se sincroniza con Google', [
                        'user_id' => auth()->id(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error syncing cita with Google Calendar: ' . $e->getMessage());
            }

            // 4) Enviar correo al cliente
            try {
                $cita->load(['cliente', 'servicio', 'servicios', 'empleado']);

                $cliente = $cita->cliente;

                Log::info('DEBUG: Datos de cliente para correo', [
                    'cliente_id'    => optional($cliente)->id,
                    'cliente_email' => optional($cliente)->email,
                ]);

                if ($cliente && !empty($cliente->email)) {
                    Mail::to($cliente->email)->send(
                        new CitaConfirmadaMail($cita, $googleEventLink)
                    );

                    Log::info('DEBUG: Correo de cita enviado sin excepción', [
                        'cita_id' => $cita->id_cita ?? null,
                        'email'   => $cliente->email,
                    ]);
                } else {
                    Log::warning('DEBUG: No se envió correo: cliente sin email', [
                        'cita_id' => $cita->id_cita ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending cita confirmation email: ' . $e->getMessage());
            }

            // 5) Redirección normal
            return redirect()->route('admin.citas.index')
                ->with('success', 'Cita creada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error general al crear cita: ' . $e->getMessage());

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
        $cita->load('servicios'); // 👈 importante para multi-servicio

        $clientes = User::all();
        $servicios = Servicio::all();
        $empleados = User::all();
        return view('admin.citas.edit', compact('cita', 'clientes', 'servicios', 'empleados'));
    }


    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:users,id',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_servicios'  => 'nullable|array',
            'id_servicios.*'=> 'nullable|distinct|exists:servicios,id_servicio',
            'duracion_total_minutos' => 'nullable|integer|min:1|max:600',
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

            // Si llega duración manual, guardarla (si no llega, se conserva la existente)
            $duracionManual = $request->input('duracion_total_minutos');
            if (!is_null($duracionManual) && $duracionManual !== '') {
                $cita->duracion_total_minutos = (int) $duracionManual;
                $cita->save();
            }




            // ✅ Multi-servicio: si el form ya manda id_servicios[], sincronizamos pivote.
            // Si NO lo manda (porque tu edit aún es legacy), NO borramos extras; solo garantizamos que el servicio principal exista en pivote.
            if ($request->has('id_servicios')) {
                $extraServicios = (array) $request->input('id_servicios', []);
                $idsServicios = collect(array_merge([$validated['id_servicio']], $extraServicios))
                    ->filter(fn($v) => !is_null($v) && $v !== '')
                    ->map(fn($v) => (int) $v)
                    ->unique()
                    ->values();

                $serviciosSeleccionados = Servicio::whereIn('id_servicio', $idsServicios)
                    ->get(['id_servicio', 'precio', 'duracion_minutos']);

                $pivotData = [];
                foreach ($serviciosSeleccionados as $s) {
                    $pivotData[$s->id_servicio] = [
                        'precio_snapshot'   => $s->precio,
                        'duracion_snapshot' => $s->duracion_minutos ?? 60,
                    ];
                }

                $cita->servicios()->sync($pivotData);
            } else {
                // Legacy edit: no tocar extras existentes
                $primaryId = (int) $validated['id_servicio'];

                // Si la cita no tiene pivote aún, lo creamos con el servicio principal
                if (!$cita->servicios()->exists()) {
                    $s = Servicio::where('id_servicio', $primaryId)->first();
                    if ($s) {
                        $cita->servicios()->sync([
                            $primaryId => [
                                'precio_snapshot'   => $s->precio,
                                'duracion_snapshot' => $s->duracion_minutos ?? 60,
                            ]
                        ]);
                    }
                } else {
                    // Si ya hay pivote, solo aseguramos que el principal esté incluido
                    $exists = $cita->servicios()->where('servicios.id_servicio', $primaryId)->exists();
                    if (!$exists) {
                        $s = Servicio::where('id_servicio', $primaryId)->first();
                        if ($s) {
                            $cita->servicios()->attach($primaryId, [
                                'precio_snapshot'   => $s->precio,
                                'duracion_snapshot' => $s->duracion_minutos ?? 60,
                            ]);
                        }
                    }
                }
            }
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


    function crearVentaDesdeCita(Cita $cita)
    {
        try {
            // Verificar si ya existe una venta para esta cita
            if ($cita->venta) {
                return $cita->venta;
            }

            // Calcular total (multi-servicio si existe pivote; fallback a servicio legacy)
            $cita->loadMissing(['servicios', 'servicio']);
            $total = 0;

            if ($cita->servicios && $cita->servicios->count() > 0) {
                $total = $cita->servicios->sum(function ($s) {
                    return (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0);
                });
            } else {
                $total = (float) ($cita->servicio->precio ?? 0);
            }

            // ✅ Crear la venta SOLO con las columnas que existen en la tabla `ventas`
            $venta = \App\Models\Venta::create([
                'id_cita'    => $cita->id_cita,
                'total'      => $total,
                'forma_pago' => 'efectivo', // Valor por defecto
                // No guardamos comisión ni notas en la BD por ahora
            ]);

            \Log::info(
                'Venta creada automáticamente para cita #' . $cita->id_cita . ', Venta #' . $venta->id_venta
            );

            return $venta;

        } catch (\Exception $e) {
            \Log::error('Error al crear venta desde cita: ' . $e->getMessage(), [
                'cita_id' => $cita->id_cita ?? null,
            ]);

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