// app/Services/Email.php (o donde esté tu clase Email)

<?php

namespace App\Services;

// Importar la clase SendGrid Mail
use SendGrid\Mail\Mail;

class Email
{
    /**
     * Envía un correo electrónico utilizando la API de SendGrid.
     *
     * @param string $to Correo del destinatario.
     * @param string $subject Asunto del correo.
     * @param string $body Contenido HTML del correo.
     * @return array Resultado del envío.
     */
    public function sendMail($to, $subject, $body)
    {
        // Obtiene la API Key directamente de la variable de entorno SENDGRID_API_KEY
        $apiKey = getenv('SENDGRID_API_KEY'); 

        // Si no hay API Key, retorna un error
        if (empty($apiKey)) {
            return [
                'status' => 'error',
                'message' => 'API Key de SendGrid no encontrada en el entorno.'
            ];
        }

        $email = new \SendGrid\Mail\Mail();

        // 🎯 MODIFICACIÓN: Obtener remitente y nombre desde el archivo .env
        // Si las variables de entorno no existen, usa los valores predeterminados anteriores.
        $fromEmail = getenv('SENDGRID_FROM_EMAIL') ?: "againsafegas.ascii@gmail.com";
        $fromName = getenv('SENDGRID_FROM_NAME') ?: "App Name";

        $email->setFrom($fromEmail, $fromName); // Usa las variables del .env
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/html", $body);

        $sendgrid = new \SendGrid($apiKey);
        try {
            $response = $sendgrid->send($email);

            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                return [
                    'status' => 'success',
                    'message' => 'Correo enviado exitosamente.',
                    'statusCode' => $response->statusCode()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Fallo al enviar el correo. Código: ' . $response->statusCode(),
                    'details' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'exception',
                'message' => 'Excepción de SendGrid: ' . $e->getMessage()
            ];
        }
    }
}