<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompraDispositivoModel;
use App\Models\DispositivoModel;

class PayPalController extends BaseController // Extender de BaseController
{
    use ResponseTrait;

    protected $compraDispositivoModel;
    protected $dispositivoModel;

    public function __construct()
    {
        parent::__construct(); // Llama al constructor del padre
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->dispositivoModel = new DispositivoModel();

        // Carga el helper URL para base_url()
        helper('url');
    }

    /**
     * Endpoint para crear una orden en PayPal.
     * Llamado por el SDK de PayPal desde el frontend (createOrder).
     * @return \CodeIgniter\HTTP\Response
     */
    public function createOrder()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->failUnauthorized('No autenticado. Por favor, inicie sesión.');
        }

        $mac = $this->request->getPost('mac_dispositivo'); // Obtener MAC del post
        $amount = '19.99'; // Precio fijo para el ejemplo
        $currency = 'USD'; // Moneda

        if (empty($mac) || !preg_match('/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i', $mac)) {
            return $this->failValidationErrors('MAC de dispositivo no proporcionada o con formato inválido.');
        }
        $mac = strtoupper(str_replace(['-', ' ', ':'], '', $mac));

        // Verificar si la MAC existe en tu tabla de dispositivos
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();
        if (!$dispositivo) {
            return $this->failNotFound('La MAC del dispositivo no existe en nuestra base de datos.');
        }

        // Aquí deberías tener tus credenciales de PayPal.
        // CUIDADO: Estas credenciales DEBEN ser cargadas de forma segura (ej. variables de entorno, archivo de configuración fuera del control de versiones).
        // NUNCA las coloques directamente aquí en un entorno de producción público.
        // Para Render.com, puedes usar variables de entorno para esto.
        $paypal_client_id = getenv('PAYPAL_CLIENT_ID') ?: 'TU_PAYPAL_CLIENT_ID'; // Reemplaza con tu CLIENT_ID
        $paypal_secret = getenv('PAYPAL_SECRET') ?: 'TU_PAYPAL_SECRET'; // Reemplaza con tu SECRET

        // URL del entorno de PayPal (sandbox o live)
        $paypal_base_url = 'https://api-m.sandbox.paypal.com'; // Para pruebas en Sandbox
        // $paypal_base_url = 'https://api-m.paypal.com'; // Para producción

        // 1. Obtener Token de Acceso de PayPal
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypal_base_url . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $paypal_client_id . ':' . $paypal_secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US',
        ]);

        $token_response = curl_exec($ch);
        $token_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($token_http_code !== 200) {
            log_message('error', 'PayPal Access Token Error: ' . $token_response);
            return $this->failServerError('Error al obtener token de PayPal.');
        }
        $token_data = json_decode($token_response, true);
        $access_token = $token_data['access_token'];

        // 2. Crear la Orden de PayPal
        $order_data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount,
                    ],
                    'description' => 'Compra de Dispositivo ASG - MAC: ' . $mac,
                    'custom_id' => $mac, // Usamos la MAC como custom_id para referencia
                    'soft_descriptor' => 'ASG DEVICE',
                ],
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypal_base_url . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ]);

        $order_response = curl_exec($ch);
        $order_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($order_http_code !== 201) { // 201 Created es éxito para crear orden
            log_message('error', 'PayPal Create Order Error: ' . $order_response);
            return $this->failServerError('Error al crear la orden de PayPal.');
        }

        $order_data = json_decode($order_response, true);

        // Devolver el ID de la orden a la vista para que el SDK de PayPal pueda continuar
        return $this->respondCreated(['id' => $order_data['id']]);
    }

    /**
     * Endpoint para capturar una orden en PayPal.
     * Llamado por el SDK de PayPal desde el frontend (onApprove).
     * @return \CodeIgniter\HTTP\Response
     */
    public function captureOrder()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->failUnauthorized('No autenticado. Por favor, inicie sesión.');
        }

        $orderID = $this->request->getPost('orderID'); // ID de la orden de PayPal
        if (empty($orderID)) {
            return $this->failValidationErrors('ID de orden de PayPal no proporcionado.');
        }

        $paypal_client_id = getenv('PAYPAL_CLIENT_ID') ?: 'TU_PAYPAL_CLIENT_ID'; // Reemplaza con tu CLIENT_ID
        $paypal_secret = getenv('PAYPAL_SECRET') ?: 'TU_PAYPAL_SECRET'; // Reemplaza con tu SECRET
        $paypal_base_url = 'https://api-m.sandbox.paypal.com'; // Para pruebas en Sandbox
        // $paypal_base_url = 'https://api-m.paypal.com'; // Para producción

        // 1. Obtener Token de Acceso de PayPal
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypal_base_url . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $paypal_client_id . ':' . $paypal_secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US',
        ]);
        $token_response = curl_exec($ch);
        $token_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($token_http_code !== 200) {
            log_message('error', 'PayPal Access Token Error (Capture): ' . $token_response);
            return $this->failServerError('Error al obtener token de PayPal para captura.');
        }
        $token_data = json_decode($token_response, true);
        $access_token = $token_data['access_token'];

        // 2. Capturar la Orden de PayPal
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypal_base_url . '/v2/checkout/orders/' . $orderID . '/capture');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ]);

        $capture_response = curl_exec($ch);
        $capture_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($capture_http_code !== 201) { // 201 Created es éxito para capturar orden
            log_message('error', 'PayPal Capture Order Error: ' . $capture_response);
            return $this->failServerError('Error al capturar la orden de PayPal.');
        }

        $capture_data = json_decode($capture_response, true);

        // Verificar el estado de la captura
        if ($capture_data['status'] === 'COMPLETED') {
            // El pago fue exitoso
            $mac = $capture_data['purchase_units'][0]['custom_id'] ?? null; // Recuperar la MAC del custom_id
            $transaccionId = $capture_data['id']; // ID de la transacción de PayPal

            // Llamar a tu método para registrar la compra en tu DB
            // Necesitamos una instancia de Home para llamar a su método
            $homeController = new Home();
            $homeController->registrarCompraAutomatica($mac, $transaccionId); // Ya maneja el usuario_id de la sesión

            return $this->respond(['status' => 'success', 'message' => 'Pago completado y registrado.', 'order_id' => $transaccionId]);

        } else {
            // El pago no se completó o está en un estado pendiente
            log_message('warning', 'PayPal order status not COMPLETED: ' . json_encode($capture_data));
            return $this->fail('El pago no se ha completado.', 400);
        }
    }

    /**
     * Vista de éxito de PayPal.
     */
    public function success()
    {
        return view('paypal_success');
    }

    /**
     * Vista de cancelación/fallo de PayPal.
     */
    public function cancel()
    {
        return view('paypal_cancel');
    }
}
