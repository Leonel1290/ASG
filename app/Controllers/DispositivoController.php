<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DispositivoModel; // Asegúrate de usar el modelo correcto

class DispositivoController extends ResourceController
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    /**
     * Registra un nuevo dispositivo o verifica si ya existe.
     * Este método será llamado por la ESP32.
     * Endpoint: POST /dispositivos/registrar
     * Datos esperados: MAC, nombre (opcional), ubicacion (opcional)
     * @return \CodeIgniter\HTTP\Response
     */
    public function registrarDispositivo()
    {
        // Asegurarse de que la solicitud sea POST
        if ($this->request->getMethod() !== 'post') {
            return $this->failUnauthorized('Método no permitido. Solo se acepta POST.');
        }

        // Obtener la MAC del cuerpo de la solicitud POST
        $mac = $this->request->getPost('MAC');
        $nombre = $this->request->getPost('nombre');
        $ubicacion = $this->request->getPost('ubicacion');

        // Validar que la MAC no esté vacía
        if (empty($mac)) {
            return $this->fail('La dirección MAC es requerida.', 400, 'Bad Request');
        }

        // Limpiar y normalizar la MAC (eliminar guiones o espacios, convertir a mayúsculas)
        $mac = strtoupper(str_replace(['-', ' ', ':'], '', $mac));
        // Formatear a XX:XX:XX:XX:XX:XX si no viene así (opcional, pero buena práctica)
        if (strlen($mac) === 12) {
            $mac = implode(':', str_split($mac, 2));
        } elseif (strlen($mac) !== 17) {
            return $this->fail('Formato de MAC inválido. Esperado 17 caracteres (XX:XX:XX:XX:XX:XX).', 400, 'Bad Request');
        }


        // Buscar si el dispositivo ya existe por su MAC
        $dispositivoExistente = $this->dispositivoModel->getDispositivoByMac($mac);

        if ($dispositivoExistente) {
            // El dispositivo ya existe, no es necesario registrarlo de nuevo.
            // Puedes actualizar su información si quieres, pero por ahora solo confirmamos su existencia.
            return $this->respondCreated(['status' => 'success', 'message' => 'Dispositivo ya registrado.', 'MAC' => $mac]);
        } else {
            // El dispositivo no existe, proceder a registrarlo
            $data = [
                'MAC' => $mac,
                'nombre' => $nombre ?? 'Nuevo Dispositivo', // Nombre por defecto si no se proporciona
                'ubicacion' => $ubicacion ?? 'Desconocida', // Ubicación por defecto si no se proporciona
            ];

            if ($this->dispositivoModel->insert($data)) {
                // Registro exitoso
                return $this->respondCreated(['status' => 'success', 'message' => 'Dispositivo registrado correctamente.', 'MAC' => $mac]);
            } else {
                // Error al registrar
                log_message('error', 'Error al registrar dispositivo con MAC: ' . $mac . ' - Errores: ' . json_encode($this->dispositivoModel->errors()));
                return $this->failServerError('Error al registrar el dispositivo.');
            }
        }
    }
}
