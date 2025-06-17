<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel;

class ValveController extends Controller
{
    // Umbral de gas SEGURO para permitir la APERTURA de la válvula.
    // Si el 'ultimo_nivel_gas' es MENOR o IGUAL a este valor, se considera seguro abrir.
    // ¡AJUSTA ESTE VALOR SEGÚN LAS LECTURAS DE TU SENSOR MQ6 EN AMBIENTE SEGURO!
    private const OPEN_VALVE_SAFE_THRESHOLD = 50; 

    // Mensaje de depuración para ver los datos recibidos del ESP32.
    // En un entorno de producción, considera deshabilitarlo o enviarlo a un log.
    private const DEBUG_SENSOR_DATA = true;

    // -------------------------------------------------------------------------
    // Endpoint para que el ESP32 envíe las lecturas del sensor de gas.
    // El ESP32 solo INFORMA el nivel de gas aquí; no se toma ninguna acción directa sobre la válvula.
    // -------------------------------------------------------------------------
    public function receiveSensorData()
    {
        $model = new DispositivoModel();

        // Obtener la mac y el nivel de gas del cuerpo de la solicitud POST.
        // Asegúrate de que el ESP32 envíe estos datos como application/x-www-form-urlencoded.
        $mac = $this->request->getPost('mac');
        $nivelGas = $this->request->getPost('nivel_gas');

        if (self::DEBUG_SENSOR_DATA) {
            log_message('debug', "receiveSensorData: mac recibida: " . $mac . ", Nivel de Gas: " . $nivelGas);
        }

        if (!$mac || $nivelGas === null) {
            log_message('error', 'receiveSensorData: Datos incompletos recibidos.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Datos incompletos.'
            ])->setStatusCode(400); // Bad Request
        }

        // Buscar el dispositivo por mac. Si no existe, puedes crearlo o devolver un error.
        $dispositivo = $model->where('mac', $mac)->first();

        if (!$dispositivo) {
            log_message('warning', 'receiveSensorData: Dispositivo no encontrado con mac: ' . $mac . '. Creando nuevo dispositivo.');
            // Opcional: Crear un nuevo dispositivo si no existe.
            $model->insert([
                'mac' => $mac,
                'nombre' => 'ESP32_Nuevo_' . substr($mac, -5), // Nombre por defecto
                'ultimo_nivel_gas' => $nivelGas,
                'estado_valvula' => 0 // Por defecto, la válvula cerrada para un nuevo dispositivo
            ]);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Dispositivo creado y datos de sensor guardados.'
            ])->setStatusCode(201); // Created
        }

        // Actualizar solo el nivel de gas del dispositivo existente.
        // La última vez que se reportó el gas.
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

    // -------------------------------------------------------------------------
    // Endpoint para que el ESP32 consulte el estado deseado de su válvula.
    // La PWA actualiza este estado, y el ESP32 lo consulta periódicamente.
    // -------------------------------------------------------------------------
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
                'estado_valvula' => 0 // 0 = Cerrado
            ]);
        }

        // Devuelve el 'estado_valvula' almacenado en la base de datos.
        // Este estado es el que el usuario deseó a través de la PWA.
        log_message('debug', 'getValveState: Devolviendo estado de válvula ' . $dispositivo['estado_valvula'] . ' para mac: ' . $mac);
        return $this->response->setJSON([
            'status' => 'success',
            'estado_valvula' => (int)$dispositivo['estado_valvula']
        ]);
    }

    // -------------------------------------------------------------------------
    // Endpoint para que la PWA controle la válvula (abrir/cerrar).
    // Aquí se aplica la lógica de seguridad del gas para la acción de "abrir".
    // -------------------------------------------------------------------------
    public function controlValve()
    {
        $model = new DispositivoModel();

        // **PASO CLAVE 1: Autenticación del usuario de la PWA**
        // Este código verifica si hay un usuario autenticado en la sesión.
        // Si la PWA no tiene un sistema de login o no está manteniendo la sesión,
        // este será el origen de tu "Usuario no autenticado."
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

        // **PASO CLAVE 2: Lógica de Control y Seguridad**
        $currentGasLevel = $dispositivo['ultimo_nivel_gas'];
        $valveUpdateStatus = $dispositivo['estado_valvula']; // Estado actual en DB, por si no se cambia

        if ($action === 'open') {
            // **¡AQUÍ ES DONDE LA LÓGICA DE SEGURIDAD DEL GAS ENTRA EN JUEGO!**
            // Solo permitir abrir si el nivel de gas es seguro (menor o igual al umbral).
            if ($currentGasLevel <= self::OPEN_VALVE_SAFE_THRESHOLD) {
                $valveUpdateStatus = 1; // 1 = Abrir
                $message = 'Válvula comandada a abrir. Nivel de gas seguro (' . $currentGasLevel . ').';
                log_message('info', 'controlValve: ' . $message . ' para mac: ' . $mac);
            } else {
                // Si el gas no es seguro, NO se permite abrir la válvula.
                $message = 'No se puede abrir la válvula. Nivel de gas (' . $currentGasLevel . ') excede el umbral de seguridad (' . self::OPEN_VALVE_SAFE_THRESHOLD . ').';
                log_message('warning', 'controlValve: ' . $message . ' para mac: ' . $mac);
                return $this->response->setJSON([
                    'status' => 'warning',
                    'message' => $message,
                    'current_gas_level' => $currentGasLevel
                ])->setStatusCode(403); // Forbidden
            }
        } elseif ($action === 'close') {
            // **La acción de CERRAR es siempre permitida, independientemente del nivel de gas.**
            $valveUpdateStatus = 0; // 0 = Cerrar
            $message = 'Válvula comandada a cerrar.';
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
            'new_valve_state' => $valveUpdateStatus,
            'current_gas_level' => $currentGasLevel // Incluir el nivel de gas para feedback en PWA
        ]);
    }
}