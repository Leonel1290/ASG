<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel; // Importar el modelo de dispositivo
use CodeIgniter\Controller; // Asegúrate de extender de Controller o BaseController
// use CodeIgniter\RESTful\ResourceController; // Si extiendes de ResourceController, mantenlo. Si no, usa CodeIgniter\Controller.

// Si extiendes de BaseController, cambia 'extends Controller' a 'extends BaseController'
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

        // Cargar helpers necesarios si no están en BaseController
        helper(['url']);
    }

    // Método para mostrar los detalles de un dispositivo (GET /detalles/(:any))
    public function detalles($mac)
    {
        // Recuperar las lecturas de gas para la MAC proporcionada usando el método del modelo
        $lecturas = $this->lecturaModel->getLecturasPorMac($mac);

        // Obtener los detalles del dispositivo (incluyendo nombre y ubicacion)
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        // Usar el nombre del dispositivo si existe, si no, usar la MAC
        $nombreDispositivo = $dispositivo['nombre'] ?? $mac;
        // Usar la ubicación del dispositivo si existe, si no, 'Desconocida'
        $ubicacionDispositivo = $dispositivo['ubicacion'] ?? 'Desconocida';


        // Verificar si hay lecturas disponibles para esa MAC
        if (empty($lecturas)) {
            // Si no hay lecturas, retornar la vista 'detalles' con un mensaje
            return view('detalles', [
                'mac' => $mac,
                'nombreDispositivo' => $nombreDispositivo, // Pasar el nombre del dispositivo
                'ubicacionDispositivo' => $ubicacionDispositivo, // Pasar la ubicación
                'lecturas' => [], // Pasar array vacío de lecturas
                'labels' => [], // Pasar array vacío de labels para el gráfico
                'data' => [], // Pasar array vacío de data para el gráfico
                'message' => 'No se encontraron lecturas para este dispositivo.' // Mensaje para la vista
            ]);
        }

        // Si hay lecturas, ordenar las lecturas por fecha (ascendente para el gráfico)
        usort($lecturas, function ($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });

        // Preparar los datos para el gráfico (labels y data)
        $labels = [];
        $data = [];
        foreach ($lecturas as $lectura) {
            $labels[] = $lectura['fecha']; // Eje X: Fechas
            $data[] = $lectura['nivel_gas']; // Eje Y: Nivel de gas
        }

        // Retornar la vista 'detalles' con los datos de las lecturas, el dispositivo y el gráfico
        return view('detalles', [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo, // Pasar el nombre del dispositivo
            'ubicacionDispositivo' => $ubicacionDispositivo, // Pasar la ubicación
            'lecturas' => $lecturas, // Pasar todas las lecturas
            'labels' => $labels, // Pasar labels para el gráfico
            'data' => $data, // Pasar data para el gráfico
            'message' => null // No hay mensaje de error si hay lecturas
        ]);
    }

    // Si tienes un método 'detalle' adicional, revisa si es redundante con 'detalles()'
    // public function detalle($mac)
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
