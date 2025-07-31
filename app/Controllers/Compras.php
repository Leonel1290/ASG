<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ComprasModel;

class Compras extends BaseController
{
    use ResponseTrait;

    public function guardarCompra()

    // Verificar si el usuario está logueado
    if (!session()->has('user_id')) {
        return $this->failUnauthorized('Debe iniciar sesión para realizar una compra');
    }

    $input = $this->request->getJSON();

    // Validación básica
    if (empty($input->paypal_order_id) {
        return $this->fail('Datos de compra incompletos', 400);
    }

    $data = [
        'id_usuario' => session()->get('user_id'), // Obtenido de la sesión
        'monto' => $input->monto ?? 0,
        'moneda' => $input->moneda ?? 'USD',
        'paypal_order_id' => $input->paypal_order_id,
        'estado_pago' => $input->estado_pago ?? 'completado',
        'MAC_dispositivo' => null // Puedes asignarlo después
    ];

    $comprasModel = new ComprasModel();
    
    try {
        $comprasModel->insert($data);
        return $this->respondCreated([
            'success' => true,
            'message' => 'Compra registrada exitosamente'
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error al guardar compra: '.$e->getMessage());
        return $this->failServerError('Error al procesar la compra');
    }
}