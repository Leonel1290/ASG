<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;

/**
 * Nombre del archivo: ValveController.php
 * Ubicación: app/Controllers/ValveController.php
 *
 * Este controlador maneja la lógica para el control de la válvula de gas,
 * la recepción de lecturas de sensores y la consulta del estado del dispositivo.
 * Actúa como la API principal para la comunicación entre la PWA (interfaz de usuario)
 * y los microcontroladores ESP32.
 */
class ValveController extends Controller
{
    // Define los umbrales de gas aquí.
    // Usaremos OPEN_VALVE_SAFE_THRESHOLD para determinar si es seguro abrir.
    private const OPEN_VALVE_SAFE_THRESHOLD = 50; // Ejemplo: Si el nivel de gas es 50 o menos, es seguro abrir.
    private const CLOSE_VALVE_THRESHOLD_ALARM = 200; // Podría usarse para cerrar automáticamente o activar alarma.

    /**
     * Maneja el comando de abrir o cerrar la válvula para un dispositivo específico.
     * Recibe la MAC del dispositivo y el comando ('open' o 'close') vía JSON POST.
     * Este método es llamado por la página web.
     *
     * Reglas:
     * - La válvula SIEMPRE puede ser cerrada.
     * - La válvula SOLO puede ser abierta si el 'ultimo_nivel_gas' es igual o menor a OPEN_VALVE_SAFE_THRESHOLD.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function controlValve()
    {
        $input = $this->request->getJSON();
        $mac = $input->mac ?? null;
        $command = $input->command ?? null;

        // Validar que la MAC y el comando estén presentes
        if (!$mac || !$command) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'MAC y comando son requeridos.'
            ])->setStatusCode(400);
        }

        // --- VERIFICACIÓN DE USUARIO Y ENLACE (PARA LA PÁGINA WEB) ---
        $session = \Config\Services::session();
        $userId = $session->get('id_usuario');

        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no autenticado.'
            ])->setStatusCode(401); // Unauthorized
        }

        $enlaceModel = new EnlaceModel();
        $isEnlazada = $enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();

        if (!$isEnlazada) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El dispositivo con MAC "' . $mac . '" no está enlazado a tu cuenta.'
            ])->setStatusCode(403); // Forbidden
        }
        // --- FIN VERIFICACIÓN DE USUARIO Y ENLACE ---

        $dispositivoModel = new DispositivoModel();
        $dispositivo = $dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo con MAC "' . $mac . '" no encontrado.'
            ])->setStatusCode(404);
        }

        // Determinar el valor numérico para 'estado_valvula' basado en el comando
        // 1 para 'open' (abrir), 0 para 'close' (cerrar)
        $estadoValvula = null;
        $messageSuffix = '';

        if ($command === 'open') {
            // Lógica para abrir: SOLO si es seguro (nivel de gas bajo el umbral)
            $currentGasLevel = (int)($dispositivo->ultimo_nivel_gas ?? 0);

            if ($currentGasLevel <= self::OPEN_VALVE_SAFE_THRESHOLD) {
                $estadoValvula = 1; // Abrir la válvula
                $messageSuffix = ' abierta';
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No es seguro abrir la válvula para el dispositivo ' . $mac . '. Nivel de gas actual (' . $currentGasLevel . ') supera el umbral de seguridad (' . self::OPEN_VALVE_SAFE_THRESHOLD . ').'
                ])->setStatusCode(403); // Forbidden
            }
        } elseif ($command === 'close') {
            // Lógica para cerrar: SIEMPRE se puede cerrar
            $estadoValvula = 0; // Cerrar la válvula
            $messageSuffix = ' cerrada';
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Comando inválido. Use "open" o "close".'
            ])->setStatusCode(400);
        }

        // Si el estado de la válvula ya es el deseado, no necesitamos hacer un UPDATE real
        if ((int)$dispositivo->estado_valvula === $estadoValvula) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'La válvula para el dispositivo ' . $mac . ' ya está' . $messageSuffix . '.'
            ]);
        }

        // Actualizar el estado de la válvula en la base de datos
        $updated = $dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estadoValvula])->update();

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Válvula' . $messageSuffix . ' correctamente para el dispositivo ' . $mac . '.'
            ]);
        } else {
            // Esto puede ocurrir si hubo un problema desconocido al actualizar.
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el estado de la válvula para el dispositivo ' . $mac . '. Por favor, intente de nuevo.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Permite al microcontrolador consultar el estado deseado de la válvula
     * y el nivel de gas actual para una MAC específica.
     * Recibe la MAC como un segmento de la URL.
     * Este método es llamado por el ESP32 y la página web.
     *
     * @param string|null $mac
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getValveState($mac = null)
    {
        if (!$mac) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'MAC del dispositivo es requerida.'
            ])->setStatusCode(400);
        }

        $dispositivoModel = new DispositivoModel();
        $dispositivo = $dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setJSON([
                'status' => 'success',
                'mac' => $mac,
                'estado_valvula' => (int)$dispositivo->estado_valvula,
                'ultimo_nivel_gas' => (int)($dispositivo->ultimo_nivel_gas ?? 0)
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo con MAC "' . $mac . '" no encontrado.',
                'estado_valvula' => 0, // Por seguridad, cerrar por defecto si no se encuentra
                'ultimo_nivel_gas' => 0 // Sin datos de gas por defecto
            ])->setStatusCode(404);
        }
    }

    /**
     * Recibe las lecturas de gas desde el microcontrolador.
     * Recibe MAC y nivel_gas vía POST (form-urlencoded).
     * Este método es llamado por el ESP32.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function receiveSensorData()
    {
        $idPlaca = $this->request->getPost('MAC');
        $nivelGas = $this->request->getPost('nivel_gas');

        if (!$idPlaca || !isset($nivelGas)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'MAC y nivel_gas son requeridos.'
            ])->setStatusCode(400);
        }

        $dispositivoModel = new DispositivoModel();
        $lecturasGasModel = new LecturasGasModel();
        $enlaceModel = new EnlaceModel();

        $dispositivoExistente = $dispositivoModel->where('MAC', $idPlaca)->first();

        $dataToSaveDispositivo = [
            'ultimo_nivel_gas' => $nivelGas,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ];

        $action = '';
        if ($dispositivoExistente) {
            $updated = $dispositivoModel->where('MAC', $idPlaca)->set($dataToSaveDispositivo)->update();
            $action = 'actualizado';
        } else {
            $dataToSaveDispositivo['MAC'] = $idPlaca;
            $dataToSaveDispositivo['nombre'] = 'Dispositivo ' . $idPlaca;
            $dataToSaveDispositivo['ubicacion'] = 'Desconocida';
            $dataToSaveDispositivo['estado_valvula'] = 0; // Estado inicial por defecto (cerrado)

            $dispositivoModel->insert($dataToSaveDispositivo);
            $action = 'creado';
        }

        // --- Lógica para insertar en `lecturas_gas` ---
        $userIdForLectura = null;
        $enlace = $enlaceModel->where('MAC', $idPlaca)->first();

        if ($enlace && isset($enlace['id_usuario'])) {
            $userIdForLectura = (int)$enlace['id_usuario'];
        } else {
            log_message('warning', 'Lectura de gas recibida para MAC no enlazada: ' . $idPlaca . '. No se registró la lectura detallada en lecturas_gas.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lectura de gas recibida, pero la MAC "' . $idPlaca . '" no está enlazada a ningún usuario. No se registró la lectura detallada.'
            ])->setStatusCode(400);
        }

        $dataToSaveLectura = [
            'MAC' => $idPlaca,
            'nivel_gas' => $nivelGas,
            'fecha' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'usuario_id' => $userIdForLectura
        ];

        $lecturaInsertada = $lecturasGasModel->insert($dataToSaveLectura);

        if ($lecturaInsertada !== false) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Lectura de gas recibida y procesada para el dispositivo ' . $idPlaca . '. Nivel: ' . $nivelGas . ' (Dispositivo ' . $action . ', Lectura registrada).'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo procesar la lectura de gas para el dispositivo "' . $idPlaca . '". Error al registrar la lectura detallada.'
            ])->setStatusCode(500);
        }
    }
}