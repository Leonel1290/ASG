<?php

namespace App\Controllers;

use App\Models\CompraModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class CompraController extends Controller
{
    public function createOrder()
    {
        $session = session();
        $userId = $session->get('id');

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Debes iniciar sesión antes de comprar.']);
        }

        $compraModel = new CompraModel();
        $orderID = uniqid('ORDER_');
        $payerID = uniqid('PAYER_');
        $paymentID = uniqid('PAY_');

        $data = [
            'usuario_id'   => $userId,
            'order_id'     => $orderID,
            'payer_id'     => $payerID,
            'payment_id'   => $paymentID,
            'status'       => 'COMPLETED',
            'monto'        => 100.00,
            'fecha_compra' => date('Y-m-d H:i:s')
        ];

        $compraModel->insert($data);

        // Obtener datos del usuario para el correo
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user) {
            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setFrom('againsafegas.ascii@gmail.com', 'ASG');
            $email->setSubject('Confirmación de compra - ASG');
            $mensaje = "
                <h2>Hola {$user['nombre']} {$user['apellido']}</h2>
                <p>Gracias por tu compra en <b>ASG</b>. Tu pedido fue completado exitosamente.</p>
                <p><b>Detalles:</b></p>
                <ul>
                    <li>Monto: <b>\${$data['monto']}</b></li>
                    <li>Fecha: <b>{$data['fecha_compra']}</b></li>
                    <li>ID de orden: <b>{$data['order_id']}</b></li>
                </ul>
                <p>Te contactaremos cuando tu dispositivo esté listo para la entrega.</p>
                <br>
                <p>Atentamente,<br>El equipo de <b>ASG</b>.</p>
            ";
            $email->setMessage($mensaje);
            $email->send();
        }

        return $this->response->setJSON(['success' => true, 'order_id' => $orderID]);
    }

    public function captureOrder($orderId)
    {
        return $this->response->setJSON(['success' => true, 'order_id' => $orderId]);
    }
}
