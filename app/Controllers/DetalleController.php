<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel;

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

    // Método para migrar datos (usar temporalmente)
    public function migrarDatos()
    {
        $migrados = $this->lecturaModel->migrarARegistrosGas();
        return "Se migraron $migrados registros a la nueva tabla registros_gas";
    }

    // Método actualizado para usar la nueva tabla
    public function detalles($mac)
    {
        // Obtener detalles del dispositivo
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        $nombreDispositivo = $dispositivo['nombre'] ?? $mac;
        $ubicacionDispositivo = $dispositivo['ubicacion'] ?? 'Desconocida';

        if (empty($lecturas)) {
            return view('detalles', [
                'mac' => $mac,
                'nombreDispositivo' => $nombreDispositivo,
                'ubicacionDispositivo' => $ubicacionDispositivo,
                'lecturas' => [],
                'labels' => [],
                'data' => [],
                'message' => 'No se encontraron lecturas para este dispositivo.'
            ]);
        }

        // Ordenar para el gráfico (más antiguo primero)
        usort($lecturas, function ($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });

        // Preparar datos para el gráfico
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
            'lecturas' => array_reverse($lecturas), // Para mostrar más reciente primero en tabla
            'labels' => $labels,
            'data' => $data,
            'message' => null
        ]);
    }
}