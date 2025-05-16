<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table      = 'dispositivos'; // Tabla 'dispositivos'
    protected $primaryKey = 'id';         // Llave primaria 'id'

    // Campos permitidos para insertar o actualizar.
    // Asegúrate de incluir 'MAC', 'nombre', y 'ubicacion'.
    protected $allowedFields = [
        'MAC',
        'nombre',
        'ubicacion',
        // 'created_at' y 'updated_at' se manejan automáticamente si useTimestamps es true
    ];

    // Habilitamos el uso de timestamps si quieres manejar la fecha de creación o actualización de registros
    // Tu tabla `dispositivos` en el script SQL tiene `created_at` y `updated_at`.
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // O 'int' si guardas timestamps como enteros
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // No aplica si useSoftDeletes es false

    // Validación (opcional, puedes añadir reglas si es necesario)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks (si necesitas, por ejemplo, formatear la MAC)
    protected $allowCallbacks = true;
    // protected $beforeInsert   = ['formatMac'];
    // protected $beforeUpdate   = ['formatMac'];

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
        return $this->where('MAC', $mac)->set($data)->update();
    }

    // Método para actualizar un dispositivo por su ID
    public function updateDispositivo($id, $data)
    {
        return $this->update($id, $data);
    }

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
}
