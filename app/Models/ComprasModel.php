<?php

namespace App\Models;

use CodeIgniter\Model;

class ComprasModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id',
        'payer_id',
        'payment_id',
        'status',
        'monto',
        'nombre', // Nombre del comprador (extraído de PayPal)
        'email',  // Email del comprador
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