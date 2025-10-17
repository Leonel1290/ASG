<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\RESTful\ResourceController;

// Extiende de ResourceController para mantener la estructura de API
class ValveController extends ResourceController
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    // ====================================================================
    // ⚙️ MÉTODOS DE CONTROL PRINCIPALES (POST /valve/control) ⚙️
    // ====================================================================

    /**
     * Función principal para controlar la válvula (abrir/cerrar) desde la página web.
     */
    public function controlValve()
    {
        $action = $this->request->getPost('action');
        $mac = $this->request->getPost('mac');

        if (empty($mac)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'MAC del dispositivo no proporcionada.'
                ]);
            }
        }

        $estado = ($action === 'open') ? 1 : 0; 
        $updated = $this->dispositivoModel->updateDispositivoByMac($mac, [
            'estado_valvula' => $estado,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Válvula ' . ($action === 'open' ? 'abierta' : 'cerrada') . ' correctamente.',
                    'new_state' => $estado
                ]);
            } else {
                return redirect()->to('/detalles/' . $mac)->with('success', 'Válvula ' . ($action === 'open' ? 'abierta' : 'cerrada') . ' correctamente.');
            }
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar el estado de la válvula.'
            ]);
        }
    }

    public function actualizarEstado()
    {
        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        if ($mac === null || !in_array($estado, ['0', '1'])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'MAC o estado no válidos.']);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            $updated = $this->dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estado])->update();

            if ($updated) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Estado de válvula actualizado.', 'nuevo_estado' => (int)$estado]);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al actualizar la base de datos.']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }
    
    public function obtenerEstadoSimple()
    {
        $mac = $this->request->getGet('mac');
        $apiKey = $this->request->getGet('api_key');
        
        $API_KEY_EXPECTED = 'SUPER_SECRET_API_MLUS'; 

        // 1. -4: Verificar parámetros
        if (empty($mac) || empty($apiKey)) {
            $this->response->setBody("-4")->setStatusCode(200)->send();
            exit; // ⬅️ CRÍTICO: Asegura que solo se envíe "-4"
        }

        // 2. -2: Verificar clave API
        if ($apiKey !== $API_KEY_EXPECTED) {
            $this->response->setBody("-2")->setStatusCode(200)->send();
            exit; // ⬅️ CRÍTICO: Asegura que solo se envíe "-2"
        }

        // 3. Consultar DB
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            $estado = (string)$dispositivo->estado_valvula;
            $this->response->setBody($estado)->setStatusCode(200)->send();
            exit; // ⬅️ CRÍTICO: Asegura que solo se envíe "1" o "0"
        } else {
            // -1: Dispositivo no encontrado
            $this->response->setBody("-1")->setStatusCode(200)->send();
            exit; // ⬅️ CRÍTICO: Asegura que solo se envíe "-1"
        }
    }
}