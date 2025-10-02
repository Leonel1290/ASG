<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use CodeIgniter\Controller;

class ServoController extends BaseController
{
    protected $dispositivoModel;
    protected $enlaceModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel();
        helper(['form', 'url']);
    }

    /**
     * Interfaz principal para control de válvulas
     */
    public function index()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');
        
        // Obtener dispositivos enlazados al usuario
        $dispositivos = $this->enlaceModel
            ->select('dispositivos.*')
            ->join('dispositivos', 'dispositivos.MAC = enlace.MAC')
            ->where('enlace.id_usuario', $usuarioId)
            ->findAll();

        return view('servo/index', [
            'dispositivos' => $dispositivos
        ]);
    }

    /**
     * Abrir válvula - API para frontend
     */
    public function abrir()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error', 
                'message' => 'No autorizado'
            ]);
        }

        $mac = $this->request->getPost('mac');
        
        if (!$mac) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 
                'message' => 'MAC requerida'
            ]);
        }

        // Verificar que el usuario tiene acceso a este dispositivo
        $acceso = $this->enlaceModel
            ->where('id_usuario', $session->get('id'))
            ->where('MAC', $mac)
            ->first();

        if (!$acceso) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error', 
                'message' => 'No tienes acceso a este dispositivo'
            ]);
        }

        // Actualizar estado en la base de datos - usar update directo
        $result = $this->dispositivoModel
            ->where('MAC', $mac)
            ->set([
                'estado_valvula' => 0,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Válvula abierta',
                'estado_valvula' => 0
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'Error al abrir válvula'
            ]);
        }
    }

    /**
     * Cerrar válvula - API para frontend
     */
    public function cerrar()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error', 
                'message' => 'No autorizado'
            ]);
        }

        $mac = $this->request->getPost('mac');
        
        if (!$mac) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 
                'message' => 'MAC requerida'
            ]);
        }

        // Verificar que el usuario tiene acceso a este dispositivo
        $acceso = $this->enlaceModel
            ->where('id_usuario', $session->get('id'))
            ->where('MAC', $mac)
            ->first();

        if (!$acceso) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error', 
                'message' => 'No tienes acceso a este dispositivo'
            ]);
        }

        // Actualizar estado en la base de datos - usar update directo
        $result = $this->dispositivoModel
            ->where('MAC', $mac)
            ->set([
                'estado_valvula' => 1,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Válvula cerrada',
                'estado_valvula' => 1
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'Error al cerrar válvula'
            ]);
        }
    }

    /**
     * Obtener estado actual de la válvula - API para frontend
     */
    public function obtenerEstado($mac)
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error', 
                'message' => 'No autorizado'
            ]);
        }

        // Verificar que el usuario tiene acceso a este dispositivo
        $acceso = $this->enlaceModel
            ->where('id_usuario', $session->get('id'))
            ->where('MAC', $mac)
            ->first();

        if (!$acceso) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error', 
                'message' => 'No tienes acceso a este dispositivo'
            ]);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setJSON([
                'status' => 'success',
                'estado_valvula' => (int)$dispositivo->estado_valvula,
                'nivel_gas' => (float)$dispositivo->ultimo_nivel_gas,
                'ultima_actualizacion' => $dispositivo->ultima_actualizacion
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error', 
                'message' => 'Dispositivo no encontrado'
            ]);
        }
    }

    /**
     * Actualizar estado de la válvula - API para ESP32
     * Esta es la API que consulta tu código Python
     */
    public function obtenerEstadoValvulaPlano()
    {
        $mac = $this->request->getGet('mac');
        $apiKey = $this->request->getGet('api_key');

        // Validar API key
        $validApiKey = 'SUPER_SECRET_API_MLUS'; // Debe coincidir con tu código ESP32
        if ($apiKey !== $validApiKey) {
            return $this->response->setStatusCode(401)->setBody('0');
        }

        if (!$mac) {
            return $this->response->setStatusCode(400)->setBody('0');
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Devolver solo el estado como texto plano (0 o 1)
            return $this->response->setBody((string)$dispositivo->estado_valvula);
        } else {
            return $this->response->setStatusCode(404)->setBody('0');
        }
    }

    /**
     * Actualizar estado desde el frontend
     */
    public function actualizarEstado()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error', 
                'message' => 'No autorizado'
            ]);
        }

        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');
        
        if (!$mac || $estado === null) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 
                'message' => 'Datos incompletos'
            ]);
        }

        // Verificar acceso
        $acceso = $this->enlaceModel
            ->where('id_usuario', $session->get('id'))
            ->where('MAC', $mac)
            ->first();

        if (!$acceso) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error', 
                'message' => 'No tienes acceso a este dispositivo'
            ]);
        }

        // Actualizar estado
        $result = $this->dispositivoModel
            ->where('MAC', $mac)
            ->set([
                'estado_valvula' => (int)$estado,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Estado actualizado',
                'estado_valvula' => (int)$estado
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'Error al actualizar estado'
            ]);
        }
    }
}