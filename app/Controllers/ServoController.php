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
        $mac = session()->get('MAC'); // suponemos que el usuario tiene asociada una MAC
        $estado = $this->servoModel->getEstadoValvula($mac);

        return view('detalles', [
            'estado_valvula' => $estado
        ]);
    }

    // Abrir válvula
    public function abrir()
    {
        $mac = session()->get('MAC');
        $this->servoModel->updateEstadoValvula($mac, 0); // 0 = abierta
        return redirect()->to('/servo');
    }

    // Cerrar válvula
    public function cerrar()
    {
        $mac = session()->get('MAC');
        $this->servoModel->updateEstadoValvula($mac, 1); // 1 = cerrada
        return redirect()->to('/servo');
    }

    // API para ESP32 (consultar estado)
    public function obtenerEstadoValvula()
    {
        $mac = $this->request->getGet('mac');
        $apiKey = $this->request->getGet('api_key');

        // validación de api_key (pon tu clave real)
        if ($apiKey !== "YOUR_SUPER_SECRET_API_KEY_HERE") {
            return $this->response->setStatusCode(403, 'API Key inválida');
        }

        $estado = $this->servoModel->getEstadoValvula($mac);
        return $this->response->setBody((string) $estado);
    }
}
