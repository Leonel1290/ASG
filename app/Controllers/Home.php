<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ComprasModel;
use App\Models\LecturasGasModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;

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

                $nivel_gas = $ultimaLectura['nivel_gas'] ?? null;

                $sessionData = [
                    'id'        => $user['id'],
                    'nombre'    => $user['nombre'],
                    'email'     => $user['email'],
                    'logged_in' => true,
                ];
                $session->set($sessionData);

                // --- LOGGING PARA DEBUGGING ---
                log_message('debug', 'Home::login() - Login exitoso para usuario ID: ' . $user['id'] . '. Datos de sesión establecidos: ' . json_encode($sessionData));
                // --- FIN LOGGING ---

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

    // Rutas que parecen vistas directas o redundantes (considera limpiarlas)
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

    // Recuperación de contraseña (mantengo tu lógica existente, usa UserModel)
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

    // Método para enlazar MACs (parece que lo tienes duplicado con EnlaceController::store)
    // Considera eliminar este si usas EnlaceController::store
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

    // Rutas que parecen vistas directas o redundantes (considera limpiarlas)
    // public function inicioresetpass() { return view('reset_password'); }
    // public function obtenerperfil() { return view('perfilobtener'); }

    // Vistas varias (mantengo las que tenías)
    public function dispositivos()
    {
        return view('dispositivos');
    }

    // Método para mostrar la página de perfil (parece que PerfilController::index es la principal ahora)
    // Considera eliminar este si usas PerfilController::index
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

        $macs = $enlaceModel
                    ->select('MAC')
                    ->where('id_usuario', $usuarioId)
                    ->groupBy('MAC')
                    ->findAll();

        $lecturas = $lecturasGasModel->getLecturasPorUsuario($usuarioId);

        log_message('debug', 'Home::perfil() - Lecturas por usuario obtenidas: ' . print_r($lecturas, true));

        return view('perfil', [
            'macs' => $macs,
            'lecturas' => $lecturas
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

    // Parece que tenías un método verLecturas, asegúrate de que exista si la ruta /mac/(:segment) lo usa
    // public function verLecturas($mac) { ... }


   public function guardar_compra()
    {
        // Verifica que la solicitud sea POST y que venga del frontend (AJAX).
        if (!$this->request->isAJAX() || $this->request->getMethod() !== 'post') {
            log_message('error', 'Petición no permitida en guardar_compra. Método: ' . $this->request->getMethod() . ', esAJAX: ' . ($this->request->isAJAX() ? 'true' : 'false'));
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Método no permitido.']);
        }

        try {
            // Obtén los datos enviados desde el JavaScript.
            $data = $this->request->getJSON(true);

            // Revisa si el usuario está logueado para asociar la compra a su ID.
            $session = session();
            $usuarioId = $session->get('id');

            if (!$usuarioId) {
                // Si el usuario no está logueado, responde con un error.
                log_message('error', 'Intento de guardar compra sin usuario logueado.');
                return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no logueado.']);
            }
            
            // Carga el modelo de compras.
            $comprasModel = new ComprasModel();

            // Prepara los datos para la inserción.
            $datosCompra = [
                'usuario_id' => $usuarioId,
                'order_id' => $data['orderID'],
                'payer_id' => $data['payerID'],
                'payment_id' => $data['paymentID'],
                'status' => $data['status'],
                'monto' => 100.00, // TODO: IMPORTANTE: Este valor debe ser el monto real del pago.
                'fecha_compra' => date('Y-m-d H:i:s'),
            ];

            if ($comprasModel->insert($datosCompra)) {
                // Si la inserción fue exitosa, respondemos al frontend.
                log_message('info', 'Compra guardada exitosamente. Order ID: ' . $data['orderID']);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Compra guardada exitosamente.']);
            } else {
                // Si hubo un error en la inserción.
                log_message('error', 'Error al guardar la compra en la base de datos. Order ID: ' . $data['orderID']);
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
            }
        } catch (Exception $e) {
            // Captura cualquier excepción que pueda ocurrir y la registra.
            log_message('error', 'Excepción en guardar_compra: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error interno del servidor.']);
        }
    }
        public function simulacion()
    {
        return view('simulacion');
    }
}