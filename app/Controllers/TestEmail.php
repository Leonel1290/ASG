<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestEmail extends Controller
{
    public function index()
    {
        // Forzar la configuración desde variables de entorno
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $_ENV['email.SMTPHost'] ?? 'smtp.gmail.com',
            'SMTPUser' => $_ENV['email.SMTPUser'] ?? 'againsafegas.ascii@gmail.com',
            'SMTPPass' => $_ENV['email.SMTPPass'] ?? 'ywbn dvza fiew hcir',
            'SMTPPort' => $_ENV['email.SMTPPort'] ?? 587,
            'SMTPCrypto' => $_ENV['email.SMTPCrypto'] ?? 'tls',
            'mailType' => 'html',
            'charset' => 'UTF-8'
        ];
        
        $email = \Config\Services::email($config);
        
        try {
            $email->setTo('againsafegas.ascii@gmail.com');
            $email->setSubject('Prueba SMTP - ' . date('Y-m-d H:i:s'));
            $email->setMessage('
                <h2>Configuración usada:</h2>
                <ul>
                    <li>Host: ' . $config['SMTPHost'] . '</li>
                    <li>Puerto: ' . $config['SMTPPort'] . '</li>
                    <li>Crypto: ' . $config['SMTPCrypto'] . '</li>
                    <li>Usuario: ' . $config['SMTPUser'] . '</li>
                </ul>
            ');
            
            if ($email->send()) {
                echo '✅ Email enviado exitosamente!';
                echo '<br>📧 Revisa tu bandeja de entrada y spam.';
            } else {
                echo '❌ Error al enviar:';
                echo '<pre>';
                echo $email->printDebugger(['headers']);
                echo '</pre>';
            }
        } catch (\Exception $e) {
            echo '❌ Excepción: ' . $e->getMessage();
        }
    }
    
    public function testConnection()
    {
        // Prueba de conexión SMTP básica
        $socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
        
        if ($socket) {
            echo '✅ Conexión SMTP exitosa a smtp.gmail.com:587';
            fclose($socket);
        } else {
            echo '❌ No se pudo conectar a SMTP: ' . $errstr . ' (' . $errno . ')';
        }
    }
}