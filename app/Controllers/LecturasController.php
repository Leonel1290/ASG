<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\DispositivoModel; // Agregado: Necesario para actualizar la tabla 'dispositivos'
use CodeIgniter\RESTful\ResourceController; 

class LecturasController extends ResourceController 
{
    protected $lecturasGasModel;
    protected $dispositivoModel; // Agregado: Propiedad para el modelo de dispositivos

    public function __construct()
    {
        // Instancia los modelos
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel(); // Instanciamos el modelo
    }

    /**
     * @POST /lecturas_gas/guardar
     * Método principal para recibir y guardar lecturas de gas del ESP32.
     * * CORRECCIÓN CLAVE: NO actualiza 'estado_valvula' para evitar sobrescribir la orden del usuario.
     */
    public function guardar()
    {
        // Obtener los datos enviados en la solicitud POST
        $mac = $this->request->getVar('MAC');
        $nivel_gas = $this->request->getVar('nivel_gas');
        // El campo 'estado_valvula' que podría enviar el ESP32 se ignora aquí.
        
        if ($mac && $nivel_gas !== null) {
            
            // 1. Preparar y guardar la lectura histórica en 'lecturas_gas'
            $dataLectura = [
                'MAC' => $mac,
                'nivel_gas' => $nivel_gas,
                'fecha' => date('Y-m-d H:i:s'),
                // NOTA: Se asume que 'usuario_id' no es enviado por el ESP32 y es NULLable.
            ];
            $this->lecturasGasModel->insert($dataLectura);

            // 2. Actualizar la tabla 'dispositivos' con los datos más recientes
            $dataDispositivo = [
                'ultimo_nivel_gas' => $nivel_gas,
                'ultima_actualizacion' => date('Y-m-d H:i:s'),
                // ¡CORRECCIÓN! NO incluimos 'estado_valvula'.
                // Así, solo se actualiza el nivel de gas y la fecha,
                // manteniendo la orden de la válvula (1=abierta o 0=cerrada) intacta.
            ];
            
            // Actualizamos la tabla 'dispositivos'
            $this->dispositivoModel->where('MAC', $mac)->set($dataDispositivo)->update();

            // Retornar una respuesta JSON de éxito
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success', 
                'message' => 'Lectura guardada y dispositivo actualizado correctamente.'
            ]);
        } else {
            // Si faltan datos, retornar una respuesta JSON de error
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Faltan datos (MAC o nivel_gas).']);
        }
    }
    
    /**
     * @GET /lecturas/obtenerUltimaLectura/(.+)
     * Obtiene la última lectura de gas y el estado de la válvula para el frontend.
     * (Preservada y completada para no eliminar funcionalidades)
     */
    public function obtenerUltimaLectura($mac = null)
    {
        if (empty($mac)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Falta la dirección MAC.']);
        }
        
        // 1. Buscar dispositivo
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();
        if (!$dispositivo) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }

        // 2. Obtener la última lectura de gas para esta MAC
        $ultimaLectura = $this->lecturasGasModel
                               ->where('MAC', $mac)
                               ->orderBy('fecha', 'DESC')
                               ->first();

        // 3. Obtener el estado de la válvula del dispositivo (Valor que debe mantenerse)
        $estadoValvula = $dispositivo->estado_valvula ?? 0;

        // 4. Devolver la información en formato JSON
        if ($ultimaLectura) {
            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => (float)$ultimaLectura->nivel_gas,
                'estado_valvula' => (bool)$estadoValvula, // Devolver como booleano para el frontend
                'ultima_actualizacion' => $ultimaLectura->fecha
            ]);
        } else {
            // Si no hay lecturas, devolvemos un valor por defecto
            return $this->response->setJSON([
                'status' => 'success',
                'nivel_gas' => 0.0,
                'estado_valvula' => (bool)$estadoValvula,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // NOTA: Si tenías otros métodos como detalle(), revísalos y agrégalos aquí si son necesarios.
}