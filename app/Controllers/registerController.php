<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens

class registerController extends BaseController // Asegúrate de extender de BaseController
{
    protected $userModel; // Propiedad para el modelo de usuario

    public function __construct()
    {
        parent::__construct(); // Llama al constructor del padre
        // Instancia el modelo de usuario
        $this->userModel = new UserModel();

        // Cargar helpers necesarios
        helper(['form', 'url', 'text', 'email']); // Asegúrate de que 'text' y 'email' estén aquí
    }

    // Método para mostrar la vista del formulario de registro (GET /register)
    public function index()
    {
        return view('register'); // Tu vista del formulario de registro
    }

    // Método para procesar el formulario de registro (POST /register/store)
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
            'email' => [
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
                    'required' => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ],
            'confirmpassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Confirma tu contraseña.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Si la validación falla, redirige de nuevo al formulario con los errores
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Generar un token de verificación de email
        $verificationToken = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(1)->toDateTimeString(); // Token válido por 1 hora

        // Datos para insertar en la base de datos
        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'apellido'      => $this->request->getPost('apellido'),
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active'     => 0, // Usuario inactivo hasta verificar email
            'reset_token'   => $verificationToken, // Reutilizamos el campo reset_token para verificación
            'reset_expires' => $expires,
        ];

        // Guardar usuario en la base de datos
        if ($this->userModel->insert($data)) {
            // Enviar correo electrónico de verificación
            $emailService = \Config\Services::email();
            $emailService->setFrom('no-reply@tudominio.com', 'Sistema ASG');
            $emailService->setTo($data['email']);
            $emailService->setSubject('Verifica tu cuenta ASG');
            $verificationLink = base_url('/register/verify-email/' . $verificationToken);
            $message = "Hola " . esc($data['nombre']) . ",\n\n"
                     . "Gracias por registrarte en ASG. Por favor, haz clic en el siguiente enlace para verificar tu cuenta:\n"
                     . $verificationLink . "\n\n"
                     . "Este enlace expirará en 1 hora. Si no te registraste en ASG, ignora este correo.\n\n"
                     . "Gracias,\nSistema ASG";
            $emailService->setMessage($message);

            if ($emailService->send()) {
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se ha enviado un enlace de verificación a tu email.');
            } else {
                $error = $emailService->printDebugger(['headers']);
                log_message('error', 'Error al enviar email de verificación: ' . $error);
                return redirect()->back()->withInput()->with('error', 'Error al enviar el correo de verificación. Por favor, intenta de nuevo.');
            }
        } else {
            // Si hay un error al insertar en la DB
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el usuario. Intenta de nuevo.');
        }
    }

    // Método para mostrar la página que le dice al usuario que revise su email
    public function checkEmail()
    {
        return view('verification_message');
    }

    // Método para verificar el token de email
    public function verifyEmailToken($token)
    {
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/register')->with('error', 'Token de verificación inválido o ya utilizado.');
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
             return redirect()->to('/login')->with('info', 'Tu cuenta ya ha sido verificada. Por favor, inicia sesión.'); // Redirige a /login
        }


        // --- Token válido: Activar la cuenta del usuario ---

        // Marcar al usuario como activo en la base de datos
        $updateData = [
            'is_active' => 1, // Marcar como activo
            'reset_token' => null, // Limpiar el token después de usarlo
            'reset_expires' => null, // Limpiar la expiración
        ];
        $this->userModel->update($user['id'], $updateData);

        // Redirigir al usuario a la página de login con un mensaje de éxito
        return redirect()->to('/login')->with('success', '¡Tu cuenta ha sido verificada exitosamente! Ahora puedes iniciar sesión.'); // Redirige a /login
    }
}
