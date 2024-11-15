<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'nivel_gas', 'fecha', 'created_at'];

    // Configura automáticamente las marcas de tiempo
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false; // No se requiere `updated_at` para este modelo
}
