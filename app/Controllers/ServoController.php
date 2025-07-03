<?php
namespace App\Controllers;
use App\Models\DispositivoModel;
use CodeIgniter\RESTful\ResourceController;

class ServoController extends ResourceController
{
    public function obtenerEstado($mac)
    {
        $model = new DispositivoModel();
        $dispositivo = $model->where('MAC', $mac)->first();
        if ($dispositivo) {
            return $this->response->setJSON(['estado_valvula' => (int)$dispositivo['estado_valvula']]);
        } else {
            return $this->response->setJSON(['error' => 'Dispositivo no encontrado']);
        }
    }

    public function actualizarEstado()
    {
        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');
        $model = new DispositivoModel();
        $dispositivo = $model->where('MAC', $mac)->first();
        if ($dispositivo) {
            $model->where('MAC', $mac)->set('estado_valvula', $estado)->update();
            return $this->response->setJSON(['estado' => (int)$estado]);
        } else {
            return $this->response->setJSON(['error' => 'Dispositivo no encontrado']);
        }
    }
}
?>