<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use App\Models\CompraDispositivoModel; // Asegúrate de importar el modelo CompraDispositivoModel
use App\Models\EnlaceModel; // Importa EnlaceModel
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Home extends BaseController // Asegúrate de extender de BaseController
{
    protected $userModel;
    protected $lecturasGasModel;
    protected $compraDispositivoModel;
    protected $enlaceModel; // Agrega la propiedad para EnlaceModel

    public function __construct()
    {
        // Llama al constructor de la clase padre
        parent::__construct(); // Es importante llamar al constructor del padre si hay inicializaciones allí

        $this->userModel = new UserModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->enlaceModel = new EnlaceModel(); // Instancia EnlaceModel
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
            return redirect()->to('/login'); // Redirige a la nueva ruta /login
        }

        log_message('debug', 'Home::inicio() - Usuario logueado, mostrando vista inicio.');
        return view('inicio');
    }

    /**
     * Muestra el formulario de login (GET) o procesa el login (POST).
     */
    public function login()
    {
        $session = session();

        // Si ya está logueado, redirigir al perfil
        if ($session->get('logged_in')) {
            return redirect()->to('/perfil');
        }

        // Si es una solicitud GET, mostrar el formulario de login
        if ($this->request->getMethod() === 'get') {
            return view('login');
        }

        // Si es una solicitud POST, procesar el login
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

        // Verificar si la cuenta está activa
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
            return redirect()->to('/perfil')->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '!');
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
        // Si el usuario ya está logueado, no debería estar en esta página
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
            // Generar un token único
            $token = bin2hex(random_bytes(32));
            $expires = Time::now()->addHours(1)->toDateTimeString(); // Token válido por 1 hora

            $this->userModel->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires,
            ]);

            // Enviar correo electrónico con el enlace de restablecimiento
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

        // Verificar si el token ha expirado
        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            // Token expirado, limpiar el token en la base de datos
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

        // Actualizar la contraseña y limpiar el token
        $this->userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expires' => null,
        ]);

        return redirect()->to('/cambio_exitoso')->with('success', '¡Contraseña restablecida exitosamente!');
    }

    // Este método ya debería estar en PerfilController.php, pero lo mantengo aquí si lo usas directamente.
    // Lo ideal es que PerfilController maneje esto.
    public function perfil()
    {
        $session = session();
        log_message('debug', 'Home::perfil() - Estado de la sesión al inicio: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::perfil() - Usuario no logueado, redirigiendo a login.');
            return redirect()->to('/login'); // Redirige a la nueva ruta /login
        }

        $usuarioId = $session->get('id');

        // Los modelos ya están instanciados en el constructor si quieres
        // $enlaceModel = new \App\Models\EnlaceModel();
        // $lecturasGasModel = new \App\Models\LecturasGasModel();

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
            return redirect()->to('/login')->with('info', 'Debes iniciar sesión para comprar un dispositivo.'); // Redirige a la nueva ruta /login
        }
        return view('comprar');
    }

    /**
     * Registra una compra de dispositivo después de un pago exitoso de PayPal.
     * Es llamado por PayPalController.
     * @param string $mac_dispositivo La MAC del dispositivo comprado.
     * @param string $transaccion_paypal_id El ID de la transacción de PayPal.
     * @return bool True si la compra se registró exitosamente, false en caso contrario.
     */
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
            // 'monto' => '19.99', // Si necesitas guardar el monto específico de la compra
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
