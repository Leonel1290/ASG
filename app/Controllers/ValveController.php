<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;

class ValveController extends Controller
{
    // --- Umbrales de Gas (Puedes ajustarlos según tus necesidades) ---
    private const OPEN_VALVE_THRESHOLD = 100; // Si el nivel de gas es MENOR a este, se permite abrir la válvula.
    private const CLOSE_VALVE_THRESHOLD = 200; // Si el nivel de gas es MAYOR a este, se permite cerrar la válvula.

    /**
     * Maneja el comando de abrir o cerrar la válvula para un dispositivo específico.
     * Recibe la MAC del dispositivo y el comando ('open' o 'close') vía JSON POST.
     * Este método es llamado por la página web.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function controlValve()
    {
        $input = $this->request->getJSON();
        $mac = $input->mac ?? null;
        $command = $input->command ?? null;

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
            ])->setStatusCode(401);
        }

        $enlaceModel = new EnlaceModel();
        // Verifica si la MAC está enlazada al usuario actual
        // Esto devolverá un ARRAY porque EnlaceModel no especifica 'object'
        $isEnlazada = $enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();

        if (!$isEnlazada) { // Si es null (no encontrado) o array vacío
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El dispositivo con MAC "' . $mac . '" no está enlazado a tu cuenta.'
            ])->setStatusCode(403);
        }
        // --- FIN VERIFICACIÓN DE USUARIO Y ENLACE ---


        $dispositivoModel = new DispositivoModel();
        // DispositivoModel devuelve objetos (lo configuramos con returnType = 'object')
        $dispositivo = $dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo con MAC "' . $mac . '" no encontrado en la tabla de dispositivos.'
            ])->setStatusCode(404);
        }

        $nivelGasActual = (int)($dispositivo->ultimo_nivel_gas ?? 0); // Acceso a propiedad de objeto

        $permisoOtorgado = false;
        $motivoDenegado = '';

        if ($command === 'open') {
            if ($nivelGasActual < self::OPEN_VALVE_THRESHOLD) {
                $permisoOtorgado = true;
            } else {
                $motivoDenegado = "No se puede abrir la válvula. Nivel de gas actual ($nivelGasActual PPM) es igual o superior al umbral de seguridad (" . self::OPEN_VALVE_THRESHOLD . " PPM).";
            }
        } elseif ($command === 'close') {
            if ($nivelGasActual > self::CLOSE_VALVE_THRESHOLD) {
                $permisoOtorgado = true;
            } else {
                $motivoDenegado = "No se puede cerrar la válvula. Nivel de gas actual ($nivelGasActual PPM) es igual o inferior al umbral mínimo para cerrar (" . self::CLOSE_VALVE_THRESHOLD . " PPM).";
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Comando inválido. Use "open" o "close".'
            ])->setStatusCode(400);
        }

        if (!$permisoOtorgado) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $motivoDenegado
            ])->setStatusCode(403);
        }

        $estadoValvula = ($command === 'open') ? 1 : 0;

        $updated = $dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estadoValvula])->update();

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Válvula ' . (($command === 'open') ? 'abierta' : 'cerrada') . ' correctamente para el dispositivo ' . $mac . '.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el estado de la válvula. Es posible que el estado ya sea el solicitado.'
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
                'estado_valvula' => 0,
                'ultimo_nivel_gas' => 0
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
            $dataToSaveDispositivo['estado_valvula'] = 0;

            $dispositivoModel->insert($dataToSaveDispositivo);
            $action = 'creado';
        }

        // --- Lógica para insertar en `lecturas_gas` ---
        $userIdForLectura = null;
        $enlace = $enlaceModel->where('MAC', $idPlaca)->first(); // Esto devuelve un ARRAY

        // --- CORRECCIÓN AQUÍ: Acceso a propiedad de array con [] ---
        if ($enlace && isset($enlace['id_usuario'])) {
            $userIdForLectura = (int)$enlace['id_usuario'];
        } else {
            log_message('warning', 'Lectura de gas recibida para MAC no enlazada: ' . $idPlaca);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lectura de gas recibida, pero la MAC "' . $idPlaca . '" no está enlazada a ningún usuario. No se registró la lectura detallada.'
            ])->setStatusCode(400);
        }
        // --- FIN CORRECCIÓN ---

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
