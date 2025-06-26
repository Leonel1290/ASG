<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait; // Para respuestas JSON de API
use App\Models\ServoControlModel; // Importa el nuevo modelo ServoControlModel
use CodeIgniter\Controller;

class ServoController extends Controller
{
    use ResponseTrait; // Habilita métodos como $this->respond() y $this->fail()

    protected $servoControlModel;

    public function __construct()
    {
        // Instancia el modelo ServoControlModel
        $this->servoControlModel = new ServoControlModel();
    }

    /**
     * Endpoint para que la PWA o interfaz web pueda controlar el estado del servo.
     * Espera una petición POST con 'MAC' del dispositivo y 'state' (0 para cerrar, 1 para abrir).
     *
     * Ruta de ejemplo: POST /web/controlServo
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function controlServoFromWeb()
    {
        // Verifica que la petición sea de tipo POST.
        if ($this->request->getMethod() !== 'post') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta POST.', 405);
        }

        // Obtiene la dirección MAC del dispositivo y el estado deseado desde la petición POST.
        $macAddress = $this->request->getPost('MAC');
        $desiredState = $this->request->getPost('state'); // 0 para cerrar, 1 para abrir

        // Valida que los datos necesarios estén presentes y que el estado sea válido.
        if (empty($macAddress) || !isset($desiredState) || !in_array((int)$desiredState, [0, 1])) {
            return $this->fail('Datos incompletos o inválidos. Se requiere MAC y un estado (0 o 1).', 400);
        }

        // Convierte el estado a un entero para asegurar el tipo correcto.
        $desiredState = (int)$desiredState;

        // Llama al método del modelo para actualizar el estado del servo en la base de datos.
        if ($this->servoControlModel->updateServoState($macAddress, $desiredState)) {
            // Si la actualización es exitosa, registra y envía una respuesta de éxito.
            log_message('info', "Comando de servo recibido desde la web para MAC: {$macAddress}, Estado deseado: {$desiredState}");
            return $this->respondCreated(['status' => 'success', 'message' => 'Estado del servo actualizado correctamente.']);
        } else {
            // Si hay un error en la actualización, registra y envía una respuesta de error.
            return $this->fail('Error al actualizar el estado del servo en la base de datos.', 500);
        }
    }

    /**
     * Endpoint para que la PWA o interfaz web consulte el estado actual del servo.
     * Espera una petición GET con la MAC del dispositivo en la URL.
     *
     * Ruta de ejemplo: GET /web/getServoState/MAC_DEL_DISPOSITIVO
     *
     * @param string $macAddress La dirección MAC del dispositivo ESP32.
     * @return \CodeIgniter\HTTP\Response
     */
    public function getServoStateFromWeb(string $macAddress)
    {
        // Verifica que la petición sea de tipo GET.
        if ($this->request->getMethod() !== 'get') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta GET.', 405);
        }

        // Valida que la dirección MAC esté presente.
        if (empty($macAddress)) {
            return $this->fail('Dirección MAC no proporcionada.', 400);
        }

        // Obtiene el estado del servo desde el modelo.
        $servoState = $this->servoControlModel->getServoStateByMac($macAddress);

        // Envía el estado en una respuesta JSON.
        return $this->respond(['status' => 'success', 'estado_servo' => $servoState]);
    }
}
