<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;

class DetalleController extends BaseController
{
    protected $lecturaModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturasGasModel();
    }

    public function detalles($mac)
    {
        // Recuperar las lecturas de gas para la MAC proporcionada
        $lecturas = $this->lecturaModel->getLecturasPorMac($mac);

        // Verificar si hay lecturas disponibles para esa MAC
        if (empty($lecturas)) {
            // Si no hay lecturas, retornar una vista con un mensaje
            return view('detalles', [
                'mac' => $mac,
                'lecturas' => [],
                'labels' => [],
                'data' => [],
                'message' => 'No se encontraron lecturas para este dispositivo.'
            ]);
        }

        // Si hay lecturas, ordenar las lecturas por fecha
        usort($lecturas, function ($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });

        // Preparar los datos para el gráfico
        $labels = [];
        $data = [];
        foreach ($lecturas as $lectura) {
            $labels[] = $lectura['fecha'];
            $data[] = $lectura['nivel_gas'];
        }

        // Retornar la vista con los datos de las lecturas y el gráfico
        return view('detalles', [
            'mac' => $mac,
            'lecturas' => $lecturas,
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
