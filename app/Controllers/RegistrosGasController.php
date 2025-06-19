<?php

namespace App\Controllers;

use App\Models\RegistrosGasModel;

class RegistrosGasController extends BaseController
{
    protected $registrosGasModel;

    public function __construct()
    {
        $this->registrosGasModel = new RegistrosGasModel();
    }

    public function index()
    {
        $dispositivos = $this->registrosGasModel->getDispositivosUnicos();
        
        $data = [
            'title' => 'Registros de Gas por Dispositivo',
            'dispositivos' => $dispositivos
        ];

        return view('registros_gas/index', $data);
    }

    public function verDispositivo($dispositivoId)
    {
        $lecturas = $this->registrosGasModel->getLecturasPorDispositivo($dispositivoId);
        $ultimaLectura = $this->registrosGasModel->getUltimaLectura($dispositivoId);
        
        if (empty($lecturas)) {
            return redirect()->back()->with('error', 'No se encontraron lecturas para este dispositivo');
        }

        $data = [
            'title' => 'Detalle del Dispositivo',
            'mac' => $dispositivoId,
            'nombreDispositivo' => $ultimaLectura['nombre_dispositivo'] ?? $dispositivoId,
            'ubicacionDispositivo' => $ultimaLectura['ubicacion'] ?? 'Desconocida',
            'lecturas' => $lecturas,
            'nivelGasActualDisplay' => ($ultimaLectura['nivel_gas'] ?? '0') . ' PPM'
        ];

        return view('detalle_dispositivo', $data);
    }
}