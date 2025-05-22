<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class valveController extends ResourceController
{
    use ResponseTrait;

    protected $format    = 'json'; // Responder en formato JSON

    public function __construct()
    {
        // Puedes cargar helpers o modelos aquí si los necesitas para la lógica de la válvula
        // Por ejemplo, un modelo para actualizar el estado de la válvula en la DB
    }

    public function controlValve()
    {
        $input = $this->request->getJSON(); // Obtener el cuerpo de la petición JSON

        $mac = $input->mac ?? null;
        $command = $input->command ?? null; // 'open' o 'close'

        if (empty($mac) || !in_array($command, ['open', 'close'])) {
            return $this->failValidationError('MAC del dispositivo o comando inválido.');
        }

        // --- LÓGICA PARA COMUNICARSE CON EL DISPOSITIVO FÍSICO ---
        // Aquí es donde deberías implementar la comunicación con tu dispositivo.
        // Esto podría ser:
        // 1. Enviar una petición HTTP a una API de un servidor intermedio (MQTT, WebSockets, etc.)
        // 2. Enviar un comando a un servicio que interactúa con el hardware.
        // 3. Registrar el comando en una base de datos para que el dispositivo lo "lea" y ejecute.

        // Ejemplo simulado de lógica (Reemplazar con tu implementación real)
        $success = false;
        $message = '';

        try {
            // Simular una acción de comunicación
            if ($command === 'open') {
                // Lógica real para abrir la válvula asociada a $mac
                $message = "Comando 'Abrir Válvula' enviado a la MAC: " . $mac;
                $success = true;
            } elseif ($command === 'close') {
                // Lógica real para cerrar la válvula asociada a $mac
                $message = "Comando 'Cerrar Válvula' enviado a la MAC: " . $mac;
                $success = true;
            }

            // Aquí podrías añadir una validación si la comunicación fue exitosa
            // Por ejemplo, si recibes una confirmación del dispositivo.

        } catch (\Exception $e) {
            log_message('error', 'Error al controlar la válvula para MAC: ' . $mac . ' - ' . $e->getMessage());
            return $this->failServerError('Error interno al procesar el comando: ' . $e->getMessage());
        }

        if ($success) {
            return $this->respondCreated(['status' => 'success', 'message' => $message]);
        } else {
            return $this->fail('No se pudo ejecutar el comando en el dispositivo.', 500);
        }
    }
}