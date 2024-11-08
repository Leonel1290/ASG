<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class registerController extends Controller
{
    public function index()
    {
        return view('register'); // Tu vista del formulario
    }

    public function store()
    {
        // Validación del formulario
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nombre'  => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]',
            'email'   => 'required|valid_email|is_unique[usuarios.email]',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Procesar los datos del formulario
        $userModel = new UserModel();

        // Guardar el usuario
        $userModel->save([
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT), // Encriptar la contraseña
        ]);

        return redirect()->to('/loginobtener')->with('success', 'Usuario registrado con éxito');
    }
}
