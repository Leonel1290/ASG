<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table          = 'dispositivos'; // Tabla 'dispositivos'
    protected $primaryKey     = 'id';           // Llave primaria 'id'
    protected $useAutoIncrement = true;         // El ID es auto-incremental
    protected $returnType     = 'object';       // <-- CAMBIADO: Usaremos 'object' para acceder propiedades con ->
    protected $useSoftDeletes = false;          // No usas soft deletes

    // Campos permitidos para insertar o actualizar.
    // ¡IMPORTANTE: Estos campos deben coincidir exactamente con tus columnas de DB!
    protected $allowedFields = [
        'MAC',
        'nombre',
        'ubicacion',
        'estado_valvula',
        'ultimo_nivel_gas',      // <-- AÑADIDO: Campo para el último nivel de gas reportado
        'ultima_actualizacion'   // <-- AÑADIDO: Campo para la última fecha/hora de actualización
        // Campos como 'usuario_id', 'numero_serie', 'estado', 'ultima_conexion'
        // no están en tu esquema SQL de 'dispositivos', por lo tanto, se han ELIMINADO.
    ];

    // Habilitamos el uso de timestamps si quieres manejar la fecha de creación o actualización de registros
    // Tu tabla `dispositivos` en el script SQL tiene `created_at` y `updated_at` con DEFAULT CURRENT_TIMESTAMP.
    // Configurando useTimestamps a true, CodeIgniter los gestionará automáticamente.
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // El DUMP muestra 'timestamp', 'datetime' es apropiado para CI4
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Solo si useSoftDeletes es true

    // Validación (opcional, puedes añadir reglas si es necesario)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks (si necesitas, por ejemplo, formatear la MAC antes de guardar)
    protected $allowCallbacks = true;
    // protected $beforeInsert   = ['formatMac']; // Ejemplo de callback
    // protected $beforeUpdate   = ['formatMac']; // Ejemplo de callback

    /**
     * Obtiene un dispositivo por su ID.
     * @param int $id El ID del dispositivo.
     * @return array|object|null Los datos del dispositivo o null si no se encuentra.
     */
    public function getDispositivoById($id)
    {
        return $this->find($id);
    }

    /**
     * Obtiene un dispositivo por su dirección MAC.
     * @param string $mac La dirección MAC del dispositivo.
     * @return array|object|null Los datos del dispositivo o null si no se encuentra.
     */
    public function getDispositivoByMac($mac)
    {
        return $this->where('MAC', $mac)->first();
    }

    /**
     * Actualiza un dispositivo por su dirección MAC.
     * @param string $mac La dirección MAC del dispositivo a actualizar.
     * @param array $data Los datos a actualizar (ej. ['estado_valvula' => 1]).
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateDispositivoByMac($mac, $data)
    {
        return $this->where('MAC', $mac)->update(null, $data);
    }
}
