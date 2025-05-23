<?php

namespace App\Controllers;

use App\Models\LecturasGasModel;
use App\Models\EnlaceModel; // Necesitamos el modelo Enlace para obtener el usuario_id
use App\Models\DispositivoModel; // Opcional: para verificar si el dispositivo existe
use CodeIgniter\RESTful\ResourceController;

class LecturasController extends ResourceController
{
    protected $lecturasGasModel;
    protected $enlaceModel;
    protected $dispositivoModel; // Instanciar para verificar dispositivos

    public function __construct()
    {
        $this->lecturasGasModel = new LecturasGasModel();
        $this->enlaceModel = new EnlaceModel();
        $this->dispositivoModel = new DispositivoModel(); // Instanciar
    }

    /**
     * Método para recibir y guardar lecturas de gas.
     * Endpoint: POST /lecturas_gas/guardar
     * Datos esperados: MAC, nivel_gas
     * @return \CodeIgniter\HTTP\Response
     */
    public function guardar()
    {
        // Asegurarse de que la solicitud sea POST
        if ($this->request->getMethod() !== 'post') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta POST.');
        }

        // Obtener los datos enviados en la solicitud POST
        $mac = $this->request->getPost('MAC');
        $nivel_gas = $this->request->getPost('nivel_gas');

        // Validar que se recibieron los datos necesarios
        if (empty($mac) || $nivel_gas === null) {
            return $this->fail('Faltan datos (MAC o nivel_gas) en la solicitud.', 400, 'Bad Request');
        }

        // Limpiar y normalizar la MAC (eliminar guiones o espacios, convertir a mayúsculas)
        $mac = strtoupper(str_replace(['-', ' ', ':'], '', $mac));
        if (strlen($mac) === 12) {
            $mac = implode(':', str_split($mac, 2));
        } elseif (strlen($mac) !== 17) {
            return $this->fail('Formato de MAC inválido. Esperado 17 caracteres (XX:XX:XX:XX:XX:XX).', 400, 'Bad Request');
        }

        // Paso 1: Verificar si el dispositivo existe en la tabla 'dispositivos'
        // Esto es una capa de seguridad/integridad de datos
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);
        if (!$dispositivo) {
            // Si el dispositivo no existe en la tabla 'dispositivos', no se puede guardar la lectura
            // Esto podría indicar que la ESP32 no se registró correctamente o hay un error en la MAC
            log_message('warning', 'Lectura recibida para MAC no registrada: ' . $mac);
            return $this->failNotFound('Dispositivo con MAC ' . $mac . ' no encontrado en la base de datos de dispositivos. Registre el dispositivo primero.');
        }

        // Paso 2: Obtener el id_usuario asociado a esta MAC desde la tabla 'enlace'
        $enlace = $this->enlaceModel->where('MAC', $mac)->first();

        $usuario_id = null;
        if ($enlace) {
            $usuario_id = $enlace['id_usuario'];
        } else {
            // Opcional: Si una MAC no está enlazada a ningún usuario, puedes decidir si:
            // 1. No guardar la lectura (la opción actual para mantener la integridad)
            // 2. Guardarla con un usuario_id nulo (si tu tabla lo permite y tiene sentido para tu lógica)
            // 3. Guardarla con un usuario_id por defecto (ej. un usuario "anónimo")
            log_message('warning', 'Lectura recibida para MAC no enlazada a ningún usuario: ' . $mac);
            return $this->failForbidden('La dirección MAC ' . $mac . ' no está enlazada a ningún usuario. La lectura no puede ser guardada.');
        }

        // Preparar los datos para insertar en la base de datos
        $data = [
            'MAC' => $mac,
            'nivel_gas' => $nivel_gas,
            'fecha' => date('Y-m-d H:i:s'), // Captura la fecha y hora actual del servidor
            'usuario_id' => $usuario_id // ¡Importante! Añadir el ID del usuario
        ];

        // Intentar insertar la lectura
        if ($this->lecturasGasModel->insert($data)) {
            $insertedId = $this->lecturasGasModel->getInsertID();
            return $this->respondCreated(['status' => 'success', 'message' => 'Lectura guardada correctamente', 'id' => $insertedId]);
        } else {
             // Si hubo un error en la inserción, loguearlo y retornar una respuesta JSON de error
             log_message('error', 'Error al guardar lectura de gas para MAC: ' . $mac . ' - Datos: ' . json_encode($data) . ' - Error DB: ' . json_encode($this->lecturasGasModel->errors()));
             return $this->failServerError('Error al guardar la lectura en la base de datos.');
        }
    }

    // ... (otros métodos existentes como detalle, si los mantienes)
}
