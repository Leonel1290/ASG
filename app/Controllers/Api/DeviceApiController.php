<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\DispositivoModel;

class DeviceApiController extends BaseController
{
    use ResponseTrait;

    /**
     * POST /api/registrar_mac
     * Cuerpo: mac o MAC (string)
     * - Normaliza y valida la MAC
     * - Inserta en 'dispositivos' si no existe
     * - Devuelve JSON con estado
     */
    public function registrarMac()
    {
        // Permitir CORS básico (útil si el dispositivo envía desde otra red/origen)
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type');

        if ($this->request->getMethod(true) === 'OPTIONS') {
            return $this->respondNoContent();
        }

        $mac = $this->request->getPost('mac');
        if (!$mac) {
            $mac = $this->request->getPost('MAC');
        }

        if (!$mac) {
            return $this->failValidationErrors('Parámetro "mac" es requerido.');
        }

        // Normalización: mayúsculas, separador ":", sin espacios
        $mac = strtoupper(trim($mac));
        $mac = str_replace([' ', '-'], ['', ':'], $mac);

        // Si viene sin ":" pero con 12 hex, insertar separadores cada 2 chars
        $compact = str_replace(':', '', $mac);
        if (strlen($compact) === 12 && ctype_xdigit($compact)) {
            $mac = implode(':', str_split($compact, 2));
        }

        // Validación de formato
        if (!preg_match('/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/', $mac)) {
            return $this->failValidationErrors('Formato de MAC inválido. Use XX:XX:XX:XX:XX:XX');
        }

        $model = new DispositivoModel();

        // ¿Existe ya?
        $existing = $model->where('MAC', $mac)->first();
        if ($existing) {
            return $this->respond([
                'status'  => 'ok',
                'message' => 'MAC ya registrada',
                'mac'     => $mac,
                'exists'  => true,
            ], 200);
        }

        try {
            // Inserción con valores por defecto requeridos por la BD (NOT NULL)
            $defaultName = 'Dispositivo Pendiente';
            $defaultLocation = 'Sin asignar';

            $model->insert([
                'MAC'       => $mac,
                'nombre'    => $defaultName,
                'ubicacion' => $defaultLocation,
            ]);

            return $this->respondCreated([
                'status'  => 'ok',
                'message' => 'MAC registrada correctamente',
                'mac'     => $mac,
                'exists'  => false,
            ]);
        } catch (\Throwable $e) {
            // Si UNIQUE KEY (MAC) provoca colisión, tratamos como ya registrada
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate') !== false || strpos($msg, '1062') !== false) {
                return $this->respond([
                    'status'  => 'ok',
                    'message' => 'MAC ya registrada',
                    'mac'     => $mac,
                    'exists'  => true,
                ], 200);
            }
            return $this->failServerError('Error al registrar MAC: ' . $msg);
        }
    }
}
