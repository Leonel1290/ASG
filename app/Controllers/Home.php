<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use App\Models\CompraDispositivoModel;
use App\Models\EnlaceModel;
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

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->userModel = new UserModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->enlaceModel = new EnlaceModel();

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

        $usuarioId = $session->get('id');

        $macs = $this->enlaceModel
            ->select('MAC')
            ->where('id_usuario', $usuarioId)
            ->groupBy('MAC')
            ->findAll();

        $lecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        log_message('debug', 'Home::perfil() - Lecturas por usuario obtenidas: ' . print_r($lecturas, true));

        return view('perfil', [
            'macs' => $macs,
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
        return view('comprar');
    }

    public function registrarCompraAutomatica($mac_dispositivo, $transaccion_paypal_id)
    {
        $session = session();
        $usuario_id = $session->get('id');

        if (!$usuario_id) {
            log_message('error', 'registrarCompraAutomatica: No se pudo obtener el ID de usuario de la sesión.');
            return false;
        }

        $data = [
            'MAC_dispositivo' => $mac_dispositivo,
            'id_usuario' => $usuario_id,
            'fecha_compra' => date('Y-m-d H:i:s'),
            'transaccion_paypal_id' => $transaccion_paypal_id,
        ];

        try {
            $this->compraDispositivoModel->insert($data);
            log_message('info', 'Compra registrada exitosamente para MAC: ' . $mac_dispositivo . ' y usuario: ' . $usuario_id);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error al registrar compra automática: ' . $e->getMessage());
            return false;
        }
    }
}
