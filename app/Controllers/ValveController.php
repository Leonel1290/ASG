<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\DispositivoModel;
use Config\Services;

class ValveController extends BaseController
{
    use ResponseTrait;

    public function controlValve()
    {
        // Validar entrada
        $input = $this->request->getJSON();
        if (!$input || !isset($input->mac) {
            return $this->failValidationError('Se requiere MAC del dispositivo');
        }

        $mac = $input->mac;
        $command = $input->command ?? null;
        
        if (!in_array($command, ['open', 'close'])) {
            return $this->failValidationError('Comando inválido (use open/close)');
        }

        // Obtener dispositivo
        $model = new DispositivoModel();
        $device = $model->where('MAC', $mac)->first();
        
        if (!$device) {
            return $this->failNotFound('Dispositivo no encontrado');
        }

        // Actualizar estado en DB
        $newState = ($command === 'open') ? 1 : 0;
        $model->update($device['id'], ['estado_valvula' => $newState]);

        // Si no tiene IP, retornar solo éxito en DB
        if (empty($device['ip_local'])) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Estado actualizado en DB (sin IP local)',
                'device_state' => $newState
            ]);
        }

        // Enviar comando al dispositivo
        $client = Services::curlrequest();
        try {
            $response = $client->post("http://{$device['ip_local']}/valve", [
                'json' => ['command' => $command],
                'timeout' => 3,
                'connect_timeout' => 3
            ]);
            
            $deviceResponse = json_decode($response->getBody(), true);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Comando ejecutado en dispositivo',
                'device_response' => $deviceResponse,
                'device_state' => $newState
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error control válvula: ' . $e->getMessage());
            
            return $this->respond([
                'status' => 'partial_success',
                'message' => 'Estado actualizado pero error en dispositivo: ' . $e->getMessage(),
                'device_state' => $newState
            ], 207); // Código 207 Multi-Status
        }
    }
}