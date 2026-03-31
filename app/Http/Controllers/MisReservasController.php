<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisReservasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1) Resolver el "cliente_id" correcto (soporta 2 estructuras)
        $clienteId = $this->resolveClienteId($user);

        $query = Cita::with([
                'servicios',  // many-to-many
                'empleado',   // belongsTo (si existe)
            ])
            ->where('cliente_id', $clienteId)
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc');

        // 2) Filtros opcionales (si los usas en la vista)
        if ($request->filled('estado')) {
            $query->where('estado_cita', $request->string('estado')->toString());
        }

        if ($request->filled('from')) {
            $query->whereDate('fecha_cita', '>=', $request->date('from')->format('Y-m-d'));
        }

        if ($request->filled('to')) {
            $query->whereDate('fecha_cita', '<=', $request->date('to')->format('Y-m-d'));
        }

        if ($request->filled('q')) {
            $q = '%' . $request->string('q')->toString() . '%';

            $query->where(function ($qq) use ($q) {
                // buscar en observaciones
                $qq->where('observaciones', 'like', $q)

                   // buscar en servicios relacionados (nombre_servicio o nombre)
                   ->orWhereHas('servicios', function ($s) use ($q) {
                        $s->where('nombre_servicio', 'like', $q)
                          ->orWhere('nombre', 'like', $q);
                   });
            });
        }

        $citas = $query->get();

        return view('misreservas', compact('citas'));
    }

    /**
     * Soporta:
     * A) citas.cliente_id = users.id
     * B) citas.cliente_id = clientes.id, donde clientes.user_id = users.id
     */
    private function resolveClienteId($user): int
    {
        // Caso A (más común): cliente_id apunta a users.id
        $fallback = (int) $user->id;

        // Caso B: existe tabla/model Cliente con user_id
        // OJO: NO importo Cliente para evitar error si no existe el modelo.
        if (class_exists(\App\Models\Cliente::class)) {
            $cliente = \App\Models\Cliente::where('user_id', $user->id)->first();
            if ($cliente && isset($cliente->id)) {
                return (int) $cliente->id;
            }
            if ($cliente && isset($cliente->id_cliente)) { // por si tu PK es id_cliente
                return (int) $cliente->id_cliente;
            }
        }

        // Si tu User tiene relación cliente() (opcional)
        if (method_exists($user, 'cliente')) {
            $profile = $user->cliente;
            if ($profile && isset($profile->id)) return (int) $profile->id;
            if ($profile && isset($profile->id_cliente)) return (int) $profile->id_cliente;
        }

        return $fallback;
    }
    public function cancel(Request $request, $cita)
{
    $user = Auth::user();
    $clienteId = $this->resolveClienteId($user);

    // Busca la cita SOLO si pertenece al cliente
    $primaryKey = (new Cita())->getKeyName();
    $citaModel = Cita::where('cliente_id', $clienteId)
        ->where($primaryKey, $cita)
        ->firstOrFail();

    // Reglas básicas: solo cancelar pendiente/confirmada
    if (!in_array($citaModel->estado_cita, ['pendiente', 'confirmada'], true)) {
        return back()->with('error', 'Esta reserva no se puede cancelar.');
    }

    // Si está en el futuro, ok. Si ya pasó, no.
    try {
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $citaModel->fecha_cita.' '.$citaModel->hora_cita);
        if ($dt->lt(now())) {
            return back()->with('error', 'No puedes cancelar una reserva pasada.');
        }
    } catch (\Throwable $e) {
        // si por alguna razón no parsea fecha/hora, aún permitimos cancelar
    }

    $citaModel->estado_cita = 'cancelada';
    $citaModel->save();

    return back()->with('success', 'Reserva cancelada.');
}

}
