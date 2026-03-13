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
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaConfirmadaMail;

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

        $anticipo = 10; //Anticipo de todos los servicios

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
        } catch (\Throwable $e) {
            Log::error('Stripe webhook inválido', ['error' => $e->getMessage()]);
            return response('Invalid', 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response('OK', 200);
        }

        $session = $event->data->object ?? null;

        if (!$session || ($session->payment_status ?? null) !== 'paid') {
            return response('OK', 200);
        }

        $idCita = (int) ($session->metadata->id_cita ?? 0);
        if (!$idCita) {
            return response('OK', 200);
        }

        $total      = ((int) ($session->amount_total ?? 0)) / 100;
        $referencia = $session->payment_intent ?? $session->id;

        /**
         * ==============================
         * 1️⃣ PERSISTENCIA (SOLO DB)
         * ==============================
         */
        try {

            DB::transaction(function () use ($idCita, $total, $referencia, $session) {

                Venta::updateOrCreate(
                    ['referencia_pago' => $referencia],
                    [
                        'id_cita' => $idCita,
                        'fecha_venta' => now(),
                        'total' => $total,
                        'forma_pago' => 'tarjeta_credito',
                        'estado_venta' => 'pagada',
                        'metodo_pago_especifico' => 'stripe_webhook',
                        'notas' => 'Confirmado vía webhook. session_id=' . ($session->id ?? ''),
                        'comision_empleado' => 0,
                    ]
                );

                Cita::where('id_cita', $idCita)
                    ->update([
                        'estado_cita' => Cita::ESTADO_CONFIRMADA,
                        'updated_at'  => now(),
                    ]);
            });

        } catch (\Throwable $e) {

            Log::error('Error DB en webhook', [
                'id_cita' => $idCita,
                'error'   => $e->getMessage(),
            ]);

            return response('Error DB', 200);
        }

        /**
         * ==============================
         * 2️⃣ ACCIONES POST-COMMIT
         * ==============================
         */

        try {

            $cita = Cita::with(['servicios','cliente','empleado'])
                ->find($idCita);

            if (!$cita) {
                return response('OK', 200);
            }

            /**
             * 🔹 Google Calendar (no bloqueante)
             */
            try {
                if ($cita->empleado_id) {
                    app(\App\Services\GoogleCalendarService::class)
                        ->createEventFromCita($cita);
                }
            } catch (\Throwable $e) {
                Log::error('Google Calendar error', [
                    'id_cita' => $idCita,
                    'error'   => $e->getMessage(),
                ]);
            }

            /**
             * 🔹 Correos (no bloqueante)
             */
            try {

                if ($cita->empleado?->email) {
                    Mail::to($cita->empleado->email)
                        ->send(new CitaConfirmadaMail($cita));
                }

                if ($cita->cliente?->email) {
                    Mail::to($cita->cliente->email)
                        ->send(new CitaConfirmadaMail($cita));
                }

            } catch (\Throwable $e) {
                Log::error('Mail error', [
                    'id_cita' => $idCita,
                    'error'   => $e->getMessage(),
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Post processing error', [
                'id_cita' => $idCita,
                'error'   => $e->getMessage(),
            ]);
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
