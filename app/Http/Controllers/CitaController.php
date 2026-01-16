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
use App\Models\Cliente;
use App\Models\Empleado;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Venta;

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
        $clientes = Cliente::select('id', 'nombre', 'email')->get();
        $servicios = Servicio::all();

        // ✅ Empleados = users(role_id=2) + join a empleados para traer "departamento"
        $empleados = User::query()
            ->where('users.role_id', 2)
            ->leftJoin('empleados', 'empleados.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'empleados.departamento',
                'empleados.puesto',
            ])
            ->orderByRaw('COALESCE(empleados.departamento, "") ASC')
            ->orderBy('users.name')
            ->get();

        // ✅ si quieres ya mandarlo agrupado por departamento:
        $empleadosPorDepto = $empleados->groupBy(fn($e) => $e->departamento ?: 'Sin departamento');
        
        $clientesForJs = $clientes->map(function ($c) {
        return [
            'id' => $c->id,
            'label' => trim(($c->nombre ?? '') . ' - ' . ($c->email ?? '')),
            'nombre' => $c->nombre ?? '',
            'email' => $c->email ?? '',
        ];
        })->values();
        $categorias = Servicio::query()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        return view('admin.citas.create', compact('empleadosPorDepto', 'clientes', 'servicios', 'empleados','clientesForJs', 'categorias'));
    }

    // En el método store, después de $cita = Cita::create($validated);
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:clientes,id',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_servicios' => 'nullable|array',
            'id_servicios.*' => 'nullable|distinct|exists:servicios,id_servicio',
            'id_empleado' => 'nullable|exists:users,id',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required|date_format:H:i',
            'estado_cita' => 'required|in:pendiente,confirmada,cancelada,completada',
            'metodo_pago' => [
                Rule::requiredIf(fn() => $request->estado_cita === 'completada'),
                Rule::in(['efectivo', 'transferencia', 'tarjeta']),
                'nullable',
            ],
            'observaciones' => 'nullable|string|max:500',
            'descuento' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        if (($validated['estado_cita'] ?? null) !== 'completada') {
            $validated['metodo_pago'] = null;
        }

        try {
            // 1) Crear la cita
            $cita = Cita::create([
            ...$validated,
            'descuento' => $validated['descuento'] ?? 0,
        ]);

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

        // ✅ Empleados = users(role_id=2) + join a empleados para traer "departamento"
        $empleados = User::query()
            ->where('users.role_id', 2)
            ->leftJoin('empleados', 'empleados.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'empleados.departamento',
                'empleados.puesto',
            ])
            ->orderByRaw('COALESCE(empleados.departamento, "") ASC')
            ->orderBy('users.name')
            ->get();

            $empleadosPorDepto = $empleados->groupBy(fn($e) => $e->departamento ?: 'Sin departamento');

            $clientes = Cliente::select('id', 'nombre', 'email')->get();
            $servicios = Servicio::all();
            $categorias = Servicio::query()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        return view('admin.citas.edit', compact('empleadosPorDepto', 'cita', 'clientes', 'servicios', 'empleados', 'categorias'));
    }


    public function update(Request $request, Cita $cita)
    {
            $validated = $request->validate([
                'id_cliente' => 'required|exists:clientes,id',
                'id_servicio' => 'required|exists:servicios,id_servicio',
                'id_servicios'   => 'nullable|array',
                'id_servicios.*' => 'nullable|distinct|exists:servicios,id_servicio',
                'id_empleado' => 'nullable|exists:users,id',
                'fecha_cita' => 'required|date',
                'hora_cita'  => 'required|date_format:H:i',
                'estado_cita'=> 'required|in:pendiente,confirmada,cancelada,completada',
                'metodo_pago' => [
                    Rule::requiredIf(fn() => $request->estado_cita === 'completada'),
                    Rule::in(['efectivo', 'transferencia', 'tarjeta']),
                    'nullable',
                ],
                'observaciones' => 'nullable|string|max:500',
                'descuento' => 'nullable|numeric|min:0|max:100000',
            ]);

            if (($validated['estado_cita'] ?? null) !== 'completada') {
                $validated['metodo_pago'] = null;
            }
 
        try {

            $oldEstado = $cita->estado_cita;
            $newEstado = $validated['estado_cita'];
            
            $cita->update($validated);

            
            $cita->descuento = $validated['descuento'] ?? 0;
            $cita->save();

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

    //=======================================================================
    //CREAR VENTA DESDE CITA FUNCIÓN ========================================
    private function crearVentaDesdeCita(Cita $cita): void
    {
        // Cargar relaciones necesarias (multi-servicio o servicio principal)
        $cita->loadMissing(['servicios', 'servicio']);

        // Si falta empleado o cliente, no podemos crear venta (ventas los requiere)
        if (empty($cita->id_cliente) || empty($cita->id_empleado)) {
            \Log::warning('Cita completada sin cliente/empleado, no se creó venta', [
                'id_cita' => $cita->id_cita,
                'id_cliente' => $cita->id_cliente,
                'id_empleado' => $cita->id_empleado,
            ]);
            return;
        }

        // 1) Subtotal: suma snapshots del pivot (si existen) o fallback al servicio principal
        $subtotal = $cita->servicios->isNotEmpty()
            ? (float) $cita->servicios->sum(fn ($s) => (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0))
            : (float) ($cita->servicio->precio ?? 0);

        // 2) Descuento y total
        $descuento = (float) ($cita->descuento ?? 0);
        $total = max($subtotal - $descuento, 0);

        // 3) Fecha venta = fecha_cita + hora_cita
        $fecha = $cita->fecha_cita instanceof \Carbon\Carbon
            ? $cita->fecha_cita->format('Y-m-d')
            : (string) $cita->fecha_cita;

        $fechaVenta = Carbon::parse($fecha . ' ' . $cita->hora_cita);

        // 4) Método de pago de CITA -> ENUM de VENTAS
        // En citas: efectivo | transferencia | tarjeta
        // En ventas: efectivo | tarjeta_credito | tarjeta_debito
        $metodo = strtolower((string) ($cita->metodo_pago ?? ''));

        $formaPago = match ($metodo) {
            'efectivo' => 'efectivo',
            'tarjeta' => 'tarjeta_credito',         // si no distingues, mándalo a crédito
            'tarjeta_credito' => 'tarjeta_credito',
            'tarjeta_debito' => 'tarjeta_debito',
            'transferencia' => 'efectivo',          // ventas no acepta transferencia (lo reportamos desde citas)
            default => 'efectivo',
        };

        // 5) Crear o actualizar venta (evita duplicados por id_cita)
        Venta::updateOrCreate(
            ['id_cita' => $cita->id_cita],
            [
                'id_cliente' => $cita->id_cliente,      // ✅ OJO: debe apuntar a clientes.id (ver migración abajo)
                'id_empleado' => $cita->id_empleado,    // ✅ users.id (role=2)
                'id_servicio' => $cita->id_servicio,
                'fecha_venta' => $fechaVenta,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'forma_pago' => $formaPago,
                'estado_venta' => 'pagada',             // ✅ válido: pendiente|pagada|cancelada
                'observaciones' => $cita->observaciones,
            ]
        );
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