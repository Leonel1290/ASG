<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestEmail extends Controller
{
    public function index()
    {
        $email = \Config\Services::email();
        
        $email->setTo('againsafegas.ascii@gmail.com'); // Cambia por tu email de prueba
        $email->setSubject('Prueba de email desde Render - ' . date('Y-m-d H:i:s'));
        $email->setMessage('
            <h1>¡Prueba exitosa! 🎉</h1>
            <p>El email se envió correctamente desde Render.</p>
            <p><strong>Fecha:</strong> ' . date('Y-m-d H:i:s') . '</p>
            <p><strong>Servidor:</strong> ' . $_SERVER['HTTP_HOST'] . '</p>
        ');
        
        if ($email->send()) {
            echo '✅ Email enviado exitosamente a: againsafegas.ascii@gmail.com';
            echo '<br>📧 Revisa la bandeja de entrada y spam.';
        } else {
            echo '❌ Error al enviar el email:';
            echo '<pre>';
            echo $email->printDebugger();
            echo '</pre>';
        }
    }
    
    public function simpleTest()
    {
        $email = \Config\Services::email();
        $email->setTo('againsafegas.ascii@gmail.com');
        $email->setSubject('Prueba simple');
        $email->setMessage('Este es un mensaje de prueba simple.');
        
        if ($email->send()) {
            return '✅ Email simple enviado correctamente';
        } else {
            return '❌ Error: ' . $email->printDebugger();
        }
    }
}