<?php

namespace App\Controllers;

use App\Models\SuscripcionesModel;
use CodeIgniter\RESTful\ResourceController;

class PushController extends ResourceController
{
    protected $suscripcionesModel;

    public function __construct()
    {
        $this->suscripcionesModel = new SuscripcionesModel();
    }

    /**
     * Guardar la suscripción del usuario
     * POST /push/subscribe
     */
    public function subscribe()
    {
        $data = $this->request->getJSON(true);

        if (!$data || !isset($data['endpoint'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Datos de suscripción incompletos.'
            ]);
        }

        // Insertar o actualizar la suscripción según el endpoint
        $existing = $this->suscripcionesModel->where('endpoint', $data['endpoint'])->first();
        if ($existing) {
            $this->suscripcionesModel->update($existing['id'], $data);
        } else {
            $this->suscripcionesModel->insert($data);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Suscripción guardada correctamente.'
        ]);
    }

    /**
     * Enviar notificación push a todos los usuarios suscritos
     * POST /push/send
     * Recibe: title, body
     */
    public function send()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['title']) || !isset($data['body'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Faltan título o mensaje para la notificación.'
            ]);
        }

        $suscripciones = $this->suscripcionesModel->findAll();

        foreach ($suscripciones as $sub) {
            $this->enviarNotificacion($sub, $data['title'], $data['body']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Notificaciones enviadas.'
        ]);
    }

    /**
     * Función interna para enviar la notificación usando Web Push
     */
    private function enviarNotificacion($subscription, $title, $body)
    {
        // Aquí puedes usar la librería Minishlink/web-push
        // https://github.com/web-push-libs/web-push-php
        // Ejemplo:
        /*
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:tu-email@example.com',
                'publicKey' => 'TU_PUBLIC_KEY',
                'privateKey' => 'TU_PRIVATE_KEY'
            ],
        ];
        $webPush = new \Minishlink\WebPush\WebPush($auth);
        $webPush->sendNotification(
            $subscription['endpoint'],
            json_encode(['title' => $title, 'body' => $body]),
            $subscription['keys']['p256dh'],
            $subscription['keys']['auth']
        );
        */
    }
}
