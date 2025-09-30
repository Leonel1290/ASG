<?php

namespace App\Controllers;

use App\Models\ServoModel;
use CodeIgniter\Controller;

class ServoController extends Controller
{
    protected $servoModel;

    public function __construct()
    {
        $this->servoModel = new ServoModel();
    }

    // Mostrar vista
    public function index()
    {
        // Intentamos obtener la MAC del usuario
        $mac = session()->get('MAC');

        // Si no hay MAC en sesión, usamos una de prueba
        if (!$mac) {
            $mac = "CC:7B:5C:A8:0F:50"; 
        }

        // Obtener estado actual de la válvula
        $estado = $this->servoModel->getEstadoValvula($mac);

        // Pasamos la variable siempre con un valor por defecto
        return view('detalles', [
            'estado_valvula' => $estado ?? 0
        ]);
    }

    // Abrir válvula
    public function abrir()
    {
        $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50";
        $this->servoModel->updateEstadoValvula($mac, 0); // 0 = abierta
        return redirect()->to('/servo');
    }

    // Cerrar válvula
    public function cerrar()
    {
        $mac = session()->get('MAC') ?? "CC:7B:5C:A8:0F:50";
        $this->servoModel->updateEstadoValvula($mac, 1); // 1 = cerrada
        return redirect()->to('/servo');
    }

    // API para ESP32 (consultar estado)
    public function obtenerEstadoValvula()
    {
        $mac = $this->request->getGet('mac');
        $apiKey = $this->request->getGet('api_key');

        // Validación de api_key (pon tu clave real aquí)
        if ($apiKey !== "YOUR_SUPER_SECRET_API_KEY_HERE") {
            return $this->response->setStatusCode(403, 'API Key inválida');
        }

        $estado = $this->servoModel->getEstadoValvula($mac);
        return $this->response->setBody((string) ($estado ?? 0));
    }
}
