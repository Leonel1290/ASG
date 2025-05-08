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
    $mac = $this->request->getVar('MAC');
    $nivel_gas = $this->request->getVar('nivel_gas');

    if ($mac && $nivel_gas !== null) {
        $data = [
            'MAC' => $mac,
            'nivel_gas' => $nivel_gas,
            'fecha' => date('Y-m-d H:i:s') // si manejas la fecha manualmente
        ];

        $this->lecturasGasModel->insert($data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Lectura guardada correctamente']);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos']);
    }
}
public function detalle($mac)
{
    $lecturaModel = new \App\Models\LecturasGasModel();

    $lecturas = $lecturaModel->getLecturasPorMac($mac);

    // Armamos los datos para el gráfico
    $labels = array_column($lecturas, 'fecha');
    $data = array_column($lecturas, 'nivel_gas');

    return view('detalle_dispositivo', [
        'mac' => $mac,
        'lecturas' => $lecturas,
        'labels' => $labels,
        'data' => $data
    ]);
}

    }