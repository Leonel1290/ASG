<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrosGasModel extends Model
{
    protected $table = 'registros_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'dispositivo_id', 
        'nombre_dispositivo', 
        'ubicacion', 
        'nivel_gas', 
        'fecha', 
        'estado'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDispositivosUnicos()
    {
        return $this->select('dispositivo_id, nombre_dispositivo, ubicacion, MAX(fecha) as ultima_lectura')
            ->groupBy('dispositivo_id, nombre_dispositivo, ubicacion')
            ->orderBy('nombre_dispositivo')
            ->findAll();
    }

    public function getLecturasPorDispositivo($dispositivoId, $limit = 100)
    {
        return $this->where('dispositivo_id', $dispositivoId)
            ->orderBy('fecha', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getUltimaLectura($dispositivoId)
    {
        return $this->where('dispositivo_id', $dispositivoId)
            ->orderBy('fecha', 'DESC')
            ->first();
    }
}