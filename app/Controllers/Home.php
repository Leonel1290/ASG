<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Home extends BaseController
{
    public function __construct()
    {
        // No es necesario instanciar modelos aquí si no se usan en todos los métodos o en el constructor.
        // Los instanciamos directamente en los métodos que los necesitan.
    }

    public function index()
    {
        // Carga la vista de inicio por defecto (puede ser la página pública de bienvenida)
        return view('inicio');
    }

    // Este método ahora asume que el filtro SessionAdmin ya ha verificado la sesión.
    // Si llegamos aquí, el usuario debería estar logueado.
    public function inicio()
    {
        $session = session();
        log_message('debug', 'Home::inicio() - Acceso a página de inicio para usuario logueado. Sesión ID: ' . ($session->get('id') ?? 'null'));
        return view('inicio'); // Muestra la vista de inicio para usuarios logueados
    }

    // Método de Login (POST /login) - Maneja el envío del formulario de login
    public function login()
    {
        $session = session();
        log_message('debug', 'Home::login() - Iniciando proceso de login.');

        $userModel = new UserModel();
        $lecturasGasModel = new \App\Models\LecturasGasModel(); // Asegúrate de que el namespace sea correcto

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user) {
            if (!$user['is_active']) {
                $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu email para activarla.');
                log_message('debug', 'Home::login() - Intento de login con cuenta inactiva: ' . $email);
                return redirect()->back()->withInput();
            }

            if (password_verify($password, $user['password'])) {
                // Contraseña correcta, iniciar sesión
                $ultimaLectura = $lecturasGasModel
                    ->orderBy('id', 'DESC')
                    ->where(['usuario_id' => $user['id']])
                    ->asArray()
                    ->first();

                $nivel_gas = $ultimaLectura['nivel_gas'] ?? null; // Si no lo usas, puedes omitirlo aquí

                $sessionData = [
                    'id'        => $user['id'],
                    'nombre'    => $user['nombre'],
                    'email'     => $user['email'],
                    'logged_in' => true, // Establece que el usuario está logueado
                ];
                $session->set($sessionData);

                log_message('debug', 'Home::login() - Login exitoso para usuario ID: ' . $user['id'] . '. Datos de sesión establecidos: ' . json_encode($sessionData));

                return redirect()->to('/perfil'); // Redirige a la página de perfil después del login
            } else {
                $session->setFlashdata('error', 'Contraseña incorrecta.');
                log_message('debug', 'Home::login() - Intento de login con contraseña incorrecta para email: ' . $email);
                return redirect()->back()->withInput();
            }
        } else {
            $session->setFlashdata('error', 'Email no encontrado.');
            log_message('debug', 'Home::login() - Intento de login con email no encontrado: ' . $email);
            return redirect()->back()->withInput();
        }
    }

    // No necesitas este método aquí si registerController ya lo maneja
    // public function register()
    // {
    //     return view('register');
    // }

    public function logout()
    {
        $session = session();
        $userId = $session->get('id');
        $session->destroy();
        log_message('debug', 'Home::logout() - Sesión destruida para usuario ID: ' . $userId);
        return redirect()->to('/'); // Redirige a la página de inicio pública
    }

    // Método para mostrar el formulario de login (GET /loginobtener)
    public function loginobtener()
    {
        $session = session();
        // Si el usuario ya está logueado, redirige a la página de inicio para logueados
        if ($session->get('logged_in')) {
            return redirect()->to('/inicio');
        }
        log_message('debug', 'Home::loginobtener() - Mostrando vista de login.');
        return view('login');
    }

    // Vistas directas (mantengo las que tenías, puedes decidir si quieres que se manejen por rutas o controladores específicos)
    public function configview() { return view('configuracion'); }
    public function salirdelconfig() { return view('inicio'); }
    public function volveralinicio() { return view('inicio'); }
    public function volveralperfil() { return view('perfil'); } // Considera redirigir a /perfil, no renderizar directamente

    // Recuperación de contraseña
    public function forgotPPassword()
    {
        $session = session();
        $userModel = new UserModel();
        $emailInput = $this->request->getPost('email');
        $user = $userModel->where('email', $emailInput)->first();

        if ($user) {
            if (!$user['is_active']) {
                 $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. No se puede recuperar la contraseña.');
                 return redirect()->back()->withInput();
            }

            $token = bin2hex(random_bytes(50));
            $expires = Time::now()->addHours(1);

            $updated = $userModel->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires->toDateTimeString(),
            ]);

            $resetLink = base_url("/reset-password/$token");

            $emailService = \Config\Services::email();
            $emailService->setTo($user['email']);
            $emailService->setSubject('Recuperación de contraseña');
            $emailService->setMessage("Haz clic en este enlace para recuperar tu contraseña: " . $resetLink);

            if (!$emailService->send()) {
                $data = $emailService->printDebugger(['headers']);
                log_message('error', 'Error enviando correo de recuperación a ' . $user['email'] . ': ' . $data);
                 $session->setFlashdata('error', 'Hubo un error al enviar el correo de recuperación.');
                 return redirect()->back()->withInput();
            }

            if ($updated) {
                log_message('debug', 'Actualización de token de recuperación exitosa para usuario ID: ' . $user['id']);
            } else {
                log_message('error', 'Actualización de token de recuperación fallida para usuario ID: ' . $user['id']);
            }

            $session->setFlashdata('success', 'Se ha enviado un enlace de recuperación a tu correo.');
            return redirect()->back()->withInput();
        } else {
            $session->setFlashdata('error', 'Correo electrónico no encontrado.');
            return redirect()->back()->withInput();
        }
    }

    public function forgotpassword()
    {
        return view('forgotpassword');
    }

    public function showResetPasswordForm($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
            if (!$user['is_active']) {
                 $userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
                 session()->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. No se puede resetear la contraseña.');
                 return redirect()->to('/register');
            }

            return view('reset_password', ['token' => $token]);
        } else {
            session()->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->to('/forgotpassword');
        }
    }

    public function resetPassword()
    {
        $session = session();
        $userModel = new UserModel();

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $userModel->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
            if (!$user['is_active']) {
                 $userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
                 $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. No se puede resetear la contraseña.');
                 return redirect()->to('/register');
            }

            $userModel->update($user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_expires' => null,
            ]);

            $session->setFlashdata('success', 'Tu contraseña ha sido actualizada.');
            return redirect()->to('/loginobtener'); // Redirige al formulario de login
        } else {
            $session->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->back()->withInput();
        }
    }

    // --- MÉTODOS DUPLICADOS O REDUNDANTES ELIMINADOS ---
    // public function storeMac() { ... } // Ahora en EnlaceController::store
    // public function perfil() { ... } // Ahora en PerfilController::index

    public function dispositivos()
    {
        // Esta vista ahora puede estar protegida por el filtro en Routes.php
        return view('dispositivos');
    }

    public function comprar()
    {
        $session = session(); // Mantener para logging o si necesitas info de sesión
        log_message('debug', 'Home::comprar() - Mostrando vista de comprar. Estado de la sesión: ' . json_encode($session->get()));
        return view('comprar');
    }

    // Si tienes un método verLecturas en Home, descomenta y úsalo, si no, elimínalo
    // public function verLecturas($mac) { ... }
}