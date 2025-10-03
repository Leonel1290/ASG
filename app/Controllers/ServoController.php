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


    // 1. Mostrar vista de detalles (GET /servo)
    public function index()
    {
        // Si el usuario llega a /servo, asumimos que tiene una MAC en sesión.
        $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50"; 
        
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        
        return view('detalles', [
            'estado_valvula' => $dispositivo->estado_valvula ?? 0 // Usar el estado de la DB
        ]);
    }

    // 2. Abrir válvula (POST /servo/abrir)
    public function abrir()
{
    $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50";
    // CORRECCIÓN CLAVE: 1 = Abierta
    $this->dispositivoModel->where('MAC', $mac)->set('estado_valvula', 1)->update(); 
    return redirect()->to('/servo');
}

    // 3. Cerrar válvula (POST /servo/cerrar)
    public function cerrar()
{
    $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50";
    // CORRECCIÓN CLAVE: 0 = Cerrada
    $this->dispositivoModel->where('MAC', $mac)->set('estado_valvula', 0)->update(); 
    return redirect()->to('/servo');
}

    // =================================================================
    // MÉTODOS AJAX (Usados por el frontend)
    // =================================================================


    /**
     * Obtiene el estado actual de la válvula y el último nivel de gas para una MAC específica.
     * Este método es llamado por AJAX desde el frontend para actualizar la vista en tiempo real.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return \CodeIgniter\HTTP\ResponseInterface Una respuesta JSON con el estado de la válvula y el nivel de gas.
     */
    public function obtenerEstado(string $mac)
{
    // 1. Validar la MAC
    if (empty($mac)) {
        return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Falta la MAC.']);
    }

    // 2. Buscar el dispositivo
    $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

    if ($dispositivo) {
        // 3. Devolver el estado actual (0 o 1)
        return $this->response->setJSON([
            'status' => 'success',
            // Asegúrate de que 'estado_valvula' es el nombre de la columna en tu DB
            'estado' => (int)$dispositivo->estado_valvula 
        ]);
    } else {
        // 4. Dispositivo no encontrado
        return $this->response->setStatusCode(404)->setJSON([
            'status' => 'error',
            'message' => 'Dispositivo no encontrado.'
        ]);
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