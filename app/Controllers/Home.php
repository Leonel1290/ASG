<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Home extends BaseController
{
    // Puedes instanciar modelos aquí si los usas en múltiples métodos
    // protected $userModel;
    // protected $lecturasGasModel;

    public function __construct()
    {
        // Llama al constructor de la clase padre si es necesario
        // parent::__construct();

        // Instanciar modelos si se usan en el constructor o en varios métodos
        // $this->userModel = new UserModel();
        // $this->lecturasGasModel = new LecturasGasModel();
    }

    public function index()
    {
        // Carga la vista de inicio pública (tu página de aterrizaje)
        return view('inicio');
    }

    // --- MÉTODOS DE LA APLICACIÓN PRINCIPAL (PROTEGIDOS POR FILTRO) ---

    public function inicio()
    {
        // El filtro SessionAdmin (modificado para verificar 'logged_in')
        // ya ha asegurado que el usuario está logueado antes de llegar aquí.
        // Por lo tanto, simplemente muestra la vista de inicio para usuarios logueados.
        log_message('debug', 'Home::inicio() - Mostrando vista "inicio" para usuario logueado (acceso permitido por filtro).');
        return view('inicio');
    }

    // Método de Login (POST /login) - Maneja el envío del formulario de login
    public function login()
    {
        $session = session();

        log_message('debug', 'Home::login() - Iniciando proceso de login. Datos de sesión al inicio: ' . json_encode($session->get()));

        $userModel = new UserModel();

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
                // Consulta de lecturas eliminada: no es necesaria para iniciar la sesión y causaba error si la columna no existe.

                $sessionData = [
                    'id'        => $user['id'],
                    'nombre'    => $user['nombre'],
                    'email'     => $user['email'],
                    'logged_in' => true, // <-- ¡Esta es la variable clave que verifica tu filtro!
                    // Si tienes un tipo de usuario (admin, normal, etc.), también puedes guardarlo aquí:
                    // 'type' => $user['tipo_de_usuario'] ?? 'normal',
                ];
                $session->set($sessionData);

                log_message('debug', 'Home::login() - Login exitoso para usuario ID: ' . $user['id'] . '. Datos de sesión establecidos: ' . json_encode($sessionData));

                // ¡Esta es la redirección crucial a la página de perfil!
                return redirect()->to('/perfil');
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

    public function logout()
    {
        $session = session();
        $userId = $session->get('id');
        $session->destroy(); // Destruye toda la sesión
        log_message('debug', 'Home::logout() - Sesión destruida para usuario ID: ' . $userId);
        return redirect()->to('/'); // Redirige a la página de inicio pública
    }

    // --- RUTAS DE FORMULARIOS Y VISTAS BÁSICAS ---

    public function loginobtener()
    {
        // Muestra el formulario de login (GET)
        $session = session();
        log_message('debug', 'Home::loginobtener() - Mostrando vista de login. Estado de la sesión: ' . json_encode($session->get()));
        return view('login');
    }

    public function register()
    {
        // Muestra la vista de registro
        return view('register');
    }

    public function comprar()
    {
        // Muestra la vista de "comprar" (asumimos que es pública o gestionada por otro filtro)
        $session = session(); // Solo para logging, si es necesario
        log_message('debug', 'Home::comprar() - Mostrando vista de comprar. Estado de la sesión: ' . json_encode($session->get()));
        return view('comprar');
    }

    public function dispositivos()
    {
        // Muestra la vista de "dispositivos" (protegida por SessionAdmin en tus rutas)
        return view('dispositivos');
    }

    // --- MÉTODOS DE RECUPERACIÓN DE CONTRASEÑA ---

    public function forgotpassword()
    {
        // Muestra el formulario para "olvidé mi contraseña"
        return view('forgotpassword');
    }

    public function forgotPPassword()
    {
        // Procesa el formulario de "olvidé mi contraseña" (envío de email)
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
            // $emailService->setFrom('againsafegas.ascii@gmail.com', 'ASG'); // Descomentar y configurar si no está en Config/Email.php
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

    public function showResetPasswordForm($token)
    {
        // Muestra el formulario para resetear la contraseña con el token
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
        // Procesa el reseteo de la contraseña
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
            return redirect()->to('/loginobtener'); // Redirige al login para que el usuario inicie sesión con la nueva contraseña
        } else {
            $session->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->back()->withInput();
        }
    }

    // --- MÉTODOS OBSOLETOS O DUPLICADOS (CONSIDERAR ELIMINAR/MOVER) ---

    // Este método parece duplicar la funcionalidad de EnlaceController::store.
    // Si EnlaceController::store es el controlador principal para esto, considera eliminar este.
    public function storeMac()
    {
        $session = session();
        $mac = $this->request->getPost('mac');

        if (!$session->get('logged_in')) {
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');
        $enlaceModel = new \App\Models\EnlaceModel();

        $existe = $enlaceModel
                    ->where('id_usuario', $usuarioId)
                    ->where('MAC', $mac)
                    ->first();

        if ($existe) {
            $session->setFlashdata('error', 'Esta MAC ya está enlazada.');
        } else {
            $enlaceModel->insert([
                'id_usuario' => $usuarioId,
                'MAC' => $mac
            ]);
            $session->setFlashdata('success', 'MAC enlazada correctamente.');
        }

        return redirect()->to('/perfil'); // Redirige al perfil después de enlazar
    }

    // Estos métodos simplemente retornan vistas sin lógica significativa
    // y pueden ser redundantes si ya tienes rutas y controladores más específicos.
    // public function configview() { return view('configuracion'); }
    // public function salirdelconfig() { return view('inicio'); }
    // public function volveralinicio() { return view('inicio'); }
    // public function volveralperfil() { return view('perfil'); }

    // Este método Home::perfil() debería ser gestionado por PerfilController::index
    // Si ya tienes un PerfilController, considera eliminar este método de Home.
    // public function perfil() { /* ... tu lógica anterior para perfil ... */ }
}