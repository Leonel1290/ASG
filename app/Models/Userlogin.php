<?php

namespace App\Models;

use CodeIgniter\Model;

class Userlogin extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $allowedFields = ['nombre', 'apellido', 'email', 'password'];
}