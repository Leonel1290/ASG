<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Simulacion extends Controller
{
    public function index()
    {
        // Puedes pasar datos a la vista si es necesario
        return view('simulacion_modal');
    }
}