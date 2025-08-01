<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = null;

    protected $allowedFields = [
        'id_usuario',
        'paypal_order_id',
        'monto',
        'moneda',
        'estado_pago',
    ];

    protected $validationRules = [
        'id_usuario'      => 'required|integer',
        'paypal_order_id' => 'required|string|max_length[255]',
        'monto'           => 'required|numeric|greater_than[0]',
        'moneda'          => 'required|string|exact_length[3]',
        'estado_pago'     => 'required|in_list[pendiente,completado,fallido,reembolsado]',
    ];

    protected $validationMessages = [
        'id_usuario' => [
            'required' => 'El ID de usuario es obligatorio.',
            'integer'  => 'El ID de usuario debe ser un número entero.',
        ],
        'paypal_order_id' => [
            'required'   => 'El ID de orden de PayPal es obligatorio.',
            'max_length' => 'El ID de orden de PayPal no puede exceder los 255 caracteres.',
        ],
        'monto' => [
            'required'    => 'El monto es obligatorio.',
            'numeric'     => 'El monto debe ser un valor numérico.',
            'greater_than' => 'El monto debe ser mayor que cero.',
        ],
        'moneda' => [
            'required'    => 'La moneda es obligatoria.',
            'exact_length' => 'La moneda debe tener exactamente 3 caracteres (ej. USD).',
        ],
        'estado_pago' => [
            'required'  => 'El estado del pago es obligatorio.',
            'in_list'   => 'El estado del pago no es válido. Debe ser pendiente, completado, fallido o reembolsado.',
        ],
    ];

    protected $beforeInsert = [];
    protected $afterInsert  = [];
    protected $beforeUpdate = [];
    protected $afterUpdate  = [];
    protected $beforeFind   = [];
    protected $afterFind    = [];
    protected $beforeDelete = [];
    protected $afterDelete  = [];
}