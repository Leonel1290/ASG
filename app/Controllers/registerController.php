<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivosModel;
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController
{
    public function guardar()
    {
        $lecturasModel = new LecturasGasModel();
        $dispositivosModel = new DispositivosModel();

        // Obtén los datos enviados desde la ESP32
        $nivel_gas = $this->request->getPost('nivel_gas');
        $numero_serie = $this->request->getPost('numero_serie');

        // Validación básica
        if (!$numero_serie || !$nivel_gas) {
            return $this->respond(['status' => 'error', 'message' => 'Datos incompletos'], 400);
        }

        // Verifica si el dispositivo está registrado
        $dispositivo = $dispositivosModel->where('numero_serie', $numero_serie)->first();

        if (!$dispositivo) {
            return $this->respond(['status' => 'error', 'message' => 'Dispositivo no registrado'], 404);
        }

        // Inserta la lectura en la base de datos
        $data = [
            'usuario_id' => $dispositivo['usuario_id'], // Relaciona con el usuario
            'nivel_gas' => $nivel_gas,
            'fecha' => date('Y-m-d H:i:s'),
        ];

        if ($lecturasModel->insert($data)) {
            // Actualiza la última conexión del dispositivo
            $dispositivosModel->update($dispositivo['id'], ['ultima_conexion' => date('Y-m-d H:i:s')]);

            return $this->respond(['status' => 'success', 'message' => 'Lectura guardada']);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Error al guardar la lectura'], 500);
        }
    }
}
