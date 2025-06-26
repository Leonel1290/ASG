<?php
namespace App\Models;

use CodeIgniter\Model;

class ServoModel extends Model
{
    protected $table = 'servos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['MAC', 'nombre', 'ubicacion', 'estado_valvula'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getServoByMac($mac)
    {
        return $this->where('MAC', $mac)->first();
    }

    public function updateEstadoValvula($mac, $estado)
    {
        return $this->where('MAC', $mac)
                    ->set(['estado_valvula' => $estado])
                    ->update();
    }
}
