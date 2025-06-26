<?php namespace App\Models;

use CodeIgniter\Model;

class ServoControlModel extends Model
{
    protected $table = 'servos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'last_updated_at';
    protected $allowedFields = ['MAC_dispositivo', 'estado_servo'];

    public function getServoStateByMac(string $macAddress): int
    {
        $servoControl = $this->where('MAC_dispositivo', $macAddress)->first();
        if ($servoControl) {
            return (int)$servoControl['estado_servo'];
        }
        return 0;
    }

    public function updateServoState(string $macAddress, int $state): bool
    {
        $state = ($state === 1) ? 1 : 0;
        $data = [
            'MAC_dispositivo' => $macAddress,
            'estado_servo' => $state
        ];
        $existingServo = $this->where('MAC_dispositivo', $macAddress)->first();
        if ($existingServo) {
            return $this->update($existingServo['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}
