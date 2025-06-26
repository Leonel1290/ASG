<?php namespace App\Models;

use CodeIgniter\Model;

class ServoControlModel extends Model
{
    // Tabla de la base de datos que este modelo representará.
    protected $table = 'servos';

    // La clave primaria de la tabla 'servos' es 'id'.
    protected $primaryKey = 'id';

    // El tipo de retorno de los resultados de las consultas (array, object, etc.).
    protected $returnType = 'array';

    // Indica si se utilizan los campos `created_at` y `updated_at`.
    // Tu tabla `servos` tiene `last_updated_at`, así que lo usaremos como `updatedField`.
    protected $useTimestamps = true;
    protected $createdField  = ''; // No hay un campo 'created_at' específico en `servos`
    protected $updatedField  = 'last_updated_at'; // Campo que se actualiza automáticamente al modificar

    // Campos que están permitidos para ser insertados o actualizados en la base de datos.
    // Es crucial incluir 'MAC_dispositivo' para que el método `insert` funcione correctamente
    // si un dispositivo aún no tiene una entrada en la tabla `servos`.
    protected $allowedFields = ['MAC_dispositivo', 'estado_servo'];

    /**
     * Obtiene el estado actual del servo para una dirección MAC específica de un dispositivo.
     * Este método es utilizado por el ESP32 para saber en qué estado debe estar el servo.
     *
     * @param string $macAddress La dirección MAC del dispositivo.
     * @return int El estado del servo (0 = cerrado, 1 = abierto) o 0 si no se encuentra.
     */
    public function getServoStateByMac(string $macAddress): int
    {
        // Busca el registro del servo utilizando la dirección MAC del dispositivo.
        $servoControl = $this->where('MAC_dispositivo', $macAddress)->first();

        // Si se encuentra el registro, devuelve el estado del servo.
        if ($servoControl) {
            return (int)$servoControl['estado_servo'];
        }

        // Si no se encuentra un registro para la MAC, por seguridad, devuelve 0 (servo cerrado).
        return 0;
    }

    /**
     * Actualiza el estado del servo para una dirección MAC específica del dispositivo.
     * Si no existe un registro para esta MAC en la tabla `servos`, lo crea.
     * Este método es utilizado por la PWA (o cualquier interfaz web) para enviar comandos al servo.
     *
     * @param string $macAddress La dirección MAC del dispositivo.
     * @param int $state El nuevo estado deseado del servo (0 = cerrar, 1 = abrir).
     * @return bool Verdadero si la operación de guardar/actualizar fue exitosa, falso en caso contrario.
     */
    public function updateServoState(string $macAddress, int $state): bool
    {
        // Normaliza el estado para asegurar que sea 0 o 1.
        $state = ($state === 1) ? 1 : 0;

        // Prepara los datos para insertar o actualizar.
        $data = [
            'MAC_dispositivo' => $macAddress,
            'estado_servo' => $state
        ];

        // Intenta encontrar un registro existente por la MAC del dispositivo.
        $existingServo = $this->where('MAC_dispositivo', $macAddress)->first();

        if ($existingServo) {
            // Si el registro existe, actualiza su estado.
            return $this->update($existingServo['id'], $data);
        } else {
            // Si el registro no existe, crea uno nuevo.
            // Es importante que la MAC_dispositivo exista en la tabla `dispositivos`
            // para mantener la integridad referencial debido a la clave foránea.
            return $this->insert($data);
        }
    }
}

