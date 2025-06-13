<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas'; // Nombre de tu tabla de lecturas de gas
    protected $primaryKey = 'id'; // Llave primaria de la tabla

    // Campos permitidos para insertar o actualizar.
    // Estos campos coinciden con las columnas de tu tabla 'lecturas_gas' en el DUMP SQL.
    protected $allowedFields = ['usuario_id', 'MAC', 'nivel_gas', 'fecha'];
    // El campo 'created_at' en tu DB tiene DEFAULT CURRENT_TIMESTAMP.
    // Como useTimestamps es false, el controlador debe enviarlo manualmente (lo cual ya hace).

    protected $useTimestamps = false; // Se mantiene en false según tu modelo original.
    // Si quisieras que CodeIgniter gestionara `created_at` automáticamente para este modelo,
    // deberías poner `protected $useTimestamps = true;` y `protected $createdField = 'created_at';`
    // Dado que tu DUMP de `lecturas_gas` tiene `created_at datetime DEFAULT CURRENT_TIMESTAMP`,
    // el controlador ya lo está enviando explícitamente con `date('Y-m-d H:i:s')`. Esto es funcional.


    // Método para obtener lecturas de gas por la dirección MAC
    public function getLecturasPorMac($mac)
    {
        return $this->db->table($this->table)
            ->where('MAC', $mac)
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Método para obtener lecturas de gas por el id del usuario
    public function getLecturasPorUsuario($id_usuario)
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
