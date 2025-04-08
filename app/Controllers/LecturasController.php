<?php

namespace App\Controllers;
use App\Models\LecturasGasModel;
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController 
    {
        protected $lecturasGasModel;
    
        public function __construct()
        {
            $this->lecturasGasModel = new LecturasGasModel();
        }
    
        public function guardar()
        {
            // Obtiene los datos enviados por el sensor (id de la placa y nivel de gas)
            $id_placa = $this->request->getVar('id_placa');
            $nivel_gas = $this->request->getVar('nivel_gas');
    
            // Valida los datos (puedes agregar más validaciones si es necesario)
            if ($id_placa && $nivel_gas !== null) {
                // Guarda la lectura de gas en la base de datos
                $data = [
                    'usuario_id' => 1, // Puedes ajustar esto según el usuario que envía los datos
                    'nivel_gas' => $nivel_gas,
                    'fecha' => date('Y-m-d H:i:s'),
                ];
                
                // Guarda los datos en la base de datos
                $this->lecturasGasModel->insert($data);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Lectura guardada correctamente']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos']);
            }
        }
    }