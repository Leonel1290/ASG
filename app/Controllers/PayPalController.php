<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompraDispositivoModel;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel; // ¡Importa EnlaceModel si necesitas usarlo para el proceso de compra!

class PayPalController extends BaseController // Extender de BaseController es correcto
{
    use ResponseTrait;

    protected $compraDispositivoModel;
    protected $dispositivoModel;
    protected $enlaceModel; // Declara la propiedad para EnlaceModel si la necesitas

    public function __construct()
    {
        parent::__construct();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel(); // Inicializa EnlaceModel si la necesitas

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

        // Recupera la MAC si ya la tienes asignada o es para un producto específico.
        // Si el usuario no ingresa la MAC, ¿cómo llega aquí?
        // Asumiré que tu lógica de negocio te dice qué MAC vender.
        // Por ejemplo, podrías obtenerla del método getAvailableMacForAssignment() en CompraDispositivoModel
        $mac = $this->compraDispositivoModel->getAvailableMacForAssignment();

        if (empty($mac)) {
            return $this->failServerError('No hay dispositivos disponibles para la compra en este momento.');
        }

        $amount = '19.99'; // Precio fijo para el ejemplo
        $currency = 'USD'; // Moneda

        // ALMACENAR LA MAC TEMPORALMENTE EN LA SESIÓN
        // Esto es crucial para poder recuperarla en captureOrder()
        $session->set('paypal_buying_mac', $mac);
        $session->set('paypal_buying_amount', $amount); // También guarda el monto si es variable

        // ... (Resto de tu código para obtener token de acceso y crear la orden de PayPal) ...

        // URL del entorno de PayPal (sandbox o live)
        $paypal_client_id = getenv('PAYPAL_CLIENT_ID') ?: 'TU_PAYPAL_CLIENT_ID';
        $paypal_secret = getenv('PAYPAL_SECRET') ?: 'TU_PAYPAL_SECRET';
        $paypal_base_url = 'https://api-m.sandbox.paypal.com';

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

        if ($order_http_code !== 201) {
            log_message('error', 'PayPal Create Order Error: ' . $order_response);
            return $this->failServerError('Error al crear la orden de PayPal.');
        }

        $order_data = json_decode($order_response, true);

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

        $orderID = $this->request->getPost('orderID');
        if (empty($orderID)) {
            return $this->failValidationErrors('ID de orden de PayPal no proporcionado.');
        }

        $paypal_client_id = getenv('PAYPAL_CLIENT_ID') ?: 'TU_PAYPAL_CLIENT_ID';
        $paypal_secret = getenv('PAYPAL_SECRET') ?: 'TU_PAYPAL_SECRET';
        $paypal_base_url = 'https://api-m.sandbox.paypal.com';

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

        if ($capture_http_code !== 201) {
            log_message('error', 'PayPal Capture Order Error: ' . $capture_response);
            return $this->failServerError('Error al capturar la orden de PayPal.');
        }

        $capture_data = json_decode($capture_response, true);

        if ($capture_data['status'] === 'COMPLETED') {
            // El pago fue exitoso

            // Recuperar la MAC y el monto de la sesión (donde se guardaron en createOrder)
            $mac = $session->get('paypal_buying_mac');
            $amount = $session->get('paypal_buying_amount');
            $transaccionId = $capture_data['id']; // ID de la transacción de PayPal
            $userId = $session->get('id'); // ID del usuario logueado

            // Asegurarse de que la MAC y el UserID sean válidos
            if (empty($mac) || empty($userId)) {
                log_message('error', 'MAC o User ID no encontrados en sesión durante la captura de PayPal.');
                return $this->failServerError('Datos de compra faltantes. Por favor, inténtalo de nuevo.');
            }

            // Iniciar una transacción de base de datos
            $this->db->transBegin();

            try {
                // 1. Registrar la compra en la tabla compras_dispositivos
                $dataCompra = [
                    'id_usuario_comprador'  => $userId,
                    'MAC_dispositivo'       => $mac,
                    'fecha_compra'          => date('Y-m-d H:i:s'),
                    'transaccion_paypal_id' => $transaccionId,
                    'estado_compra'         => 'completada',
                    // Si tienes un campo 'monto' en compras_dispositivos
                    // 'monto'                 => $amount,
                ];

                if (!$this->compraDispositivoModel->insert($dataCompra)) {
                    throw new \Exception('Error al registrar la compra en compras_dispositivos.');
                }

                // 2. Actualizar el estado del dispositivo a 'en_uso' (o 'asignado')
                // Esta es la MAC que SOLO TÚ verás y que se asigna automáticamente al dispositivo vendido.
                if (!$this->dispositivoModel->updateDeviceStatusByMac($mac, 'en_uso')) {
                    throw new \Exception('Error al actualizar el estado del dispositivo en la tabla dispositivos.');
                }

                // *** ESTA ES LA SECCIÓN QUE DEBES ELIMINAR O COMENTAR PARA EVITAR LA ASOCIACIÓN AUTOMÁTICA EN 'enlace' ***
                /*
                $dataEnlace = [
                    'id_usuario' => $userId,
                    'MAC'        => $mac,
                ];
                if (!$this->enlaceModel->insert($dataEnlace)) {
                    throw new \Exception('Error al crear el enlace de dispositivo.');
                }
                */
                // ***************************************************************************************************

                $this->db->transCommit(); // Confirma la transacción

                // Limpiar variables de sesión de PayPal
                $session->remove('paypal_buying_mac');
                $session->remove('paypal_buying_amount');

                return $this->respond(['status' => 'success', 'message' => 'Pago completado y registrado.', 'order_id' => $transaccionId]);

            } catch (\Exception $e) {
                $this->db->transRollback(); // Revertir la transacción
                log_message('error', 'Error en PayPalController::captureOrder durante el registro de compra: ' . $e->getMessage());
                return $this->failServerError('Ocurrió un error al procesar tu compra después del pago. Por favor, contacta a soporte.');
            }

        } else {
            log_message('warning', 'PayPal order status not COMPLETED: ' . json_encode($capture_data));
            return $this->fail('El pago no se ha completado.', 400);
        }
    }

    /**
     * Vista de éxito de PayPal.
     */
    public function success()
    {
        // Limpiar variables de sesión de PayPal si no se hizo en captureOrder
        session()->remove('paypal_buying_mac');
        session()->remove('paypal_buying_amount');

        // Mostrar un mensaje de éxito al usuario
        return view('paypal_success');
    }

    /**
     * Vista de cancelación/fallo de PayPal.
     */
    public function cancel()
    {
        // Limpiar variables de sesión de PayPal
        session()->remove('paypal_buying_mac');
        session()->remove('paypal_buying_amount');

        return view('paypal_cancel');
    }
}
