<?php

namespace Config; // NOTA: Si tu clase está en App\Services, cambia este namespace

use CodeIgniter\Config\BaseConfig;
use SendGrid\Mail\Mail;
use Exception; // Importar la clase Exception

class Email extends BaseConfig
{
    /**
     * Función personalizada para enviar emails a través de la API de SendGrid.
     * Retorna un array: ['success' => bool, 'message' => string]
     */
    public function enviarEmail($datos)
    {
        // 1. Obtener la API Key de la variable de entorno
        $apiKey = getenv('SENDGRID_API_KEY');
        if (!$apiKey) {
            return ['success' => false, 'message' => 'ERROR DE CONFIGURACIÓN: La variable de entorno SENDGRID_API_KEY no está definida.'];
        }
        
        $email = new Mail();
        
        // 🎯 CORRECCIÓN FINAL DEL REMITENTE: Usar las variables de entorno para el nombre y email.
        $fromEmail = getenv('SENDGRID_FROM_EMAIL') ?: "againsafegas.ascii@gmail.com";
        $fromName = getenv('SENDGRID_FROM_NAME') ?: "App Name";
        
        $email->setFrom($fromEmail, $fromName); 
        
        $email->setSubject($datos['asunto']);
        $email->addTo($datos['email']);
        $email->addContent("text/html", $datos['mensaje']);

        $sendgrid = new \SendGrid($apiKey);

        try {
            $response = $sendgrid->send($email);

            // 2. Revisar el código de estado de la respuesta de SendGrid
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                return ['success' => true]; // Éxito
            } else {
                // 3. Devolver el error específico de SendGrid
                $status = $response->statusCode();
                $body = $response->body();
                
                $error_details = 'Respuesta de SendGrid. Código: ' . $status;
                
                // Intenta decodificar el cuerpo JSON para obtener el mensaje de error de SendGrid
                if (!empty($body)) {
                    $json_body = json_decode($body, true);
                    if (isset($json_body['errors'][0]['message'])) {
                         $error_details .= ' - Mensaje: ' . $json_body['errors'][0]['message'];
                    } else {
                        $error_details .= ' - Cuerpo: ' . $body;
                    }
                }
                
                return ['success' => false, 'message' => 'FALLO DE ENVÍO: ' . $error_details];
            }
        } catch (Exception $e) {
            // 4. Capturar errores de conexión o librería
            return ['success' => false, 'message' => 'Excepción en el envío: ' . $e->getMessage()];
        }
    }
}