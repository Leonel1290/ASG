<?php

namespace App\Controllers;

use App\Models\ServoModel;
use App\Models\EnlaceModel;
use App\Models\DispositivoModel; // <-- Importar DispositivoModel
use CodeIgniter\Controller;

class ServoController extends BaseController
{
    protected $ServoModel;
    protected $DispositivoModel; // <-- Declarar DispositivoModel
    protected $EnlaceModel; // <-- Declarar EnlaceModel

    public function __construct()
    {
        $this->ServoModel = new ServoModel();
        $this->DispositivoModel = new DispositivoModel(); // <-- Instanciar DispositivoModel
        $this->EnlaceModel = new EnlaceModel(); // <-- Instanciar EnlaceModel
    }

    // Nuevo método para mostrar los detalles del servo en la vista
    public function mostrarDetalles($mac = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        if ($mac === null) {
            return redirect()->to('/')->with('error', 'MAC de dispositivo no especificada.');
        }

        // Verificar que el usuario tiene permiso sobre este Servo
        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            return redirect()->to('/dashboard')->with('error', 'No tienes permiso para ver este dispositivo.');
        }

        // Obtener los datos del dispositivo (donde está estado_valvula)
        $dispositivo = $this->DispositivoModel->getDispositivoByMac($mac);

        if ($dispositivo) {
            $data['dispositivo'] = $dispositivo;
            return view('detalles', $data);
        } else {
            return redirect()->to('/dashboard')->with('error', 'Dispositivo no encontrado.');
        }
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
        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        // AHORA: Actualizar 'estado_valvula' en la tabla 'dispositivos' usando DispositivoModel
        // Se ha cambiado de updateEstadoValvula a updateDispositivoByMac
        $actualizado = $this->DispositivoModel->updateDispositivoByMac($mac, ['estado_valvula' => $estado]);

        // También puedes querer actualizar el 'estado_servo' en la tabla 'servos' si es diferente
        // $this->ServoModel->updateEstadoServo($mac, $estado); // Si tu tabla servos tiene 'estado_servo'

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
        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        // Obtener el dispositivo de la tabla 'dispositivos' para obtener 'estado_valvula', 'nombre', 'ubicacion'
        $dispositivo = $this->DispositivoModel->getDispositivoByMac($mac);
        
        if ($dispositivo) {
            return $this->response->setJSON([
                'estado_valvula' => $dispositivo['estado_valvula'], // Usar datos de 'dispositivos'
                'nombre' => $dispositivo['nombre'],
                'ubicacion' => $dispositivo['ubicacion']
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Dispositivo no encontrado']);
        }
    }
}
