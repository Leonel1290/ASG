<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel; // Necesitamos el modelo para la tabla 'enlace'
use App\Models\LecturasGasModel; // Necesitamos el modelo para la tabla 'lecturas_gas'

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
        // Asumimos que la sesión del usuario ya está iniciada y su ID está disponible.
        // Adaptar esto según cómo manejas las sesiones en CodeIgniter.
        $session = \Config\Services::session();
        $userId = $session->get('id_usuario'); // Asume que el ID del usuario se guarda en 'id_usuario' en la sesión.

        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no autenticado.'
            ])->setStatusCode(401); // Unauthorized
        }

        $enlaceModel = new EnlaceModel();
        // Verifica si la MAC está enlazada al usuario actual
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
                'message' => 'Dispositivo con MAC "' . $mac . '" no encontrado en la tabla de dispositivos.'
            ])->setStatusCode(404);
        }

        $nivelGasActual = (int)($dispositivo->ultimo_nivel_gas ?? 0); // Asume 0 si no hay datos de gas

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

        // --- VERIFICACIÓN DE USUARIO Y ENLACE (PARA LA PÁGINA WEB) ---
        // Si esta llamada viene de la página web, también debe verificar que el usuario
        // esté autenticado y tenga la MAC enlazada.
        // Si esta llamada es SOLO para el ESP32, puedes omitir esta verificación aquí
        // o asegurarte de que el ESP32 no tiene un 'id_usuario' de sesión.
        // Para la vista 'detalles', la verificación se hace en el controlador que carga la vista,
        // no en esta API que solo devuelve datos.
        // Sin embargo, si quieres que la API de estado también requiera autenticación,
        // puedes descomentar y adaptar la siguiente sección.
        /*
        $session = \Config\Services::session();
        $userId = $session->get('id_usuario');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no autenticado para consultar estado.'
            ])->setStatusCode(401);
        }
        $enlaceModel = new EnlaceModel();
        $isEnlazada = $enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();
        if (!$isEnlazada) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No tienes permiso para consultar el estado de esta MAC.'
            ])->setStatusCode(403);
        }
        */
        // --- FIN VERIFICACIÓN DE USUARIO Y ENLACE ---

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
        $lecturasGasModel = new LecturasGasModel(); // Instanciamos el modelo de lecturas_gas
        $enlaceModel = new EnlaceModel(); // Instanciamos el modelo de enlace

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
            // Si el dispositivo NO existe, crearlo
            $dataToSaveDispositivo['MAC'] = $idPlaca;
            $dataToSaveDispositivo['estado_valvula'] = 0; // Estado inicial por defecto (cerrado)
            $dataToSaveDispositivo['nombre'] = 'Dispositivo ' . $idPlaca; // Nombre por defecto
            $dataToSaveDispositivo['ubicacion'] = 'Desconocida'; // Ubicación por defecto
            $dispositivoModel->insert($dataToSaveDispositivo); // Insertar el nuevo dispositivo
            $action = 'creado';
            // Después de insertar, el dispositivoExistente sigue siendo null, lo obtenemos
            $dispositivoExistente = $dispositivoModel->where('MAC', $idPlaca)->first();
        }

        // --- Lógica para insertar en `lecturas_gas` ---
        $userIdForLectura = null;
        // Buscar el id_usuario asociado a esta MAC en la tabla 'enlace'
        $enlace = $enlaceModel->where('MAC', $idPlaca)->first();

        if ($enlace && $enlace->id_usuario) {
            $userIdForLectura = (int)$enlace->id_usuario;
        } else {
            // Si la MAC no está enlazada a un usuario, o no se encontró el enlace,
            // puedes optar por no registrar la lectura de gas o usar un ID de usuario por defecto
            // (Asegúrate de que 'usuario_id' en 'lecturas_gas' permita NULL o tenga un valor por defecto válido si decides no asignarlo)
            // Para tu esquema donde usuario_id es NOT NULL, debes asignar un id.
            // Opción 1: Asignar a un usuario "genérico" o "no asignado" si existe en tu tabla de usuarios.
            // $userIdForLectura = 1; // Asume ID 1 es un usuario genérico.
            // Opción 2: Si no encuentras un enlace y quieres que falle, puedes retornar un error aquí.
            // Por ahora, si no hay enlace, no asignamos usuario_id, lo que causará un error si es NOT NULL.
            // Considerando tu `lecturas_gas` esquema: `usuario_id` int NOT NULL.
            // Esto significa que SIEMPRE necesitas un usuario. Si la MAC no está enlazada,
            // NECESITAS una estrategia. Una opción es no insertar la lectura de gas.
            // Otra opción es insertar con un ID de usuario predefinido para "dispositivos no asignados".
            log_message('warning', 'Lectura de gas recibida para MAC no enlazada: ' . $idPlaca);
            // Si usuario_id es NOT NULL, debemos evitar insertar si no tenemos un id válido.
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lectura de gas recibida, pero la MAC "' . $idPlaca . '" no está enlazada a ningún usuario. No se registró la lectura detallada.'
            ])->setStatusCode(400); // Bad Request o 403 Forbidden
        }

        $dataToSaveLectura = [
            'MAC' => $idPlaca,
            'nivel_gas' => $nivelGas,
            'fecha' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'usuario_id' => $userIdForLectura // Asignamos el ID de usuario encontrado
        ];

        // Intentar insertar la nueva lectura de gas
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