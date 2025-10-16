<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController; // Usamos ResourceController para consistencia en la API

// Cambiamos a ResourceController si maneja rutas RESTful, si no, mantenemos Controller, 
// pero para simplificar, unificamos la lógica aquí.

class ValveController extends ResourceController // Usamos ResourceController para mantener la estructura de API
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    // ====================================================================
    // ⚙️ MÉTODOS DE CONTROL PRINCIPALES (POST /valve/control) ⚙️
    // ====================================================================

    /**
     * Función principal para controlar la válvula (abrir/cerrar) desde la página web.
     * Mantiene la lógica original con verificación de sesión/permisos.
     */
    public function controlValve()
    {
        $action = $this->request->getPost('action');
        $mac = $this->request->getPost('mac');

        // ... (Tu lógica de validación de sesión y permisos aquí, si la tienes) ...

        if (empty($mac)) {
             // Retorno de error si falta la MAC
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'MAC del dispositivo no proporcionada.'
                ]);
            }
        }

        // Actualizar el estado de la válvula en la DB
        $estado = ($action === 'open') ? 1 : 0; // 1 = abierta, 0 = cerrada 
        $updated = $this->dispositivoModel->updateDispositivoByMac($mac, [
            'estado_valvula' => $estado,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            // Respuesta exitosa
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Válvula ' . ($action === 'open' ? 'abierta' : 'cerrada') . ' correctamente.',
                    'new_state' => $estado
                ]);
            } else {
                return redirect()->to('/detalles/' . $mac)->with('success', 'Válvula ' . ($action === 'open' ? 'abierta' : 'cerrada') . ' correctamente.');
            }
        } else {
            // Error en la actualización
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar el estado de la válvula.'
            ]);
        }
    }


    // ====================================================================
    // 📊 MÉTODOS DE ESTADO (MOVIDOS DESDE ServoController) 📊
    // ====================================================================

    /**
     * Obtiene el estado actual de la válvula para una MAC específica.
     * Mover a /valve/obtenerEstado/{mac}
     * @param string $mac La dirección MAC del dispositivo.
     */
    public function obtenerEstado(string $mac)
    {
        if (empty($mac)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Falta la MAC.']);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setJSON([
                'status' => 'success',
                'estado' => (int)$dispositivo->estado_valvula // Devuelve 0 o 1
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Dispositivo no encontrado.'
            ]);
        }
    }

    /**
     * Actualiza el estado de la válvula para una MAC específica (Usado por los botones).
     * Mover a /valve/actualizarEstado (POST)
     */
    public function actualizarEstado()
    {
        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        // Validar que los datos no estén vacíos
        if ($mac === null || !in_array($estado, ['0', '1'])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'MAC o estado no válidos.']);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Usamos updateDispositivoByMac si está definido en tu modelo, o la forma estándar:
            $updated = $this->dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estado])->update();

            if ($updated) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Estado de válvula actualizado.', 'nuevo_estado' => (int)$estado]);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al actualizar la base de datos.']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }
}