<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ComprasModel;

class Compras extends BaseController
{
    use ResponseTrait;

    public function guardarCompra()
    {
        $input = $this->request->getJSON();

        if (empty($input->id_usuario) || empty($input->monto) || empty($input->moneda) || empty($input->paypal_order_id)) {
            return $this->fail('Datos incompletos para registrar la compra.', 400);
        }

        $comprasModel = new ComprasModel();

        $data = [
            'id_usuario' => $input->id_usuario,
            'MAC_dispositivo' => $input->MAC_dispositivo, // Puede ser nulo
            'monto' => $input->monto,
            'moneda' => $input->moneda,
            'paypal_order_id' => $input->paypal_order_id,
            'estado_pago' => $input->estado_pago // 'completado' o 'fallido' desde el frontend
        ];

        try {
            $comprasModel->insert($data);
            return $this->respondCreated(['success' => true, 'message' => 'Compra registrada exitosamente.']);
        } catch (\Exception $e) {
            log_message('error', 'Error al insertar compra: ' . $e->getMessage());
            return $this->failServerError('Error al registrar la compra: ' . $e->getMessage());
        }
    }
}