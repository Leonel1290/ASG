<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'nivel_gas', 'fecha'];
    protected $useTimestamps = false; // No usar timestamps automáticos
}
