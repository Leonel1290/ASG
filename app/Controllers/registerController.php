<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller; // Asegúrate de extender de Controller o BaseController
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens

// Si extiendes de BaseController, cambia 'extends Controller' a 'extends BaseController'
class registerController extends BaseController // Asumo que extiendes de BaseController
{
    protected $userModel; // Propiedad para el modelo de usuario

    /**
     * Constructor del controlador registerController.
     * Instancia el modelo de usuario y carga helpers necesarios.
     */
    public function __construct()
    {
        // NO se llama a parent::__construct() aquí cuando se extiende de BaseController.
        // La inicialización del padre se maneja a través de initController().

        // Instancia el modelo de usuario
        $this->userModel = new UserModel();

        // Cargar helpers necesarios
        helper(['form', 'url', 'text', 'email']);
    }

    /**
     * Muestra la vista del formulario de registro.
     * Endpoint: GET /register
     */
    public function index()
    {
        return view('register'); // Tu vista del formulario de registro
    }

    /**
     * Procesa el formulario de registro, valida los datos y crea un nuevo usuario.
     * Endpoint: POST /register/store
     */
    public function store()
    {
        // Validación del formulario
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nombre'  => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'El campo nombre es obligatorio.',
                    'min_length' => 'El campo nombre debe tener al menos 3 caracteres.',
                    'max_length' => 'El campo nombre no puede exceder los 50 caracteres.'
                ]
            ],
            'apellido' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'El campo apellido es obligatorio.',
                    'min_length' => 'El campo apellido debe tener al menos 3 caracteres.',
                    'max_length' => 'El campo apellido no puede exceder los 50 caracteres.'
                ]
            ],
            'email'    => [
                'rules' => 'required|valid_email|is_unique[usuarios.email]',
                'errors' => [
                    'required' => 'El campo email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.',
                    'is_unique' => 'Este email ya está registrado.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'El campo contraseña es obligatorio.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Debes confirmar tu contraseña.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ],
            'terminos' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Debes aceptar los términos y condiciones.'
                ]
            ]
        ]);

        // Si la validación falla, redirige de vuelta con los errores y los datos ingresados
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Generar un token de verificación único y su tiempo de expiración
        $verificationToken = bin2hex(random_bytes(32)); // Genera un token aleatorio
        $tokenExpires = Time::now()->addHours(24)->toDateTimeString(); // Expira en 24 horas

        // Preparar los datos del usuario para insertar en la base de datos
        $data = [
            'nombre'     => $this->request->getPost('nombre'),
            'apellido'   => $this->request->getPost('apellido'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active'  => 0, // El usuario no estará activo hasta verificar el email
            'reset_token' => $verificationToken, // Almacenar el token para verificación
            'reset_expires' => $tokenExpires, // Almacenar la fecha de expiración
        ];

        // Intentar insertar el usuario en la base de datos
        if ($this->userModel->insert($data)) {
            // Envío del correo de verificación
            $email = \Config\Services::email();

            $email->setFrom('no-reply@tudominio.com', 'Sistema de Monitoreo de Gas');
            $email->setTo($data['email']);
            $email->setSubject('Verifica tu cuenta en ASG');

            $verificationLink = base_url('/register/verify-email/' . $verificationToken);
            $message = view('email/verification_email', ['verificationLink' => $verificationLink, 'userName' => $data['nombre']]);

            $email->setMessage($message);

            if ($email->send()) {
                log_message('info', 'Correo de verificación enviado a ' . $data['email']);
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Por favor, verifica tu correo electrónico para activar tu cuenta.');
            } else {
                // Si falla el envío del email, loguear el error y notificar al usuario
                log_message('error', 'Fallo al enviar correo de verificación a ' . $data['email'] . ': ' . $email->printDebugger(['headers']));
                // Aunque el email falle, el usuario ya está en la DB, solo que inactivo.
                // Podrías ofrecer reenviar el email o que contacte soporte.
                return redirect()->to('/register/check-email')->with('error', 'Registro exitoso, pero no pudimos enviar el correo de verificación. Por favor, contacta a soporte.');
            }
        } else {
            // Si hay un error al insertar el usuario en la base de datos
            return redirect()->back()->withInput()->with('error', 'Hubo un error al registrar el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Muestra la página que informa al usuario que debe revisar su email.
     * Endpoint: GET /register/check-email
     */
    public function checkEmail()
    {
        return view('verification_message'); // Asume que tienes esta vista
    }

    /**
     * Verifica el token de activación recibido por email.
     * Endpoint: GET /register/verify-email/(:segment)
     */
    public function verifyEmailToken($token)
    {
        $user = $this->userModel->where('reset_token', $token)->first();

        // Verificar si el token es válido
        if (!$user) {
            return redirect()->to('/register')->with('error', 'El token de verificación es inválido o ya ha sido utilizado.');
        }

        // Verificar si el token ha expirado
        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            // Token expirado, limpiar el token en la base de datos
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/register')->with('error', 'El token de verificación ha expirado. Por favor, regístrate de nuevo para obtener un nuevo token.');
        }

        // Verificar si el usuario ya está activo (evitar usar el mismo enlace varias veces)
        if ($user['is_active']) {
             // Limpiar el token aunque ya esté activo
             $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
             return redirect()->to('/loginobtener')->with('info', 'Tu cuenta ya ha sido verificada. Por favor, inicia sesión.');
        }

        // --- Token válido: Activar la cuenta del usuario ---
        $updateData = [
            'is_active' => 1, // Marcar como activo
            'reset_token' => null, // Limpiar el token después de usarlo
            'reset_expires' => null, // Limpiar la expiración
        ];
        $this->userModel->update($user['id'], $updateData);

        // --- REDIRECCIÓN DESPUÉS DEL REGISTRO/VERIFICACIÓN ---
        // Verificar si hay una URL de redirección guardada en flashdata (ej. si venía de /comprar)
        $redirectUrl = session()->getFlashdata('redirect_after_login');
        if ($redirectUrl) {
            session()->removeFlashdata('redirect_after_login'); // Limpiar flashdata
            return redirect()->to($redirectUrl)->with('success', '¡Tu cuenta ha sido verificada exitosamente! Por favor, completa tu compra.');
        } else {
            // Redirigir al usuario a la página de login con un mensaje de éxito
            return redirect()->to('/loginobtener')->with('success', '¡Tu cuenta ha sido verificada exitosamente! Ahora puedes iniciar sesión.');
        }
        // --- FIN REDIRECCIÓN ---
    }
}
