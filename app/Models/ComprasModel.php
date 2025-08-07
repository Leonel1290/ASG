<?php

namespace App\Models;

use CodeIgniter\Model;

class ComprasModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'usuario_id',
        'order_id',
        'payer_id',
        'payment_id',
        'status',
        'monto',
        'fecha_compra'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_compra';
    protected $updatedField  = null;
}