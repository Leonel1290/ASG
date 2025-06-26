<?php
namespace App\Models;

use CodeIgniter\Model;

class ServoModel extends Model
{
    protected $table = 'servos';
    protected $primaryKey = 'id';
    // Cambiado 'MAC' a 'MAC_dispositivo' para que coincida con tu esquema SQL
    protected $allowedFields = ['MAC_dispositivo', 'estado_servo', 'last_updated_at'];
    protected $useTimestamps = true; // Si 'created_at' y 'updated_at' están en la tabla, déjalo en true.
                                      // Tu tabla 'servos' tiene 'last_updated_at' así que puedes gestionarlo manualmente o ajustar.
                                      // Para este caso, solo necesitamos que el campo que se usa en el where sea el correcto.
    protected $createdField = 'created_at'; // Si no tienes 'created_at' en 'servos', puedes removerlo o ajustar.
    protected $updatedField = 'last_updated_at'; // Ajustar para que coincida con el nombre de tu columna de actualización

    public function getServoByMac($mac)
    {
        // Cambiado 'MAC' a 'MAC_dispositivo'
        return $this->where('MAC_dispositivo', $mac)->first();
    }

    public function updateEstadoValvula($mac, $estado)
    {
        // Cambiado 'MAC' a 'MAC_dispositivo'
        // Nota: Si 'estado_valvula' no existe directamente en la tabla 'servos',
        // pero sí en 'dispositivos', este método necesitará una lógica diferente
        // para actualizar la tabla 'dispositivos' o relacionarse con ella.
        // Asumiendo por el momento que 'estado_valvula' está en 'servos' (aunque no lo veo en tu CREATE TABLE de 'servos').
        // Si 'estado_valvula' está en la tabla `dispositivos`, deberás ajustar el modelo y la lógica.
        // A la luz de tu esquema, 'estado_valvula' SÍ está en la tabla `dispositivos`.
        // Por lo tanto, el ServoModel NO debería intentar actualizar `estado_valvula` directamente.
        // En su lugar, el `ServoController` o un nuevo modelo de `Dispositivo` debería hacerlo.
        // Dejaré el código como está por ahora, pero ten en cuenta la inconsistencia.
        return $this->where('MAC_dispositivo', $mac)
                    ->set(['estado_servo' => $estado]) // Actualiza 'estado_servo' en 'servos'
                    ->update();
    }
}
