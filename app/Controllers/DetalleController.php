<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;

class DetalleController extends BaseController
{
    protected $dispositivoModel;
    protected $enlaceModel;
    protected $lecturasGasModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel();
        $this->lecturasGasModel = new LecturasGasModel();
    }

    public function detalles($mac)
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');

        // Verificar que el usuario tiene acceso a este dispositivo
        $acceso = $this->enlaceModel
            ->where('id_usuario', $usuarioId)
            ->where('MAC', $mac)
            ->first();

        if (!$acceso) {
            return redirect()->to('/perfil')->with('error', 'No tienes acceso a este dispositivo');
        }

        // Obtener información del dispositivo
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado');
        }

        // Obtener lecturas recientes para el gráfico
        $lecturas = $this->lecturasGasModel
            ->where('MAC', $mac)
            ->orderBy('fecha', 'DESC')
            ->limit(50)
            ->findAll();

        // Obtener todos los dispositivos del usuario para el sidebar/navegación
        $dispositivosUsuario = $this->enlaceModel
            ->select('dispositivos.*')
            ->join('dispositivos', 'dispositivos.MAC = enlace.MAC')
            ->where('enlace.id_usuario', $usuarioId)
            ->findAll();

        // Preparar datos para el gráfico
        $datosGrafico = [
            'labels' => [],
            'niveles' => []
        ];

        foreach (array_reverse($lecturas) as $lectura) {
            $datosGrafico['labels'][] = date('H:i', strtotime($lectura['fecha']));
            $datosGrafico['niveles'][] = $lectura['nivel_gas'];
        }

        return view('detalles', [
            'dispositivo' => $dispositivo,
            'lecturas' => $lecturas,
            'dispositivos' => $dispositivosUsuario, // Esta es la variable que falta
            'datosGrafico' => $datosGrafico
        ]);
    }
}