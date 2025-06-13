<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use App\Models\CompraDispositivoModel; // Usa CompraDispositivoModel
use App\Models\EnlaceModel;
use App\Models\DispositivoModel; // ¡Importa el DispositivoModel!
use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Home extends BaseController
{
    protected $userModel;
    protected $lecturasGasModel;
    protected $compraDispositivoModel;
    protected $enlaceModel;
    protected $dispositivoModel; // ¡Declara la propiedad para DispositivoModel!

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->userModel = new UserModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->enlaceModel = new EnlaceModel();
        $this->dispositivoModel = new DispositivoModel(); // ¡Inicializa DispositivoModel!

        helper(['url', 'form', 'text', 'email']);
    }

    public function index()
    {
        return view('inicio');
    }

    public function inicio()
    {
        $session = session();
        log_message('debug', 'Home::inicio() - Estado de la sesión: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::inicio() - Usuario no logueado, redirigiendo a login.');
            return redirect()->to('/login');
        }

        log_message('debug', 'Home::inicio() - Usuario logueado, mostrando vista inicio.');
        return view('inicio');
    }

    public function login()
    {
        $session = session();

        if ($session->get('logged_in')) {
            return redirect()->to('/perfil');
        }

        if ($this->request->getMethod() === 'get') {
            return view('login');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }

        if (!$user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Tu cuenta aún no ha sido activada. Por favor, revisa tu correo electrónico para verificarla.');
        }

        if (password_verify($password, $user['password'])) {
            $ses_data = [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'email' => $user['email'],
                'logged_in' => TRUE,
            ];
            $session->set($ses_data);

            $redirectUrl = $session->getFlashdata('redirect_after_login') ?? '/perfil';
            return redirect()->to($redirectUrl)->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '!');

        } else {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }
    }


    /**
     * Muestra el formulario de login específico para el flujo de PayPal.
     */
    public function loginPaypal()
    {
        $session = session();
        if ($session->get('logged_in')) {
            return redirect()->to('/comprar'); // Si ya está logueado, va directo a la compra
        }
        return view('login_paypal');
    }

    /**
     * Procesa el login específico para el flujo de PayPal.
     */
    public function processLoginPaypal()
    {
        $session = session();
        if ($session->get('logged_in')) {
            return redirect()->to('/comprar'); // Si ya está logueado, va directo a la compra
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Pasar los errores a la vista específica de PayPal login
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }

        if (!$user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Tu cuenta aún no ha sido activada. Por favor, revisa tu correo electrónico para verificarla.');
        }

        if (password_verify($password, $user['password'])) {
            $ses_data = [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'email' => $user['email'],
                'logged_in' => TRUE,
            ];
            $session->set($ses_data);
            return redirect()->to('/comprar')->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '! Ahora puedes continuar con tu compra.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }
    }


    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/')->with('info', 'Has cerrado sesión correctamente.');
    }

    public function forgotpassword()
    {
        $session = session();
        if ($session->get('logged_in')) {
            return redirect()->to('/perfil');
        }
        return view('forgot_password');
    }

    public function forgotPPassword()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = Time::now()->addHours(1)->toDateTimeString();

            $this->userModel->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires,
            ]);

            $emailService = \Config\Services::email();
            $emailService->setFrom('no-reply@tudominio.com', 'Sistema ASG');
            $emailService->setTo($email);
            $emailService->setSubject('Restablecimiento de Contraseña');
            $resetLink = base_url('/reset-password/' . $token);
            $message = "Hola " . esc($user['nombre']) . ",\n\n"
                     . "Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:\n"
                     . $resetLink . "\n\n"
                     . "Este enlace expirará en 1 hora. Si no solicitaste un restablecimiento de contraseña, ignora este correo.\n\n"
                     . "Gracias,\nSistema ASG";
            $emailService->setMessage($message);

            if ($emailService->send()) {
                return redirect()->to('/forgotpassword')->with('success', 'Se ha enviado un enlace de restablecimiento de contraseña a tu email.');
            } else {
                $error = $emailService->printDebugger(['headers']);
                log_message('error', 'Error al enviar email de restablecimiento: ' . $error);
                return redirect()->back()->with('error', 'No se pudo enviar el correo de restablecimiento. Inténtalo de nuevo más tarde.');
            }
        }

        return redirect()->back()->with('error', 'No se encontró una cuenta con ese email.');
    }

    public function showResetPasswordForm($token = null)
    {
        if (is_null($token)) {
            return redirect()->to('/forgotpassword')->with('error', 'Token de restablecimiento de contraseña no proporcionado.');
        }

        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/forgotpassword')->with('error', 'Token de restablecimiento de contraseña inválido o ya utilizado.');
        }

        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/forgotpassword')->with('error', 'El token de restablecimiento de contraseña ha expirado. Por favor, solicita uno nuevo.');
        }

        return view('reset_password', ['token' => $token]);
    }

    public function resetPassword()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'token' => 'required',
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'La confirmación de contraseña es obligatoria.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/forgotpassword')->with('error', 'Token de restablecimiento de contraseña inválido o ya utilizado.');
        }

        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/forgotpassword')->with('error', 'El token de restablecimiento de contraseña ha expirado. Por favor, solicita uno nuevo.');
        }

        $this->userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expires' => null,
        ]);

        return redirect()->to('/cambio_exitoso')->with('success', '¡Contraseña restablecida exitosamente!');
    }

    public function perfil()
    {
        $session = session();
        log_message('debug', 'Home::perfil() - Estado de la sesión al inicio: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::perfil() - Usuario no logueado, redirigiendo a login.');
            $session->setFlashdata('redirect_after_login', current_url());
            return redirect()->to('/login')->with('info', 'Para ver tu perfil, por favor, inicia sesión.');
        }

        $userId = $session->get('id');

        // Modifica esta consulta para obtener los detalles del dispositivo (nombre y ubicacion)
        $linkedDevices = $this->enlaceModel
            ->select('enlace.id as enlace_id, dispositivos.id as dispositivo_id, dispositivos.MAC, dispositivos.nombre, dispositivos.ubicacion')
            ->join('dispositivos', 'enlace.MAC = dispositivos.MAC')
            ->where('enlace.id_usuario', $userId)
            ->findAll();

        $lecturas = $this->lecturasGasModel->getLecturasPorUsuario($userId);

        log_message('debug', 'Home::perfil() - Lecturas por usuario obtenidas: ' . print_r($lecturas, true));

        return view('perfil', [
            'linkedDevices' => $linkedDevices, // Pasa los dispositivos enlazados con sus detalles
            'lecturas' => $lecturas
        ]);
    }

    public function comprar()
    {
        $session = session();
        log_message('debug', 'Home::comprar() - Mostrando vista de comprar. Estado de la sesión: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::comprar() - Usuario no logueado, redirigiendo a login.');
            // Redirigir a la página de login específica para PayPal
            return redirect()->to('/login/paypal')->with('info', 'Debes iniciar sesión para comprar un dispositivo.');
        }
        // La vista de compra simplemente presentará un botón para iniciar la compra
        return view('comprar');
    }

    /**
     * Procesa la compra de un dispositivo, asignando automáticamente una MAC disponible.
     * Esta función es llamada después de que el usuario "confirma" la compra (ej. clic en un botón de PayPal).
     */
    public function procesarCompra()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para completar la compra.');
        }

        $userId = session()->get('id');

        // 1. (Opcional pero recomendado) Implementa la lógica de pasarela de pago aquí (ej. PayPal)
        // Por ahora, simularemos un ID de transacción.
        $transaccionPaypalId = 'PAYPAL_SIMULADO_' . uniqid(); // ID de transacción simulado

        // 2. Encuentra una MAC disponible para asignar
        $assignedMAC = $this->compraDispositivoModel->getAvailableMacForAssignment();

        if (empty($assignedMAC)) {
            // Esto significa que no hay dispositivos 'disponible' en tu inventario.
            return redirect()->back()->with('error', 'Lo sentimos, no hay dispositivos disponibles para la compra en este momento. Por favor, inténtalo más tarde.');
        }

        // Inicia una transacción de base de datos para asegurar la atomicidad de las operaciones
        $this->db->transBegin();

        try {
            // 3. Registra la compra en la tabla compras_dispositivos
            $dataCompra = [
                'id_usuario_comprador' => $userId,
                'MAC_dispositivo'      => $assignedMAC,
                'fecha_compra'         => date('Y-m-d H:i:s'), // Timestamp actual
                'transaccion_paypal_id'=> $transaccionPaypalId,
                'estado_compra'        => 'completada'
            ];

            if (!$this->compraDispositivoModel->insert($dataCompra)) {
                throw new \Exception('Error al registrar la compra: ' . json_encode($this->compraDispositivoModel->errors()));
            }

            // 4. Crea el enlace en la tabla 'enlace'
            $dataEnlace = [
                'id_usuario' => $userId,
                'MAC'        => $assignedMAC,
            ];

            if (!$this->enlaceModel->insert($dataEnlace)) {
                throw new \Exception('Error al crear el enlace de dispositivo: ' . json_encode($this->enlaceModel->errors()));
            }

            // 5. Actualiza el estado del dispositivo a 'en_uso' (o 'asignado') en la tabla 'dispositivos'
            if (!$this->dispositivoModel->updateDeviceStatusByMac($assignedMAC, 'en_uso')) {
                throw new \Exception('Error al actualizar el estado del dispositivo: ' . json_encode($this->dispositivoModel->errors()));
            }

            $this->db->transCommit(); // Confirma la transacción si todas las operaciones son exitosas

            return redirect()->to('/perfil')->with('success', '¡Felicidades! Tu dispositivo ASG ha sido comprado y enlazado exitosamente a tu cuenta. ¡Comienza a monitorear!');

        } catch (\Exception $e) {
            $this->db->transRollback(); // Revierte la transacción en caso de error
            log_message('error', 'Error en procesarCompra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar tu compra. Por favor, inténtalo de nuevo o contacta a soporte.');
        }
    }


    // El método registrarCompraAutomatica parece ser un auxiliar que no se usa directamente con una ruta.
    // Su lógica se ha movido/fusionado con procesarCompra para un flujo más completo.
    // Si aún lo necesitas para otros propósitos, asegúrate de que no se duplique la funcionalidad.
    /*
    public function registrarCompraAutomatica($mac_dispositivo, $transaccion_paypal_id)
    {
        // ... (Tu código existente si aún lo necesitas)
    }
    */

    // Métodos para eliminar y editar dispositivos enlazados (manteniendo MAC oculta al usuario)
    public function eliminarDispositivo($mac)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        $userId = session()->get('id');

        // Verify that the device is actually linked to the current user
        $enlace = $this->enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para desvincular este dispositivo.');
        }

        // Start a database transaction
        $this->db->transBegin();

        try {
            // 1. Delete the link from the 'enlace' table
            if (!$this->enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->delete()) {
                throw new \Exception('Error al eliminar el enlace del dispositivo.');
            }

            // 2. Update the device status back to 'disponible' (or another appropriate status)
            // This makes the device available for another purchase.
            if (!$this->dispositivoModel->updateDeviceStatusByMac($mac, 'disponible')) {
                throw new \Exception('Error al actualizar el estado del dispositivo.');
            }

            $this->db->transCommit(); // Commit if both operations succeed

            return redirect()->to('/perfil')->with('success', 'Dispositivo desvinculado exitosamente.');

        } catch (\Exception $e) {
            $this->db->transRollback(); // Rollback on error
            log_message('error', 'Error al desvincular dispositivo: ' . $e->getMessage());
            return redirect()->to('/perfil')->with('error', 'Ocurrió un error al desvincular el dispositivo. Por favor, inténtalo de nuevo.');
        }
    }


    public function editarDispositivo($mac)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para editar un dispositivo.');
        }

        $userId = session()->get('id');

        // Verify that the device is actually linked to the current user
        $enlace = $this->enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo o no está enlazado a tu cuenta.');
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        $data = [
            'titulo' => 'Editar Dispositivo',
            'dispositivo' => $dispositivo
        ];
        return view('edit_device', $data);
    }

    public function actualizarDispositivo()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para actualizar un dispositivo.');
        }

        $userId = session()->get('id');
        $mac = $this->request->getPost('MAC'); // Get MAC from hidden input

        // First, validate input (nombre and ubicacion)
        $rules = [
            'nombre'    => 'required|max_length[255]',
            'ubicacion' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            // If validation fails, redirect back with errors and old input
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify that the device is actually linked to the current user before updating
        $enlace = $this->enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para actualizar este dispositivo.');
        }

        $dataToUpdate = [
            'nombre'    => $this->request->getPost('nombre'),
            'ubicacion' => $this->request->getPost('ubicacion'),
        ];

        // Update the device
        // Necesitas el ID del dispositivo, no la MAC para el método update() de CodeIgniter.
        // Si tu DispositivoModel tiene un método como updateByMac, úsalo.
        // Si no, primero busca el ID del dispositivo por la MAC:
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();
        if ($dispositivo && $this->dispositivoModel->update($dispositivo['id'], $dataToUpdate)) {
             return redirect()->to('/perfil')->with('success', 'Dispositivo actualizado exitosamente.');
        } else {
             return redirect()->back()->withInput()->with('error', 'Error al actualizar el dispositivo. Por favor, inténtalo de nuevo.');
        }
    }
}
