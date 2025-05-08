<?php

namespace App\Controllers;

use App\Models\DispositivoModel; // Asegúrate de que este use esté presente
use App\Models\EnlaceModel;

class EnlaceController extends BaseController
{
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

        $dispositivoModel = new DispositivoModel();
        $enlaceModel = new EnlaceModel();

        $dispositivo = $dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return redirect()->back()->with('error', 'La dirección MAC ingresada es incorrecta.');
        }

        $idUsuario = session()->get('id');

        $yaExiste = $enlaceModel
            ->where('id_usuario', $idUsuario)
            ->where('MAC', $mac)
            ->first();

        if (!$yaExiste) {
            $enlaceModel->insert([
                'id_usuario' => $idUsuario,
                'MAC' => $mac
            ]);
        }

        // Redirigir a la vista de perfil en lugar de volver atrás
        return redirect()->to('/perfil')->with('success', 'MAC enlazada correctamente');
    }
}
