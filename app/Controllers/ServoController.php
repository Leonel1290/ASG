<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ServoControlModel;
use App\Models\LecturasGasModel;
use App\Models\EnlaceModel;
use CodeIgniter\Controller;

class ServoController extends Controller
{
    use ResponseTrait;

    protected $servoControlModel;
    protected $lecturasGasModel;
    protected $enlaceModel;

    public function __construct()
    {
        $this->servoControlModel = new ServoControlModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->enlaceModel = new EnlaceModel();
    }

    public function receiveSensorData()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta POST.', 405);
        }

        $macAddress = $this->request->getPost('MAC');
        $gasLevel = $this->request->getPost('nivel_gas');

        if (empty($macAddress) || !isset($gasLevel)) {
            return $this->fail('Datos incompletos. Se requiere MAC y nivel_gas.', 400);
        }

        $usuarioId = $this->enlaceModel->getUserIdByMac($macAddress);

        if (is_null($usuarioId)) {
            log_message('warning', "MAC de dispositivo no encontrada en tabla 'enlace': {$macAddress}. No se pudo asociar a un usuario.");
            return $this->fail('Dispositivo no asociado a ningún usuario. No se puede guardar la lectura.', 404);
        }

        $dataToInsert = [
            'MAC'         => $macAddress,
            'nivel_gas'   => (float)$gasLevel,
            'usuario_id'  => $usuarioId,
            'fecha'       => date('Y-m-d H:i:s')
        ];

        try {
            $insertedId = $this->lecturasGasModel->insert($dataToInsert);
            if ($insertedId) {
                log_message('info', "Lectura de gas ({$gasLevel}) guardada para MAC: {$macAddress}, Usuario ID: {$usuarioId}");
                return $this->respondCreated(['status' => 'success', 'message' => 'Datos de sensor recibidos y guardados.', 'id_lectura' => $insertedId]);
            } else {
                log_message('error', "Error al insertar lectura de gas. MAC: {$macAddress}, Datos: " . json_encode($dataToInsert) . ", Errores: " . json_encode($this->lecturasGasModel->errors()));
                return $this->fail('Error al guardar la lectura de gas en la base de datos.', 500);
            }
        } catch (\Exception $e) {
            log_message('error', "Excepción al guardar lectura de gas para MAC: {$macAddress}. Error: " . $e->getMessage());
            return $this->fail('Error interno del servidor al guardar la lectura de gas.', 500);
        }
    }

    public function getValveState()
    {
        $macAddress = $this->request->uri->getSegment(3); // Asume /api/getValveState/{MAC_ADDRESS}

        if ($this->request->getMethod() !== 'get') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta GET.', 405);
        }

        if (empty($macAddress)) {
            return $this->fail('Dirección MAC no proporcionada.', 400);
        }

        $servoState = $this->servoControlModel->getServoStateByMac($macAddress);

        return $this->respond(['status' => 'success', 'estado_valvula' => $servoState]);
    }

    public function controlServoFromWeb()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta POST.', 405);
        }

        $macAddress = $this->request->getPost('MAC');
        $desiredState = $this->request->getPost('state');

        if (empty($macAddress) || !isset($desiredState) || !in_array((int)$desiredState, [0, 1])) {
            return $this->fail('Datos incompletos o inválidos. Se requiere MAC y un estado (0 o 1).', 400);
        }

        $desiredState = (int)$desiredState;

        if ($this->servoControlModel->updateServoState($macAddress, $desiredState)) {
            log_message('info', "Comando de servo recibido desde la web para MAC: {$macAddress}, Estado deseado: {$desiredState}");
            return $this->respondCreated(['status' => 'success', 'message' => 'Estado del servo actualizado correctamente.']);
        } else {
            return $this->fail('Error al actualizar el estado del servo en la base de datos.', 500);
        }
    }

    public function getServoStateFromWeb(string $macAddress)
    {
        if ($this->request->getMethod() !== 'get') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta GET.', 405);
        }

        if (empty($macAddress)) {
            return $this->fail('Dirección MAC no proporcionada.', 400);
        }

        $servoState = $this->servoControlModel->getServoStateByMac($macAddress);

        return $this->respond(['status' => 'success', 'estado_servo' => $servoState]);
    }
}
