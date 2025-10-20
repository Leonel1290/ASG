<?php
namespace App\Models;

use CodeIgniter\Model;

class DireccionesEnvioModel extends Model
{
    protected $table = 'direcciones_envio';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'compra_id', 'id_usuario', 'nombre', 'apellido', 'telefono', 
        'pais', 'provincia', 'ciudad', 'calle', 'numero', 'piso', 
        'depto', 'codigo_postal', 'referencias'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}