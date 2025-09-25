<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use CodeIgniter\Controller;

class EnlaceController extends BaseController
{
    protected $dispositivoModel;
    protected $enlaceModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        return view('enlace_mac');
    }

    public function store()
    {
        $mac = strtoupper($this->request->getPost('mac'));

        if (!preg_match('/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/', $mac)) {
            return redirect()->back()->with('error', 'Formato de MAC inválido');
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return redirect()->back()->with('error', 'La dirección MAC ingresada es incorrecta.');
        }

        $idUsuario = session()->get('id');

        $yaExiste = $this->enlaceModel
            ->where('id_usuario', $idUsuario)
            ->where('MAC', $mac)
            ->first();

        if (!$yaExiste) {
            $this->enlaceModel->insert([
                'id_usuario' => $idUsuario,
                'MAC' => $mac
            ]);
            return redirect()->to('/perfil')->with('success', 'MAC enlazada correctamente');
        } else {
            return redirect()->back()->with('error', 'Esta MAC ya está enlazada a tu cuenta.');
        }
    }

    /**
     * Muestra la página de detalles para un dispositivo específico.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return mixed
     */
    public function detalles($mac)
    {
        $session = session();

        // 1. Verificar si el usuario está logueado
        if (!$session->get('logged_in')) {
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');

        // 2. Verificar que el dispositivo está enlazado a este usuario (Seguridad)
        $enlace = $this->enlaceModel->where('MAC', $mac)
                                    ->where('id_usuario', $usuarioId)
                                    ->first();

        if (!$enlace) {
            // Si no hay enlace, el usuario no tiene permiso para ver este dispositivo.
            return view('detalles', ['error_message' => 'No tienes permiso para acceder a este dispositivo o no existe.']);
        }

        // 3. Obtener los datos del dispositivo
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        // 4. Si no se encuentra el dispositivo en la tabla de dispositivos, mostrar error
        if (!$dispositivo) {
            return view('detalles', ['error_message' => 'Dispositivo no encontrado en la base de datos.']);
        }

        // 5. Cargar la vista con los datos del dispositivo
        return view('detalles', ['dispositivo' => $dispositivo]);
    }
}