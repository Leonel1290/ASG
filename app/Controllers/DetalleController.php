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
        // Obtener fechas del GET
        $fechaInicio = $this->request->getGet('fechaInicio');
        $fechaFin = $this->request->getGet('fechaFin');

        // Obtener detalles del dispositivo
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        $nombreDispositivo = $dispositivo['nombre'] ?? $mac;
        $ubicacionDispositivo = $dispositivo['ubicacion'] ?? 'Desconocida';

        // Inicializar $lecturas, $labels, $data y $message para evitar errores
        $lecturas = [];
        $labels = [];
        $data = [];
        $message = null;

        // Si no hay rango de fechas, no mostrar registros y mostrar un mensaje
        if (empty($fechaInicio) || empty($fechaFin)) {
            $message = 'Por favor, selecciona un rango de fechas para ver los registros.';
            // Retornar la vista con los datos básicos y el mensaje
            return view('detalles', [
                'mac' => $mac,
                'nombreDispositivo' => $nombreDispositivo,
                'ubicacionDispositivo' => $ubicacionDispositivo,
                'lecturas' => [], // Vacío
                'labels' => [],   // Vacío
                'data' => [],     // Vacío
                'message' => $message,
                'request' => $this->request // ¡Importante: Pasar el objeto request!
            ]);
        }

        // Obtener lecturas filtradas
        $lecturas = $this->lecturaModel->getLecturasPorMac($mac, $fechaInicio, $fechaFin);

        if (empty($lecturas)) {
            $message = 'No se encontraron lecturas para este dispositivo en el periodo seleccionado.';
            // Retornar la vista con los datos básicos y el mensaje
            return view('detalles', [
                'mac' => $mac,
                'nombreDispositivo' => $nombreDispositivo,
                'ubicacionDispositivo' => $ubicacionDispositivo,
                'lecturas' => [], // Vacío
                'labels' => [],   // Vacío
                'data' => [],     // Vacío
                'message' => $message,
                'request' => $this->request // ¡Importante: Pasar el objeto request!
            ]);
        }

        // Ordenar para el gráfico (más antiguo primero)
        $lecturasAsc = $lecturas; // Haz una copia si $lecturas se usa en otro lugar y necesitas que mantenga su orden original
        usort($lecturasAsc, function ($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });

        // Preparar datos para el gráfico
        foreach ($lecturasAsc as $lectura) {
            $labels[] = $lectura['fecha'];
            $data[] = $lectura['nivel_gas'];
        }

        return view('detalles', [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo,
            'ubicacionDispositivo' => $ubicacionDispositivo,
            'lecturas' => $lecturas, // Las lecturas se envían en el orden original (DESC) para la tabla
            'labels' => $labels,
            'data' => $data,
            'message' => $message, // El mensaje puede ser null si hay datos
            'request' => $this->request // ¡Importante: Pasar el objeto request!
        ]);
    }
}