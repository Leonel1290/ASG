<?php

namespace App\Controllers;

use App\Models\DispositivoModel; // Asegúrate de importar tu modelo de Dispositivo
use CodeIgniter\RESTful\ResourceController;

class ServoController extends ResourceController
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    /**
     * Obtiene el estado actual de la válvula y el último nivel de gas para una MAC específica.
     * Este método es llamado por AJAX desde el frontend para actualizar la vista en tiempo real.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return \CodeIgniter\HTTP\ResponseInterface Una respuesta JSON con el estado de la válvula y el nivel de gas.
     */
    public function obtenerEstado($mac)
    {
        // Buscar el dispositivo en la base de datos usando la MAC
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        // Verificar si el dispositivo fue encontrado
        if ($dispositivo) {
            // Si se encuentra, devolver el estado de la válvula y el último nivel de gas.
            // Es importante castear a (bool) para estado_valvula y (float) para nivel_gas
            // para asegurar el tipo de dato correcto en la respuesta JSON.
            return $this->response->setJSON([
                'status' => 'success', // ¡Asegúrate de incluir este campo!
                'estado_valvula' => (bool)$dispositivo['estado_valvula'],
                'nivel_gas' => (float)$dispositivo['ultimo_nivel_gas'] // ¡Este es el valor que el velocímetro usará!
            ]);
        } else {
            // Si el dispositivo no se encuentra, devolver un error 404
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }

    /**
     * Actualiza el estado de la válvula para una MAC específica.
     * Este método es llamado por AJAX desde el frontend cuando se presiona un botón.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface Una respuesta JSON con el nuevo estado de la válvula.
     */
    public function actualizarEstado()
    {
        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        if ($mac === null || $estado === null) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'MAC o estado no proporcionados.']);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            $updated = $this->dispositivoModel->where('MAC', $mac)->set('estado_valvula', $estado)->update();

            if ($updated) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Estado de válvula actualizado.', 'estado' => (bool)$estado]);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al actualizar el estado de la válvula en la base de datos.']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }
}
