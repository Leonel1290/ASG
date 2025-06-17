<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class registerController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url', 'text', 'email']);
    }

    public function index()
    {
        return view('register');
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nombre'  => [
                'rules' => 'required|min_length[1]|max_length[50]',
                'errors' => [
                    'required' => 'El campo nombre es obligatorio.',
                    'min_length' => 'El campo nombre debe tener al menos 1 carácter.',
                    'max_length' => 'El campo nombre no puede exceder los 50 caracteres.'
                ]
            ],
            'apellido' => [
                'rules' => 'required|min_length[1]|max_length[50]',
                'errors' => [
                    'required' => 'El campo apellido es obligatorio.',
                    'min_length' => 'El campo apellido debe tener al menos 1 carácter.',
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
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[A-Z]).*$/]|regex_match[/^(?=.*[a-z]).*$/]|regex_match[/^(?=.*\d).*$/]|regex_match[/^(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).*$/]',
                'errors' => [
                    'required' => 'El campo contraseña es obligatorio.',
                    'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
                    'regex_match' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

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
            $message = "Hola {$nombre},\n\nGracias por registrarte en ASG.\n\nPor favor, haz clic en el siguiente enlace para verificar tu cuenta:\n{$verificationLink}\n\nEste enlace expirará en 24 horas.\n\nSi no te registraste en ASG, puedes ignorar este correo.\n\nAtentamente,\nEl equipo de ASG";
            $emailService->setMessage($message);

            if ($emailService->send()) {
                log_message('debug', 'Correo de verificación de registro enviado a: ' . $email);
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se ha enviado un correo de verificación a tu email. Por favor, revisa tu bandeja de entrada para activar tu cuenta.');
            } else {
                log_message('error', 'Error al enviar correo de verificación de registro a ' . $email . ': ' . $emailService->printDebugger(['headers', 'subject', 'body']));
                return redirect()->back()->withInput()->with('error', 'Hubo un error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
            }

        } else {
            return redirect()->back()->withInput()->with('error', 'Hubo un error al registrar el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    public function checkEmail()
    {
        return view('register/verification_message');
    }

    public function verifyEmailToken($token = null)
    {
        if ($token === null) {
            return redirect()->to('/register')->with('error', 'Token de verificación no proporcionado.');
        }

        $user = $this->userModel->getUserByToken($token);

        if (!$user) {
            return redirect()->to('/register')->with('error', 'Token de verificación inválido.');
        }

        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/register')->with('error', 'El token de verificación ha expirado. Por favor, regístrate de nuevo para obtener un nuevo token.');
        }

        if ($user['is_active']) {
             $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
             return redirect()->to('/loginobtener')->with('info', 'Tu cuenta ya ha sido verificada. Por favor, inicia sesión.');
        }

        $updateData = [
            'is_active' => 1,
            'reset_token' => null,
            'reset_expires' => null,
        ];
        $this->userModel->update($user['id'], $updateData);

        return redirect()->to('/loginobtener')->with('success', '¡Tu cuenta ha sido verificada exitosamente! Ahora puedes iniciar sesión.');
    }
}