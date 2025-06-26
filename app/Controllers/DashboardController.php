<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DashboardController extends BaseController
{
    public function index()
    {
        // Puedes cargar una vista para el dashboard aquí, por ejemplo:
        // return view('dashboard_view');

        // Por ahora, solo mostraremos un mensaje simple para confirmar que la ruta funciona.
        return "Bienvenido a tu Dashboard!";
    }
}
