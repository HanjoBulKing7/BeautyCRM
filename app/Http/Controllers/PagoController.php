<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Cita;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

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

        $anticipo = 100; //Anticipo de todos los servicios

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

    //Webhook importante para producción no usar "succes"
    public function webhook(Request $request)
    {

        Log::info('Webhook hit', [
            'mode' => str_starts_with((string) config('services.stripe.secret'), 'sk_live_') ? 'LIVE' : 'TEST',
            'has_signature' => (bool) $request->header('Stripe-Signature'),
        ]);

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook firma inválida', [
                'error' => $e->getMessage(),
            ]);
            return response('Firma inválida', 400);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook payload inválido', [
                'error' => $e->getMessage(),
            ]);
            return response('Payload inválido', 400);
        }

        // Solo nos interesa este evento por ahora
        if ($event->type !== 'checkout.session.completed') {
            return response('OK', 200);
        }

        $session = $event->data->object ?? null;

        if (!$session) {
        Log::warning('Stripe webhook sin session object', [
            'event_id' => $event->id ?? null,
        ]);
        return response('OK', 200);
        }

        Log::info('Webhook session snapshot', [
        'event_id' => $event->id ?? null,
        'session_id' => $session->id ?? null,
        'payment_status' => $session->payment_status ?? null,
        'amount_total' => $session->amount_total ?? null,
        'metadata' => (array) ($session->metadata ?? []),
        ]);


        if (($session->payment_status ?? null) !== 'paid') {
            // No es error; simplemente no está pagado
            Log::info('Stripe checkout.session.completed no pagado', [
                'session_id'     => $session->id ?? null,
                'payment_status' => $session->payment_status ?? null,
            ]);
            return response('OK', 200);
        }

        // ✅ CLAVE: si viene sin metadata (ej. stripe trigger), NO intentes guardar nada
        $idCita = (int) (($session->metadata->id_cita ?? 0));

        if (!$idCita) {
            Log::warning('checkout.session.completed sin metadata id_cita (se ignora)', [
                'session_id' => $session->id ?? null,
                'event_id'   => $event->id ?? null,
            ]);
            return response('OK', 200);
        }

        $total      = ((int) ($session->amount_total ?? 0)) / 100;
        $referencia = $session->payment_intent ?? $session->id;

        try {
            DB::transaction(function () use ($idCita, $total, $referencia, $session) {

                Venta::updateOrCreate(
                    ['referencia_pago' => $referencia],
                    [
                        'id_cita' => $idCita,
                        'fecha_venta' => now(),
                        'total' => $total,
                        'forma_pago' => 'tarjeta_credito',
                        'estado_venta' => 'confirmada',
                        'metodo_pago_especifico' => 'stripe_webhook',
                        'notas' => 'Confirmado vía webhook. session_id=' . ($session->id ?? ''),
                        'comision_empleado' => 0,
                    ]
                );

                $rows = Cita::where('id_cita', $idCita)
                    ->update([
                        'estado_cita' => 'confirmada',
                        'updated_at'  => now(),
                    ]);

                Log::info('UPDATE cita estado_cita', [
                    'id_cita' => $idCita,
                    'rows' => $rows,
                ]);
            });

        } catch (\Throwable $e) {
            // Importante: loguear y NO romper el webhook con 500 en dev/trigger
            Log::error('Error procesando webhook checkout.session.completed', [
                'id_cita'    => $idCita,
                'referencia' => $referencia,
                'error'      => $e->getMessage(),
            ]);

            // En producción puedes preferir 200 para evitar reintentos infinitos si es un error lógico.
            // Para debug, puedes dejar 500 si quieres que Stripe reintente.
            return response('Error interno', 200);
        }

        return response('OK', 200);
    }


    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) abort(400);

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($sessionId);

        $idCita = (int) ($session->metadata->id_cita ?? 0);
        $cita = Cita::find($idCita);

        return view('pagos.exito', [
            'cita' => $cita,
            'total' => ($session->amount_total ?? 0) / 100,
        ]);
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
        return view('pagos.cancelado', [
            'mensaje' => 'El pago fue cancelado.',
        ]);
    }
}
