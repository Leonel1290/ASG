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
        // Asegúrate de que esta variable de entorno esté configurada en Render
        // con el nombre 'API_KEY_SECRET' y su valor correspondiente.
        $apiKeyExpected = getenv('API_KEY_SECRET'); // MODIFICADO: Usar 'API_KEY_SECRET'

        // 3. Validar la clave API
        if (empty($apiKeyExpected) || $apiKeyReceived !== $apiKeyExpected) {
            // Aseguramos que la respuesta sea texto plano para evitar inyecciones HTML
            return $this->response->setStatusCode(401)->setContentType('text/plain')->setBody('-2'); // Clave API inválida
        }

        // 4. Validar la MAC
        if (empty($mac)) {
            // Aseguramos que la respuesta sea texto plano
            return $this->response->setStatusCode(400)->setContentType('text/plain')->setBody('-4'); // Falta parámetro
        }

        // 5. Consultar el estado de la válvula
        $model = new DispositivoModel();
        // Asegúrate de que la columna 'MAC' en tu base de datos no tenga espacios extra
        // y que la MAC enviada por el ESP coincida exactamente.
        $dispositivo = $model->where('MAC', $mac)->first();

        if ($dispositivo) {
            // Aseguramos que la respuesta sea texto plano
            return $this->response->setStatusCode(200)->setContentType('text/plain')->setBody((string)$dispositivo->estado_valvula);
        } else {
            // Aseguramos que la respuesta sea texto plano
            return $this->response->setStatusCode(404)->setContentType('text/plain')->setBody('-1'); // Dispositivo no encontrado
        }
    }
}