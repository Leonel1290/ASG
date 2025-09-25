<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel; // <-- AÑADIR ESTA LÍNEA
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController
{
    protected $lecturasGasModel;
    protected $dispositivosModel; // <-- AÑADIR ESTA LÍNEA

    public function __construct()
    {
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivosModel = new DispositivoModel(); // <-- AÑADIR ESTA LÍNEA
    }

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
                // Actualizar el último nivel de gas en la tabla de dispositivos para consistencia
                $this->dispositivosModel->where('MAC', $mac)->set(['ultimo_nivel_gas' => $nivel_gas])->update();
                
                return $this->response->setJSON(['status' => 'success', 'message' => 'Lectura guardada correctamente', 'id' => $inserted]);
            } else {
                 log_message('error', 'Error al guardar lectura de gas para MAC: ' . $mac . ' - Datos: ' . json_encode($data) . ' - Error DB: ' . json_encode($this->lecturasGasModel->errors()));
                 return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al guardar la lectura en la base de datos']);
            }
        } else {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Faltan datos (MAC o nivel_gas)']);
        }
    }

    // MÉTODO CORREGIDO PARA OBTENER LA ÚLTIMA LECTURA Y ESTADO DE VÁLVULA
    public function obtenerUltimaLectura($mac)
    {
        // 1. Obtener el dispositivo por su MAC
        $dispositivo = $this->dispositivosModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }

        // 2. Obtener la última lectura de gas para esta MAC (el dato más reciente)
        $ultimaLectura = $this->lecturasGasModel
                               ->where('MAC', $mac)
                               ->orderBy('fecha', 'DESC')
                               ->first();

        // 3. Obtener el estado de la válvula del dispositivo
        $estadoValvula = (bool)($dispositivo['estado_valvula'] ?? false);

        // 4. Determinar el nivel de gas a mostrar (el más actualizado)
        $nivelGas = $ultimaLectura['nivel_gas'] ?? $dispositivo['ultimo_nivel_gas'] ?? 0;

        // 5. Devolver la información en formato JSON
        return $this->response->setJSON([
            'status' => 'success',
            'nivel_gas' => (float)$nivelGas,
            'estado_valvula' => $estadoValvula,
            'ultima_actualizacion' => $ultimaLectura['fecha'] ?? 'N/A'
        ]);
    }
}
