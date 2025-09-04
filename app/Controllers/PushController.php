<?php

namespace App\Controllers;

use App\Models\SuscripcionesModel;
use CodeIgniter\RESTful\ResourceController;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushController extends ResourceController
{
    protected $suscripcionesModel;

    // Claves VAPID (usa tus propias claves)
    private $vapidPublicKey  = 'TU_PUBLIC_KEY';
    private $vapidPrivateKey = 'TU_PRIVATE_KEY';
    private $vapidSubject    = 'mailto:tu-email@example.com';

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

        if (!$data || !isset($data['endpoint']) || !isset($data['keys'])) {
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
     * Método público para enviar notificación desde LecturasController
     */
    public function sendNotificationPush($title, $body)
    {
        $suscripciones = $this->suscripcionesModel->findAll();

        foreach ($suscripciones as $sub) {
            $this->enviarNotificacion($sub, $title, $body);
        }
    }

    /**
     * Función interna para enviar la notificación usando Web Push
     */
    private function enviarNotificacion($subscription, $title, $body)
    {
        try {
            $auth = [
                'VAPID' => [
                    'subject' => $this->vapidSubject,
                    'publicKey' => $this->vapidPublicKey,
                    'privateKey' => $this->vapidPrivateKey,
                ],
            ];

            $webPush = new WebPush($auth);

            $sub = Subscription::create([
                'endpoint' => $subscription['endpoint'],
                'publicKey' => $subscription['keys']['p256dh'],
                'authToken' => $subscription['keys']['auth'],
            ]);

            $webPush->sendOneNotification($sub, json_encode([
                'title' => $title,
                'body'  => $body,
            ]));
        } catch (\Exception $e) {
            log_message('error', 'Error enviando notificación: ' . $e->getMessage());
        }
    }
}
