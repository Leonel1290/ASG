<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Controllers\PushController; // Importamos el controlador de notificaciones
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController
{
    protected $lecturasGasModel;
    protected $pushController; // Instancia del controlador de push

    public function __construct()
    {
        $this->lecturasGasModel = new LecturasGasModel();
        $this->pushController = new PushController(); // Instanciamos PushController
    }

    // Método para recibir y guardar lecturas de gas (POST /lecturas_gas/guardar)
    public function guardar()
    {
        $mac = $this->request->getVar('MAC');
        $nivel_gas = $this->request->getVar('nivel_gas');

        if ($mac && $nivel_gas !== null) {
            $data = [
                'MAC' => $mac,
                'nivel_gas' => $nivel_gas,
                'fecha' => date('Y-m-d H:i:s')
            ];

            $inserted = $this->lecturasGasModel->insert($data);

            if ($inserted) {

                // --- ALERTA PUSH AUTOMÁTICA ---
                $nivelCritico = 300; // Ajusta según tu sensor
                if ($nivel_gas >= $nivelCritico) {
                    $titulo = "¡Alerta de fuga de gas!";
                    $mensaje = "Se ha detectado un nivel crítico de gas en tu hogar. Por favor revisa inmediatamente.";
                        $this->pushController->sendNotificationPush($titulo, $mensaje);
    }


                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Lectura guardada correctamente',
                    'id' => $inserted
                ]);
            } else {
                log_message('error', 'Error al guardar lectura de gas para MAC: ' . $mac . ' - Datos: ' . json_encode($data) . ' - Error DB: ' . $this->lecturasGasModel->errors());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Error al guardar la lectura en la base de datos'
                ]);
            }
        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos (MAC o nivel_gas)'
            ]);
        }
    }

    // NUEVO MÉTODO PARA OBTENER LA ÚLTIMA LECTURA
    public function obtenerUltimaLectura($mac)
    {
        // 1. Obtener el dispositivo por su MAC
        $dispositivo = $this->dispositivosModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo no encontrado.'
            ]);
        }

        // 2. Obtener la última lectura de gas para esta MAC
        $ultimaLectura = $this->lecturasGasModel
                               ->where('MAC', $mac)
                               ->orderBy('fecha', 'DESC')
                               ->first();

        // 3. Obtener el estado de la válvula del dispositivo
        $estadoValvula = $dispositivo['estado_valvula'] ?? false;

        // 4. Devolver la información en formato JSON
        if ($ultimaLectura) {
            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => $ultimaLectura['nivel_gas'],
                'estado_valvula' => $estadoValvula,
                'ultima_actualizacion' => $ultimaLectura['fecha']
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => 0,
                'estado_valvula' => $estadoValvula,
                'message' => 'No hay lecturas disponibles para este dispositivo.'
            ]);
        }
    }
}
