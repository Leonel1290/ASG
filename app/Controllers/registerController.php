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

        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(2)->toDateTimeString();

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active' => 0,
            'reset_token' => $token,
            'reset_expires' => $expires,
        ];

        if ($this->userModel->insert($data)) {
            $user_id = $this->userModel->getInsertID();

            $emailService = \Config\Services::email();
            $emailService->setFrom('no-reply@tudominio.com', 'Sistema ASG');
            $emailService->setTo($data['email']);
            $emailService->setSubject('Verifica tu Cuenta en ASG');
            $verificationLink = base_url('/register/verify-email/' . $token);
            $message = "Hola " . esc($data['nombre']) . ",\n\n"
                     . "Gracias por registrarte en ASG. Por favor, haz clic en el siguiente enlace para activar tu cuenta:\n"
                     . $verificationLink . "\n\n"
                     . "Este enlace expirará en 2 horas.\n\n"
                     . "Si no te registraste en ASG, puedes ignorar este correo.\n\n"
                     . "Saludos,\nEl equipo de ASG";
            $emailService->setMessage($message);

            if ($emailService->send()) {
                return redirect()->to('/register/check-email')->with('success', '¡Registro exitoso! Se ha enviado un enlace de verificación a tu email. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).');
            } else {
                $error = $emailService->printDebugger(['headers']);
                log_message('error', 'Error al enviar email de verificación: ' . $error);
                return redirect()->back()->with('error', 'Error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el usuario. Inténtalo de nuevo.');
        }
    }

    public function checkEmail()
    {
        return view('verification_message');
    }

    public function verifyEmailToken($token)
    {
        if (empty($token)) {
            return redirect()->to('/register')->with('error', 'Token de verificación no proporcionado.');
        }

        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/register')->with('error', 'Token de verificación inválido o ya utilizado.');
        }

        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/register')->with('error', 'El token de verificación ha expirado. Por favor, regístrate de nuevo para obtener un nuevo token.');
        }

        if ($user['is_active']) {
             $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
             return redirect()->to('/login')->with('info', 'Tu cuenta ya ha sido verificada. Por favor, inicia sesión.');
        }

        $updateData = [
            'is_active' => 1,
            'reset_token' => null,
            'reset_expires' => null,
        ];
        $this->userModel->update($user['id'], $updateData);

        return redirect()->to('/login')->with('success', '¡Tu cuenta ha sido verificada exitosamente! Ahora puedes iniciar sesión.');
    }


    /**
     * Muestra el formulario de registro específico para el flujo de PayPal.
     */
    public function registerPaypal()
    {
        $session = session();
        if ($session->get('logged_in')) {
            return redirect()->to('/comprar'); // Si ya está logueado, va directo a la compra
        }
        return view('register_paypal');
    }

    /**
     * Procesa el registro específico para el flujo de PayPal.
     */
    public function storePaypal()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nombre'  => ['rules' => 'required|min_length[3]|max_length[50]', 'errors' => ['required' => 'El campo nombre es obligatorio.']],
            'apellido' => ['rules' => 'required|min_length[3]|max_length[50]', 'errors' => ['required' => 'El campo apellido es obligatorio.']],
            'email' => [
                'rules' => 'required|valid_email|is_unique[usuarios.email]',
                'errors' => [
                    'required' => 'El campo email es obligatorio.',
                    'valid_email' => 'Por favor, introduce una dirección de correo válida.',
                    'is_unique' => 'Este correo electrónico ya está registrado.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'El campo contraseña es obligatorio.',
                    'min_length' => 'La contraseña debe tener al menos 8 caracteres.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Por favor, confirma tu contraseña.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ]);

        if (!$this->validate($validation->getRules(), $validation->getErrors())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(2)->toDateTimeString();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'apellido'      => $this->request->getPost('apellido'),
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active'     => 0, // Por defecto, inactivo hasta verificar email
            'reset_token'   => $token,
            'reset_expires' => $expires,
        ];

        if ($this->userModel->insert($data)) {
            $emailService = \Config\Services::email();
            $emailService->setFrom('tucorreo@ejemplo.com', 'ASG Support'); // Cambia esto
            $emailService->setTo($data['email']);
            $emailService->setSubject('Verifica tu dirección de correo electrónico');

            $verificationLink = site_url('register/verify-email/' . $token); // La verificación es la misma
            $message = "Hola " . esc($data['nombre']) . ",\\n\\n"
                     . "Gracias por registrarte en ASG. Por favor, haz clic en el siguiente enlace para activar tu cuenta:\\n"
                     . $verificationLink . "\\n\\n"
                     . "Este enlace expirará en 2 horas.\\n\\n"
                     . "Si no te registraste en ASG, puedes ignorar este correo.\\n\\n"
                     . "Saludos,\\nEl equipo de ASG";
            $emailService->setMessage($message);

            if ($emailService->send()) {
                // Después de un registro exitoso (y la verificación es enviada),
                // redirigir al login específico de PayPal para que inicie sesión.
                return redirect()->to('/login/paypal')->with('success', '¡Registro exitoso! Se ha enviado un enlace de verificación a tu email. Por favor, revisa tu bandeja de entrada para activar tu cuenta y luego inicia sesión para continuar con tu compra.');
            } else {
                $error = $emailService->printDebugger(['headers']);
                log_message('error', 'Error al enviar email de verificación (PayPal flow): ' . $error);
                return redirect()->back()->with('error', 'Error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el usuario. Inténtalo de nuevo.');
        }
    }
}

