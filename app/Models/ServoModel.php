<?php

namespace App\Models;

use CodeIgniter\Model;

class ServoModel extends Model
{
    protected $table = 'dispositivos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['estado_valvula', 'updated_at'];

    // Obtener estado actual de la válvula
    public function getEstadoValvula($mac)
    {
        $dispositivo = $this->where('MAC', $mac)->first();
        return $dispositivo ? $dispositivo['estado_valvula'] : 0;
    }

    // Actualizar estado de la válvula
    public function updateEstadoValvula($mac, $estado)
    {
        return $this->where('MAC', $mac)->set([
            'estado_valvula' => $estado,
            'updated_at' => date('Y-m-d H:i:s')
        ])->update();
    }
}
