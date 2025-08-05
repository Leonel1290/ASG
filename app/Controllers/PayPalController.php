<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class PayPalController extends Controller
{
    private $clientId = "AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_";
    private $clientSecret = "EEOWwqaRKfgtQYKYReuEcNZrRJJuGcJBWaUlKrYmzPLu4f7zGjHovQ8l9T_xASTSq9lDCErw6vR-RxKb";

    public function createOrder()
    {
        $input = $this->request->getJSON();
        $amount = $input->amount ?? "10.00";

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return $this->response->setJSON(["error" => "No se pudo obtener el token"])->setStatusCode(500);
        }

        $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders";
        $body = json_encode([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "ARS",
                        "value" => $amount
                    ]
                ]
            ]
        ]);

        $options = [
            "http" => [
                "header" => "Authorization: Bearer $accessToken\r\n" .
                            "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => $body
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $this->response->setJSON(json_decode($result, true));
    }

    public function captureOrder()
    {
        $input = $this->request->getJSON();
        $orderID = $input->orderID ?? null;

        if (!$orderID) {
            return $this->response->setJSON(["error" => "No se recibió un Order ID"])->setStatusCode(400);
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return $this->response->setJSON(["error" => "No se pudo obtener el token"])->setStatusCode(500);
        }

        $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID/capture";
        $options = [
            "http" => [
                "header" => "Authorization: Bearer $accessToken\r\n" .
                            "Content-Type: application/json\r\n",
                "method" => "POST"
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $resultData = json_decode($result, true);

        // Guardar en base de datos si el pago fue exitoso
        if(isset($resultData['status']) && $resultData['status'] === 'COMPLETED') {
            $this->savePaymentToDatabase($resultData);
        }

        return $this->response->setJSON($resultData);
    }
    
    // Función para guardar el pago en la base de datos
    private function savePaymentToDatabase($paymentData)
    {
        // Obtener la instancia de la base de datos de CodeIgniter
        $db = \Config\Database::connect();
        $builder = $db->table('pagos_paypal');

        // Extraer los datos relevantes de la respuesta de PayPal
        $data = [
            'order_id' => $paymentData['id'],
            'payer_id' => $paymentData['payer']['payer_id'],
            'payer_email' => $paymentData['payer']['email_address'],
            'amount_value' => $paymentData['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
            'currency_code' => $paymentData['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
            'payment_status' => $paymentData['status']
        ];
        
        // Insertar los datos en la tabla
        $builder->insert($data);
    }

    private function getAccessToken()
    {
        $url = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
        $headers = [
            "Accept: application/json",
            "Accept-Language: es_ES",
            "Content-Type: application/x-www-form-urlencoded"
        ];
        $auth = base64_encode($this->clientId . ":" . $this->clientSecret);

        $options = [
            "http" => [
                "header" => array_merge($headers, ["Authorization: Basic $auth"]),
                "method" => "POST",
                "content" => "grant_type=client_credentials"
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        $data = json_decode($response, true);
        return $data["access_token"] ?? null;
    }
}