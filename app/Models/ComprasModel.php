<?php namespace App\Models;

use CodeIgniter\Model;

class ComprasModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_usuario', 'MAC_dispositivo', 'monto', 'moneda', 'paypal_order_id', 'estado_pago'];
    protected $useTimestamps = true; // Usa created_at y updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = false; // Solo created_at se maneja automáticamente para esta tabla

    // Aquí podrías añadir reglas de validación si es necesario
}