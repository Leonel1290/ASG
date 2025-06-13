<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table            = 'dispositivos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // O 'object' si prefieres objetos
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['MAC', 'nombre', 'ubicacion', 'estado_dispositivo', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Aunque useSoftDeletes sea false, mantenerlo para consistencia

    // Validation
    protected $validationRules      = [
        'MAC' => 'required|is_unique[dispositivos.MAC]|exact_length[17]',
        'nombre' => 'required|max_length[255]',
        'ubicacion' => 'max_length[255]',
        'estado_dispositivo' => 'required|in_list[disponible,en_uso,mantenimiento]'
    ];
    protected $validationMessages   = [
        'MAC' => [
            'is_unique' => 'Esta dirección MAC ya está registrada.',
            'exact_length' => 'La MAC debe tener exactamente 17 caracteres.'
        ],
        'estado_dispositivo' => [
            'in_list' => 'El estado del dispositivo debe ser "disponible", "en_uso" o "mantenimiento".'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

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
     * @param string $status El nuevo estado ('disponible', 'en_uso', 'mantenimiento').
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateDeviceStatusByMac(string $mac, string $status): bool
    {
        return $this->where('MAC', $mac)->set(['estado_dispositivo' => $status])->update();
    }

    /**
     * Obtiene un dispositivo por su dirección MAC.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return array|null El dispositivo como un array, o null si no se encuentra.
     */
    public function getDispositivoByMac(string $mac): ?array
    {
        return $this->where('MAC', $mac)->first();
    }
}
