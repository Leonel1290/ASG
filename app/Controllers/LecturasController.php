<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel; // Importar el EnlaceModel
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController
{
    protected $lecturasGasModel;
    protected $dispositivoModel;
    protected $enlaceModel; // Declarar la propiedad para el modelo de enlace

    public function __construct()
    {
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel(); // Instanciar el modelo de enlace
    }

    /**
     * Método para recibir y guardar lecturas de gas (POST /lecturas_gas/guardar)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function guardar()
    {
        $mac = $this->request->getVar('MAC');
        $nivel_gas = $this->request->getVar('nivel_gas');

        if ($mac && $nivel_gas !== null) {
            // Obtener el usuario_id a partir de la MAC
            $usuario_id = $this->enlaceModel->getUserIdByMac($mac);

            if ($usuario_id === null) {
                // Si la MAC no está vinculada a ningún usuario, puedes:
                // 1. Devolver un error (recomendado si cada lectura debe estar asociada a un usuario)
                log_message('error', 'Error al guardar lectura: MAC no vinculada a ningún usuario. MAC: ' . $mac);
                return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'MAC no vinculada a ningún usuario.']);

                // 2. Opcional: Usar un ID de usuario por defecto (si tienes un usuario "genérico" para lecturas no asignadas)
                // $usuario_id = 1; // Reemplaza '1' con el ID de tu usuario por defecto
                // log_message('warning', 'MAC no vinculada a usuario, usando ID por defecto para MAC: ' . $mac);
            }

            $data = [
                'MAC' => $mac,
                'nivel_gas' => $nivel_gas,
                'fecha' => date('Y-m-d H:i:s'),
                'usuario_id' => $usuario_id // ¡Ahora incluimos el usuario_id!
            ];

            $inserted = $this->lecturasGasModel->insert($data);

            if ($inserted) {
                // Opcional: Actualizar el último_nivel_gas y ultima_actualizacion en la tabla 'dispositivos'
                // Esto es útil para tener el estado más reciente del dispositivo en la tabla principal
                $this->dispositivoModel->set('ultimo_nivel_gas', $nivel_gas)
                                       ->set('ultima_actualizacion', date('Y-m-d H:i:s'))
                                       ->where('MAC', $mac)
                                       ->update();

                return $this->response->setJSON(['status' => 'success', 'message' => 'Lectura guardada correctamente', 'id' => $inserted]);
            } else {
                log_message('error', 'Error al guardar lectura de gas para MAC: ' . $mac . ' - Datos: ' . json_encode($data) . ' - Error DB: ' . $this->lecturasGasModel->errors());
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al guardar la lectura en la base de datos']);
            }
        } else {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Faltan datos (MAC o nivel_gas)']);
        }
    }

    /**
     * Muestra los detalles de un dispositivo y sus lecturas de gas.
     * Este método reemplaza la funcionalidad que antes estaba comentada.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return string
     */
    public function obtenerUltimaLectura($mac)
{
    try {
        // DECODIFICAR LA MAC
        $mac_decoded = urldecode($mac);
        
        log_message('debug', 'MAC recibida: ' . $mac);
        log_message('debug', 'MAC decodificada: ' . $mac_decoded);
        
        // Validar formato de MAC
        if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac_decoded)) {
            log_message('error', 'Formato de MAC inválido: ' . $mac_decoded);
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Formato de MAC inválido'
            ]);
        }

        // Buscar la última lectura del dispositivo
        $lectura = $this->lecturasGasModel
                        ->where('MAC', $mac_decoded)
                        ->orderBy('id', 'desc')
                        ->first();

        if ($lectura) {
            // Obtener estado de la válvula
            $dispositivo = $this->dispositivoModel->where('MAC', $mac_decoded)->first();
            $estado_valvula = $dispositivo['estado_valvula'] ?? false;

            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => (float)$lectura['nivel_gas'],
                'estado_valvula' => (bool)$estado_valvula,
                'timestamp' => $lectura['fecha'] ?? null
            ]);
        } else {
            log_message('info', 'No se encontraron lecturas para MAC: ' . $mac_decoded);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se encontró lectura para este dispositivo'
            ]);
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Error en obtenerUltimaLectura: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setJSON([
            'status' => 'error',
            'message' => 'Error interno del servidor'
        ]);
    }
}


    public function detalle($mac)
    {
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        $lecturasCrude = $this->lecturasGasModel->getLecturasPorMac($mac);

        $lecturasParaGrafico = array_reverse($lecturasCrude);

        $labels = [];
        $data = [];
        foreach ($lecturasParaGrafico as $lectura) {
            $labels[] = date('Y-m-d H:i', strtotime($lectura['fecha']));
            $data[] = (float) $lectura['nivel_gas'];
        }

        $nivelGasActualDisplay = 'Sin datos';
        if (!empty($lecturasCrude) && isset($lecturasCrude[0]['nivel_gas'])) {
            $nivelGasActualDisplay = esc($lecturasCrude[0]['nivel_gas']) . ' PPM';
        }

        $dataForView = [
            'mac'                   => esc($mac),
            'nombreDispositivo'     => esc($dispositivo['nombre'] ?? $mac),
            'ubicacionDispositivo' => esc($dispositivo['ubicacion'] ?? 'Desconocida'),
            'lecturas'              => $lecturasCrude,
            'labels'                => $labels,
            'data'                  => $data,
            'nivelGasActualDisplay' => $nivelGasActualDisplay
        ];

        return view('detalles', $dataForView);
    }
}
