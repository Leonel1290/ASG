<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraDispositivoModel extends Model
{
    protected $table      = 'compras_dispositivos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // Campos permitidos (ahora id_usuario_comprador es el principal identificador)
    protected $allowedFields = ['id_usuario_comprador', 'MAC_dispositivo', 'transaccion_paypal_id'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fecha_compra';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Reglas de validación: id_usuario_comprador es requerido y debe existir
    protected $validationRules    = [
        'id_usuario_comprador' => 'required|integer|is_not_unique[usuarios.id]', // El usuario comprador debe existir y ser requerido
        'MAC_dispositivo'      => 'required|exact_length[17]|regex_match[/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i]|is_not_unique[dispositivos.MAC]', // La MAC debe ser válida y existir en dispositivos
    ];

    protected $validationMessages = [
        'id_usuario_comprador' => [
            'required'      => 'El ID del usuario comprador es obligatorio.',
            'integer'       => 'El ID del usuario comprador debe ser un número entero.',
            'is_not_unique' => 'El usuario comprador especificado no existe.'
        ],
        'MAC_dispositivo' => [
            'required'      => 'La dirección MAC del dispositivo es obligatoria.',
            'exact_length'  => 'La dirección MAC debe tener 17 caracteres.',
            'regex_match'   => 'El formato de la dirección MAC es inválido (ej. XX:XX:XX:XX:XX:XX).',
            'is_not_unique' => 'El dispositivo con esta MAC no está registrado en el sistema.'
        ]
    ];
    protected $skipValidation = false;
}
