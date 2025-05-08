<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_usuario', 'MAC', 'nivel_gas', 'fecha', 'update_at'];
    protected $useTimestamps = false;

    // Método para obtener lecturas de gas por la dirección MAC
    public function getLecturasPorMac($mac)
    {
        return $this->db->table('lecturas_gas')
            ->where('MAC', $mac)
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Método para obtener lecturas de gas por el id del usuario
    public function getLecturasPorUsuario($id_usuario)
    {
        return $this->db->table('lecturas_gas')
            ->select('lecturas_gas.*, dispositivos.MAC')
            ->join('dispositivos', 'lecturas_gas.MAC = dispositivos.MAC')
            ->join('enlace', 'dispositivos.MAC = enlace.MAC')
            ->where('enlace.id_usuario', $id_usuario)
            ->where('lecturas_gas.MAC IS NOT NULL')
            ->orderBy('lecturas_gas.fecha', 'DESC')
            ->get()
            ->getResultArray();
    }
}
