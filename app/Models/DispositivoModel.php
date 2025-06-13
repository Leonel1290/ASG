<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table            = 'dispositivos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // Asegúrate de que 'estado_dispositivo' esté en los campos permitidos
    protected $allowedFields    = ['MAC', 'nombre', 'ubicacion', 'estado_dispositivo'];

    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'MAC'       => 'required|exact_length[17]|regex_match[/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/]|is_unique[dispositivos.MAC,id,{id}]',
        'nombre'    => 'required|max_length[255]',
        'ubicacion' => 'required|max_length[255]',
        'estado_dispositivo' => 'required|in_list[disponible,asignado,en_uso,desactivado]', // Agrega validación para la nueva columna
    ];
    protected $validationMessages   = [
        'MAC' => [
            'required'      => 'La dirección MAC es obligatoria.',
            'exact_length'  => 'La dirección MAC debe tener 17 caracteres (ej. AA:BB:CC:DD:EE:FF).',
            'regex_match'   => 'El formato de la dirección MAC es incorrecto.',
            'is_unique'     => 'Esta dirección MAC ya está registrada.'
        ],
        'nombre' => [
            'required'      => 'El nombre del dispositivo es obligatorio.',
            'max_length'    => 'El nombre no puede exceder los 255 caracteres.'
        ],
        'ubicacion' => [
            'required'      => 'La ubicación del dispositivo es obligatoria.',
            'max_length'    => 'La ubicación no puede exceder los 255 caracteres.'
        ],
        'estado_dispositivo' => [
            'required'      => 'El estado del dispositivo es obligatorio.',
            'in_list'       => 'El estado del dispositivo no es válido.'
        ]
    ];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    /**
     * Actualiza el estado de un dispositivo por su dirección MAC.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @param string $status El nuevo estado ('disponible', 'asignado', 'en_uso', 'desactivado').
     * @return bool True si tiene éxito, false si falla.
     */
    public function updateDeviceStatusByMac(string $mac, string $status): bool
    {
        // CodeIgniter 4 update method needs the primary key or a where clause for batch updates.
        // This will update the first record found matching the MAC.
        $device = $this->where('MAC', $mac)->first();
        if ($device) {
            return $this->update($device['id'], ['estado_dispositivo' => $status]);
        }
        return false;
    }
}
