<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
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
            return redirect()->back()->with('error', 'MAC no encontrada');
        }

        $idUsuario = session()->get('id'); // Asegúrate de que esto esté en la sesión al iniciar sesión

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

        return redirect()->back()->with('success', 'MAC enlazada correctamente');
    }
}
