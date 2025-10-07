<?php namespace App\Models;

use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas'; 
    protected $primaryKey = 'id'; 

    // CORRECCIÓN CLAVE: 'usuario_id' ha sido eliminado de la base de datos, 
    // por lo tanto, debe ser eliminado de los campos permitidos.
    protected $allowedFields = ['MAC', 'nivel_gas', 'fecha']; //
    // El campo 'created_at' en tu DB tiene DEFAULT CURRENT_TIMESTAMP.
    // ... (el resto de comentarios permanece igual)

    protected $useTimestamps = false; 

    // Método para obtener lecturas de gas por la dirección MAC
    public function getLecturasPorMac($mac)
    // ... (método sin cambios)
    {
        return $this->db->table($this->table)
            ->where('MAC', $mac)
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Método para obtener lecturas de gas por el id del usuario
    public function getLecturasPorUsuario($id_usuario)
    // ... (método sin cambios, ya que usa enlace.id_usuario, no lecturas_gas.usuario_id)
    {
        return $this->db->table($this->table)
            ->select('lecturas_gas.*, dispositivos.MAC, dispositivos.nombre as dispositivo_nombre, dispositivos.ubicacion as dispositivo_ubicacion')
            ->join('dispositivos', 'lecturas_gas.MAC = dispositivos.MAC', 'left')
            ->join('enlace', 'dispositivos.MAC = enlace.MAC', 'inner')
            ->where('enlace.id_usuario', $id_usuario)
            ->where('lecturas_gas.MAC IS NOT NULL')
            ->orderBy('lecturas_gas.fecha', 'DESC')
            ->get()
            ->getResultArray();
    }
}