<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table = 'dispositivos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'MAC',
        'nombre',
        'ubicacion',
        'estado_dispositivo',
        'estado_valvula',
        'ultimo_nivel_gas',
        'ultima_actualizacion'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Método mejorado para actualizar por MAC
    public function updateDispositivoByMac($mac, $data)
    {
        if (empty($data)) {
            log_message('error', 'Error: Data vacía al actualizar dispositivo MAC: ' . $mac);
            throw new \RuntimeException('No hay datos para actualizar.');
        }

        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));
        
        if (empty($filteredData)) {
            $invalidFields = array_diff_key($data, array_flip($this->allowedFields));
            log_message('error', 'Campos no permitidos: ' . implode(', ', array_keys($invalidFields)));
            throw new \RuntimeException('Campos no permitidos: ' . implode(', ', array_keys($invalidFields)));
        }

        return $this->where('MAC', $mac)->set($filteredData)->update();
    }

    // Resto de tus métodos (getDispositivoById, getDispositivoByMac, etc.)
    // ... 
}