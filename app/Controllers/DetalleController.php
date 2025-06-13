<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LecturasGasModel; // Asegúrate de que el namespace y nombre del modelo sean correctos
use App\Models\DispositivoModel; // Asegúrate de que el namespace y nombre del modelo sean correctos

class DetalleController extends Controller
{
    protected $lecturaModel;
    protected $dispositivoModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel();
    }

    public function detalles($mac)
    {
        // La vista 'detalles' ahora obtiene la información en tiempo real vía JavaScript
        // por lo que las lecturas de gas iniciales aquí son menos críticas,
        // pero aún pueden ser útiles si quieres pre-cargar algo o manejar el primer render.
        // Sin embargo, el error viene de cómo accedes a $dispositivo.

        $lecturas = $this->lecturaModel->getLecturasPorMac($mac); // Esto devolverá arrays si tu LecturasGasModel lo hace

        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac); // Esto devolverá un OBJETO

        // --- CORRECCIÓN AQUÍ: Acceso a propiedades de objeto con -> ---
        $nombreDispositivo = $dispositivo->nombre ?? $mac;
        $ubicacionDispositivo = $dispositivo->ubicacion ?? 'Desconocida';
        // --- FIN CORRECCIÓN ---

        // La vista 'detalles' ya no usa directamente $lecturas para el display principal del gas,
        // pero si hay otras partes de la vista que lo usen, asegúrate de que manejen arrays.
        // Para el propósito principal, solo necesitamos pasar la MAC y los detalles del dispositivo.

        return view('detalles', [
            'mac' => $mac,
            'nombreDispositivo' => $nombreDispositivo,
            'ubicacionDispositivo' => $ubicacionDispositivo,
            'lecturas' => $lecturas // Aunque la vista ya no lo use para el nivel actual, lo pasamos por si acaso
        ]);
    }
}
