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
        'email'    => [
            'rules' => 'required|valid_email|is_unique[usuarios.email]|max_length[100]',
            'errors' => [
                'required' => 'El campo email es obligatorio.',
                'valid_email' => 'Debe ser un email válido.',
                'is_unique' => 'El email ya está registrado.',
                'max_length' => 'El campo email no puede exceder los 100 caracteres.'
            ]
        ],
        'password' => [
            'rules' => [
                'required',
                'min_length[6]',
                'max_length[255]',
                'regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/]',
                function ($password) {
                    return !$this->userModel->isCommonPassword($password);
                }
            ],
            'errors' => [
                'required' => 'El campo contraseña es obligatorio.',
                'min_length' => 'Debe tener al menos 6 caracteres.',
                'regex_match' => 'Debe incluir mayúscula, minúscula, número y símbolo.',
                'La contraseña es demasiado común. Elige una más segura.'
            ]
        ]
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    // 🚨 Verificación del reCAPTCHA antes de guardar
    $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
    $secretKey = getenv('RECAPTCHA_SECRET');
    $userIp = $this->request->getIPAddress();

    if (empty($recaptchaResponse)) {
        return redirect()->back()->withInput()->with('error', 'Por favor, completa el reCAPTCHA.');
    }

    // Enviar la solicitud a Google para verificar
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secretKey,
        'response' => $recaptchaResponse,
        'remoteip' => $userIp
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($verifyUrl, false, $context);
    $responseKeys = json_decode($result, true);

    if (!$responseKeys['success']) {
        return redirect()->back()->withInput()->with('error', 'Verificación reCAPTCHA fallida. Por favor, inténtalo de nuevo.');
    }

    // ✅ Si el CAPTCHA es válido, continúa con el registro normal
    $nombre = $this->request->getPost('nombre');
    $apellido = $this->request->getPost('apellido');
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $token = random_string('alnum', 32);
    $expires = Time::now()->addHours(24);

    $userData = [
        'nombre'   => $nombre,
        'apellido' => $apellido,
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'is_active' => 0,
        'reset_token' => $token,
        'reset_expires' => $expires->toDateTimeString(),
    ];

    $userId = $this->userModel->insert($userData);

    if ($userId) {
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Verifica tu cuenta de ASG');

        $verificationLink = base_url("register/verify-email/{$token}");
        $message = "Hola {$nombre},\n\nGracias por registrarte en ASG.\nPor favor, verifica tu cuenta:\n{$verificationLink}\n\nEste enlace expirará en 24 horas.\n\nEquipo ASG";

        $emailService->setMessage($message);

        if ($emailService->send()) {
            return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se envió un correo de verificación a tu email.');
        } else {
            log_message('error', 'Error al enviar correo de verificación: ' . $emailService->printDebugger(['headers', 'subject', 'body']));
            return redirect()->back()->withInput()->with('error', 'Hubo un error al enviar el correo de verificación. Inténtalo de nuevo.');
        }
    } else {
        return redirect()->back()->withInput()->with('error', 'Hubo un error al registrar el usuario. Inténtalo de nuevo.');
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