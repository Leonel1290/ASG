<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\RESTful\ResourceController;

class ServoController extends ResourceController
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    /**
     * Obtiene el estado actual de la válvula
     */
    public function obtenerEstado($mac)
    {
        try {
            $mac = urldecode($mac);
            
            $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

            if ($dispositivo) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'estado_valvula' => (bool)$dispositivo['estado_valvula']
                ]);
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error', 
                    'message' => 'Dispositivo no encontrado.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'Error interno del servidor.'
            ]);
        }
    }

    /**
     * Actualiza el estado de la válvula (solo control manual)
     */
    public function actualizarEstado()
    {
        // Verificar CSRF token
        if (!csrf_filter()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error', 
                'message' => 'Token CSRF inválido.'
            ]);
        }

        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        // Validaciones básicas
        if (empty($mac)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 
                'message' => 'MAC no proporcionada.'
            ]);
        }

        if ($estado === null) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 
                'message' => 'Estado no proporcionado.'
            ]);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error', 
                'message' => 'Dispositivo no encontrado.'
            ]);
        }

        try {
            $estado = (bool)$estado;
            
            $data = [
                'estado_valvula' => $estado,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->dispositivoModel->update($dispositivo['id'], $data);

            if ($updated) {
                return $this->response->setJSON([
                    'status' => 'success', 
                    'message' => 'Válvula ' . ($estado ? 'abierta' : 'cerrada') . ' correctamente.', 
                    'estado' => $estado
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error', 
                    'message' => 'Error al actualizar el estado de la válvula.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'Error interno del servidor.'
            ]);
        }
    }
}