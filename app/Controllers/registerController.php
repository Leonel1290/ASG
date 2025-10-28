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
        helper(['form', 'url', 'text', 'email']); 
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
                    'valid_email' => 'Por favor, introduce una dirección de email válida.',
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
                    'required' => 'Debes confirmar la contraseña.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // --- Generación de Token de Verificación ---
        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(2)->toDateTimeString();

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active' => 0, // Inactivo hasta verificar
            'reset_token' => $token, // Reutilizado para verificación de email
            'reset_expires' => $expires, // Expiración del token
        ];

        if ($this->userModel->insert($data)) {
            // $user_id = $this->userModel->getInsertID(); // Puedes usar esto si lo necesitas

            // --- INICIO: Lógica de Envío de Email con SendGrid (CodeIgniter) ---
            $emailService = \Config\Services::email();

            // 1. Configurar Remitente con variables de entorno (SENDGRID_FROM_EMAIL/NAME en Render)
            $emailService->setFrom(getenv('SENDGRID_FROM_EMAIL'), getenv('SENDGRID_FROM_NAME')); // <--- CAMBIO CLAVE

            // 2. Configurar destinatario y asunto
            $emailService->setTo($data['email']);
            $emailService->setSubject('Verifica tu Cuenta en ASG');

            // 3. Crear el enlace de verificación
            $verificationLink = base_url('/register/verify-email/' . $token); // Usa tu ruta de Routes.php

            // 4. Crear el mensaje (Usando HTML simple)
            $message = "<h2>¡Bienvenido a ASG, " . esc($data['nombre']) . "!</h2>"
                . "<p>Gracias por registrarte. Por favor, haz clic en el siguiente enlace para activar tu cuenta:</p>"
                . "<p><a href=\"{$verificationLink}\">Activar mi cuenta ahora</a></p>"
                . "<p>Este enlace expirará en 2 horas.</p>"
                . "<br>"
                . "<p>Si no te registraste en ASG, puedes ignorar este correo.</p>"
                . "<p>Saludos,<br>El equipo de ASG</p>";

            $emailService->setMessage($message);

            // 5. Enviar el correo
            if ($emailService->send()) {
                // Éxito: Redirige a la página de "revisa tu email"
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se ha enviado un enlace de verificación a tu email. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).');
            } else {
                // Fallo: Loggea el error (visible en los logs de Render) y muestra un mensaje al usuario
                $error = $emailService->printDebugger(['headers']);
                log_message('error', 'Error al enviar email de verificación (SendGrid): ' . $error);
                
                // Opcional: Podrías considerar eliminar el usuario recién creado aquí si el email es CRÍTICO.
                
                return redirect()->back()->withInput()->with('error', 'Error al enviar el correo de verificación. Por favor, inténtalo de nuevo. Asegúrate de que la API Key de SendGrid esté correcta.');
            }
            // --- FIN: Lógica de Envío de Email ---
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el usuario. Inténtalo de nuevo.');
        }
    }

    // Método para mostrar la vista que pide revisar el email
    public function checkEmail()
    {
        return view('verification_message');
    }

    // Método para procesar la verificación del token (GET /register/verify-email/$token)
    public function verifyEmailToken($token)
    {
        if (empty($token)) {
            return redirect()->to('/register')->with('error', 'Token de verificación no proporcionado.');
        }

        // Buscar al usuario por el token y asegurar que no esté activo
        $user = $this->userModel->where('reset_token', $token)
                                ->where('is_active', 0) // Solo usuarios inactivos
                                ->first();

        if (!$user) {
            // El token no existe, ya fue usado o el usuario ya está activo
            return redirect()->to('/loginobtener')->with('error', 'El enlace de verificación no es válido o ya fue utilizado.');
        }

        // --- Verificación de Expiración ---
        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            // Token expirado, limpiar el token en la base de datos
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/register')->with('error', 'El token de verificación ha expirado. Por favor, regístrate de nuevo para obtener un nuevo token.');
        }

        // Verificar si el usuario ya está activo (aunque ya filtramos arriba, es una doble verificación)
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
        return redirect()->to('/loginobtener')->with('success', '¡Cuenta verificada con éxito! Ahora puedes iniciar sesión.');
    }
}