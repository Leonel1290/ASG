<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel; // Asegúrate de que tu modelo DispositivoModel esté configurado correctamente para interactuar con tu base de datos.

class ValveController extends Controller
{
    private const DEBUG_SENSOR_DATA = true;

    // El método receiveSensorData puede permanecer si aún quieres que el ESP32 reporte el nivel de gas al servidor.
    // Su función ahora sería solo de monitoreo, no de control de la válvula.
    public function receiveSensorData()
    {
        // --- INICIO DE LÍNEA DE DEPURACIÓN CRÍTICA ---
        // Lee el cuerpo crudo de la solicitud POST.
        $raw_post_data = file_get_contents('php://input');
        log_message('debug', 'receiveSensorData: Raw POST Data: ' . $raw_post_data);
        // --- FIN DE LÍNEA DE DEPURACIÓN CRÍTICA ---

        $model = new DispositivoModel();
        // Usar getPost() para form-urlencoded data del ESP32
        $mac = $this->request->getPost('MAC'); // El ESP32 envía 'MAC'
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
            // Opcional: Crear un nuevo dispositivo si no existe.
            $model->insert([
                'mac' => $mac,
                'nombre' => 'ESP32_Nuevo_' . substr($mac, -5),
                'ultimo_nivel_gas' => $nivelGas,
                'estado_valvula' => 0 // Por defecto, la válvula cerrada para un nuevo dispositivo
            ]);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Dispositivo creado y datos de sensor guardados.'
            ])->setStatusCode(201);
        }

        // Actualizar solo el nivel de gas del dispositivo existente.
        $model->update($dispositivo['id_dispositivo'], [
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

    // El método getValveState permanece igual, ya que es el que el ESP32 consulta.
    // MODIFICADO: Ahora también devuelve 'ultimo_nivel_gas'.
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
            // Por seguridad, si el dispositivo no existe, asumimos que la válvula debe estar cerrada.
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Dispositivo no encontrado, estado por defecto: cerrado.',
                'estado_valvula' => 0, // 0 = Cerrado
                'ultimo_nivel_gas' => 0 // También se devuelve 0 para el gas si no existe el dispositivo
            ]);
        }

        log_message('debug', 'getValveState: Devolviendo estado de válvula ' . $dispositivo['estado_valvula'] . ' y nivel de gas ' . $dispositivo['ultimo_nivel_gas'] . ' para mac: ' . $mac);
        return $this->response->setJSON([
            'status' => 'success',
            'estado_valvula' => (int)$dispositivo['estado_valvula'],
            'ultimo_nivel_gas' => (int)$dispositivo['ultimo_nivel_gas'] // ¡Esta línea es la adición clave!
        ]);
    }

    // Endpoint para control directo de válvula desde la PWA.
    // Permanece esperando JSON.
    public function controlValve()
    {
        $model = new DispositivoModel();

        // **PASO CLAVE 1: Autenticación del usuario de la PWA**
        $session = \Config\Services::session();
        $userId = $session->get('id_usuario');

        if (!$userId) {
            log_message('error', 'controlValve: Acceso denegado. Usuario no autenticado.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no autenticado.'
            ])->setStatusCode(401); // Unauthorized
        }

        // Obtener los datos del cuerpo de la solicitud JSON de la PWA.
        $json = $this->request->getJSON();
        $mac = $json->mac ?? null;
        $action = $json->action ?? null; // 'open' o 'close'

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

        $valveUpdateStatus = (int)$dispositivo['estado_valvula']; // Estado actual en DB
        $message = "";

        if ($action === 'open') {
            $valveUpdateStatus = 1; // 1 = Abrir
            $message = 'Válvula comandada a abrir manualmente.';
            log_message('info', 'controlValve: ' . $message . ' para mac: ' . $mac);
        } elseif ($action === 'close') {
            $valveUpdateStatus = 0; // 0 = Cerrar
            $message = 'Válvula comandada a cerrar manualmente.';
            log_message('info', 'controlValve: ' . $message . ' para mac: ' . $mac);
        } else {
            log_message('error', 'controlValve: Acción de válvula no válida: ' . $action);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Acción de válvula no válida.'
            ])->setStatusCode(400);
        }

        // Actualizar el estado deseado de la válvula en la base de datos.
        // El ESP32 leerá este estado y ajustará su servo.
        $model->update($dispositivo['id_dispositivo'], [
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
