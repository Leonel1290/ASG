<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;

class ValveController extends Controller
{
    // Los umbrales de gas se mantienen aquí solo como referencia
    // o para otras lógicas (ej. alarmas en la ESP32 o reportes),
    // pero YA NO controlan directamente la apertura/cierre de la válvula desde esta API.
    private const OPEN_VALVE_THRESHOLD = 100;
    private const CLOSE_VALVE_THRESHOLD = 200;

    /**
     * Maneja el comando de abrir o cerrar la válvula para un dispositivo específico.
     * Recibe la MAC del dispositivo y el comando ('open' o 'close') vía JSON POST.
     * Este método es llamado por la página web.
     * La válvula puede ser abierta o cerrada siempre, sin importar los umbrales de gas.
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
        // Se mantiene la verificación de que el usuario tiene enlazado el dispositivo por seguridad.
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

        // Determinar el valor numérico para 'estado_valvula' basado en el comando
        // 1 para 'open' (abrir), 0 para 'close' (cerrar)
        $estadoValvula = null;

        if ($command === 'open') {
            $estadoValvula = 1;
        } elseif ($command === 'close') {
            $estadoValvula = 0;
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Comando inválido. Use "open" o "close".'
            ])->setStatusCode(400);
        }

        // No se requiere verificar $permisoOtorgado basado en niveles de gas,
        // ya que la solicitud es que la válvula siempre pueda ser controlada.

        // Actualizar el estado de la válvula en la base de datos
        // NOTA: Se actualiza el campo 'estado_valvula' de la tabla 'dispositivos'.
        $updated = $dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estadoValvula])->update();

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Válvula ' . (($command === 'open') ? 'abierta' : 'cerrada') . ' correctamente para el dispositivo ' . $mac . '.'
            ]);
        } else {
            // Esto puede ocurrir si la MAC no se encontró (aunque ya se verificó antes)
            // o si el estado de la válvula ya es el solicitado (no se realiza un UPDATE real en la DB).
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el estado de la válvula. Es posible que el estado ya sea el solicitado o la MAC no existe.'
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
        // DispositivoModel devuelve objetos (lo configuramos con returnType = 'object')
        $dispositivo = $dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setJSON([
                'status' => 'success',
                'mac' => $mac,
                'estado_valvula' => (int)$dispositivo->estado_valvula, // Acceso a propiedad de objeto
                'ultimo_nivel_gas' => (int)($dispositivo->ultimo_nivel_gas ?? 0) // Acceso a propiedad de objeto
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

        // Datos para actualizar/insertar en la tabla 'dispositivos'
        $dataToSaveDispositivo = [
            'ultimo_nivel_gas' => $nivelGas,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ];

        $action = '';
        if ($dispositivoExistente) {
            // Si el dispositivo existe, actualizarlo
            $updated = $dispositivoModel->where('MAC', $idPlaca)->set($dataToSaveDispositivo)->update();
            $action = 'actualizado';
        } else {
            // Si el dispositivo NO existe, crearlo
            $dataToSaveDispositivo['MAC'] = $idPlaca;
            $dataToSaveDispositivo['nombre'] = 'Dispositivo ' . $idPlaca;
            $dataToSaveDispositivo['ubicacion'] = 'Desconocida';
            $dataToSaveDispositivo['estado_valvula'] = 0; // Estado inicial por defecto (cerrado)

            $dispositivoModel->insert($dataToSaveDispositivo);
            $action = 'creado';
        }

        // --- Lógica para insertar en `lecturas_gas` ---
        $userIdForLectura = null;
        $enlace = $enlaceModel->where('MAC', $idPlaca)->first(); // Esto devuelve un ARRAY

        if ($enlace && isset($enlace['id_usuario'])) { // Acceso a propiedad de array con []
            $userIdForLectura = (int)$enlace['id_usuario'];
        } else {
            // Si la MAC no está enlazada a un usuario, no se registra la lectura detallada.
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
