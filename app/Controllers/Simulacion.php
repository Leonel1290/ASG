<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Simulacion extends Controller
{
    public function index()
    {
        return view('simulacion_simple');
    }
}