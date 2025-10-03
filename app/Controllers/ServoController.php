<?php

namespace App\Controllers;

use App\Models\DispositivoModel; // Necesario para gestionar el estado_valvula
use App\Controllers\BaseController; // Extender de BaseController

class ServoController extends BaseController
{
    protected $dispositivoModel;
    // Se elimina ServoModel si su única función era actualizar estado_valvula
    // (el cual está en la tabla 'dispositivos').

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
     * @GET /servo/obtenerEstado/(.+)
     * Obtiene el estado de la válvula y nivel de gas para una MAC específica.
     */
    public function obtenerEstado($mac)
    {
        if (empty($mac)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Falta la dirección MAC.']);
        }
        
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setJSON([
                'status' => 'success',
                // El frontend espera un booleano, y 1=true, 0=false, lo cual es correcto.
                'estado_valvula' => (bool)($dispositivo->estado_valvula ?? 0), 
                'ultimo_nivel_gas' => (float)($dispositivo->ultimo_nivel_gas ?? 0.0),
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
        $estado_int = $this->request->getPost('estado'); // Estado recibido (0 o 1)

        if ($mac === null || $estado_int === null) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'MAC o estado no proporcionados.']);
        }
        
        // Asegurar que el estado es un valor válido (0 o 1)
        $estado_int = in_array((int)$estado_int, [0, 1]) ? (int)$estado_int : null;
        if ($estado_int === null) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'El valor de estado es inválido (debe ser 0 o 1).']);
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Se usa el valor de estado_int (0 o 1) directamente ya que el frontend ya lo envía.
            $this->dispositivoModel->where('MAC', $mac)->set('estado_valvula', $estado_int)->update();

            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Estado de válvula actualizado.', 
                // Devolvemos el estado como booleano para el frontend
                'estado' => (bool)$estado_int
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Dispositivo no encontrado.']);
        }
    }
}