<?php

namespace App\Controllers;

use App\Models\AlertaModel;
use CodeIgniter\Controller;

class AlertasController extends BaseController
{
    public function index()
    {
        // Verifica si el usuario ha iniciado sesión
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $alertaModel = new AlertaModel();
        $userId = session()->get('id');
        $umbral = 400; // Define el umbral de gas para considerar una alerta

        $data = [
            'alertas' => $alertaModel->getAlertasPorUsuario($userId, $umbral),
            'nombre' => session()->get('nombre'),
            'email' => session()->get('email')
        ];

        return view('alertas/index', $data);
    }
}
