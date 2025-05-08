<?php

namespace App\Models;

use CodeIgniter\Model;

class EnlaceModel extends Model
{
    protected $table = 'enlace';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_usuario', 'MAC'];
    public $timestamps = false;
}