<?php

namespace App\Controllers;

use App\Models\ComprasModel;
use CodeIgniter\Controller;
use Config\Services;

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
     * Enviar correo de confirmación de compra al comprador
     */
    private function sendPurchaseEmail(string $to, array $data): void
    {
        try {
            $email = Services::email();

            $nombre = $data['nombre'] ?? 'Cliente';
            $monto = isset($data['monto']) ? number_format((float) $data['monto'], 2) : '0.00';
            $fecha = isset($data['fecha_compra']) ? date('d/m/Y H:i', strtotime($data['fecha_compra'])) : date('d/m/Y H:i');
            $orderId = $data['order_id'] ?? '';
            $paymentId = $data['payment_id'] ?? '';
            $status = $data['status'] ?? '';
            $emailCliente = $data['email'] ?? $to;

            $html = <<<HTML
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Confirmación de compra</title>
  </head>
  <body style="font-family:Arial,Helvetica,sans-serif;background:#f6f8fa;padding:24px;">
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
      <div style="background:#0d6efd;color:#fff;padding:16px 20px;">
        <h2 style="margin:0;font-size:18px;">ASG - Confirmación de compra</h2>
      </div>
      <div style="padding:20px;">
        <p style="margin:0 0 12px;">Hola {$nombre},</p>
        <p style="margin:0 0 16px;">Gracias por tu compra. A continuación encontrarás el detalle de la transacción:</p>

        <table role="presentation" cellspacing="0" cellpadding="8" style="width:100%;border-collapse:collapse;">
          <tbody>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;width:35%;">Nombre</td>
              <td style="border:1px solid #e5e7eb;">{$nombre}</td>
            </tr>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;">Email</td>
              <td style="border:1px solid #e5e7eb;">{$emailCliente}</td>
            </tr>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;">Orden (Order ID)</td>
              <td style="border:1px solid #e5e7eb;">{$orderId}</td>
            </tr>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;">Pago (Payment ID)</td>
              <td style="border:1px solid #e5e7eb;">{$paymentId}</td>
            </tr>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;">Estado</td>
              <td style="border:1px solid #e5e7eb;">{$status}</td>
            </tr>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;">Monto</td>
              <td style="border:1px solid #e5e7eb;">USD {$monto}</td>
            </tr>
            <tr>
              <td style="border:1px solid #e5e7eb;background:#f9fafb;font-weight:bold;">Fecha</td>
              <td style="border:1px solid #e5e7eb;">{$fecha}</td>
            </tr>
          </tbody>
        </table>

        <p style="margin:16px 0 0;color:#6b7280;font-size:12px;">Si no reconoces esta compra, contáctanos respondiendo a este correo.</p>
      </div>
      <div style="background:#f3f4f6;color:#374151;padding:12px 20px;font-size:12px;text-align:center;">
        © ASG. Todos los derechos reservados.
      </div>
    </div>
  </body>
</html>
HTML;

            $configEmail = config('Email');
            $email->setFrom($configEmail->fromEmail, $configEmail->fromName);
            $email->setTo($to);
            $email->setSubject('Confirmación de compra - ASG');
            $email->setMailType('html');
            $email->setMessage($html);

            log_message('debug', 'Intentando enviar email de compra a ' . $to . ' (order_id=' . $orderId . ')');

            if (!$email->send()) {
                // printDebugger can be verbose; limit sections
                $debug = method_exists($email, 'printDebugger') ? $email->printDebugger(['headers', 'subject']) : 'No debug info';
                log_message('error', 'Error enviando email de compra: ' . $debug);
            } else {
                log_message('debug', 'Email de compra enviado a ' . $to);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Excepción enviando email de compra: ' . $e->getMessage());
        }
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
            
            if (curl_error($ch)) {
                log_message('error', 'cURL Error capturing order: ' . curl_error($ch));
            }
            
            curl_close($ch);

            if ($httpCode !== 201) {
                log_message('error', 'PayPal Capture Order Error: HTTP ' . $httpCode . ' - Response: ' . $response);
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al capturar la orden en PayPal']);
            }

            $result = json_decode($response, true);
            log_message('debug', 'Orden capturada: ' . print_r($result, true));

            // ✅ Guardar en base de datos solo si fue exitoso
            if (isset($result['status']) && $result['status'] === "COMPLETED") {
                $purchaseUnit = $result['purchase_units'][0];
                $capture = $purchaseUnit['payments']['captures'][0];
                
                // Extraer nombre del pagador si está disponible
                $payerName = null;
                if (isset($result['payer']['name'])) {
                    $given = $result['payer']['name']['given_name'] ?? '';
                    $surname = $result['payer']['name']['surname'] ?? '';
                    $payerName = trim($given . ' ' . $surname) ?: null;
                }
                
                // Extraer email del pagador (si PayPal lo envía)
                $payerEmail = $result['payer']['email_address'] ?? null;

    $data = [
        'order_id'   => $result['id'],
        'payer_id'   => $result['payer']['payer_id'] ?? null,
        'payment_id' => $capture['id'] ?? null,
        'status'     => $result['status'],
        'monto'      => $capture['amount']['value'] ?? null,
        'nombre'     => $payerName,
        'email'      => $payerEmail,
        'fecha_compra' => date('Y-m-d H:i:s')
    ];
    
    log_message('debug', 'Datos a guardar: ' . print_r($data, true));
    
    try {
        $this->comprasModel->insert($data);
        log_message('debug', 'Compra guardada en BD con ID: ' . $this->comprasModel->getInsertID());

        $recipient = $payerEmail ?: (session()->get('email') ?? null);
        if (!empty($recipient)) {
            $this->sendPurchaseEmail($recipient, $data);
        } else {
            log_message('warning', 'No se envió email: email del pagador y email de sesión no disponibles.');
        }
    } catch (\Exception $e) {
        log_message('error', 'Error al guardar compra en BD: ' . $e->getMessage());
        // No devolvemos error para no afectar la experiencia del usuario
    }

    // ✅ MODIFICACIÓN: Devolver respuesta personalizada con payment_id
    $responseData = [
        'status' => 'COMPLETED',
        'payment_id' => $capture['id'] ?? null, // ← Esto es lo que necesita el frontend
        'order_id' => $result['id'],
        'payer_id' => $result['payer']['payer_id'] ?? null,
        'message' => 'Compra procesada exitosamente'
    ];
    
    log_message('debug', 'Enviando respuesta al frontend: ' . print_r($responseData, true));
    
    return $this->response->setJSON($responseData);
}

// Si no fue COMPLETED, devolver la respuesta original de PayPal
return $this->response->setJSON($result);

            
        } catch (\Exception $e) {
            log_message('error', 'Exception in captureOrder: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error interno del servidor']);
        }
    }
    public function guiaDeCompra()
{
    // Carga la vista guia_compra.php
    return view('guia_compra'); 
}
}