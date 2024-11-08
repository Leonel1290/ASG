<?php

namespace App\Controllers;

use App\Models\LecturaModel;
use CodeIgniter\RESTful\ResourceController;

class LecturaController extends ResourceController
{
    protected $lecturaModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturaModel();
    }

    // Método para actualizar o insertar lectura de gas
    public function actualizarLectura()
    {
        $usuario_id = $this->request->getPost('usuario_id');
        $nivel_gas = $this->request->getPost('nivel_gas');

        if (!$usuario_id || !$nivel_gas) {
            return $this->respond(['status' => 'error', 'message' => 'Datos insuficientes'], 400);
        }

        // Actualiza o inserta el nivel de gas del usuario
        $result = $this->lecturaModel->updateLectura($usuario_id, $nivel_gas);

        if ($result) {
            return $this->respond(['status' => 'success', 'message' => 'Lectura actualizada correctamente']);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Error al actualizar lectura'], 500);
        }
    }
}
public function verUltimaLectura()
{
    $model = new LecturaModel();  // Asumiendo que has configurado LecturaModel
    $ultimaLectura = $model->orderBy('created_at', 'DESC')->first();  // Obtener la última lectura

    return view('ver_lectura', ['lectura' => $ultimaLectura]);
}
