<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens
use Config\Email; // <--- AGREGADO: Importar la clase Email personalizada

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

            // --- INICIO: Lógica de Envío de Email con SendGrid (USANDO CLASE PERSONALIZADA Email.php) ---
            
            // 1. Crear el Mensaje y Datos para el Email
            $verificationLink = base_url('/register/verify-email/' . $token); // Usa tu ruta de Routes.php

            $message = "<h2>¡Bienvenido a ASG, " . esc($data['nombre']) . "!</h2>"
                . "<p>Gracias por registrarte. Por favor, haz clic en el siguiente enlace para activar tu cuenta:</p>"
                . "<p><a href=\"{$verificationLink}\">Activar mi cuenta ahora</a></p>"
                . "<p>Este enlace expirará en 2 horas.</p>"
                . "<br>"
                . "<p>Si no te registraste en ASG, puedes ignorar este correo.</p>"
                . "<p>Saludos,<br>El equipo de ASG</p>";

            $emailData = [
                'email' => $data['email'],
                'asunto' => 'Verifica tu Cuenta en ASG',
                'mensaje' => $message
            ];

            // 2. Enviar el correo usando la clase Email personalizada
            $emailService = new Email();
            $resultadoEnvio = $emailService->enviarEmail($emailData);

            if ($resultadoEnvio['success']) {
                log_message('info', 'Correo de verificación enviado a: ' . $data['email']);
                // Éxito: Redirige a la página de "revisa tu email"
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se ha enviado un enlace de verificación a tu email. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).');
            } else {
                // Fallo: Loggea el error y muestra un mensaje al usuario con el detalle del error
                $errorMensaje = $resultadoEnvio['message'];
                log_message('error', 'Error al enviar correo de verificación (SendGrid): ' . $errorMensaje);
                
                // Mensaje amigable para el usuario final (usando el detalle del error para el diagnóstico)
                $displayMessage = 'Error al enviar el correo de verificación. Razón: ' . $errorMensaje;

                // Puedes suavizar el mensaje si detectas errores de configuración internos:
                if (strpos($errorMensaje, 'SENDGRID_API_KEY no está definida') !== false) {
                    $displayMessage = 'Error interno en el servicio de correo. Contacta al soporte.';
                }
                
                return redirect()->back()->withInput()->with('error', $displayMessage);
            }
            // --- FIN: Lógica de Envío de Email ---
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el usuario. Inténtalo de nuevo.');
        }
    }
    
    // ... (Mantén los métodos checkEmail() y verifyEmailToken($token) sin cambios) ...
}