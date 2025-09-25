<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use CodeIgniter\RESTful\ResourceController; // Extiende de ResourceController si manejas recursos RESTful

// Si extiendes de BaseController, considera si es apropiado para un controlador RESTful
class LecturasController extends ResourceController // Mantengo ResourceController según tu archivo
{
    protected $lecturasGasModel;

    public function __construct()
    {
        // Instancia el modelo de lecturas de gas
        $this->lecturasGasModel = new LecturasGasModel();
        // No es necesario llamar a parent::__construct() si no hay lógica en BaseController que necesites aquí
    }

    // Método para recibir y guardar lecturas de gas (POST /lecturas_gas/guardar)
    public function guardar()
    {
        // Obtener los datos enviados en la solicitud POST (asumo JSON o form-data)
        // getVar() funciona para ambos POST y GET, getPost() es más específico para POST
        $mac = $this->request->getVar('MAC');
        $nivel_gas = $this->request->getVar('nivel_gas');

        // Verificar que se recibieron los datos necesarios
        if ($mac && $nivel_gas !== null) {
            // Preparar los datos para insertar en la base de datos
            $data = [
                'MAC' => $mac,
                'nivel_gas' => $nivel_gas,
                'fecha' => date('Y-m-d H:i:s') // Captura la fecha y hora actual
                // 'update_at' se manejará automáticamente si useTimestamps es true en el modelo,
                // pero tu modelo LecturasGasModel tiene useTimestamps = false.
                // Si necesitas update_at, debes incluirlo en $data y en $allowedFields del modelo.
            ];

            // Insertar los datos en la tabla 'lecturas_gas'
            $inserted = $this->lecturasGasModel->insert($data);

            // Verificar si la inserción fue exitosa
            if ($inserted) {
                // Si fue exitosa, retornar una respuesta JSON de éxito
                 // El ID del registro insertado se puede obtener con $this->lecturasGasModel->insertID()
                return $this->response->setJSON(['status' => 'success', 'message' => 'Lectura guardada correctamente', 'id' => $inserted]);
            } else {
                 // Si hubo un error en la inserción, loguearlo y retornar una respuesta JSON de error
                 log_message('error', 'Error al guardar lectura de gas para MAC: ' . $mac . ' - Datos: ' . json_encode($data) . ' - Error DB: ' . $this->lecturasGasModel->errors());
                 return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al guardar la lectura en la base de datos']);
            }
        } else {
            // Si faltan datos, retornar una respuesta JSON de error
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Faltan datos (MAC o nivel_gas)']);
        }
    }
// NUEVO MÉTODO PARA OBTENER LA ÚLTIMA LECTURA
    public function obtenerUltimaLectura($mac)
    {
        // 1. Obtener el dispositivo por su MAC
        $dispositivo = $this->dispositivosModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }

        // 2. Obtener la última lectura de gas para esta MAC
        // Asumo que tu modelo LecturasGasModel tiene un método para esto.
        // Si no, lo crearemos en el paso 2.
        $ultimaLectura = $this->lecturasGasModel
                               ->where('MAC', $mac)
                               ->orderBy('fecha', 'DESC')
                               ->first();

        // 3. Obtener el estado de la válvula del dispositivo
        $estadoValvula = $dispositivo['estado_valvula'] ?? false; // Asume 'estado_valvula' es el nombre de la columna

        // 4. Devolver la información en formato JSON
        if ($ultimaLectura) {
            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => $ultimaLectura['nivel_gas'],
                'estado_valvula' => $estadoValvula,
                'ultima_actualizacion' => $ultimaLectura['fecha']
            ]);
        } else {
            // Si no hay lecturas, devolvemos un valor por defecto
            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => 0,
                'estado_valvula' => $estadoValvula,
                'message' => 'No hay lecturas disponibles para este dispositivo.'
            ]);
        }
    }
}