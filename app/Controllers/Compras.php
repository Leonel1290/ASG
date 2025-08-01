<?php namespace App\Controllers;

use App\Models\CompraModel;
use CodeIgniter\API\ResponseTrait;

class Compras extends BaseController
{
    use ResponseTrait;

    public function guardarCompra()
    {
        // Verificar si es una solicitud AJAX y POST
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->fail('Método no permitido', 400);
        }

        // Obtener datos del POST
        $data = $this->request->getJSON(true);
        
        // Validar datos
        if (empty($data['id_usuario']) || empty($data['monto']) || empty($data['direccion'])) {
            return $this->fail('Datos incompletos', 400);
        }

        // Preparar datos para la inserción
        $compra_data = [
            'id_usuario' => $data['id_usuario'],
            'monto' => $data['monto'],
            'moneda' => $data['moneda'] ?? 'USD',
            'direccion_envio' => $data['direccion'],
            'paypal_order_id' => $data['paypal_order_id'] ?? null,
            'estado_pago' => $data['estado_pago'] ?? 'completado',
            'fecha_compra' => date('Y-m-d H:i:s')
        ];

        // Insertar en la base de datos
        $model = new CompraModel();
        $compra_id = $model->crearCompra($compra_data);
        
        if ($compra_id) {
            return $this->respond([
                'success' => true,
                'message' => 'Compra registrada correctamente',
                'compra_id' => $compra_id
            ]);
        } else {
            return $this->fail('Error al guardar la compra', 500);
        }
    }

    public function misCompras()
    {
        // Verificar sesión
        if (!session()->has('user_id')) {
            return redirect()->to('login');
        }

        $model = new CompraModel();
        $data['compras'] = $model->obtenerComprasUsuario(session('user_id'));
        
        return view('header') . 
               view('mis_compras', $data) . 
               view('footer');
    }
}