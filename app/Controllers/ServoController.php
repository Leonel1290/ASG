<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\API\ResponseTrait;

class ServoController extends BaseController
{
    use ResponseTrait;

    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
        helper('url');
    }

    /**
     * Obtiene el estado actual de la válvula - CORREGIDO
     */
    public function obtenerEstado($mac = null)
    {
        try {
            // Verificar que se proporcionó la MAC
            if (empty($mac)) {
                return $this->fail('MAC no proporcionada', 400);
            }

            // Decodificar la MAC (los %3A se convierten a :)
            $mac = urldecode($mac);
            $mac = str_replace('%3A', ':', $mac);
            
            log_message('debug', 'MAC recibida: ' . $mac);

            // Validar formato básico de MAC
            if (!preg_match('/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/', $mac)) {
                return $this->fail('Formato de MAC inválido: ' . $mac, 400);
            }

            // Buscar el dispositivo
            $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

            if ($dispositivo) {
                return $this->respond([
                    'status' => 'success',
                    'estado_valvula' => (bool)$dispositivo['estado_valvula'],
                    'mensaje' => 'Estado obtenido correctamente'
                ]);
            } else {
                return $this->fail('Dispositivo no encontrado para MAC: ' . $mac, 404);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error en obtenerEstado: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza el estado de la válvula - CORREGIDO
     */
    public function actualizarEstado()
    {
        try {
            // Verificar método HTTP
            if (!$this->request->is('post')) {
                return $this->fail('Método no permitido', 405);
            }

            // Verificar CSRF token
            if (!csrf_filter()) {
                return $this->fail('Token CSRF inválido', 403);
            }

            // Obtener datos del POST
            $mac = $this->request->getPost('mac');
            $estado = $this->request->getPost('estado');

            // Validaciones
            if (empty($mac)) {
                return $this->fail('MAC no proporcionada', 400);
            }

            if ($estado === null) {
                return $this->fail('Estado no proporcionado', 400);
            }

            // Limpiar y validar MAC
            $mac = str_replace('%3A', ':', $mac);
            if (!preg_match('/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/', $mac)) {
                return $this->fail('Formato de MAC inválido', 400);
            }

            // Convertir a booleano
            $estado = (bool)$estado;

            // Buscar dispositivo
            $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

            if (!$dispositivo) {
                return $this->fail('Dispositivo no encontrado', 404);
            }

            // Actualizar estado
            $data = [
                'estado_valvula' => $estado,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->dispositivoModel->update($dispositivo['id'], $data);

            if ($updated) {
                log_message('info', "Válvula " . ($estado ? "ABIERTA" : "CERRADA") . " para MAC: {$mac}");
                
                return $this->respond([
                    'status' => 'success', 
                    'message' => 'Válvula ' . ($estado ? 'abierta' : 'cerrada') . ' correctamente', 
                    'estado' => $estado,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else {
                return $this->fail('Error al actualizar el estado de la válvula', 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error en actualizarEstado: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }
}