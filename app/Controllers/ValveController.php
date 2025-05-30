<?php
// En tu Controlador de CodeIgniter (ej. DeviceController.php)

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DispositivoModel; // Suponiendo que tienes un modelo para la tabla 'dispositivos'

class Api extends Controller
{
    public function valve_control()
    {
        $input = $this->request->getJSON(); // Obtener la entrada JSON

        $mac = $input->mac ?? null;
        $command = $input->command ?? null;

        if (!$mac || !$command) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'MAC y comando son requeridos.'
            ])->setStatusCode(400); // Solicitud incorrecta
        }

        $dispositivoModel = new DispositivoModel(); // Instanciar tu DispositivoModel

        // Determinar el estado de la válvula a establecer
        $estadoValvula = ($command === 'open') ? 1 : 0; // 1 para abrir, 0 para cerrar

        // Actualizar la base de datos
        $updated = $dispositivoModel->where('MAC', $mac)->set(['estado_valvula' => $estadoValvula])->update();

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Válvula ' . (($command === 'open') ? 'abierta' : 'cerrada') . ' correctamente para el dispositivo ' . $mac . '.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el estado de la válvula o el dispositivo no fue encontrado.'
            ])->setStatusCode(500); // Error interno del servidor
        }
    }
}

// Ejemplo de un DispositivoModel simple (si aún no tienes uno)
// namespace App\Models;

// use CodeIgniter\Model;

// class DispositivoModel extends Model
// {
//     protected $table      = 'dispositivos';
//     protected $primaryKey = 'id';
//     protected $allowedFields = ['MAC', 'nombre', 'ubicacion', 'estado_valvula']; // Añadir estado_valvula aquí
//     protected $useTimestamps = true;
//     protected $createdField  = 'created_at';
//     protected $updatedField  = 'updated_at';
// }

?>
