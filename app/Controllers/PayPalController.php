<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompraDispositivoModel;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use CodeIgniter\I18n\Time; // Importar Time para fechas

class PayPalController extends BaseController // Extender de BaseController es correcto
{
    use ResponseTrait;

    protected $compraDispositivoModel;
    protected $dispositivoModel;
    protected $enlaceModel;

    public function __construct()
    {
        // Llama al constructor de BaseController si es necesario para initController
        parent::__construct();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel();

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

        // Recupera la MAC que se reservó al cargar la página de compra
        $macToAssign = $session->get('paypal_buying_mac');
        $amount = $session->get('paypal_buying_amount'); // Monto del dispositivo

        if (empty($macToAssign) || empty($amount)) {
            log_message('error', 'PayPalController::createOrder - MAC o monto no encontrados en sesión.');
            return $this->failServerError('Error al preparar la compra: MAC o monto no especificado.');
        }

        // Llama a la API de PayPal para crear la orden
        // URL de la API de PayPal para crear órdenes (Sandbox o Producción)
        $paypalApiBaseUrl = 'https://api-m.sandbox.paypal.com'; // O 'https://api-m.paypal.com' para producción
        $clientId = getenv('PAYPAL_CLIENT_ID');
        $clientSecret = getenv('PAYPAL_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            log_message('error', 'PayPal credentials (client ID or secret) are not set in .env');
            return $this->failServerError('Credenciales de PayPal no configuradas.');
        }

        try {
            // 1. Obtener Token de Acceso
            $ch = curl_init($paypalApiBaseUrl . '/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Accept-Language: en_US',
                'Content-Type: application/x-www-form-urlencoded',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
            curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 200) {
                log_message('error', 'PayPal Token Error (' . $httpcode . '): ' . $response);
                return $this->failServerError('Error al obtener token de PayPal.');
            }

            $auth_data = json_decode($response, true);
            $access_token = $auth_data['access_token'];

            // 2. Crear Orden
            $ch = curl_init($paypalApiBaseUrl . '/v2/checkout/orders');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amount, 2, '.', ''), // Asegúrate de que el formato sea correcto
                        ],
                        'description' => 'Dispositivo ASG (MAC: ' . $macToAssign . ')', // Información útil para PayPal
                        // Puedes añadir custom_id o invoice_id si necesitas referenciarlo más adelante
                    ],
                ],
                'application_context' => [
                    'return_url' => base_url('paypal/success'),
                    'cancel_url' => base_url('paypal/cancel'),
                    'brand_name' => 'ASG',
                    'locale' => 'es-ES',
                    'shipping_preference' => 'NO_SHIPPING', // No se requiere dirección de envío
                    'user_action' => 'PAY_NOW', // Muestra "Pagar ahora" en el botón
                ],
            ]));

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 201) {
                log_message('error', 'PayPal Create Order Error (' . $httpcode . '): ' . $response);
                return $this->failServerError('Error al crear la orden de PayPal.');
            }

            $order_data = json_decode($response, true);
            return $this->respond($order_data);

        } catch (\Exception $e) {
            log_message('error', 'Error en PayPalController::createOrder: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al crear la orden de PayPal.');
        }
    }

    /**
     * Endpoint para capturar el pago de una orden de PayPal.
     * Llamado por el SDK de PayPal desde el frontend (onApprove).
     * @param string $orderId El ID de la orden de PayPal.
     * @return \CodeIgniter\HTTP\Response
     */
    public function captureOrder(string $orderId)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->failUnauthorized('No autenticado.');
        }

        // Recuperar la MAC asignada para esta compra desde la sesión
        $macAssigned = $session->get('paypal_buying_mac');
        $userId = $session->get('id');

        if (empty($macAssigned)) {
            log_message('error', 'PayPalController::captureOrder - MAC no encontrada en sesión para el usuario ID: ' . $userId);
            return $this->failServerError('Error al procesar el pago: MAC del dispositivo no encontrada.');
        }

        $paypalApiBaseUrl = 'https://api-m.sandbox.paypal.com'; // O 'https://api-m.paypal.com' para producción
        $clientId = getenv('PAYPAL_CLIENT_ID');
        $clientSecret = getenv('PAYPAL_CLIENT_SECRET');

        try {
            // 1. Obtener Token de Acceso
            $ch = curl_init($paypalApiBaseUrl . '/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Accept-Language: en_US',
                'Content-Type: application/x-www-form-urlencoded',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
            curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 200) {
                log_message('error', 'PayPal Token Error (Capture) (' . $httpcode . '): ' . $response);
                return $this->failServerError('Error al obtener token de PayPal para la captura.');
            }

            $auth_data = json_decode($response, true);
            $access_token = $auth_data['access_token'];

            // 2. Capturar la Orden
            $ch = curl_init($paypalApiBaseUrl . '/v2/checkout/orders/' . $orderId . '/capture');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 201 && $httpcode !== 200) { // 201 Created o 200 OK
                log_message('error', 'PayPal Capture Order Error (' . $httpcode . '): ' . $response);
                return $this->failServerError('Error al capturar la orden de PayPal.');
            }

            $capture_data = json_decode($response, true);

            // Verificar el estado del pago
            if (isset($capture_data['status']) && $capture_data['status'] === 'COMPLETED') {
                $transaccionId = $capture_data['id']; // ID de la transacción de PayPal
                $payerEmail = $capture_data['payer']['email_address'] ?? 'N/A';

                // Iniciar transacción de base de datos para asegurar atomicidad
                $this->db->transBegin();
                try {
                    // 1. Registrar la compra en `compras_dispositivos`
                    $this->compraDispositivoModel->insert([
                        'id_usuario_comprador'  => $userId,
                        'MAC_dispositivo'       => $macAssigned,
                        'fecha_compra'          => Time::now()->toDateTimeString(),
                        'transaccion_paypal_id' => $transaccionId,
                        'estado_compra'         => 'completada',
                    ]);

                    // 2. Enlazar la MAC al usuario en la tabla `enlace`
                    // Primero, verificar si ya existe un enlace para evitar duplicados
                    $existingEnlace = $this->enlaceModel
                                            ->where('id_usuario', $userId)
                                            ->where('MAC', $macAssigned)
                                            ->first();
                    if (!$existingEnlace) {
                        $this->enlaceModel->insert([
                            'id_usuario' => $userId,
                            'MAC'        => $macAssigned,
                        ]);
                    } else {
                        log_message('info', 'Intento de enlazar MAC ya existente para usuario ID: ' . $userId . ' y MAC: ' . $macAssigned);
                    }


                    // 3. Actualizar el estado del dispositivo en `dispositivos` a 'en_uso'
                    $this->dispositivoModel->updateDeviceStatusByMac($macAssigned, 'en_uso');

                    $this->db->transCommit(); // Confirmar transacción

                    // Limpiar las variables de sesión de PayPal después de una compra exitosa
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
        } catch (\Exception $e) {
            log_message('error', 'Error general en PayPalController::captureOrder: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error inesperado al procesar el pago.');
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