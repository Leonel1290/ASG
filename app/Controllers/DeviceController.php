<?php

namespace App\Controllers;

use App\Models\DeviceModel; // Asumiendo que tienes un modelo para tus dispositivos y lecturas
use CodeIgniter\Controller;

class DeviceController extends Controller
{
    protected $deviceModel; // Instancia de tu modelo

    public function __construct()
    {
        $this->deviceModel = new DeviceModel(); // O el nombre de tu modelo
    }

    /**
     * Muestra la lista de todos los dispositivos únicos.
     */
    public function listDevices()
    {
        // Esto es un ejemplo. Deberías obtener las MACs únicas
        // junto con su nombre y ubicación de tu base de datos.
        // Asumo que tu modelo puede proveer un método para esto.
        $uniqueMacs = $this->deviceModel->getUniqueDevices(); // Deberías implementar este método en tu modelo

        $data = [
            'uniqueMacs' => $uniqueMacs,
        ];

        return view('devices_list', $data);
    }

    /**
     * Muestra el detalle del dispositivo (gráfico y última lectura).
     * @param string $mac La MAC del dispositivo.
     */
    public function showDeviceDetail($mac)
    {
        // Obtener todas las lecturas para esta MAC
        $lecturas = $this->deviceModel->getLecturasByMac($mac); // Implementa este método en tu modelo

        // Preparar datos para el gráfico
        $labels = [];
        $data = [];
        foreach ($lecturas as $lectura) {
            $labels[] = date('d/m H:i', strtotime($lectura['fecha'])); // Formato de fecha para el eje X
            $data[] = (float) $lectura['nivel_gas'];
        }

        // Obtener nombre y ubicación del dispositivo
        $deviceInfo = $this->deviceModel->getDeviceInfoByMac($mac); // Implementa este método
        $nombreDispositivo = $deviceInfo['nombre'] ?? $mac;
        $ubicacionDispositivo = $deviceInfo['ubicacion'] ?? 'Desconocida';

        $viewData = [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo,
            'ubicacionDispositivo' => $ubicacionDispositivo,
            'lecturas' => $lecturas, // Se sigue pasando para obtener la última lectura y el display
            'labels' => $labels,
            'data' => $data,
        ];

        return view('device_detail', $viewData);
    }

    /**
     * Muestra todos los registros de gas para una MAC específica en una nueva vista.
     * @param string $mac La MAC del dispositivo.
     */
    public function showGasRecords($mac)
    {
        $lecturas = $this->deviceModel->getLecturasByMac($mac); // Obtén todas las lecturas de gas

        // Obtener nombre y ubicación del dispositivo
        $deviceInfo = $this->deviceModel->getDeviceInfoByMac($mac);
        $nombreDispositivo = $deviceInfo['nombre'] ?? $mac;
        $ubicacionDispositivo = $deviceInfo['ubicacion'] ?? 'Desconocida';

        $viewData = [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo,
            'ubicacionDispositivo' => $ubicacionDispositivo,
            'lecturas' => $lecturas,
        ];

        return view('gas_records', $viewData);
    }
}