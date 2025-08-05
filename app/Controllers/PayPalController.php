<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class PayPalController extends Controller
{
    private $clientId = "AdGS2GrGBbZXq41yYDW2A-0dVD5avVuWiQO-XQDVAOxMepuO0HmkCL6kFfwIbLLjIc0gT9tB3KmIL0hJ";
    private $clientSecret = "ENwZmSdEKvlXWlybPNngQbhf1KZhN9S_1bVV3lfJbtTF1oc0waa3RxYjImQaeeafjMKQe48pbJM07A";

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
                        "currency_code" => "USD", // Asegúrate de que esta moneda coincida con la del cliente si es ARS, como indicas en el HTML.
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
            $this->savePaymentToDatabase($orderID, $resultData);
        }

        // Enviar email de confirmación
        // Asegúrate de que tu servicio de correo esté configurado y funcionando.
        if(isset($resultData['payer']['email_address'])) {
            $email = $resultData['payer']['email_address'];
            // Esto asume que tienes un método 'sendEmail' configurado en Services.php
            // Si no, necesitarás cargar una librería de correo o configurarlo.
            // Por ejemplo, con el Email Library de CodeIgniter 4:
            $emailService = Services::email();
            $emailService->setTo($email);
            $emailService->setSubject('¡Gracias por comprar AgainSafeGas!');
            $emailService->setMessage("<h1>Su compra ha sido cargada en nuestro sistema<br><br>Cuando reciba el producto, ya podrá disfrutar de todas las funciones de AgainSafeGas</h1>");
            $emailService->send();
        }

        return $this->response->setJSON($resultData);
    }

    private function savePaymentToDatabase($orderID, $paymentData)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Asegúrate de que estas claves existan antes de intentar acceder a ellas
            $payerEmail = $paymentData['payer']['email_address'] ?? 'unknown@example.com';
            $amountValue = $paymentData['purchase_units'][0]['amount']['value'] ?? '0.00';
            $currencyCode = $paymentData['purchase_units'][0]['amount']['currency_code'] ?? 'USD';
            $paymentStatus = strtolower($paymentData['status'] ?? 'pending');

            $data = [
                'order_id' => $orderID,
                'email' => $payerEmail,
                'monto' => $amountValue,
                'moneda' => $currencyCode,
                'fecha' => date('Y-m-d H:i:s'),
                'estado' => $paymentStatus,
                'detalles' => json_encode($paymentData) // Guarda todos los detalles de la respuesta de PayPal
            ];

            $db->table('pagos')->insert($data);

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