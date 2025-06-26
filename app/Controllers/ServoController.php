<?php

namespace App\Controllers;

use App\Models\ServoModel;
use App\Models\EnlaceModel;
use App\Models\DispositivoModel; // Asegúrate de que DispositivoModel está importado
use CodeIgniter\Controller;

class ServoController extends BaseController
{
    protected $ServoModel;
    protected $DispositivoModel;
    protected $EnlaceModel;

    public function __construct()
    {
        $this->ServoModel = new ServoModel();
        $this->DispositivoModel = new DispositivoModel();
        $this->EnlaceModel = new EnlaceModel();
    }

    // Método para mostrar los detalles del servo en la vista
    public function mostrarDetalles($mac = null)
    {
        $session = session();
        $data = []; // Inicializar array de datos para la vista

        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        if ($mac === null) {
            $data['dispositivo'] = null;
            $data['error_message'] = 'MAC de dispositivo no especificada.';
            return view('detalles', $data);
        }

        // Verificar que el usuario tiene permiso sobre este Servo
        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            $data['dispositivo'] = null;
            $data['error_message'] = 'No tienes permiso para ver este dispositivo.';
            return view('detalles', $data);
        }

        // Obtener los datos del dispositivo (donde está estado_valvula)
        $dispositivo = $this->DispositivoModel->getDispositivoByMac($mac);

        if ($dispositivo) {
            $data['dispositivo'] = $dispositivo;
            return view('detalles', $data);
        } else {
            $data['dispositivo'] = null;
            $data['error_message'] = 'Dispositivo no encontrado.';
            return view('detalles', $data);
        }
    }

    // ... (Mantén los métodos actualizarEstado y obtenerEstado como están)
    public function actualizarEstado()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        $actualizado = $this->DispositivoModel->updateDispositivoByMac($mac, ['estado_valvula' => $estado]);

        if ($actualizado) {
            return $this->response->setJSON(['success' => true, 'estado' => $estado]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar']);
        }
    }

    public function obtenerEstado($mac)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        $dispositivo = $this->DispositivoModel->getDispositivoByMac($mac);
        
        if ($dispositivo) {
            return $this->response->setJSON([
                'estado_valvula' => $dispositivo->estado_valvula, // Acceder como propiedad de objeto
                'nombre' => $dispositivo->nombre,
                'ubicacion' => $dispositivo->ubicacion
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Dispositivo no encontrado']);
        }
    }
}
