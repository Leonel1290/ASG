<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceModel extends Model
{
    protected $table = 'lecturas_gas'; // Tu tabla principal para las lecturas
    protected $primaryKey = 'id'; // O tu clave primaria

    protected $allowedFields = ['mac', 'nivel_gas', 'fecha']; // Tus campos permitidos

    /**
     * Obtiene todas las MACs únicas con su nombre y ubicación.
     */
    public function getUniqueDevices()
    {
        // Suponiendo que tienes una tabla 'dispositivos' para almacenar la información de los dispositivos
        // y que la relación entre 'dispositivos' y 'lecturas_gas' es por la columna 'mac'.
        // Si no tienes una tabla 'dispositivos', tendrías que obtener la MAC de las lecturas
        // y luego quizás un nombre/ubicación por defecto o de otra fuente.

        // Ejemplo con una tabla 'dispositivos':
        return $this->db->table('dispositivos')
                        ->select('mac, nombre, ubicacion')
                        ->distinct()
                        ->get()
                        ->getResultArray();

        /*
        // Si solo tienes la tabla lecturas_gas y quieres extraer MACs únicas:
        return $this->select('mac')
                    ->distinct()
                    ->get()
                    ->getResultArray();
        // En este caso, no tendrías 'nombre' ni 'ubicacion' para mostrar en listDevices
        // a menos que los guardes en la tabla de lecturas o tengas otra forma de obtenerlos.
        */
    }

    /**
     * Obtiene todas las lecturas de gas para una MAC específica.
     * Ordena por fecha de forma ascendente (más antiguo primero).
     */
    public function getLecturasByMac(string $mac)
    {
        return $this->where('mac', $mac)
                    ->orderBy('fecha', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene la información del dispositivo (nombre y ubicación) por MAC.
     */
    public function getDeviceInfoByMac(string $mac)
    {
        // Suponiendo que tienes una tabla 'dispositivos' con campos 'mac', 'nombre', 'ubicacion'
        return $this->db->table('dispositivos')
                        ->where('mac', $mac)
                        ->select('nombre, ubicacion')
                        ->get()
                        ->getRowArray();
    }
}