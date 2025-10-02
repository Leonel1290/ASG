<?php
namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table            = 'dispositivos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['MAC', 'nombre', 'ubicacion', 'estado_dispositivo', 'estado_valvula', 'ultimo_nivel_gas', 'ultima_actualizacion'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    // Validation
    protected $validationRules      = [
        'MAC' => 'required|max_length[17]',
        'nombre' => 'required|max_length[255]',
        'ubicacion' => 'required|max_length[255]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function obtenerPorMAC($mac)
    {
        return $this->where('MAC', $mac)->first();
    }

    public function actualizarEstadoValvula($mac, $estado)
    {
        return $this->where('MAC', $mac)
                    ->set([
                        'estado_valvula' => $estado,
                        'ultima_actualizacion' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }

    public function actualizarNivelGas($mac, $nivelGas)
    {
        return $this->where('MAC', $mac)
                    ->set([
                        'ultimo_nivel_gas' => $nivelGas,
                        'ultima_actualizacion' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }
}