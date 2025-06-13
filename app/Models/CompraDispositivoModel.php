<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraDispositivoModel extends Model
{
    protected $table = 'compras_dispositivos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_usuario', 'MAC_dispositivo', 'fecha_compra', 'transaccion_paypal_id', 'monto_compra', 'estado_compra'];
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_compra';
    protected $updatedField  = null; // No usamos un campo 'updated_at' para esta tabla si no lo necesitas.

    protected $validationRules    = [
        'id_usuario' => 'required|integer',
        'MAC_dispositivo' => 'required|alpha_numeric|exact_length[17]',
        'transaccion_paypal_id' => 'permit_empty|string|max_length[255]',
        'monto_compra' => 'permit_empty|numeric',
        'estado_compra' => 'permit_empty|in_list[pendiente,completada,fallida]',
    ];
}
