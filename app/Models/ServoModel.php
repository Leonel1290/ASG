<?php
namespace App\Models;

use CodeIgniter\Model;

class ServoModel extends Model
{
    protected $table            = 'servos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['MAC_dispositivo', 'estado_servo', 'last_updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'last_updated_at';

    // Validation
    protected $validationRules      = [
        'MAC_dispositivo' => 'required|max_length[17]',
        'estado_servo' => 'required|in_list[0,1]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener estado del servo por MAC
     */
    public function obtenerPorMAC($mac)
    {
        return $this->where('MAC_dispositivo', $mac)->first();
    }

    /**
     * Actualizar estado del servo
     */
    public function actualizarEstado($mac, $estado)
    {
        $servo = $this->obtenerPorMAC($mac);
        
        if ($servo) {
            // Actualizar registro existente
            return $this->update($servo['id'], [
                'estado_servo' => $estado,
                'last_updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Crear nuevo registro
            return $this->insert([
                'MAC_dispositivo' => $mac,
                'estado_servo' => $estado,
                'last_updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Sincronizar estado con tabla dispositivos
     */
    public function sincronizarConDispositivo($mac, $estadoValvula)
    {
        $dispositivoModel = new DispositivoModel();
        
        // Actualizar tabla servos
        $this->actualizarEstado($mac, $estadoValvula);
        
        // Actualizar tabla dispositivos
        return $dispositivoModel->actualizarEstadoValvula($mac, $estadoValvula);
    }
}