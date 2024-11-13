<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model {
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nivel_gas'];
}
