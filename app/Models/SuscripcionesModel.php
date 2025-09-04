<?php
namespace App\Models;
use CodeIgniter\Model;

class SuscripcionesModel extends Model {
    protected $table = 'suscripciones';
    protected $primaryKey = 'id';
    protected $allowedFields = ['endpoint_json', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
