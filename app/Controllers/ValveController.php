<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel;

class ValveController extends Controller
{
    private const DEBUG_SENSOR_DATA = true;

    public function receiveSensorData()
    {
        $raw_post_data = file_get_contents('php://input');
        log_message('debug', 'receiveSensorData: Raw POST Data: ' . $raw_post_data);

        $model = new DispositivoModel();
        $mac = $this->request->getPost('MAC'); // ESP32 envía 'MAC'
        $nivelGas = $this->request->getPost('nivel_gas');

        if (self::DEBUG_SENSOR_DATA) {
            log_message('debug', "receiveSensorData: mac recibida: " . $mac . ", Nivel de Gas: " . $nivelGas);
        }

        if (!$mac || $nivelGas === null) {
            log_message('error', 'receiveSensorData: Datos incompletos recibidos.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Datos incompletos.'
            ])->setStatusCode(400);
        }

        $dispositivo = $model->where('mac', $mac)->first();

        if (!$dispositivo) {
            log_message('warning', 'receiveSensorData: Dispositivo no encontrado con mac: ' . $mac . '. Creando nuevo dispositivo.');
            $model->insert([
                'mac' => $mac,
                'nombre' => 'ESP32_Nuevo_' . substr($mac, -5),
                'ultimo_nivel_gas' => $nivelGas,
                'estado_valvula' => 0
            ]);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Dispositivo creado y datos de sensor guardados.'
            ])->setStatusCode(201);
        }

        $model->update($dispositivo->id, [
            'ultimo_nivel_gas' => $nivelGas,
            'ultima_actualizacion_gas' => date('Y-m-d H:i:s')
        ]);

        if (self::DEBUG_SENSOR_DATA) {
            log_message('debug', 'receiveSensorData: Nivel de gas actualizado para mac ' . $mac . ': ' . $nivelGas);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Datos de sensor guardados correctamente.'
        ]);
    }

    public function getValveState($mac)
    {
        $model = new DispositivoModel();

        if (!$mac) {
            log_message('error', 'getValveState: mac Address no proporcionada.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'mac Address requerida.'
            ])->setStatusCode(400);
        }

        $dispositivo = $model->where('mac', $mac)->first();

        if (!$dispositivo) {
            log_message('warning', 'getValveState: Dispositivo no encontrado con mac: ' . $mac);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Dispositivo no encontrado, estado por defecto: cerrado.',
                'estado_valvula' => 0,
                'ultimo_nivel_gas' => 0
            ]);
        }

        log_message('debug', 'getValveState: Devolviendo estado de válvula ' . $dispositivo->estado_valvula . ' y nivel de gas ' . $dispositivo->ultimo_nivel_gas . ' para mac: ' . $mac);
        return $this->response->setJSON([
            'status' => 'success',
            'estado_valvula' => (int)$dispositivo->estado_valvula,
            'ultimo_nivel_gas' => (int)$dispositivo->ultimo_nivel_gas
        ]);
    }

    public function controlValve()
    {
        $model = new DispositivoModel();

        $session = \Config\Services::session();

        // --- INICIO DE LOGGING DEPURACIÓN DE SESIÓN ---
        log_message('debug', 'controlValve: Iniciando verificación de sesión.');
        log_message('debug', 'controlValve: Sesión activa: ' . ($session->has('id_usuario') ? 'Sí' : 'No'));
        log_message('debug', 'controlValve: Contenido completo de la sesión: ' . json_encode($session->get()));

        $userId = $session->get('id_usuario');
        log_message('debug', 'controlValve: Valor de $userId obtenido: ' . ($userId ?? 'NULL'));
        // --- FIN DE LOGGING DEPURACIÓN DE SESIÓN ---

        if (!$userId) {
            log_message('error', 'controlValve: Acceso denegado. Usuario no autenticado.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no autenticado.'
            ])->setStatusCode(401);
        }

        $json = $this->request->getJSON();
        $mac = $json->mac ?? null;
        $action = $json->action ?? null;

        if (!$mac || !$action) {
            log_message('error', 'controlValve: Datos incompletos desde la PWA. mac o acción faltante.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'mac Address o acción requerida.'
            ])->setStatusCode(400);
        }

        $dispositivo = $model->where('mac', $mac)->first();

        if (!$dispositivo) {
            log_message('error', 'controlValve: Dispositivo no encontrado con mac: ' . $mac);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo no encontrado.'
            ])->setStatusCode(404);
        }

        $valveUpdateStatus = (int)$dispositivo->estado_valvula;
        $message = "";

        if ($action === 'open') {
            $valveUpdateStatus = 1;
            $message = 'Válvula comandada a abrir manualmente.';
            log_message('info', 'controlValve: ' . $message . ' para mac: ' . $mac);
        } elseif ($action === 'close') {
            $valveUpdateStatus = 0;
            $message = 'Válvula comandada a cerrar manualmente.';
            log_message('info', 'controlValve: ' . $message . ' para mac: ' . $mac);
        } else {
            log_message('error', 'controlValve: Acción de válvula no válida: ' . $action);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Acción de válvula no válida.'
            ])->setStatusCode(400);
        }

        $model->update($dispositivo->id, [
            'estado_valvula' => $valveUpdateStatus,
            'ultima_actualizacion_valvula' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'new_valve_state' => $valveUpdateStatus
        ]);
    }
}
