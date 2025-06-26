<?php

namespace App\Controllers;

use App\Models\ServoModel;
use App\Models\EnlaceModel;
use CodeIgniter\Controller; // Asegúrate de que esta importación sea correcta según tu BaseController

class DetalleController extends BaseController
{
    protected $servoModel;
    protected $enlaceModel;

    public function __construct()
    {
        $this->servoModel = new ServoModel();
        $this->enlaceModel = new EnlaceModel();
    }

    public function detalles($mac = null)
    {
        $session = session();

        // 1. Verificar si el usuario está logueado
        if (!$session->get('logged_in')) {
            // Si no está logueado, redirigir a la página de login con un mensaje de error
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a los detalles del dispositivo.');
        }

        // 2. Verificar si se proporcionó una MAC en la URL
        if ($mac === null) {
            // Si no hay MAC, redirigir al dashboard (o una página de error)
            return redirect()->to('/dashboard')->with('error', 'No se especificó la MAC del dispositivo.');
        }

        // 3. Verificar si el usuario tiene permiso sobre este dispositivo (servo)
        $tieneAcceso = $this->enlaceModel
                            ->where('id_usuario', $session->get('id'))
                            ->where('MAC', $mac)
                            ->first();

        if (!$tieneAcceso) {
            // Si el usuario no tiene acceso, redirigir al dashboard con un mensaje de error
            return redirect()->to('/dashboard')->with('error', 'No tienes permiso para ver los detalles de este dispositivo.');
        }

        // 4. Obtener los datos del dispositivo (servo) usando la MAC
        $dispositivo = $this->servoModel->getServoByMac($mac);

        // 5. Verificar si el dispositivo fue encontrado
        if ($dispositivo) {
            // Si el dispositivo se encontró, pasar los datos a la vista
            // La clave 'dispositivo' es crucial para que la variable $dispositivo esté disponible en 'detalles.php'
            $data['dispositivo'] = $dispositivo;

            // Cargar la vista 'detalles.php' y pasarle los datos
            return view('detalles', $data);
        } else {
            // Si el dispositivo no se encontró en la base de datos
            return redirect()->to('/dashboard')->with('error', 'Dispositivo no encontrado.');
        }
    }
}
    