<?php

namespace App\Models;

use CodeIgniter\Model;

class EnlaceModel extends Model
{
    protected $table            = 'enlace';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_usuario', 'MAC'];

    protected $useTimestamps = false;
}
