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
     * Obtener Access Token de PayPal
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
            log_message('error', 'cURL Error (getAccessToken): ' . curl_error($ch));
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
     * Crear orden en PayPal
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
            ]],
            "application_context" => [
                "return_url" => base_url('compra/completada'),
                "cancel_url" => base_url('compra/cancelada')
                // opcional: "shipping_preference" => "GET_FROM_FILE" o "NO_SHIPPING"
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
            log_message('error', 'cURL Error (createOrder): ' . curl_error($ch));
        }

        curl_close($ch);

        if ($httpCode !== 201) {
            log_message('error', 'PayPal Create Order Error: HTTP ' . $httpCode . ' - Response: ' . $response);
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al crear la orden en PayPal']);
        }

        $result = json_decode($response, true);
        log_message('debug', 'Orden creada: ' . print_r($result, true));
        return $this->response->setJSON($result);
    }

    /**
     * Capturar orden, guardar en BD (incluye nombre de usuario) y enviar correo
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
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            log_message('error', 'cURL Error (captureOrder): ' . curl_error($ch));
        }

        curl_close($ch);

        if ($httpCode !== 201) {
            log_message('error', 'PayPal Capture Order Error: HTTP ' . $httpCode . ' - Response: ' . $response);
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al capturar la orden en PayPal']);
        }

        $result = json_decode($response, true);
        log_message('debug', 'Orden capturada: ' . print_r($result, true));

        if (isset($result['status']) && $result['status'] === "COMPLETED") {
            $purchaseUnit = $result['purchase_units'][0] ?? null;
            $capture = $purchaseUnit['payments']['captures'][0] ?? null;

            // --- Extraer nombre del comprador desde PayPal (varias posibilidades) ---
            $nombreUsuario = null;

            if (isset($result['payer']['name'])) {
                $payerName = $result['payer']['name'];
                // PayPal puede devolver given_name / surname
                $given = $payerName['given_name'] ?? '';
                $surname = $payerName['surname'] ?? '';
                $full = $payerName['full_name'] ?? null; // algunas respuestas pueden traer full_name
                $nombreUsuario = trim($given . ' ' . $surname);
                if (empty($nombreUsuario) && !empty($full)) {
                    $nombreUsuario = $full;
                }
            }

            // Si no vino del payer.name, intentar buscar en shipping (pocas veces)
            if (empty($nombreUsuario) && isset($purchaseUnit['shipping']['name'])) {
                $shipName = $purchaseUnit['shipping']['name'];
                $nombreUsuario = $shipName['full_name'] ?? ($shipName['full_name'] ?? null);
            }

            // Fallback: intentar guardar nombre desde sesión (si el usuario está logueado)
            $session = session();
            if (empty($nombreUsuario)) {
                $nombreUsuario = $session->get('nombre') ?? null;
            }

            // Último fallback: usar el email del payer como nombre identificador
            $emailPayer = $result['payer']['email_address'] ?? null;
            if (empty($nombreUsuario)) {
                $nombreUsuario = $emailPayer ?? 'Usuario';
            }

            // Loguear para depuración si quedó vacío o fue tomado por fallback
            if (isset($result['payer']['name'])) {
                log_message('info', 'Nombre obtenido desde PayPal: ' . $nombreUsuario);
            } else {
                log_message('warning', 'Nombre no provisto por PayPal. Usando fallback: ' . $nombreUsuario);
            }

            // --- Preparar datos para BD ---
            $data = [
                'order_id'       => $result['id'],
                'payer_id'       => $result['payer']['payer_id'] ?? null,
                'payment_id'     => $capture['id'] ?? null,
                'status'         => $result['status'],
                'monto'          => $capture['amount']['value'] ?? null,
                'fecha_compra'   => date('Y-m-d H:i:s'),
                'nombre_usuario' => $nombreUsuario
            ];

            try {
                $this->comprasModel->insert($data);
                log_message('info', 'Compra guardada en BD con ID: ' . $this->comprasModel->getInsertID());
            } catch (\Exception $e) {
                log_message('error', 'Error al guardar compra en BD: ' . $e->getMessage());
                // seguimos para intentar enviar correo
            }

            // --- Enviar correo de confirmación si existe email del payer (fallback a correo de prueba si querés) ---
            $emailComprador = $emailPayer ?? null;
            if ($emailComprador) {
                $this->enviarCorreoConfirmacion($emailComprador, $data);
            } else {
                log_message('warning', 'No se encontró email del comprador en la respuesta de PayPal. No se envió correo.');
            }
        }

        return $this->response->setJSON($result);
    }

    /**
     * Enviar correo de confirmación
     */
    private function enviarCorreoConfirmacion($emailComprador, $data)
    {
        $email = \Config\Services::email();

        // Desde Email.php ya está la configuración (SMTP, from, etc).
        // Asegurarse de setear explícitamente el from y mailType:
        $email->setFrom('againsafegas.ascii@gmail.com', 'ASG');
        $email->setTo($emailComprador);
        $email->setMailType('html');
        $email->setSubject('Confirmación de tu compra - AgainSafeGas Sentinel');

        $mensaje = "
            <div style='font-family:Arial,Helvetica,sans-serif;color:#222;'>
                <h2>¡Gracias por tu compra, " . htmlspecialchars($data['nombre_usuario']) . "!</h2>
                <p>Detalles de tu compra:</p>
                <ul>
                    <li><strong>Producto:</strong> AgainSafeGas Sentinel</li>
                    <li><strong>Orden ID:</strong> " . htmlspecialchars($data['order_id']) . "</li>
                    <li><strong>Monto pagado:</strong> USD " . htmlspecialchars($data['monto']) . "</li>
                    <li><strong>Fecha:</strong> " . htmlspecialchars($data['fecha_compra']) . "</li>
                </ul>
                <hr>
                <h4>Información adicional:</h4>
                <ul>
                    <li>📦 Envío: recibirás tu producto en 3 a 5 días hábiles.</li>
                    <li>🛠️ Garantía: 6 meses por defectos de fabricación.</li>
                    <li>📞 Soporte: soporte@againsafegas.com</li>
                    <li>🌐 Sitio web: <a href='" . base_url() . "'>AgainSafeGas</a></li>
                </ul>
                <p>Gracias por confiar en <strong>AgainSafeGas</strong>.</p>
            </div>
        ";

        $email->setMessage($mensaje);

        if (!$email->send()) {
            log_message('error', '❌ Error al enviar correo: ' . $email->printDebugger(['headers']));
        } else {
            log_message('info', '✅ Correo de confirmación enviado a ' . $emailComprador);
        }
    }
}
