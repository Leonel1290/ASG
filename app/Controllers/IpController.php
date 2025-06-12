<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\DispositivoModel;

class IpController extends BaseController
{
    use ResponseTrait;

    public function updateIp()
    {
        $input = $this->request->getJSON();
        
        // Validación básica
        if (!isset($input->mac) {
            return $this->failValidationError('MAC es requerida');
        }
        
        if (!filter_var($input->ip ?? null, FILTER_VALIDATE_IP)) {
            return $this->failValidationError('IP inválida');
        }

        // Actualizar en base de datos
        $model = new DispositivoModel();
        $updated = $model->where('MAC', $input->mac)
                        ->set([
                            'ip_local' => $input->ip,
                            'ultima_conexion' => date('Y-m-d H:i:s')
                        ])->update();

        if ($updated === false) {
            return $this->failServerError('Error al actualizar IP');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'IP actualizada correctamente',
            'updated_rows' => $updated
        ]);
    }
}