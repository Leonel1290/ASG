<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    // --- CORRECCIÓN: Cambiar 'id_usuario' a 'usuario_id' para que coincida con la DB ---
    protected $allowedFields = ['usuario_id', 'MAC', 'nivel_gas', 'fecha', 'update_at'];
    // --- FIN CORRECCIÓN ---
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
    // Este método ya usa 'enlace.id_usuario' (correcto para 'enlace')
    // y 'lecturas_gas.usuario_id' (correcto para 'lecturas_gas').
    public function getLecturasPorUsuario($id_usuario)
    {
        return $this->db->table('lecturas_gas')
            ->select('lecturas_gas.*, dispositivos.MAC')
            ->join('dispositivos', 'lecturas_gas.MAC = dispositivos.MAC')
            ->join('enlace', 'dispositivos.MAC = enlace.MAC')
            ->where('enlace.id_usuario', $id_usuario) // 'id_usuario' es correcto para la tabla 'enlace'
            ->where('lecturas_gas.MAC IS NOT NULL') // Asegura que la MAC no sea nula
            ->orderBy('lecturas_gas.fecha', 'DESC')
            ->get()
            ->getResultArray();
    }
}
