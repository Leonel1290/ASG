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
        $dispositivoId = $input->dispositivo_id ?? null;
        $usuarioId = $input->usuario_id ?? null;

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
                        "currency_code" => "USD",
                        "value" => $amount
                    ],
                    "custom_id" => json_encode([
                        'usuario_id' => $usuarioId,
                        'dispositivo_id' => $dispositivoId
                    ])
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
            $this->savePaymentToDatabase($orderID, $resultData);
        }

        return $this->response->setJSON($resultData);
    }

    private function savePaymentToDatabase($orderID, $paymentData)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();
            
            // Extraer datos del custom_id
            $customData = json_decode($paymentData['purchase_units'][0]['custom_id'] ?? '{}', true);
            $usuarioId = $customData['usuario_id'] ?? null;
            $dispositivoId = $customData['dispositivo_id'] ?? null;

            $data = [
                'order_id' => $orderID,
                'usuario_id' => $usuarioId,
                'dispositivo_id' => $dispositivoId,
                'email' => $paymentData['payer']['email_address'] ?? '',
                'monto' => $paymentData['purchase_units'][0]['amount']['value'] ?? 0,
                'moneda' => $paymentData['purchase_units'][0]['amount']['currency_code'] ?? 'USD',
                'fecha' => date('Y-m-d H:i:s'),
                'estado' => strtolower($paymentData['status']),
                'detalles' => json_encode($paymentData)
            ];

            $db->table('pagos')->insert($data);
            
            // Si hay un dispositivo asociado, actualizar su estado
            if ($dispositivoId) {
                $db->table('dispositivos')
                  ->where('id', $dispositivoId)
                  ->update(['estado_dispositivo' => 'asignado']);
            }
            
            $db->transComplete();
            
            if(!$db->transStatus()) {
                log_message('error', 'Error al guardar pago en BD: ' . print_r($db->error(), true));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Excepción al guardar pago: ' . $e->getMessage());
        }
    }

    private function getAccessToken()
    {
        $url = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
        $credentials = base64_encode("$this->clientId:$this->clientSecret");

        $options = [
            "http" => [
                "header" => "Authorization: Basic $credentials\r\n" .
                            "Content-Type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => "grant_type=client_credentials"
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result, true)["access_token"] ?? null;
    }
}