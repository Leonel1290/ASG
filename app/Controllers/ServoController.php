<?php
namespace App\Controllers;

use App\Models\ServoModel;
use CodeIgniter\Controller;

class ServoController extends BaseController
{
    protected $ServoModel;

    public function __construct()
    {
        $this->ServoModel = new ServoModel();
    }

    // Actualizar estado del servo
    public function actualizarEstado()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        // Verificar que el usuario tiene permiso sobre este Servo
        $enlaceModel = new \App\Models\EnlaceModel();
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                  ->where('MAC', $mac)
                                  ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        // Actualizar estado en la base de datos
        $actualizado = $this->ServoModel->updateEstadoValvula($mac, $estado);

        if ($actualizado) {
            return $this->response->setJSON(['success' => true, 'estado' => $estado]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar']);
        }
    }

    // Obtener estado actual del servo
    public function obtenerEstado($mac)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        // Verificar que el usuario tiene permiso sobre este Servo
        $enlaceModel = new \App\Models\EnlaceModel();
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                  ->where('MAC', $mac)
                                  ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        $Servo = $this->ServoModel->getServoByMac($mac);
        
        if ($Servo) {
            return $this->response->setJSON([
                'estado_valvula' => $Servo['estado_valvula'],
                'nombre' => $Servo['nombre'],
                'ubicacion' => $Servo['ubicacion']
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Servo no encontrado']);
        }
    }
}