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
            // Si el dispositivo no existe, lo insertamos.
            $model->insert([
                'mac' => $mac,
                'nombre' => 'ESP32_Nuevo_' . substr($mac, -5),
                'ultimo_nivel_gas' => $nivelGas,
                'estado_valvula' => 0 // Por defecto, la válvula cerrada
            ]);
            // Después de insertar, el dispositivo no está en la variable $dispositivo actual.
            // Si la lógica continúa y se necesita el ID del dispositivo recién creado para otra cosa,
            // se debería hacer un nuevo `->where('mac', $mac)->first()` o usar el ID devuelto por insert().
            // Pero como la lógica aquí termina con un 'return', no es un problema.
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Dispositivo creado y datos de sensor guardados.'
            ])->setStatusCode(201);
        }

        // --- CORRECCIÓN CLAVE AQUÍ: Asumiendo que la clave primaria es 'id' ---
        // Si tu columna de clave primaria es 'id_dispositivo' y estás seguro,
        // entonces esto apunta a que tu modelo no está configurado para devolver
        // esa columna. Pero la mayoría de las veces, la clave primaria es 'id'.
        $model->update($dispositivo->id, [ // CAMBIO: de $dispositivo->id_dispositivo a $dispositivo->id
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
        $userId = $session->get('id_usuario');

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

        // --- CORRECCIÓN CLAVE AQUÍ: Asumiendo que la clave primaria es 'id' ---
        $model->update($dispositivo->id, [ // CAMBIO: de $dispositivo->id_dispositivo a $dispositivo->id
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
