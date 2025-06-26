<?php

namespace App\Controllers;

use App\Models\ServoModel;
use CodeIgniter\Controller; // Asegúrate de que esta importación esté presente si BaseController extiende Controller
use App\Models\EnlaceModel; // Importar el modelo EnlaceModel

// Asegúrate de que BaseController existe y es accesible.
// Si tu BaseController extiende CodeIgniter\Controller, entonces la línea `use CodeIgniter\Controller;`
// no es estrictamente necesaria aquí, pero no hace daño.
class ServoController extends BaseController
{
    protected $ServoModel;

    public function __construct()
    {
        $this->ServoModel = new ServoModel();
    }

    // Nuevo método para mostrar los detalles del servo en la vista
    // Este método es el que el error original estaba buscando.
    public function mostrarDetalles($mac = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            // Redirigir o mostrar un error si el usuario no está logueado
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        if ($mac === null) {
            // Si no se proporciona una MAC en la URL, redirigir o mostrar un error
            return redirect()->to('/')->with('error', 'MAC de dispositivo no especificada.');
        }

        // Verificar que el usuario tiene permiso sobre este Servo
        $enlaceModel = new EnlaceModel(); // Instancia el EnlaceModel
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                    ->where('MAC', $mac)
                                    ->first();

        if (!$tieneAcceso) {
            // Si el usuario no tiene acceso, redirigir o mostrar un error de permiso
            return redirect()->to('/dashboard')->with('error', 'No tienes permiso para ver este dispositivo.');
        }

        // Obtener los datos del servo usando la MAC
        $dispositivo = $this->ServoModel->getServoByMac($mac);

        if ($dispositivo) {
            // Si el dispositivo se encontró, pasar los datos a la vista 'detalles'
            // La clave 'dispositivo' hace que la variable $dispositivo esté disponible en la vista.
            $data['dispositivo'] = $dispositivo;
            return view('detalles', $data);
        } else {
            // Si el dispositivo no se encontró, redirigir o mostrar un error 404
            return redirect()->to('/dashboard')->with('error', 'Dispositivo no encontrado.');
        }
    }


    // Actualizar estado del servo
    public function actualizarEstado()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        // Verificar que el usuario tiene permiso sobre este Servo
        $enlaceModel = new EnlaceModel(); // Instancia el EnlaceModel
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                    ->where('MAC', $mac)
                                    ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        // Actualizar estado en la base de datos
        $actualizado = $this->ServoModel->updateEstadoValvula($mac, $estado);

        if ($actualizado) {
            return $this->response->setJSON(['success' => true, 'estado' => $estado]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar']);
        }
    }

    // Obtener estado actual del servo (usado para peticiones AJAX, no para cargar la vista)
    public function obtenerEstado($mac)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        // Verificar que el usuario tiene permiso sobre este Servo
        $enlaceModel = new EnlaceModel(); // Instancia el EnlaceModel
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                    ->where('MAC', $mac)
                                    ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        $Servo = $this->ServoModel->getServoByMac($mac);
        
        if ($Servo) {
            return $this->response->setJSON([
                'estado_valvula' => $Servo['estado_valvula'],
                'nombre' => $Servo['nombre'],
                'ubicacion' => $Servo['ubicacion']
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Servo no encontrado']);
        }
    }
}
