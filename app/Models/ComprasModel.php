<?php
namespace App\Models;

use CodeIgniter\Model;

class ComprasModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre', 'email', 'order_id', 'payer_id', 'payment_id', 
        'status', 'monto', 'fecha_compra', 'id_usuario'
    ];
    protected $useTimestamps = false;
    
    /**
     * Obtener compras por ID de usuario
     */
    public function getComprasByUsuario($userId)
    {
        return $this->where('id_usuario', $userId)->findAll();
    }
    
    /**
     * Asignar usuario a una compra por payment_id
     */
    public function asignarUsuario($paymentId, $userId)
    {
        return $this->where('payment_id', $paymentId)
                    ->set('id_usuario', $userId)
                    ->update();
    }
}