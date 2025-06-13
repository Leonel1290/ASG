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
    protected $allowedFields    = [
        'MAC',
        'nombre',
        'ubicacion',
        'estado_dispositivo', // <-- ¡Asegúrate de que este campo esté aquí!
        // created_at y updated_at se gestionan automáticamente si useTimestamps es true
    ];

    protected $useTimestamps = true; // Si usas los campos created_at y updated_at
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Reglas de validación para los campos del dispositivo
    protected $validationRules = [
        'MAC'               => 'required|exact_length[17]|regex_match[/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/]|is_unique[dispositivos.MAC,id,{id}]',
        'nombre'            => 'required|min_length[3]|max_length[255]',
        'ubicacion'         => 'required|min_length[3]|max_length[255]',
        'estado_dispositivo'=> 'required|in_list[disponible,asignado,en_uso,desactivado]', // Regla para el nuevo campo
    ];

    protected $validationMessages = [
        'MAC' => [
            'required'      => 'La dirección MAC es obligatoria.',
            'exact_length'  => 'La dirección MAC debe tener 17 caracteres (ej. AA:BB:CC:DD:EE:FF).',
            'regex_match'   => 'El formato de la dirección MAC es incorrecto. Usa el formato AA:BB:CC:DD:EE:FF o AA-BB-CC-DD-EE-FF.',
            'is_unique'     => 'Esta dirección MAC ya está registrada.',
        ],
        'nombre' => [
            'required'   => 'El nombre del dispositivo es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
        ],
        'ubicacion' => [
            'required'   => 'La ubicación es obligatoria.',
            'min_length' => 'La ubicación debe tener al menos 3 caracteres.',
        ],
        'estado_dispositivo' => [ // Mensajes para el nuevo campo
            'required'  => 'El estado del dispositivo es obligatorio.',
            'in_list'   => 'El estado del dispositivo no es válido.',
        ],
    ];

    /**
     * Obtiene una MAC disponible para asignación.
     * Busca un dispositivo que no esté enlazado y que tenga estado 'disponible'.
     * @return string|null La MAC disponible o null si no hay.
     */
    public function getAvailableMacForAssignment(): ?string
    {
        // Buscar un dispositivo que tenga estado 'disponible' en la tabla 'dispositivos'
        // Y que no esté ya enlazado a ningún usuario en la tabla 'enlace'.
        // Esto es crucial para asegurar que la MAC no se asigne dos veces a diferentes compras/usuarios.
        $builder = $this->db->table('dispositivos d');
        $builder->select('d.MAC');
        $builder->join('enlace e', 'd.MAC = e.MAC', 'left'); // LEFT JOIN para encontrar los que NO están en 'enlace'
        $builder->where('d.estado_dispositivo', 'disponible');
        $builder->where('e.id IS NULL'); // Asegura que no haya un enlace existente para esta MAC

        $result = $builder->get()->getRowArray();

        return $result ? $result['MAC'] : null;
    }
}
