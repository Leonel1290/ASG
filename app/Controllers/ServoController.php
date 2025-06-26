<?php

namespace App\Controllers;

use App\Models\ServoModel;
use App\Models\EnlaceModel;
use App\Models\DispositivoModel;
use CodeIgniter\Controller;

class ServoController extends BaseController
{
    protected $ServoModel;
    protected $DispositivoModel;
    protected $EnlaceModel;

    // !!! MUY IMPORTANTE: Define tu API Key secreta aquí. Debe ser la misma que en el ESP32.
    // En una aplicación real, esto se cargaría desde un archivo de configuración seguro (ej. .env)
    const DEVICE_API_KEY = "leo123"; 

    public function __construct()
    {
        $this->ServoModel = new ServoModel();
        $this->DispositivoModel = new DispositivoModel();
        $this->EnlaceModel = new EnlaceModel();
    }

    /**
     * Valida si la solicitud proviene de un usuario logueado (sesión)
     * o de un dispositivo con la API Key correcta.
     * @return bool True si está autorizado, false en caso contrario.
     */
    private function _checkAuthentication()
    {
        $session = session();
        
        // Primero, intentar autenticar por sesión (para usuarios web)
        if ($session->get('logged_in')) {
            return true;
        }

        // Si no está logueado, intentar autenticar por API Key (para dispositivos)
        $apiKey = $this->request->getHeaderLine('X-API-Key');

        if (!empty($apiKey) && $apiKey === self::DEVICE_API_KEY) {
            return true;
        }

        return false;
    }

    // Método para mostrar los detalles del servo en la vista web (para usuarios humanos)
    public function mostrarDetalles($mac = null)
    {
        $session = session();
        $data = []; // Inicializar array de datos para la vista

        if (!$session->get('logged_in')) {
            // Si el usuario web no está logueado, redirigir a login
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        if ($mac === null) {
            $data['dispositivo'] = null;
            $data['error_message'] = 'No se especificó la MAC del dispositivo.';
            return view('detalles', $data);
        }

        // Verificar que el usuario tiene permiso sobre este Servo
        // Esta verificación sigue siendo solo para usuarios web con sesiones
        $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                        ->where('MAC', $mac)
                                        ->first();

        if (!$tieneAcceso) {
            $data['dispositivo'] = null;
            $data['error_message'] = 'No tienes permiso para ver este dispositivo.';
            return view('detalles', $data);
        }

        // Obtener los datos del dispositivo (donde está estado_valvula)
        $dispositivo = $this->DispositivoModel->getDispositivoByMac($mac);

        if ($dispositivo) {
            $data['dispositivo'] = $dispositivo;
            return view('detalles', $data);
        } else {
            $data['dispositivo'] = null;
            $data['error_message'] = 'Dispositivo no encontrado.';
            return view('detalles', $data);
        }
    }

    // Actualizar estado del servo (usado por la web o el dispositivo)
    public function actualizarEstado()
    {
        // Validar autenticación: API Key para dispositivo o sesión para web
        if (!$this->_checkAuthentication()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        $mac = $this->request->getPost('mac');
        $estado = $this->request->getPost('estado');

        // Para dispositivos, no usamos id_usuario. La validación de acceso se basará en la API Key y la MAC.
        // Para usuarios web, la validación de acceso basada en id_usuario y MAC es necesaria.
        $session = session();
        $tieneAcceso = false;
        if ($session->get('logged_in')) {
            // Si hay sesión activa, verificar que el usuario tiene permiso sobre este Servo
            $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                            ->where('MAC', $mac)
                                            ->first();
        } else {
            // Si es un dispositivo autenticado por API Key, asumimos que tiene permiso si la API Key es válida.
            // Opcional: Aquí podrías añadir una validación más compleja si el dispositivo solo puede controlar
            // ciertas MACs asociadas a su API Key. Por simplicidad, si la API Key es correcta, se permite.
            $tieneAcceso = true; // Si llegamos aquí con API Key válida, asumimos acceso.
        }

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        // Actualizar 'estado_valvula' en la tabla 'dispositivos' usando DispositivoModel
        $actualizado = $this->DispositivoModel->updateDispositivoByMac($mac, ['estado_valvula' => $estado]);

        if ($actualizado) {
            return $this->response->setJSON(['success' => true, 'estado' => $estado]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar']);
        }
    }

    // Obtener estado actual del servo (usado por la web o el dispositivo)
    public function obtenerEstado($mac)
    {
        // Validar autenticación: API Key para dispositivo o sesión para web
        if (!$this->_checkAuthentication()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        // Similar a actualizarEstado, manejar la lógica de permiso para web vs. dispositivo
        $session = session();
        $tieneAcceso = false;
        if ($session->get('logged_in')) {
            $tieneAcceso = $this->EnlaceModel->where('id_usuario', $session->get('id'))
                                            ->where('MAC', $mac)
                                            ->first();
        } else {
            // Si es un dispositivo autenticado por API Key, asumimos permiso
            $tieneAcceso = true; 
        }

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para este Servo']);
        }

        // Obtener el dispositivo de la tabla 'dispositivos' para obtener 'estado_valvula', 'nombre', 'ubicacion'
        $dispositivo = $this->DispositivoModel->getDispositivoByMac($mac);
        
        if ($dispositivo) {
            return $this->response->setJSON([
                'estado_valvula' => $dispositivo->estado_valvula,
                'nombre' => $dispositivo->nombre,
                'ubicacion' => $dispositivo->ubicacion
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Dispositivo no encontrado']);
        }
    }
}
