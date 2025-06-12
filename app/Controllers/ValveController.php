<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel; // Asegúrate de que este namespace y nombre de modelo sean correctos

class ValveController extends Controller
{
    /**
     * Maneja el comando de abrir o cerrar la válvula para un dispositivo específico.
     * Recibe la MAC del dispositivo y el comando ('open' o 'close') vía JSON POST.
     * Este método es llamado por la página web.
     */
    public function controlValve()
    {
        // Obtener los datos del cuerpo de la solicitud JSON
        $input = $this->request->getJSON();

        $mac = $input->mac ?? null;
        $command = $input->command ?? null;

        // Validar que la MAC y el comando estén presentes
        if (!$mac || !$command) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'MAC y comando son requeridos.'
            ])->setStatusCode(400); // Código de estado HTTP 400 Bad Request
        }

        // Instanciar el modelo del dispositivo
        $dispositivoModel = new DispositivoModel();

        // Determinar el valor numérico para 'estado_valvula'
        // 1 para 'open' (abrir), 0 para 'close' (cerrar)
        $estadoValvula = ($command === 'open') ? 1 : 0;

        // Actualizar el estado de la válvula en la base de datos
        // Asegúrate de que 'MAC' es la columna correcta para identificar tu dispositivo
        $updated = $dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estadoValvula])->update();

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Válvula ' . (($command === 'open') ? 'abierta' : 'cerrada') . ' correctamente para el dispositivo ' . $mac . '.'
            ]);
        } else {
            // Si no se pudo actualizar (ej. MAC no encontrada o no hubo cambios)
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el estado de la válvula. Es posible que el dispositivo con MAC "' . $mac . '" no exista o que el estado ya sea el solicitado.'
            ])->setStatusCode(500); // Código de estado HTTP 500 Internal Server Error
        }
    }

    /**
     * Permite al microcontrolador consultar el estado deseado de la válvula
     * para una MAC específica.
     * Recibe la MAC como un segmento de la URL.
     * Este método es llamado por el ESP32.
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

        // Busca el dispositivo por su MAC
        $dispositivo = $dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Si el dispositivo existe, devuelve el estado de la válvula
            return $this->response->setJSON([
                'status' => 'success',
                'mac' => $mac,
                'estado_valvula' => (int)$dispositivo->estado_valvula // Asegura que sea un entero
            ]);
        } else {
            // Si el dispositivo no se encuentra, por seguridad, devuelve un estado cerrado por defecto.
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo con MAC "' . $mac . '" no encontrado.',
                'estado_valvula' => 0 
            ])->setStatusCode(404);
        }
    }

    /**
     * Recibe las lecturas de gas desde el microcontrolador.
     * Recibe id_placa y nivel_gas vía POST (form-urlencoded).
     * Este método es llamado por el ESP32.
     */
    public function receiveSensorData()
    {
        // Obtener los datos del cuerpo de la solicitud POST (form-urlencoded)
        $idPlaca = $this->request->getPost('id_placa');
        $nivelGas = $this->request->getPost('nivel_gas');

        if (!$idPlaca || !isset($nivelGas)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'id_placa y nivel_gas son requeridos.'
            ])->setStatusCode(400);
        }

        $dispositivoModel = new DispositivoModel();

        // Actualiza una columna 'ultimo_nivel_gas' y 'ultima_actualizacion' en tu tabla de dispositivos.
        // Asegúrate de que estas columnas existan en tu tabla 'dispositivos'.
        $updated = $dispositivoModel->where('MAC', $idPlaca)->set(['ultimo_nivel_gas' => $nivelGas, 'ultima_actualizacion' => date('Y-m-d H:i:s')])->update();

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Lectura de gas recibida y procesada para el dispositivo ' . $idPlaca . '. Nivel: ' . $nivelGas
            ]);
        } else {
            // Si no se pudo actualizar (ej. MAC no encontrada o no hubo cambios)
            // Considera que si la MAC no existe, podrías querer crear el dispositivo en la DB.
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo procesar la lectura de gas para el dispositivo "' . $idPlaca . '". Puede que no exista o no haya cambios.'
            ])->setStatusCode(500);
        }
    }
}