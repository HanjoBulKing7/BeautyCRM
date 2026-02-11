<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Cita;


class PagoController extends Controller
{
    public function checkout(Request $request)
{
    $idCita = (int) $request->query('id_cita');

    if (!$idCita) {
        abort(400, 'Falta id_cita');
    }

    $cita = Cita::findOrFail($idCita);

    if ($cita->estado_cita !== 'pendiente') {
        abort(403, 'Esta cita no se puede pagar.');
    }

    // 🔹 calcular total real
    $total = DB::table('cita_servicio')
        ->where('id_cita', $idCita)
        ->sum('precio_snapshot');

    $anticipo = min(200, $total);

    Stripe::setApiKey(config('services.stripe.secret'));

    $session = StripeSession::create([
        'mode' => 'payment',
        'payment_method_types' => ['card'],

        'line_items' => [[
            'price_data' => [
                'currency' => 'mxn',
                'product_data' => [
                    'name' => 'Anticipo cita #' . $idCita,
                ],
                'unit_amount' => (int) round($anticipo * 100),
            ],
            'quantity' => 1,
        ]],

        'metadata' => [
            'id_cita' => (string) $idCita,
            'tipo' => 'anticipo',
        ],

        'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => route('cancel') . '?id_cita=' . $idCita,
    ]);

    return redirect()->away($session->url);
}


    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) abort(400, 'Falta session_id');

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::retrieve($sessionId);

        if (($session->payment_status ?? null) !== 'paid') {
            return response("Pago aún no confirmado (payment_status={$session->payment_status}).", 409);
        }

        $idCita = (int) ($session->metadata->id_cita ?? 0);
        if (!$idCita) abort(500, 'Stripe session sin metadata id_cita');

        $total = ((int) ($session->amount_total ?? 0)) / 100;
        $referencia = $session->payment_intent ?? $session->id;

        DB::transaction(function () use ($idCita, $total, $referencia, $sessionId) {
            Venta::updateOrCreate(
                ['referencia_pago' => $referencia],
                [
                    'id_cita' => $idCita,
                    'fecha_venta' => now(),
                    'total' => $total,
                    'forma_pago' => 'tarjeta_credito',
                    'estado_venta' => 'pagada',
                    'metodo_pago_especifico' => 'stripe_checkout',
                    'notas' => "Anticipo confirmado por Stripe. session_id={$sessionId}",
                    'comision_empleado' => 0,
                ]
            );
        });

        // Ajusta esta ruta si no existe
        return redirect()->route('metodo.pago')
  ->with('success', 'Pago confirmado. Anticipo registrado.');

    }
    public function pagar(Request $request)
{
    $citaId = $request->query('id_cita');

    if (!$citaId) {
        abort(404);
    }

    $cita = Cita::with('cliente')->findOrFail($citaId);

    return view('metodo_pago', compact('cita'));
}


    public function cancel(Request $request)
    {
        return redirect()->back()->with('error', 'Pago cancelado.');
    }
}
