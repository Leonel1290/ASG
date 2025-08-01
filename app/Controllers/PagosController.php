<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PagosPaypalModel;

class PagosController extends BaseController
{
    public function registrarPagoPaypal()
    {
        helper(['url', 'session']);
        $session = session();
        $usuario_id = $session->get('usuario_id');

        $paymentId = $this->request->getGet('paymentId');
        $payerId = $this->request->getGet('PayerID');

        // Datos del cliente PayPal Sandbox
        $clientId = 'AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_';
        $secret = 'EEOWwqaRKfgtQYKYReuEcNZrRJJuGcJBWaUlKrYmzPLu4f7zGjHovQ8l9T_xASTSq9lDCErw6vR-RxKb';

        // Obtener token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Accept-Language: en_US",
        ]);
        $result = curl_exec($ch);
        $token = json_decode($result)->access_token;
        curl_close($ch);

        // Obtener detalles del pago
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/payments/payment/$paymentId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $token"
        ]);
        $paymentDetails = curl_exec($ch);
        curl_close($ch);

        $payment = json_decode($paymentDetails);
        $monto = $payment->transactions[0]->amount->total ?? 0;
        $moneda = $payment->transactions[0]->amount->currency ?? 'ARS';
        $estado = $payment->state ?? 'unknown';

        // Guardar en la base de datos
        $pagoModel = new PagosPaypalModel();
        $pagoModel->insert([
            'usuario_id' => $usuario_id,
            'payer_id' => $payerId,
            'payment_id' => $paymentId,
            'monto' => $monto,
            'moneda' => $moneda,
            'estado' => $estado
        ]);

        return view('pago_exitoso', ['monto' => $monto]);
    }
}
