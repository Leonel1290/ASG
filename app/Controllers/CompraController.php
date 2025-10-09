<?php

namespace App\Controllers;

use App\Models\ComprasModel;
use CodeIgniter\Controller;

class CompraController extends Controller
{
    private $clientId     = "AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_";
    private $clientSecret = "EEOWwqaRKfgtQYKYReuEcNZrRJJuGcJBWaUlKrYmzPLu4f7zGjHovQ8l9T_xASTSq9lDCErw6vR-RxKb";
    private $paypalApiUrl = "https://api-m.sandbox.paypal.com";

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
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Accept-Language: en_US"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            log_message('error', 'cURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            log_message('error', 'PayPal Token Error: HTTP ' . $httpCode . ' - Response: ' . $response);
            return null;
        }

        $result = json_decode($response, true);
        return $result['access_token'] ?? null;
    }

    /**
     * ✅ Crear orden en PayPal
     */
    public function createOrder()
    {
        try {
            $token = $this->getAccessToken();

            if (!$token) {
                log_message('error', 'No se pudo obtener el token de acceso de PayPal');
                return $this->response->setStatusCode(500)->setJSON(['error' => 'No se pudo obtener el token de PayPal']);
            }

            $body = [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00"
                    ]
                ]],
                "application_context" => [
                    "return_url" => base_url('compra/completada'),
                    "cancel_url" => base_url('compra/cancelada')
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->paypalApiUrl . "/v2/checkout/orders");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token",
                "Content-Type: application/json",
                "Prefer: return=representation"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                log_message('error', 'cURL Error creating order: ' . curl_error($ch));
            }
            
            curl_close($ch);

            if ($httpCode !== 201) {
                log_message('error', 'PayPal Create Order Error: HTTP ' . $httpCode . ' - Response: ' . $response);
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al crear la orden en PayPal']);
            }

            $result = json_decode($response, true);
            log_message('debug', 'Orden creada: ' . print_r($result, true));
            
            return $this->response->setJSON($result);
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in createOrder: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * ✅ Capturar orden y guardar en BD (sin depender de usuario)
     */
    public function captureOrder($orderId)
{
    try {
        $token = $this->getAccessToken();

        if (!$token) {
            log_message('error', 'No se pudo obtener el token de acceso de PayPal para capturar orden');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'No se pudo obtener el token de PayPal']);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paypalApiUrl . "/v2/checkout/orders/{$orderId}/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            log_message('error', 'PayPal Capture Order Error: HTTP ' . $httpCode . ' - Response: ' . $response);
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al capturar la orden en PayPal']);
        }

        $result = json_decode($response, true);

        // ✅ Guardar en base de datos solo si fue exitoso
        if (isset($result['status']) && $result['status'] === "COMPLETED") {
            $purchaseUnit = $result['purchase_units'][0];
            $capture = $purchaseUnit['payments']['captures'][0];
            
            $data = [
                'order_id'   => $result['id'],
                'payer_id'   => $result['payer']['payer_id'] ?? null,
                'payment_id' => $capture['id'] ?? null,
                'status'     => $result['status'],
                'monto'      => $capture['amount']['value'] ?? null,
                'fecha_compra' => date('Y-m-d H:i:s')
            ];

            // Guarda en BD
            $this->comprasModel->insert($data);

            // ✅ Enviar correo al comprador
            $emailComprador = $result['payer']['email_address'] ?? null;
            if ($emailComprador) {
                $this->enviarCorreoConfirmacion($emailComprador, $data);
            }
        }

        return $this->response->setJSON($result);
        
    } catch (\Exception $e) {
        log_message('error', 'Exception in captureOrder: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Error interno del servidor']);
    }
}

/**
 * ✅ Envía correo de confirmación de compra al comprador
 */
private function enviarCorreoConfirmacion($emailComprador, $data)
{
    $email = \Config\Services::email();

    $email->setTo($emailComprador);
    $email->setSubject('Confirmación de tu compra - AgainSafeGas Sentinel');

    $mensaje = "
        <h2>¡Gracias por tu compra!</h2>
        <p>Tu pedido se ha procesado correctamente. A continuación los detalles de tu compra:</p>
        <ul>
            <li><strong>Producto:</strong> AgainSafeGas Sentinel</li>
            <li><strong>Orden ID:</strong> {$data['order_id']}</li>
            <li><strong>Monto pagado:</strong> USD {$data['monto']}</li>
            <li><strong>Fecha:</strong> {$data['fecha_compra']}</li>
        </ul>
        <hr>
        <h4>Información adicional:</h4>
        <ul>
            <li>📦 Envío: recibirás tu producto en un plazo de 3 a 5 días hábiles.</li>
            <li>🛠️ Garantía: 6 meses por defectos de fabricación.</li>
            <li>📞 Soporte: soporte@againsafegas.com</li>
            <li>🌐 Sitio web: <a href='" . base_url() . "'>AgainSafeGas</a></li>
        </ul>
        <p>Gracias por confiar en <strong>AgainSafeGas</strong>.<br>Tu seguridad, nuestra prioridad.</p>
    ";

    $email->setMessage($mensaje);

    if (!$email->send()) {
        log_message('error', '❌ Error al enviar correo: ' . $email->printDebugger(['headers']));
    } else {
        log_message('info', '✅ Correo de confirmación enviado a ' . $emailComprador);
    }
}
}