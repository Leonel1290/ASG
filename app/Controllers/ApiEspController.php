<?php
namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\RESTful\ResourceController;

class ApiEspController extends ResourceController
{
    public function estadoValvula()
    {
        // 1. Obtener parámetros GET
        $mac = $this->request->getGet('mac');
        $apiKeyReceived = $this->request->getGet('api_key');

        // 2. Leer la clave API desde variable de entorno
        $apiKeyExpected = getenv('YOUR_SUPER_SECRET_API_KEY_HERE');

        // 3. Validar la clave API
        if (empty($apiKeyExpected) || $apiKeyReceived !== $apiKeyExpected) {
            return $this->response->setStatusCode(401)->setBody('-2'); // Clave API inválida
        }

        // 4. Validar la MAC
        if (empty($mac)) {
            return $this->response->setStatusCode(400)->setBody('-4'); // Falta parámetro
        }

        // 5. Consultar el estado de la válvula
        $model = new DispositivoModel();
        $dispositivo = $model->where('MAC', $mac)->first();

        if ($dispositivo) {
            return $this->response->setStatusCode(200)->setBody((string)$dispositivo->estado_valvula);
        } else {
            return $this->response->setStatusCode(404)->setBody('-1'); // Dispositivo no encontrado
        }
    }
}
