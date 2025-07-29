<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table        = 'dispositivos'; // Tabla 'dispositivos'
    protected $primaryKey = 'id';         // Llave primaria 'id'
    protected $useAutoIncrement = true; // Asumo que el ID es auto-incremental
    protected $returnType       = 'array'; // Usaremos 'array'
    protected $useSoftDeletes   = false; // No usas soft deletes

    // Campos permitidos para insertar o actualizar.
    // ¡IMPORTANTE! Asegúrate de incluir 'MAC', 'nombre', y 'ubicacion',
    // y también 'estado_valvula', 'ultimo_nivel_gas', 'ultima_actualizacion'.
    protected $allowedFields = [
        'MAC',
        'nombre',
        'ubicacion',
        'estado_dispositivo', // Este campo también está en tu tabla 'dispositivos'
        'estado_valvula',
        'ultimo_nivel_gas',
        'ultima_actualizacion'
    ];

    // Habilitamos el uso de timestamps si quieres manejar la fecha de creación o actualización de registros
    // Tu tabla `dispositivos` en el script SQL tiene `created_at` y `updated_at`.
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime'; // O 'int' si usas UNIX timestamps

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
        // Verifica si el array de datos está vacío
        if (empty($data)) {
            throw new \RuntimeException('No se proporcionaron datos para actualizar el dispositivo.');
        }
    
        // Filtra solo los campos permitidos en $allowedFields
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));
    
        // Si después del filtrado no hay datos válidos, lanza excepción
        if (empty($filteredData)) {
            throw new \RuntimeException('Los datos proporcionados no contienen campos permitidos para actualización.');
        }
    
        // Intenta realizar la actualización
        $result = $this->where('MAC', $mac)
                       ->set($filteredData)
                       ->update();
    
        if (!$result) {
            log_message('error', 'Falló la actualización del dispositivo con MAC: ' . $mac);
            return false;
        }
    
        return true;
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
