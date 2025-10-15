<?php

namespace App\Models;

use CodeIgniter\Model;

class DireccionEnvioModel extends Model
{
    protected $table = 'direcciones_envio';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'compra_id',
        'nombre',
        'apellido',
        'telefono',
        'calle',
        'numero',
        'piso',
        'depto',
        'ciudad',
        'provincia',
        'codigo_postal',
        'pais',
        'referencias',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
