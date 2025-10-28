<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use SendGrid\Mail\Mail; 

class Email extends BaseConfig
{
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
    // ESTOS VALORES SON SÓLO DEFENSA/DEFECTO.
    // SERÁN SOBRESCRITOS POR LA CONFIGURACIÓN DE TU ARCHIVO .env
    
    // Eliminado getenv() para evitar el error 'Constant expression contains invalid operations'.
    public string $fromEmail  = ''; 
    public string $fromName   = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     * Dejar en blanco o 'localhost'. El valor de .env lo sobrescribirá.
     */
    public string $SMTPHost = ''; 

    /**
     * SMTP Username
     * Dejar en blanco para que el .env lo controle.
     */
    public string $SMTPUser = ''; 

    /**
     * SMTP Password
     * ¡CRÍTICO! DEBE ESTAR VACÍO ('') AQUÍ para no exponer la clave en Git.
     */
    public string $SMTPPass = ''; 

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587; 

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 60;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     */
    public string $SMTPCrypto = 'tls'; 

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = true;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Set to true to use Delivery Status Notification
     */
    public bool $DSN = false;
}