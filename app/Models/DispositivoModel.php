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
    protected $dateFormat    = 'datetime'; // O 'int' si guardas timestamps como enteros
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Solo si useSoftDeletes es true

    // Validación (opcional, puedes añadir reglas si es necesario)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false; // Cambia a true para omitir la validación del modelo
    protected $cleanValidationRules = true;

    // Callbacks (si necesitas, por ejemplo, formatear la MAC antes de guardar)
    protected $allowCallbacks = true;
    // protected $beforeInsert   = ['formatMac']; // Ejemplo de callback
    // protected $beforeUpdate   = ['formatMac']; // Ejemplo de callback

    // Método para obtener dispositivo por su id
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

    // Método para actualizar un dispositivo por su ID (ya existe en el modelo base find/update)
    // public function updateDispositivo($id, $data)
    // {
    //     return $this->update($id, $data);
    // }

    // Ejemplo de callback para formatear MAC si fuera necesario
    // protected function formatMac(array $data)
    // {
    //     if (isset($data['data']['MAC'])) {
    //         // Ejemplo: convertir a mayúsculas y formato con guiones
    //         $mac = strtoupper(str_replace([':', '-'], '', $data['data']['MAC']));
    //         $data['data']['MAC'] = implode(':', str_split($mac, 2));
    //     }
    //     return $data;
    // }

    // NOTA: Tienes otro modelo llamado DispositivosModel.php que parece apuntar a la misma tabla 'dispositivos'.
    // Es recomendable usar UN SOLO modelo (DispositivoModel) para interactuar con la tabla de dispositivos
    // y eliminar el modelo DispositivosModel.php para evitar confusión y redundancia.
}
