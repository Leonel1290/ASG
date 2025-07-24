<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel;
use CodeIgniter\I18n\Time; // Asegúrate de que esta línea esté presente si la necesitas

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

        // --- INICIO DE LOGGING PARA DEBUGGING ---
        log_message('debug', "DetalleController: Solicitud para MAC: " . $mac);
        log_message('debug', "DetalleController: Fecha Inicio: " . ($fechaInicio ?? 'null'));
        log_message('debug', "DetalleController: Fecha Fin: " . ($fechaFin ?? 'null'));
        // --- FIN DE LOGGING ---

        // Obtener detalles del dispositivo
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        $nombreDispositivo = $dispositivo['nombre'] ?? $mac;
        $ubicacionDispositivo = $dispositivo['ubicacion'] ?? 'Desconocida';

        // Inicializar $lecturas, $labels, $data y $message para evitar errores
        $lecturas = [];
        $labels = [];
        $data = [];
        $message = null;

        // Si no hay rango de fechas, mostrar un mensaje y no cargar registros inicialmente
        // Permitir ver los registros aunque no haya un rango seleccionado para el gráfico
        if (empty($fechaInicio) || empty($fechaFin)) {
            // Si quieres que el gráfico se cargue con datos por defecto (ej. últimos 30 días)
            // puedes establecer fechaInicio y fechaFin aquí, si no, se mostrará "Sin datos"
            $message = 'Selecciona un rango de fechas para ver los registros históricos.';
        }

        // Obtener lecturas filtradas (siempre se intentan obtener, incluso sin un rango completo para el gráfico)
        // Si fechaInicio o fechaFin son nulos, LecturasGasModel::getLecturasPorMac manejará eso para devolver todos los datos si es apropiado,
        // o solo el último dato si se usa para el gauge. Para el gráfico, necesitaremos fechas.
        $lecturas = $this->lecturaModel->getLecturasPorMac($mac, $fechaInicio, $fechaFin);

        if (empty($lecturas)) {
            $message = 'No se encontraron lecturas para este dispositivo en el periodo seleccionado.';
            // Si no hay lecturas para el periodo, asegurarse de que las variables del gráfico estén vacías
            $labels = [];
            $data = [];
        } else {
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
        }

        return view('detalles', [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo,
            'ubicacionDispositivo' => $ubicacionDispositivo,
            'lecturas' => $lecturas, // Las lecturas se envían en el orden original (DESC) para la tabla
            'labels' => $labels,
            'data' => $data,
            'message' => $message, // El mensaje puede ser null aquí si se encontraron lecturas
            'request' => $this->request // ¡Importante: Pasar el objeto request para el date picker!
        ]);
    }

    /**
     * Devuelve el último nivel de gas para una MAC específica en formato JSON.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getLatestGasLevel(string $mac)
    {
        $lectura = $this->lecturaModel->getLatestLecturaPorMac($mac);
        if ($lectura) {
            return $this->response->setJSON(['nivel_gas' => (float)$lectura['nivel_gas']]);
        } else {
            return $this->response->setJSON(['nivel_gas' => null])->setStatusCode(404, 'No latest data found');
        }
    }
}