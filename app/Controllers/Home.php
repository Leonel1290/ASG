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
        log_message('debug', 'Home::inicio() - Usuario ID de la sesión: ' . ($session->get('id') ?? 'null'));

        // Redirige al perfil si ya está logueado
        if ($session->get('logged_in')) {
            return redirect()->to('/perfil');
        }

        return view('inicio');
    }

    public function login()
    {
        $session = session();

        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'Home::login() - Iniciando proceso de login. Datos de sesión al inicio: ' . json_encode($session->get()));
        // --- FIN LOGGING ---

        $userModel = new UserModel();
        $lecturasGasModel = new \App\Models\LecturasGasModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user) {
            if (!$user['is_active']) {
                $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu email para activarla.');
                log_message('debug', 'Home::login() - Intento de login con cuenta inactiva: ' . $email);
                return redirect()->back()->withInput();
            }

            if (password_verify($password, $user['password'])) {
                // Contraseña correcta, iniciar sesión
                $ultimaLectura = $lecturasGasModel
                    ->orderBy('id', 'DESC')
                    ->where(['usuario_id' => $user['id']])
                    ->asArray()
                    ->first();

                $nivel_gas = $ultimaLectura['nivel_gas'] ?? null;

                $sessionData = [
                    'id'        => $user['id'],
                    'nombre'    => $user['nombre'],
                    'email'     => $user['email'],
                    'logged_in' => true,
                ];
                $session->set($sessionData);

                // --- LOGGING PARA DEBUGGING ---
                log_message('debug', 'Home::login() - Login exitoso para usuario ID: ' . $user['id'] . '. Datos de sesión establecidos: ' . json_encode($sessionData));
                // --- FIN LOGGING ---

                return redirect()->to('/perfil');
            } else {
                $session->setFlashdata('error', 'Contraseña incorrecta.');
                log_message('debug', 'Home::login() - Intento de login con contraseña incorrecta para email: ' . $email);
                return redirect()->back()->withInput();
            }
        } else {
            $session->setFlashdata('error', 'Email no encontrado.');
            log_message('debug', 'Home::login() - Intento de login con email no encontrado: ' . $email);
            return redirect()->back()->withInput();
        }
    }
    public function loginobtener() {
        $session = session();
        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'Home::loginobtener() - Mostrando vista de login. Estado de la sesión: ' . json_encode($session->get()));
        // --- FIN LOGGING ---
        return view('login');
    }
    public function processLoginPaypal()
    {
        $session = session();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0) {
                return redirect()->back()->with('error', 'Tu cuenta no está activa. Por favor, verifica tu email.');
            }

            $ses_data = [
                'id'        => $user['id'],
                'nombre'    => $user['nombre'],
                'apellido'  => $user['apellido'],
                'email'     => $user['email'],
                'logged_in' => TRUE
            ];
            $session->set($ses_data);

            // Redirige al usuario a la página de compra después del login exitoso.
            return redirect()->to('/comprar')->with('success', '¡Has iniciado sesión correctamente! Ahora puedes proceder con tu compra.');
        } else {
            $session->setFlashdata('error', 'Email o contraseña incorrectos.');
            return redirect()->back();
        }
    }


    // Método para mostrar el formulario de login para la compra (GET /login/paypal)
    public function loginPaypal()
    {
        $session = session();
        if ($session->get('logged_in')) {
            // Si ya está logueado, redirigir directamente a la página de compra
            return redirect()->to('comprar')->with('info', 'Ya has iniciado sesión. Puedes proceder con tu compra.');
        }
        return view('login_paypal');
    }


    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }

    // Funciones de recuperación de contraseña
    public function forgotpassword()
    {
        return view('forgotpassword');
    }

    public function forgotPPassword()
    {
        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expires = Time::now()->addHours(2)->toDateTimeString();

            $this->userModel->update($user['id'], [
                'reset_token'   => $token,
                'reset_expires' => $expires
            ]);

            $emailService = \Config\Services::email();
            $emailService->setFrom('tucorreo@ejemplo.com', 'ASG Support');
            $emailService->setTo($email);
            $emailService->setSubject('Restablecer Contraseña');
            $resetLink = site_url('reset-password/' . $token);
            $message = "Hola " . esc($user['nombre']) . ",\n\n"
                . "Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace:\n"
                . $resetLink . "\n\n"
                . "Este enlace expirará en 2 horas.\n\n"
                . "Si no solicitaste esto, ignora este correo.\n\n"
                . "Saludos,\nEl equipo de ASG";
            $emailService->setMessage($message);

            if ($emailService->send()) {
                return redirect()->to('/forgotpassword')->with('success', 'Se ha enviado un enlace de recuperación a tu email.');
            } else {
                log_message('error', 'Error al enviar email de restablecimiento: ' . $emailService->printDebugger(['headers']));
                return redirect()->back()->with('error', 'Error al enviar el correo de recuperación.');
            }
        } else {
            return redirect()->back()->with('error', 'No se encontró una cuenta con ese correo electrónico.');
        }
    }

    public function showResetPasswordForm($token)
    {
        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_expires >', Time::now()->toDateTimeString())
            ->first();

        if (!$user) {
            return redirect()->to('/forgotpassword')->with('error', 'El enlace de restablecimiento es inválido o ha expirado.');
        }

        return view('reset_password', ['token' => $token]);
    }

    public function resetPassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_expires >', Time::now()->toDateTimeString())
            ->first();

        if (!$user) {
            return redirect()->to('/forgotpassword')->with('error', 'El enlace de restablecimiento es inválido o ha expirado.');
        }

        $rules = [
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->userModel->update($user['id'], [
            'password'      => $hashedPassword,
            'reset_token'   => null,
            'reset_expires' => null,
        ]);

        return redirect()->to('/login')->with('success', 'Tu contraseña ha sido restablecida exitosamente. Ahora puedes iniciar sesión.');
    }


    // --- RUTAS DE COMPRA (Home::comprar, Home::procesarCompra) ---

    // Vista de compra (GET /comprar)
    public function comprar()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            // Si no está logueado, redirigir al login específico para compra
            return redirect()->to('/login/paypal')->with('error', 'Por favor, inicia sesión para continuar con la compra.');
        }

        // Antes de mostrar la vista de compra, podemos verificar si hay MACs disponibles
        // Aunque la asignación real ocurre en PayPalController, es buena práctica validar.
        $availableMac = $this->compraDispositivoModel->getAvailableMacForAssignment();

        if (!$availableMac) {
            // Si no hay MACs disponibles, informar al usuario
            return redirect()->to('/perfil')->with('error', 'Lo sentimos, no hay dispositivos disponibles para la compra en este momento. Por favor, inténtalo más tarde.');
        }

        // Almacenar la MAC "tentativa" en sesión o simplemente mostrar la vista de compra
        // La asignación REAL se hará en PayPalController::captureOrder
        $session->set('paypal_buying_mac', $availableMac); // Guardamos la MAC que se intentará asignar
        $session->set('paypal_buying_amount', 10.00); // Ejemplo: El precio del dispositivo

        return view('comprar');
    }

    // La ruta '/procesar-compra' (POST) NO se usa directamente para la compra de PayPal
    // El frontend llama a PayPalController::createOrder y PayPalController::captureOrder
    // Este método podría eliminarse si no se usa para otras formas de pago.
    // Lo comento por ahora, asumiendo que solo usas PayPal.
    /*
    public function procesarCompra()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->failUnauthorized('No autenticado.');
        }

        $rules = [
            'mac_dispositivo' => 'required|exact_length[17]|regex_match[/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/]',
            // Aquí puedes añadir reglas de validación para el monto, etc.
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mac = strtoupper($this->request->getPost('mac_dispositivo'));
        $userId = $session->get('id');

        // Lógica de compra:
        // 1. Verificar si la MAC existe y está disponible
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo || $dispositivo['estado_dispositivo'] !== 'disponible') {
            return redirect()->back()->with('error', 'La MAC no es válida o no está disponible.');
        }

        // 2. Verificar si la MAC ya está enlazada a otro usuario
        $enlaceExistente = $this->enlaceModel->where('MAC', $mac)->first();
        if ($enlaceExistente) {
            return redirect()->back()->with('error', 'Esta MAC ya está enlazada a otro usuario.');
        }

        // 3. Registrar la compra (tabla compras_dispositivos)
        $compraData = [
            'id_usuario_comprador' => $userId,
            'MAC_dispositivo'      => $mac,
            'fecha_compra'         => date('Y-m-d H:i:s'),
            'estado_compra'        => 'completada', // O 'pendiente' hasta que PayPal confirme
            'transaccion_paypal_id' => 'PENDIENTE_PAYPAL' // Placeholder
        ];

        // Usar una transacción de base de datos para asegurar atomicidad
        $this->db->transBegin();
        try {
            $this->compraDispositivoModel->insert($compraData);
            $compraId = $this->compraDispositivoModel->insertID();

            // 4. Enlazar la MAC al usuario (tabla enlace)
            $this->enlaceModel->insert([
                'id_usuario' => $userId,
                'MAC'        => $mac
            ]);

            // 5. Actualizar el estado del dispositivo a 'en_uso'
            $this->dispositivoModel->updateDeviceStatusByMac($mac, 'en_uso');

            $this->db->transCommit();
            return redirect()->to('/perfil')->with('success', '¡Dispositivo comprado y enlazado exitosamente!');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error en procesarCompra: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al procesar la compra. Por favor, inténtalo de nuevo.');
        }
    }
    */


    public function editDevice($mac)
    {
        $session = session();
        $userId = $session->get('id');

        // Obtener el dispositivo por la MAC
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        // Verificar que el dispositivo esté enlazado al usuario actual
        $enlace = $this->enlaceModel->where('id_usuario', $userId)->where('MAC', $mac)->first();
        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        return view('edit_device', ['dispositivo' => $dispositivo]);
    }

    public function updateDevice($mac)
    {
        $session = session();
        $userId = $session->get('id');

        // Validar la entrada del formulario
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