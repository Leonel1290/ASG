<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel; // Importar el modelo de dispositivo
use CodeIgniter\RESTful\ResourceController; // Si extiendes de ResourceController, mantenlo. Si no, usa CodeIgniter\Controller.

// Si extiendes de BaseController, cambia 'extends ResourceController' a 'extends BaseController'
// Si no, usa 'extends Controller'
class DetalleController extends BaseController // Asumo que extiendes de BaseController
{
    protected $lecturaModel;
    protected $dispositivoModel; // Propiedad para el modelo de dispositivo

    public function __construct()
    {
        // Llama al constructor de la clase padre si es necesario
        // parent::__construct();

        $this->lecturaModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel(); // Instancia el modelo de dispositivo
    }

    public function detalles($mac)
    {
        // Recuperar las lecturas de gas para la MAC proporcionada
        $lecturas = $this->lecturaModel->getLecturasPorMac($mac);

        // --- NUEVO: Obtener los detalles del dispositivo (incluyendo nombre y ubicacion) ---
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        $nombreDispositivo = $dispositivo['nombre'] ?? $mac; // Usar nombre si existe, si no, la MAC
        $ubicacionDispositivo = $dispositivo['ubicacion'] ?? 'Desconocida';
        // --- FIN NUEVO ---


        // Verificar si hay lecturas disponibles para esa MAC
        if (empty($lecturas)) {
            // Si no hay lecturas, retornar una vista con un mensaje
            return view('detalles', [
                'mac' => $mac,
                'nombreDispositivo' => $nombreDispositivo, // Pasar el nombre del dispositivo
                'ubicacionDispositivo' => $ubicacionDispositivo, // Pasar la ubicación
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
            'nombreDispositivo' => $nombreDispositivo, // Pasar el nombre del dispositivo
            'ubicacionDispositivo' => $ubicacionDispositivo, // Pasar la ubicación
            'lecturas' => $lecturas,
            'labels' => $labels,
            'data' => $data,
            'message' => null // No hay mensaje de error si hay lecturas
        ]);
    }

    // Puedes tener otros métodos en este controlador si los usas.
    // public function detalle($mac) // Si tienes este método, revisa si es redundante con detalles()
    // {
    //     $lecturaModel = new \App\Models\LecturasGasModel();
    //     $lecturas = $lecturaModel->getLecturasPorMac($mac);
    //     $labels = array_column($lecturas, 'fecha');
    //     $data = array_column($lecturas, 'nivel_gas');
    //     return view('detalle_dispositivo', [
    //         'mac' => $mac,
    //         'labels' => $labels,
    //         'data' => $data
    //     ]);
    // }
}
