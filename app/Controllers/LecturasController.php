<?php

namespace App\Controllers;
use App\Models\LecturasGasModel;
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController {
    public function guardar() {
        $model = new LecturasGasModel();
        $nivel_gas = $this->request->getPost('nivel_gas');
        $id_lectura = $this->request->getPost('id_placa');  // Obtiene el id de la lectura

        // Verifica si el id de la lectura está disponible
        if (!$id_lectura) {
            return $this->respond(['status' => 'error', 'message' => 'ID de lectura no proporcionado'], 400);
        }

        // Verifica si ya existe una lectura con el id proporcionado
        $lectura = $model->find($id_lectura);

        if ($lectura) {
            // Si ya existe una lectura, actualiza el nivel de gas
            $model->update($id_lectura, ['nivel_gas' => $nivel_gas]);
            return $this->respond(['status' => 'success', 'message' => 'Lectura actualizada']);
        } else {
            // Si no existe una lectura con ese id, crea una nueva
            $data = [
                'nivel_gas' => $nivel_gas,
                'id' => $id_lectura  // Establece el id proporcionado
            ];

            if ($model->insert($data)) {
                return $this->respond(['status' => 'success', 'message' => 'Lectura guardada']);
            } else {
                return $this->respond(['status' => 'error', 'message' => 'Error al guardar la lectura'], 500);
            }
        }
    }
}
