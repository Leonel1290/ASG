<?php

namespace App\Controllers;

use App\Models\ServoModel;
use App\Models\DispositivoModel; // Necesario para la lógica de estado_valvula
use App\Controllers\BaseController; // CAMBIO CLAVE: Extender de BaseController

class ServoController extends BaseController
{
    protected $servoModel;
    protected $dispositivoModel; // Nuevo modelo para estado y lecturas

    public function __construct()
    {
        // Instancia los modelos
        $this->servoModel = new ServoModel();
        $this->dispositivoModel = new DispositivoModel(); // Instancia el modelo de Dispositivo
    }

    // 1. Mostrar vista (index)
    public function index()
    {
        // ... Mantiene la lógica original del usuario ...
        $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50"; 
        
        // Obtener estado actual de la válvula (usando el método del modelo original)
        $estado = $this->servoModel->getEstadoValvula($mac); 

        return view('detalles', [
            'estado_valvula' => $estado ?? 0
        ]);
    }

    // 2. Abrir válvula
    public function abrir()
    {
        $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50";
        // NOTA: Asegúrate que ServoModel::updateEstadoValvula actualice la tabla `dispositivos`
        $this->servoModel->updateEstadoValvula($mac, 0); // 0 = abierta
        return redirect()->to('/servo');
    }

    // 3. Cerrar válvula
    public function cerrar()
    {
        $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50";
        // NOTA: Asegúrate que ServoModel::updateEstadoValvula actualice la tabla `dispositivos`
        $this->servoModel->updateEstadoValvula($mac, 1); // 1 = cerrada
        return redirect()->to('/servo');
    }
    
    // =================================================================
    // MÉTODOS AJAX CORREGIDOS (obtenerEstado y actualizarEstado)
    // =================================================================

    /**
     * @GET /servo/obtenerEstado/(.+)
     * Obtiene el estado de la válvula y nivel de gas para una MAC específica.
     */
    public function obtenerEstado($mac)
    {
        if (empty($mac)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Falta la dirección MAC.']);
        }
        
        // Buscar dispositivo en la DB (usa DispositivoModel)
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Devolver respuesta JSON con los tipos de datos correctos
            return $this->response->setJSON([
                'status' => 'success',
                'estado_valvula' => (bool)($dispositivo->estado_valvula ?? 0), // 0 o 1
                'ultimo_nivel_gas' => (float)($dispositivo->ultimo_nivel_gas ?? 0.0), // Número con decimales
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }

    /**
     * @POST /servo/actualizarEstado
     * Actualiza el estado de la válvula.
     */
    public function actualizarEstado()
    {
        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        if ($mac === null || $estado === null) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'MAC o estado no proporcionados.']);
        }
        
        // Asegurar que el estado es un valor válido (0 o 1)
        $estado = in_array((int)$estado, [0, 1]) ? (int)$estado : null;
        if ($estado === null) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'El valor de estado es inválido (debe ser 0 o 1).']);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Actualizar el estado en la tabla 'dispositivos'
            $updated = $this->dispositivoModel->where('MAC', $mac)->set('estado_valvula', $estado)->update();

            // Si $updated es falso, puede que el valor ya fuera el mismo, lo tratamos como éxito.
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Estado de válvula actualizado.', 
                'estado' => (bool)$estado
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }
    
    // 4. API para ESP32 (consultar estado) - Mantenido
    public function obtenerEstadoValvula()
    {
        // Aunque tienes un ApiEspController, mantengo esta ruta como estaba.
        return $this->response->setStatusCode(200)->setJSON(['message' => 'Endpoint de válvula. Se recomienda usar ApiEspController para la API del dispositivo.']);
    }
}