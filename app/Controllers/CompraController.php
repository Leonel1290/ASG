<?php

namespace App\Controllers;

use App\Models\ComprasModel;
use CodeIgniter\Controller;

class CompraController extends Controller
{
    private $clientId     = "AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_";
    private $clientSecret = "EEOWwqaRKfgtQYKYReuEcNZrRJJuGcJBWaUlKrYmzPLu4f7zGjHovQ8l9T_xASTSq9lDCErw6vR-RxKb";
    private $paypalApiUrl = "https://api-m.sandbox.paypal.com"; // Sandbox

    protected $comprasModel;

    public function __construct()
    {
        $this->comprasModel = new ComprasModel();
    }

    /**
     * ✅ Obtener Access Token de PayPal
     */
    private function getAccessToken()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->paypalApiUrl . "/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ":" . $this->clientSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        return $result['access_token'] ?? null;
    }

    /**
     * ✅ Crear orden en PayPal
     */
    public function createOrder()
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'No se pudo obtener el token de PayPal']);
        }

        $body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => "100.00"
                ]
            ]]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paypalApiUrl . "/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $response = curl_exec($ch);
        curl_close($ch);

        return $this->response->setJSON(json_decode($response, true));
    }

    /**
     * ✅ Capturar orden y guardar en BD
     */
    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'No se pudo obtener el token de PayPal']);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paypalApiUrl . "/v2/checkout/orders/{$orderId}/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        // ✅ Guardar en base de datos solo si fue exitoso
        if (isset($result['status']) && $result['status'] === "COMPLETED") {
            $purchaseUnit = $result['purchase_units'][0];

            $this->comprasModel->insert([
                'usuario_id' => session()->get('id') ?? null,
                'order_id'   => $result['id'],
                'payer_id'   => $result['payer']['payer_id'] ?? null,
                'payment_id' => $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                'status'     => $result['status'],
                'monto'      => $purchaseUnit['amount']['value'] ?? null,
            ]);
        }

        return $this->response->setJSON($result);
    }
}
