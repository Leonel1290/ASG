<?php
namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\Controller;

class ServoController extends BaseController
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
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

        // Verificar que el usuario tiene permiso sobre este dispositivo
        $enlaceModel = new \App\Models\EnlaceModel();
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                  ->where('MAC', $mac)
                                  ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este dispositivo']);
        }

        // Actualizar estado en la base de datos
        $actualizado = $this->dispositivoModel->updateEstadoValvula($mac, $estado);

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

        // Verificar que el usuario tiene permiso sobre este dispositivo
        $enlaceModel = new \App\Models\EnlaceModel();
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                  ->where('MAC', $mac)
                                  ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este dispositivo']);
        }

        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        
        if ($dispositivo) {
            return $this->response->setJSON([
                'estado_valvula' => $dispositivo['estado_valvula'],
                'nombre' => $dispositivo['nombre'],
                'ubicacion' => $dispositivo['ubicacion']
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Dispositivo no encontrado']);
        }
    }
}