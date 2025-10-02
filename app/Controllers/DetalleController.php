<?php

namespace App\Controllers;

use App\Models\ServoModel;
use App\Models\EnlaceModel;
use App\Models\DispositivoModel; // Asegúrate de que este modelo está importado
use CodeIgniter\Controller;

class DetalleController extends BaseController
{
    protected $servoModel;
    protected $enlaceModel;
    protected $dispositivoModel; // Instanciaremos DispositivoModel

    public function __construct()
    {
        $this->servoModel = new ServoModel();
        $this->enlaceModel = new EnlaceModel();
        $this->dispositivoModel = new DispositivoModel(); // Instanciar DispositivoModel
    }

    public function detalles($mac = null)
    {
        $session = session();
        $data = []; // Inicializar array de datos para la vista

        // 1. Verificar si el usuario está logueado
        if (!$session->get('logged_in')) {
            // Si no está logueado, redirigir a la página de login con un mensaje de error
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a los detalles del dispositivo.');
        }

        // 2. Verificar si se proporcionó una MAC en la URL
        if ($mac === null) {
            // Si no hay MAC, preparar un mensaje de error para mostrar en la vista detalles.php
            $data['dispositivo'] = null; // No hay dispositivo para pasar
            $data['error_message'] = 'No se especificó la MAC del dispositivo.';
            return view('detalles', $data); // Cargar la vista con el error
        }

        // 3. Verificar si el usuario tiene permiso sobre este dispositivo (servo)
        $tieneAcceso = $this->enlaceModel
                            ->where('id_usuario', $session->get('id'))
                            ->where('MAC', $mac)
                            ->first();

        if (!$tieneAcceso) {
            // Si el usuario no tiene acceso, preparar un mensaje de error para mostrar en la vista detalles.php
            $data['dispositivo'] = null; // No hay dispositivo para pasar
            $data['error_message'] = 'No tienes permiso para ver los detalles de este dispositivo.';
            return view('detalles', $data); // Cargar la vista con el error
        }

        // 4. Obtener los datos del dispositivo (servo) usando la MAC
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);

        // 5. Verificar si el dispositivo fue encontrado
        if ($dispositivo) {
            // Si el dispositivo se encontró, pasar los datos a la vista
            $data['dispositivo'] = $dispositivo;
            return view('detalles', $data);
        } else {
            // Si el dispositivo no se encontró en la base de datos
            $data['dispositivo'] = null; // No hay dispositivo para pasar
            $data['error_message'] = 'Dispositivo no encontrado en la base de datos.';
            return view('detalles', $data); // Cargar la vista con el error
        }
    }
}
