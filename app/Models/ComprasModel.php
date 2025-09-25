<?php

namespace App\Models;

use CodeIgniter\Model;

class ComprasModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'usuario_id', // Lo mantenemos por si acaso, pero no es obligatorio
        'order_id',
        'payer_id',
        'payment_id',
        'status',
        'monto',
        'fecha_compra'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'fecha_compra';
    protected $updatedField  = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->useTimestamps = false;
    }
}