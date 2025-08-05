<?php
namespace App\Models;
use CodeIgniter\Model;

class PagosPaypalModel extends Model
{
    protected $table = 'pagos_paypal';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'usuario_id', 'payer_id', 'payment_id', 'monto', 'moneda', 'estado', 'fecha'
    ];
}
