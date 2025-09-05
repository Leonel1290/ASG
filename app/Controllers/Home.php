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
        // Constructor vacío o con lógica si es necesaria
    }

    public function index()
    {
        // Carga la vista de inicio
        return view('inicio');
    }

    public function inicio()
    {
        $session = session();

        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'Home::inicio() - Estado de la sesión: ' . json_encode($session->get()));
        // --- FIN LOGGING ---

        // Si el usuario no está logueado, redirige a la página de login
        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::inicio() - Usuario no logueado, redirigiendo a login.');
            return view('login');
        }

        log_message('debug', 'Home::inicio() - Usuario logueado, mostrando vista inicio.');
        // Si está logueado, muestra la vista de inicio
        return view('inicio');
    }

    // Método de Login (POST /login) - Maneja el envío del formulario de login
    public function login()
    {
        $session = session();

        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'Home::login() - Iniciando proceso de login. Datos de sesión al inicio: ' . json_encode($session->get()));
        // --- FIN LOGGING ---

        $userModel = new UserModel();
        $lecturasGasModel = new \App\Models\LecturasGasModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $useBiometric = $this->request->getPost('use_biometric');

        $user = $userModel->where('email', $email)->first();

        if ($user) {
            if (!$user['is_active']) {
                $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu email para activarla.');
                log_message('debug', 'Home::login() - Intento de login con cuenta inactiva: ' . $email);
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu email para activarla.'
                    ]);
                }
                return redirect()->back()->withInput();
            }

            if (password_verify($password, $user['password'])) {
                // Contraseña correcta, iniciar sesión
                $ultimaLectura = $lecturasGasModel
                    ->orderBy('id', 'DESC')
                    ->where(['usuario_id' => $user['id']])
                    ->asArray()
                    ->first();

                $nivel_gas = $ultimaLectura['nivel_gas'] ?? null;

                $sessionData = [
                    'id'        => $user['id'],
                    'nombre'    => $user['nombre'],
                    'email'     => $user['email'],
                    'logged_in' => true,
                    'biometric_enabled' => $useBiometric ? 1 : 0
                ];
                $session->set($sessionData);

                // --- LOGGING PARA DEBUGGING ---
                log_message('debug', 'Home::login() - Login exitoso para usuario ID: ' . $user['id'] . '. Datos de sesión establecidos: ' . json_encode($sessionData));
                // --- FIN LOGGING ---

                // Si el usuario quiere habilitar autenticación biométrica
                if ($useBiometric) {
                    // Generar credenciales para autenticación biométrica
                    $biometricToken = bin2hex(random_bytes(32));
                    $expires = Time::now()->addDays(30); // Válido por 30 días
                    
                    // Guardar token en la base de datos - CORREGIDO
                    $updateData = [
                        'biometric_token' => $biometricToken,
                        'biometric_expires' => $expires->toDateTimeString()
                    ];
                    
                    // Verificar que el usuario existe antes de actualizar
                    $affectedRows = $userModel->update($user['id'], $updateData);
                    
                    if ($affectedRows === false) {
                        log_message('error', 'Error al actualizar token biométrico para usuario ID: ' . $user['id']);
                        // Continuar con el login aunque falle la actualización biométrica
                    } else {
                        log_message('debug', 'Token biométrico actualizado para usuario ID: ' . $user['id']);
                        
                        // Devolver el token para almacenamiento local (para solicitudes AJAX)
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'success' => true,
                                'biometric_token' => $biometricToken,
                                'redirect' => '/perfil'
                            ]);
                        }
                        
                        // Para solicitudes normales, almacenar token en flashdata
                        $session->setFlashdata('biometric_token', $biometricToken);
                    }
                }

                // Redirección normal para solicitudes no AJAX
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'redirect' => '/perfil'
                    ]);
                }
                
                return redirect()->to('/perfil');
            } else {
                $errorMsg = 'Contraseña incorrecta.';
                $session->setFlashdata('error', $errorMsg);
                log_message('debug', 'Home::login() - Intento de login con contraseña incorrecta para email: ' . $email);
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $errorMsg
                    ]);
                }
                return redirect()->back()->withInput();
            }
        } else {
            $errorMsg = 'Email no encontrado.';
            $session->setFlashdata('error', $errorMsg);
            log_message('debug', 'Home::login() - Intento de login con email no encontrado: ' . $email);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg
                ]);
            }
            return redirect()->back()->withInput();
        }
    }

    // Nuevo método para autenticación biométrica
    public function biometricLogin()
    {
        $session = session();
        $userModel = new UserModel();
        
        $token = $this->request->getPost('biometric_token');
        
        if (!$token) {
            return $this->response->setJSON(['success' => false, 'message' => 'Token no proporcionado']);
        }
        
        // Buscar usuario por token biométrico
        $user = $userModel->where('biometric_token', $token)
                         ->where('biometric_expires >=', Time::now()->toDateTimeString())
                         ->first();
        
        if ($user) {
            if (!$user['is_active']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Cuenta no verificada']);
            }
            
            // Iniciar sesión
            $sessionData = [
                'id'        => $user['id'],
                'nombre'    => $user['nombre'],
                'email'     => $user['email'],
                'logged_in' => true,
                'biometric_enabled' => 1
            ];
            $session->set($sessionData);
            
            return $this->response->setJSON(['success' => true, 'redirect' => '/perfil']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Autenticación biométrica fallida. Por favor, inicie sesión con sus credenciales.']);
        }
    }

    // Método para deshabilitar autenticación biométrica
    public function disableBiometric()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autenticado']);
        }
        
        $userId = $session->get('id');
        $userModel = new UserModel();
        
        // Verificar que el usuario existe antes de intentar actualizar
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuario no encontrado']);
        }
        
        $updateData = [
            'biometric_token' => null,
            'biometric_expires' => null
        ];
        
        $affectedRows = $userModel->update($userId, $updateData);
        
        if ($affectedRows === false) {
            log_message('error', 'Error al deshabilitar autenticación biométrica para usuario ID: ' . $userId);
            return $this->response->setJSON(['success' => false, 'message' => 'Error al deshabilitar autenticación biométrica']);
        }
        
        // Actualizar estado en sesión
        $session->set('biometric_enabled', 0);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Autenticación biométrica deshabilitada']);
    }

    public function register()
    {
        // Este método simplemente muestra la vista de registro
        return view('register');
    }

    public function logout()
    {
        $session = session();
        $userId = $session->get('id');
        $session->destroy();
        log_message('debug', 'Home::logout() - Sesión destruida para usuario ID: ' . $userId);
        return redirect()->to('/');
    }

    // Rutas que parecen vistas directas o redundantes
    public function loginobtener() {
        $session = session();
        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'Home::loginobtener() - Mostrando vista de login. Estado de la sesión: ' . json_encode($session->get()));
        // --- FIN LOGGING ---
        return view('login');
    }
    
    public function configview() { return view('configuracion'); }
    public function salirdelconfig() { return view('inicio'); }
    public function volveralinicio() { return view('inicio'); }
    public function volveralperfil() { return view('perfil'); }

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
            // Configura el remitente en app/Config/Email.php o .env
            // $emailService->setFrom('againsafegas.ascii@gmail.com', 'ASG');
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
            return redirect()->to('/loginobtener');
        } else {
            $session->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->back()->withInput();
        }
    }

    // Método para enlazar MACs
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

        return redirect()->to('/perfil');
    }

    // Vistas varias
    public function dispositivos()
    {
        return view('dispositivos');
    }

    // Método para mostrar la página de perfil
    public function perfil()
    {
        $session = session();

        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'Home::perfil() - Estado de la sesión al inicio: ' . json_encode($session->get()));
        // --- FIN LOGGING ---

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::perfil() - Usuario no logueado, redirigiendo a login.');
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');

        $enlaceModel = new \App\Models\EnlaceModel();
        $lecturasGasModel = new \App\Models\LecturasGasModel();
        $userModel = new UserModel();

        $macs = $enlaceModel
                    ->select('MAC')
                    ->where('id_usuario', $usuarioId)
                    ->groupBy('MAC')
                    ->findAll();

        $lecturas = $lecturasGasModel->getLecturasPorUsuario($usuarioId);
        
        // Obtener información biométrica del usuario
        $user = $userModel->find($usuarioId);
        $biometricEnabled = !empty($user['biometric_token']) && 
                           Time::parse($user['biometric_expires'])->isAfter(Time::now());

        log_message('debug', 'Home::perfil() - Lecturas por usuario obtenidas: ' . print_r($lecturas, true));

        return view('perfil', [
            'macs' => $macs,
            'lecturas' => $lecturas,
            'biometric_enabled' => $biometricEnabled
        ]);
    }

    public function comprar()
    {
        // --- LOGGING PARA DEBUGGING ---
        $session = session();
        log_message('debug', 'Home::comprar() - Mostrando vista de comprar. Estado de la sesión: ' . json_encode($session->get()));
        // --- FIN LOGGING ---
        return view('comprar');
    }
    
    public function instalarPWA()
    {
        return view('instalar_pwa');
    }
}