<?php

namespace App\Controllers;

use App\Models\EnlaceModel;

class PerfilController extends BaseController
{
    protected $enlaceModel;

    public function __construct()
    {
        $this->enlaceModel = new EnlaceModel();
    }

    public function index()
    {
        $usuarioId = session()->get('id'); // Asegúrate de que esto esté seteado en el login

        $macs = $this->enlaceModel
                    ->where('id_usuario', $usuarioId)
                    ->findAll();

        return view('perfil', ['macs' => $macs]);
    }

    public function eliminarDispositivos()
    {
        $usuarioId = session()->get('id');
        $macs = $this->request->getPost('macs');  // Obtiene el array de MACs seleccionadas

        if (!empty($macs) && is_array($macs)) {
            $this->enlaceModel->where('id_usuario', $usuarioId)->whereIn('MAC', $macs)->delete();

            return redirect()->to('/perfil')->with('success', 'Dispositivos eliminados correctamente.');
        } else {
            return redirect()->to('/perfil')->with('error', 'No se seleccionaron dispositivos para eliminar.');
        }
    }
}

