<?php

namespace App\Controllers;

use App\Models\DispositivoModel; // Asegúrate de que este use esté presente
use App\Models\EnlaceModel;
use CodeIgniter\Controller; // Asegúrate de extender de Controller o BaseController

// Si extiendes de BaseController, cambia 'extends Controller' a 'extends BaseController'
class EnlaceController extends BaseController // Asumo que extiendes de BaseController
{
    protected $dispositivoModel;
    protected $enlaceModel;

    public function __construct()
    {
        // Llama al constructor de la clase padre si es necesario
        // parent::__construct();

        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel();

        // Cargar helpers necesarios si no están en BaseController
        helper(['form', 'url']);
    }


    // Método para mostrar la vista del formulario de enlace (GET /enlace)
    public function index()
    {
        // Asegúrate de que la vista 'enlace_mac' exista en app/Views/
        return view('enlace_mac');
    }

    // Método para procesar el formulario de enlace (POST /enlace/store)
    public function store()
    {
        // Obtener la MAC del formulario y convertirla a mayúsculas
        $mac = strtoupper($this->request->getPost('mac'));

        // Validar el formato de la MAC (ej. XX:XX:XX:XX:XX:XX o XX-XX-XX-XX-XX-XX)
        if (!preg_match('/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/', $mac)) {
            // Si el formato es inválido, redirigir de vuelta con mensaje de error
            return redirect()->back()->with('error', 'Formato de MAC inválido');
        }

        // Buscar el dispositivo en la tabla 'dispositivos' por la MAC
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        // Verificar si el dispositivo existe en la base de datos
        if (!$dispositivo) {
            // Si no existe, redirigir de vuelta con mensaje de error
            return redirect()->back()->with('error', 'La dirección MAC ingresada es incorrecta.');
        }

        // Obtener el ID del usuario logueado desde la sesión
        $idUsuario = session()->get('id');

        // Verificar si el usuario ya está enlazado a esta MAC en la tabla 'enlace'
        $yaExiste = $this->enlaceModel
            ->where('id_usuario', $idUsuario)
            ->where('MAC', $mac)
            ->first();

        // Si el enlace no existe, crearlo
        if (!$yaExiste) {
            $this->enlaceModel->insert([
                'id_usuario' => $idUsuario, // ID del usuario logueado
                'MAC' => $mac // MAC del dispositivo
            ]);
             // Redirigir a la vista de perfil con un mensaje de éxito
            return redirect()->to('/perfil')->with('success', 'MAC enlazada correctamente');
        } else {
             // Si el enlace ya existe, redirigir de vuelta con un mensaje de error
             return redirect()->back()->with('error', 'Esta MAC ya está enlazada a tu cuenta.');
        }
    }
}
