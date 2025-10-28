<?php

// El namespace 'Config' es típico de CodeIgniter para archivos de configuración,
// asumiendo que este archivo está en app/Config/Email.php
namespace Config; 

use CodeIgniter\Config\BaseConfig;
use SendGrid\Mail\Mail;
use Exception; // Importar la clase Exception

class Email extends BaseConfig
{
    /**
     * Función personalizada para enviar emails a través de la API de SendGrid.
     * Retorna un array: ['success' => bool, 'message' => string]
     *
     * @param array $datos ['email' => '...', 'asunto' => '...', 'mensaje' => '...']
     * @return array
     */
    public function enviarEmail($datos)
    {
        // 1. Obtener la API Key de la variable de entorno
        $apiKey = getenv('SENDGRID_API_KEY');
        if (!$apiKey) {
            return ['success' => false, 'message' => 'ERROR DE CONFIGURACIÓN: La variable de entorno SENDGRID_API_KEY no está definida.'];
        }
        
        $email = new Mail();
        
        // 🎯 Implementación Final del Remitente: Usa las variables de entorno
        // Fallback a los valores codificados si las variables de entorno no existen (aunque deberían)
        $fromEmail = getenv('SENDGRID_FROM_EMAIL') ?: "againsafegas.ascii@gmail.com";
        $fromName = getenv('SENDGRID_FROM_NAME') ?: "ASG - (Again Safe Gas)"; 
        
        $email->setFrom($fromEmail, $fromName); 
        
        $email->setSubject($datos['asunto']);
        $email->addTo($datos['email']);
        $email->addContent("text/html", $datos['mensaje']);

        // Instanciar SendGrid
        $sendgrid = new \SendGrid($apiKey);

        try {
            $response = $sendgrid->send($email);

            // 2. Revisar el código de estado de la respuesta de SendGrid
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                return ['success' => true]; // Éxito
            } else {
                // 3. Devolver el error específico de SendGrid (como el persistente 403)
                $status = $response->statusCode();
                $body = $response->body();
                
                $error_details = 'Respuesta de SendGrid. Código: ' . $status;
                
                // Intenta decodificar el cuerpo JSON para obtener el mensaje de error detallado de SendGrid
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