<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table      = 'dispositivos'; // Tabla 'dispositivos'
    protected $primaryKey = 'id';         // Llave primaria 'id'
    protected $useAutoIncrement = true; // Asumo que el ID es auto-incremental
    protected $returnType       = 'array'; // Usaremos 'array'
    protected $useSoftDeletes   = false; // No usas soft deletes


    // Campos permitidos para insertar o actualizar.
    // Asegúrate de incluir 'MAC', 'nombre', y 'ubicacion'.
    // Si tu tabla tiene 'usuario_id', 'numero_serie', 'estado', 'ultima_conexion', inclúyelos si los gestionas aquí.
    protected $allowedFields = [
        'MAC',
        'nombre',
        'ubicacion',
        // Si existen y se gestionan:
        // 'usuario_id',
        // 'numero_serie',
        // 'estado',
        // 'ultima_conexion',
        // 'created_at' y 'updated_at' se manejan automáticamente si useTimestamps es true
    ];

    // Habilitamos el uso de timestamps si quieres manejar la fecha de creación o actualización de registros
    // Tu tabla `dispositivos` en el script SQL tiene `created_at` y `updated_at`.
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // O 'int' si usas UNIX timestamps


    // Callbacks para eventos del modelo (opcional)
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];

    // Método para obtener un dispositivo por su ID
    public function getDispositivoById($id)
    {
        return $this->find($id);
    }

    // Método para obtener dispositivo por su MAC
    public function getDispositivoByMac($mac)
    {
        return $this->where('MAC', $mac)->first();
    }

    // Método para actualizar dispositivo por su MAC
    public function updateDispositivoByMac($mac, $data)
    {
        // Usa where() para encontrar el registro por MAC y luego update()
        return $this->where('MAC', $mac)->set($data)->update();
    }

    /**
     * Recupera todos los dispositivos enlazados a un usuario específico.
     * Realiza un join con la tabla 'enlace' para filtrar por id_usuario.
     *
     * @param int $userId El ID del usuario.
     * @return array Array de dispositivos.
     */
    public function getDispositivosPorUsuario(int $userId): array
    {
        // Une la tabla 'dispositivos' con la tabla 'enlace'
        // donde la MAC del dispositivo coincide con la MAC enlazada al usuario.
        return $this->select('dispositivos.*') // Selecciona todas las columnas de la tabla dispositivos
                    ->join('enlace', 'enlace.MAC = dispositivos.MAC')
                    ->where('enlace.id_usuario', $userId)
                    ->findAll();
    }
}