<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    // Nombre de la tabla de la base de datos que este modelo representará
    protected $table = 'pagos';

    // El nombre de la columna que es la clave primaria de la tabla
    protected $primaryKey = 'id';

    // Define si los timestamps automáticos (created_at, updated_at, deleted_at) deben usarse.
    // En este caso, solo queremos created_at.
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // Formato de fecha para los timestamps
    protected $createdField  = 'created_at'; // Nombre de la columna para la fecha de creación
    protected $updatedField  = null;         // No necesitamos updated_at para pagos
    protected $deletedField  = null;         // No necesitamos deleted_at para soft deletes

    // Los campos de la tabla que están permitidos para ser insertados o actualizados
    // Asegúrate de que estos coincidan con los nombres de tus columnas en la tabla `pagos`
    protected $allowedFields = [
        'id_usuario',
        'paypal_order_id',
        'monto',
        'moneda',
        'direccion_envio',
        'estado_pago',
    ];

    // Reglas de validación para los datos antes de ser insertados o actualizados
    // Esto es opcional, pero altamente recomendado para la integridad de los datos
    protected $validationRules = [
        'id_usuario'      => 'required|integer',
        'paypal_order_id' => 'required|string|max_length[255]',
        'monto'           => 'required|numeric|greater_than[0]',
        'moneda'          => 'required|string|exact_length[3]', // Ej. USD, EUR
        'direccion_envio' => 'required|string',
        'estado_pago'     => 'required|in_list[pendiente,completado,fallido,reembolsado]',
    ];

    // Mensajes de error personalizados para las reglas de validación
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
        'direccion_envio' => [
            'required' => 'La dirección de envío es obligatoria.',
        ],
        'estado_pago' => [
            'required'  => 'El estado del pago es obligatorio.',
            'in_list'   => 'El estado del pago no es válido. Debe ser pendiente, completado, fallido o reembolsado.',
        ],
    ];

    // Define si los eventos del modelo deben invocar un callback antes o después
    // de una operación (ej. beforeInsert, afterUpdate).
    protected $beforeInsert = [];
    protected $afterInsert  = [];
    protected $beforeUpdate = [];
    protected $afterUpdate  = [];
    protected $beforeFind   = [];
    protected $afterFind    = [];
    protected $beforeDelete = [];
    protected $afterDelete  = [];
}