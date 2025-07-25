<?php

namespace App\Models;

use CodeIgniter\Model;

class EnlaceModel extends Model
{
    protected $table = 'enlace'; // Nombre de tu tabla de enlaces usuario-dispositivo
    protected $primaryKey = 'id'; // Llave primaria de la tabla
    protected $allowedFields = ['id_usuario', 'MAC'];
    protected $useTimestamps = false;

    /**
     * Obtiene el ID de usuario asociado a una dirección MAC.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return int|null El ID de usuario si se encuentra, o null si no.
     */
    public function getUserIdByMac($mac)
    {
        $result = $this->select('id_usuario')
                       ->where('MAC', $mac)
                       ->first();

        return $result['id_usuario'] ?? null;
    }
}
