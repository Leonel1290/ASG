<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraDispositivoModel extends Model
{
    protected $table            = 'compras_dispositivos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_usuario_comprador',
        'MAC_dispositivo',
        'fecha_compra',
        'transaccion_paypal_id',
        'estado_compra',
        // Si tienes una columna 'id_usuario' en compras_dispositivos para el usuario, asegúrate de que esté aquí.
        // Basado en tu Home.php, parece que usas 'id_usuario_comprador'
    ];

    protected $validationRules = [
        'id_usuario_comprador' => 'required|integer',
        'MAC_dispositivo'      => 'required|exact_length[17]|regex_match[/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/]',
        'estado_compra'        => 'permit_empty|in_list[pendiente,completada,cancelada]'
    ];

    protected $validationMessages = [
        'MAC_dispositivo' => [
            'required'      => 'La dirección MAC del dispositivo es obligatoria.',
            'exact_length'  => 'La dirección MAC debe tener 17 caracteres (ej. AA:BB:CC:DD:EE:FF).',
            'regex_match'   => 'El formato de la dirección MAC es incorrecto.',
        ],
    ];

    /**
     * Busca una dirección MAC disponible de la tabla 'dispositivos' que no esté enlazada
     * y tenga el estado 'disponible'.
     *
     * @return string|null La dirección MAC disponible o null si no se encuentra ninguna.
     */
    public function getAvailableMacForAssignment()
    {
        $builder = $this->db->table('dispositivos d');
        $builder->select('d.MAC');
        // Asegúrate de que el dispositivo no esté ya enlazado a NINGÚN usuario en la tabla 'enlace'
        $builder->join('enlace e', 'd.MAC = e.MAC', 'left');
        $builder->where('e.MAC IS NULL'); // No enlazado a ningún usuario aún
        $builder->where('d.estado_dispositivo', 'disponible'); // Debe estar disponible para la compra

        // Limita a 1 para obtener solo la primera MAC disponible
        $result = $builder->get()->getRowArray();
        return $result ? $result['MAC'] : null;
    }
}
