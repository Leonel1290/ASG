<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens

class registerController extends Controller
{
    protected $userModel; // Propiedad para el modelo de usuario

    public function __construct()
    {
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
            'email'   => [
                'rules' => 'required|valid_email|is_unique[usuarios.email]|max_length[100]',
                'errors' => [
                    'required' => 'El campo email es obligatorio.',
                    'valid_email' => 'El campo email debe contener una dirección válida.',
                    'is_unique' => 'El email ya está registrado.',
                    'max_length' => 'El campo email no puede exceder los 100 caracteres.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[120]|regex_match[/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~`])/',
                'errors' => [
                    'required' => 'El campo contraseña es obligatorio.',
                    'min_length' => 'La contraseña debe tener al menos 120 caracteres.',
                    'regex_match' => 'La contraseña debe contener al menos una letra mayúscula y un carácter especial (ej. !@#$).'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Si la validación falla, redirigir de vuelta al formulario con errores y datos antiguos
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Procesar los datos del formulario si la validación es exitosa
        $nombre = $this->request->getPost('nombre');
        $apellido = $this->request->getPost('apellido');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Generar token de verificación y definir expiración
        $token = random_string('alnum', 32); // Genera un token alfanumérico de 32 caracteres
        $expires = Time::now()->addHours(24); // Token válido por 24 horas

        // Preparar datos para guardar el usuario (inicialmente inactivo)
        $userData = [
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT), // Encriptar la contraseña
            'is_active' => 0, // <-- Marcar como inactivo por defecto
            'reset_token' => $token, // Usamos reset_token para el token de verificación
            'reset_expires' => $expires->toDateTimeString(), // Guardar expiración
        ];

        // Guardar el usuario en la base de datos
        // Usamos insert() en lugar de save() para obtener el ID del nuevo registro
        $userId = $this->userModel->insert($userData);

        if ($userId) {
            // --- Enviar el correo electrónico de verificación ---
            $emailService = \Config\Services::email();

            // Configura el remitente. Es mejor configurar esto en app/Config/Email.php o .env
            // Si no está configurado globalmente, descomenta y ajusta las siguientes líneas:
            // $emailService->setFrom('tu_correo@ejemplo.com', 'ASG'); // <-- CONFIGURA ESTO

            $emailService->setTo($email);
            $emailService->setSubject('Verifica tu cuenta de ASG');

            // Crear el enlace de verificación
            $verificationLink = base_url("register/verify-email/{$token}"); // <-- NUEVA RUTA

            $message = "Hola {$nombre},\n\nGracias por registrarte en ASG.\n\nPor favor, haz clic en el siguiente enlace para verificar tu cuenta:\n{$verificationLink}\n\nEste enlace expirará en 24 horas.\n\nSi no te registraste en ASG, puedes ignorar este correo.\n\nAtentamente,\nEl equipo de ASG";

            $emailService->setMessage($message);

            // Intentar enviar el correo
            if ($emailService->send()) {
                // Éxito al enviar el correo
                log_message('debug', 'Correo de verificación de registro enviado a: ' . $email);
                // Redirigir a una página que le dice al usuario que revise su email
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se ha enviado un correo de verificación a tu email. Por favor, revisa tu bandeja de entrada para activar tu cuenta.');
            } else {
                // Error al enviar el correo
                // Puedes loguear el error para depuración
                log_message('error', 'Error al enviar correo de verificación de registro a ' . $email . ': ' . $emailService->printDebugger(['headers', 'subject', 'body']));
                // Opcional: Eliminar el usuario recién creado si el email no se pudo enviar
                // $this->userModel->delete($userId);
                return redirect()->back()->withInput()->with('error', 'Hubo un error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
            }
            // --- Fin Enviar el correo electrónico ---

        } else {
            // Error al guardar el usuario en la base de datos
            return redirect()->back()->withInput()->with('error', 'Hubo un error al registrar el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    // Método para mostrar la página que le dice al usuario que revise su email (GET /register/check-email)
    public function checkEmail()
    {
        // Esta vista simplemente informa al usuario sobre el email enviado
        return view('register/verification_message'); // <-- Vista para mensaje de verificación
    }


    // Método para verificar el token recibido por email (GET /register/verify-email/(:segment))
    public function verifyEmailToken($token = null)
    {
        if ($token === null) {
            return redirect()->to('/register')->with('error', 'Token de verificación no proporcionado.');
        }

        // Buscar al usuario por el token
        $user = $this->userModel->getUserByToken($token); // Usamos el método del modelo

        // Verificar si se encontró un usuario con ese token
        if (!$user) {
            return redirect()->to('/register')->with('error', 'Token de verificación inválido.');
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

        // Marcar al usuario como activo en la base de datos
        $updateData = [
            'is_active' => 1, // Marcar como activo
            'reset_token' => null, // Limpiar el token después de usarlo
            'reset_expires' => null, // Limpiar la expiración
        ];
        $this->userModel->update($user['id'], $updateData);

        // Redirigir al usuario a la página de login con un mensaje de éxito
        return redirect()->to('/loginobtener')->with('success', '¡Tu cuenta ha sido verificada exitosamente! Ahora puedes iniciar sesión.');
    }
}
