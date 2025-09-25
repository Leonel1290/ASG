<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\RESTful\ResourceController;

class ServoController extends ResourceController
{
    protected $dispositivoModel;
    private $apiValveBaseUrl = 'https://pwa-1s1m.onrender.com';
    private $apiValveKey = 'YOUR_SUPER_SECRET_API_KEY_HERE';

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    /**
     * Obtiene el estado actual de la válvula
     */
    public function obtenerEstado($mac)
    {
        // Decodificar la MAC si viene codificada en URL
        $mac = urldecode($mac);
        
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setJSON([
                'status' => 'success',
                'estado_valvula' => (bool)$dispositivo['estado_valvula'],
                'nivel_gas' => (float)$dispositivo['ultimo_nivel_gas']
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error', 
                'message' => 'Dispositivo no encontrado.'
            ]);
        }
    }

    /**
     * Actualiza el estado de la válvula y notifica al ESP32
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

        // Validaciones
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

        // Convertir a booleano
        $estado = (bool)$estado;

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error', 
                'message' => 'Dispositivo no encontrado.'
            ]);
        }

        try {
            $data = [
                'estado_valvula' => $estado,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->dispositivoModel->update($dispositivo['id'], $data);

            if ($updated) {
                // NOTIFICAR AL ESP32 mediante su API
                $this->notificarESP32($mac, $estado);
                
                return $this->response->setJSON([
                    'status' => 'success', 
                    'message' => 'Estado de válvula actualizado.', 
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
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Notifica al ESP32 del cambio de estado mediante su API
     */
    private function notificarESP32($mac, $estado)
    {
        try {
            $client = \Config\Services::curlrequest();
            
            $url = $this->apiValveBaseUrl . '/api/valve_status';
            $data = [
                'mac' => $mac,
                'api_key' => $this->apiValveKey,
                'estado' => $estado ? 1 : 0
            ];
            
            $response = $client->post($url, [
                'form_params' => $data,
                'timeout' => 5
            ]);
            
            log_message('info', "ESP32 notificado - MAC: {$mac}, Estado: " . ($estado ? 'Abierta' : 'Cerrada'));
            
        } catch (\Exception $e) {
            log_message('error', 'Error al notificar ESP32: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint para que el ESP32 consulte el estado actual
     */
    public function estadoValvula($mac)
    {
        $mac = urldecode($mac);
        $apiKey = $this->request->getGet('api_key');
        
        // Validar API key
        if ($apiKey !== $this->apiValveKey) {
            return $this->response->setStatusCode(401)->setBody('Unauthorized');
        }
        
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();
        
        if ($dispositivo) {
            return $this->response->setBody((string)(int)$dispositivo['estado_valvula']);
        } else {
            return $this->response->setStatusCode(404)->setBody('Dispositivo no encontrado');
        }
    }
}