<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class PayPalController extends Controller
{
    private $clientId = "AdGS2GrGBbZXq41yYDW2A-0dVD5avVuWiQO-XQDVAOxMepuO0HmkCL6kFfwIbLLjIc0gT9tB3KmIL0hJ";
    private $clientSecret = "ENwZmSdEKvlXWlybPNngQbhf1KZhN9S_1bVV3lfJbtTF1oc0waa3RxYjImQaeeafjMKQe48pbJM07A";

    private function getAccessToken()
    {
        $url = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ":" . $this->clientSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Accept-Language: es_ES",
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', 'cURL Error for getAccessToken: ' . $err);
            return null;
        }

        $data = json_decode($response, true);
        return $data["access_token"] ?? null;
    }

    public function createOrder()
    {
        $input = $this->request->getJSON();
        $amount = $input->amount ?? "10.00";

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return $this->response->setJSON(["error" => "No se pudo obtener el token de acceso de PayPal"])->setStatusCode(500);
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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', 'cURL Error for createOrder: ' . $err);
            return $this->response->setJSON(["error" => "Error de cURL al comunicarse con PayPal"])->setStatusCode(500);
        }
        
        $resultData = json_decode($response, true);
        
        // Loguear la respuesta completa de PayPal para depuración
        log_message('info', 'PayPal createOrder API Response: ' . $response);
        
        if (!isset($resultData['id'])) {
            // Si la respuesta no contiene un ID de orden, algo salió mal
            log_message('error', 'PayPal createOrder response did not contain an ID. Full response: ' . print_r($resultData, true));
            return $this->response->setJSON(["error" => "La creación de la orden falló. Verifique sus credenciales de PayPal."])->setStatusCode(500);
        }

        return $this->response->setJSON($resultData);
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
            return $this->response->setJSON(["error" => "No se pudo obtener el token de acceso de PayPal"])->setStatusCode(500);
        }

        $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID/capture";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', 'cURL Error for captureOrder: ' . $err);
            return $this->response->setJSON(["error" => "Error de cURL al capturar la orden de PayPal"])->setStatusCode(500);
        }

        $resultData = json_decode($response, true);
        
        if(isset($resultData['status']) && $resultData['status'] === 'COMPLETED') {
            $this->savePaymentToDatabase($resultData);
        }

        return $this->response->setJSON($resultData);
    }
    
    private function savePaymentToDatabase($paymentData)
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('pagos_paypal');
    
            $data = [
                'order_id' => $paymentData['id'],
                'payer_id' => $paymentData['payer']['payer_id'],
                'payer_email' => $paymentData['payer']['email_address'],
                'amount_value' => $paymentData['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                'currency_code' => $paymentData['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
                'payment_status' => $paymentData['status']
            ];
            
            $builder->insert($data);

        } catch (\Exception $e) {
            log_message('error', 'Database Error in savePaymentToDatabase: ' . $e->getMessage());
        }
    }
}