<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel; // Asegúrate de que este namespace y nombre de modelo sean correctos

class ValveController extends Controller
{
    /**
     * Maneja el comando de abrir o cerrar la válvula para un dispositivo específico.
     * Recibe la MAC del dispositivo y el comando ('open' o 'close') vía JSON POST.
     */
    public function controlValve()
    {
        // Obtener los datos del cuerpo de la solicitud JSON
        $input = $this->request->getJSON();

        $mac = $input->mac ?? null;
        $command = $input->command ?? null;

        // Validar que la MAC y el comando estén presentes
        if (!$mac || !$command) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'MAC y comando son requeridos.'
            ])->setStatusCode(400); // Código de estado HTTP 400 Bad Request
        }

        // Instanciar el modelo del dispositivo
        // Asegúrate de que tu DispositivoModel exista y tenga la columna 'estado_valvula'
        $dispositivoModel = new DispositivoModel();

        // Determinar el valor numérico para 'estado_valvula'
        // 1 para 'open' (abrir), 0 para 'close' (cerrar)
        $estadoValvula = ($command === 'open') ? 1 : 0;

        // Actualizar el estado de la válvula en la base de datos
        // El método 'update' con 'where' solo actualiza los registros que coinciden.
        // Devuelve el número de filas afectadas.
        $updated = $dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estadoValvula])->update();

        if ($updated) {
            // Si la actualización fue exitosa
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Válvula ' . (($command === 'open') ? 'abierta' : 'cerrada') . ' correctamente para el dispositivo ' . $mac . '.'
            ]);
        } else {
            // Si no se pudo actualizar (ej. MAC no encontrada o no hubo cambios)
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el estado de la válvula. Es posible que el dispositivo con MAC "' . $mac . '" no exista o que el estado ya sea el solicitado.'
            ])->setStatusCode(500); // Código de estado HTTP 500 Internal Server Error
        }
    }
}
