<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class GasController extends Controller
{
    protected $gasThreshold = 500; // Umbral de ejemplo (ajusta según necesidad)
    protected $valveModel;

    public function __construct()
    {
        $this->valveModel = new \App\Models\ValveModel(); // Asume un modelo para control de válvula
    }

    public function checkGasLevel()
    {
        $gasLevel = $this->request->getPost('gas_level'); // Asume nivel de gas desde sensor
        $session = session();

        if ($gasLevel > $this->gasThreshold) {
            if (!$session->has('gas_alert_start')) {
                // Inicia temporizador en la primera detección
                $session->set('gas_alert_start', Time::now()->getTimestamp());
                return $this->response->setJSON([
                    'status' => 'warning',
                    'message' => '¡Nivel de gas superó el umbral! Por favor, actúa.'
                ]);
            }

            $elapsedTime = Time::now()->getTimestamp() - $session->get('gas_alert_start');
            if ($elapsedTime >= 60 && !$session->get('user_action_taken')) {
                // Sin acción del usuario tras 60 segundos, cierra válvula
                $this->valveModel->closeValve();
                $session->remove('gas_alert_start');
                return $this->response->setJSON([
                    'status' => 'closed',
                    'message' => 'Electroválvula cerrada automáticamente por falta de acción.'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'Nivel de gas aún alto. Tiempo restante: ' . (60 - $elapsedTime) . ' segundos.'
            ]);
        } else {
            // Reinicia temporizador si el nivel de gas cae por debajo del umbral
            $session->remove('gas_alert_start');
            return $this->response->setJSON([
                'status' => 'normal',
                'message' => 'Nivel de gas normal.'
            ]);
        }
    }

    public function userAction()
    {
        // Maneja acción del usuario (por ejemplo, anulación manual)
        session()->set('user_action_taken', true);
        return $this->response->setJSON([
            'status' => 'acknowledged',
            'message' => 'Acción del usuario registrada. Cierre automático de válvula cancelado.'
        ]);
    }
}
