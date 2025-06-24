<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrosGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 
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
        return $this->select('id, nombre_dispositivo, ubicacion, MAX(fecha) as ultima_lectura')
            ->groupBy('id, nombre_dispositivo, ubicacion')
            ->orderBy('nombre_dispositivo')
            ->findAll();
    }

    public function getLecturasPorDispositivo($id, $limit = 100)
    {
        return $this->where('id', $id)
            ->orderBy('fecha', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getUltimaLectura($id)
    {
        return $this->where('id', $id)
            ->orderBy('fecha', 'DESC')
            ->first();
    }
}