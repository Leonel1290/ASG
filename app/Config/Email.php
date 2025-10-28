<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use SendGrid\Mail\Mail; 

class Email extends BaseConfig
{
    /**
     * Función personalizada para enviar emails a través de la API de SendGrid.
     * Retorna un array: ['success' => bool, 'message' => string]
     */
    public function enviarEmail($datos)
    {
        // 1. Verificar datos mínimos
        if (!isset($datos['asunto']) || !isset($datos['email']) || !isset($datos['mensaje'])) {
            return ['success' => false, 'message' => 'Faltan datos esenciales para el email (asunto, destinatario o mensaje).'];
        }

        $apiKey = getenv('SENDGRID_API_KEY');
        if (!$apiKey) {
            // Error de configuración si la clave no está
            return ['success' => false, 'message' => 'ERROR DE CONFIGURACIÓN: La variable de entorno SENDGRID_API_KEY no está definida.'];
        }
        
        $email = new Mail();
        // IMPORTANTE: Asegúrate de que este correo esté verificado en SendGrid
        $email->setFrom("juancruzalv95@gmail.com", "App Name"); 
        
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
                if (!empty($body)) {
                    $json_body = json_decode($body, true);
                    // Intenta obtener el mensaje de error si está disponible en el JSON de respuesta
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
    
    // ... (Mantener las propiedades de configuración SMTP restantes: $fromEmail, $protocol, $SMTPHost, etc.) ...

    public string $fromEmail  = ''; 
    public string $fromName   = '';
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';
    public string $protocol = 'smtp';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost = ''; 
    public string $SMTPUser = ''; 
    public string $SMTPPass = ''; 
    public int $SMTPPort = 587; 
    public int $SMTPTimeout = 60;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls'; 
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType = 'html';
    public string $charset = 'UTF-8';
    public bool $validate = true;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public bool $DSN = false;
}