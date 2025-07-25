<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel; // ¡IMPORTANTE: Añade esta importación!
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController
{
    protected $lecturasGasModel;
    protected $dispositivoModel;
    protected $enlaceModel; // ¡IMPORTANTE: Declara la propiedad para el modelo de enlaces!

    public function __construct()
    {
        // Instancia los modelos necesarios
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel(); // ¡IMPORTANTE: Instancia el modelo de enlaces!
    }

    /**
     * Método para recibir y guardar lecturas de gas (POST /lecturas_gas/guardar)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function guardar()
    {
        // Obtener los datos enviados en la solicitud POST (asumo JSON o form-data)
        $mac = $this->request->getVar('MAC');
        $nivel_gas = $this->request->getVar('nivel_gas');

        // Verificar que se recibieron los datos necesarios
        if ($mac && $nivel_gas !== null) {
            // Paso 1: Obtener el usuario_id asociado a la MAC del dispositivo
            $usuario_id = $this->enlaceModel->getUsuarioIdByMac($mac);

            // Verificar si la MAC está enlazada a un usuario
            if ($usuario_id === null) {
                // Si la MAC no está enlazada, retornar un error para evitar insertar datos huérfanos
                log_message('error', 'Intento de guardar lectura para MAC no enlazada: ' . $mac);
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'La dirección MAC proporcionada no está enlazada a ningún usuario.']);
            }

            // Preparar los datos para insertar en la base de datos
            $data = [
                'MAC' => $mac,
                'nivel_gas' => $nivel_gas,
                'fecha' => date('Y-m-d H:i:s'), // Captura la fecha y hora actual
                'usuario_id' => $usuario_id // ¡IMPORTANTE: Añade el usuario_id aquí!
            ];

            // Insertar los datos en la tabla 'lecturas_gas'
            $inserted = $this->lecturasGasModel->insert($data);

            // Verificar si la inserción fue exitosa
            if ($inserted) {
                // Si fue exitosa, retornar una respuesta JSON de éxito
                return $this->response->setJSON(['status' => 'success', 'message' => 'Lectura guardada correctamente', 'id' => $inserted]);
            } else {
                // Si hubo un error en la inserción, loguearlo y retornar una respuesta JSON de error
                // Usar json_encode para los errores del modelo si están disponibles
                log_message('error', 'Error al guardar lectura de gas para MAC: ' . $mac . ' - Datos: ' . json_encode($data) . ' - Error DB: ' . json_encode($this->lecturasGasModel->errors()));
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al guardar la lectura en la base de datos.']);
            }
        } else {
            // Si faltan datos, retornar una respuesta JSON de error
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Faltan datos (MAC o nivel_gas).']);
        }
    }

    /**
     * Muestra los detalles de un dispositivo y sus lecturas de gas.
     * Este método reemplaza la funcionalidad que antes estaba comentada.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return string
     */
    public function detalle($mac)
    {
        // Obtener los detalles del dispositivo (nombre y ubicación)
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);

        // Obtener las lecturas de gas para esta MAC.
        // El método getLecturasPorMac() del modelo devuelve las lecturas en orden DESC (más recientes primero).
        $lecturasCrude = $this->lecturasGasModel->getLecturasPorMac($mac);

        // Para el gráfico (Chart.js), necesitamos que los datos estén en orden ascendente (más antiguos primero).
        $lecturasParaGrafico = array_reverse($lecturasCrude);

        // Preparar los datos para el gráfico
        $labels = [];
        $data = [];
        foreach ($lecturasParaGrafico as $lectura) {
            // Formatear la fecha para que sea legible en el eje X del gráfico
            $labels[] = date('Y-m-d H:i', strtotime($lectura['fecha']));
            $data[] = (float) $lectura['nivel_gas']; // Asegurarse de que el nivel de gas sea un número
        }

        // Obtener el último nivel de gas para la tarjeta simple superior.
        // Dado que $lecturasCrude está ordenado DESC (más reciente primero), el último valor es el primer elemento.
        $nivelGasActualDisplay = 'Sin datos';
        if (!empty($lecturasCrude) && isset($lecturasCrude[0]['nivel_gas'])) {
            $nivelGasActualDisplay = esc($lecturasCrude[0]['nivel_gas']) . ' PPM';
        }

        // Pasar los datos a la vista
        $dataForView = [
            'mac'                  => esc($mac), // Sanitizar la MAC
            'nombreDispositivo'    => esc($dispositivo['nombre'] ?? $mac), // Usar MAC si no hay nombre, y sanitizar
            'ubicacionDispositivo' => esc($dispositivo['ubicacion'] ?? 'Desconocida'), // Sanitizar
            'lecturas'             => $lecturasCrude,     // Las lecturas para la tabla (más recientes primero)
            'labels'               => $labels,             // Las etiquetas de fecha para el gráfico (orden ascendente)
            'data'                 => $data,               // Los datos de nivel de gas para el gráfico (orden ascendente)
            'nivelGasActualDisplay' => $nivelGasActualDisplay // El último nivel de gas para la tarjeta superior
        ];

        return view('detalles', $dataForView);
    }
}
