<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel; // Importar el modelo de dispositivo
use CodeIgniter\Controller;

class DetalleController extends BaseController
{
    protected $lecturaModel;
    protected $dispositivoModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel();

        helper(['url']);
    }

    public function detalles($mac)
    {
        $lecturas = $this->lecturaModel->getLecturasPorMac($mac);

        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        $nombreDispositivo = $dispositivo['nombre'] ?? $mac;
        $ubicacionDispositivo = $dispositivo['ubicacion'] ?? 'Desconocida';

        if (empty($lecturas)) {
            return view('detalles', [
                'mac' => $mac,
                'nombreDispositivo' => $nombreDispositivo,
                'ubicacionDispositivo' => $ubicacionDispositivo,
                'lecturas' => [], // Asegura que siempre se pase un array vacío
                'labels' => [],   // Asegura que siempre se pase un array vacío
                'data' => [],     // Asegura que siempre se pase un array vacío
                'message' => 'No se encontraron lecturas para este dispositivo.'
            ]);
        }

        usort($lecturas, function ($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });

        $labels = [];
        $data = [];
        foreach ($lecturas as $lectura) {
            $labels[] = $lectura['fecha'];
            $data[] = $lectura['nivel_gas'];
        }

        return view('detalles', [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo,
            'ubicacionDispositivo' => $ubicacionDispositivo,
            'lecturas' => $lecturas,
            'labels' => $labels,
            'data' => $data,
            'message' => null
        ]);
    }
}