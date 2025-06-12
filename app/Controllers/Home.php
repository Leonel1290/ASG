<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use App\Models\CompraDispositivoModel;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel; // Necesario para el método perfil()
use CodeIgniter\Controller; // BaseController ya extiende de Controller
use CodeIgniter\I18n\Time; // Para manejar fechas y horas

class Home extends BaseController
{
    // Propiedades para almacenar instancias de los modelos
    protected $userModel;
    protected $lecturasGasModel;
    protected $compraDispositivoModel;
    protected $dispositivoModel;
    protected $enlaceModel; // Para manejar enlaces de dispositivos a usuarios

    /**
     * Constructor del controlador Home.
     * Inicializa las instancias de los modelos que se utilizarán.
     */
    public function __construct()
    {
        // Llama al constructor de la clase padre (BaseController)
        // para asegurar que las configuraciones básicas (como la sesión) se inicialicen.
        parent::__construct();

        // Instanciar todos los modelos que se utilizarán en este controlador
        $this->userModel = new UserModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel(); // Instancia EnlaceModel
    }

    /**
     * Muestra la vista de inicio principal de la aplicación.
     * Endpoint: GET /
     */
    public function index()
    {
        return view('inicio'); // Carga la vista de inicio
    }

    /**
     * Muestra la vista de inicio para usuarios logueados, o redirige a login si no lo están.
     * Endpoint: GET /inicio
     */
    public function inicio()
    {
        $session = session();

        log_message('debug', 'Home::inicio() - Estado de la sesión: ' . json_encode($session->get()));

        // Si el usuario no está logueado, redirige a la página de login
        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::inicio() - Usuario no logueado, redirigiendo a login.');
            return view('login'); // Muestra la vista de login directamente
        }

        log_message('debug', 'Home::inicio() - Usuario logueado, mostrando vista inicio.');
        return view('inicio'); // Si está logueado, muestra la vista de inicio
    }

    /**
     * Muestra el formulario de login.
     * Endpoint: GET /loginobtener
     */
    public function loginobtener()
    {
        return view('login'); // Muestra la vista del formulario de login
    }

    /**
     * Procesa el formulario de login.
     * Endpoint: POST /login
     */
    public function login()
    {
        $session = session();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0) {
                return redirect()->back()->with('error', 'Tu cuenta no ha sido verificada. Por favor, revisa tu correo electrónico para el enlace de verificación.');
            }

            // Autenticación exitosa, establecer datos de sesión
            $ses_data = [
                'id'        => $user['id'],
                'nombre'    => $user['nombre'],
                'apellido'  => $user['apellido'],
                'email'     => $user['email'],
                'logged_in' => TRUE,
                'is_admin'  => $user['is_admin']
            ];
            $session->set($ses_data);

            // Redirección después del login: prioriza la URL de retorno si existe
            $redirectUrl = session()->getFlashdata('redirect_after_login');
            if ($redirectUrl) {
                session()->removeFlashdata('redirect_after_login'); // Limpiar flashdata
                return redirect()->to($redirectUrl)->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '! Por favor, completa tu acción.');
            } else {
                return redirect()->to('/inicio')->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '!');
            }
        } else {
            return redirect()->back()->with('error', 'Email o contraseña incorrectos.');
        }
    }

    /**
     * Cierra la sesión del usuario.
     * Endpoint: POST /logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/loginobtener')->with('info', 'Has cerrado sesión correctamente.');
    }

    /**
     * Muestra la vista de perfil del usuario logueado.
     * Endpoint: GET /perfil
     */
    public function perfil()
    {
        $session = session();
        log_message('debug', 'Home::perfil() - Estado de la sesión al inicio: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::perfil() - Usuario no logueado, redirigiendo a login.');
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');

        // Obtener las MACs enlazadas a este usuario
        $macs = $this->enlaceModel
                    ->select('MAC')
                    ->where('id_usuario', $usuarioId)
                    ->groupBy('MAC')
                    ->findAll();

        // Obtener las lecturas de gas asociadas a las MACs del usuario
        // Nota: Asegúrate de que `getLecturasPorUsuario` en LecturasGasModel sea eficiente.
        $lecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        log_message('debug', 'Home::perfil() - Lecturas por usuario obtenidas: ' . print_r($lecturas, true));

        return view('perfil', [
            'macs' => $macs,
            'lecturas' => $lecturas
        ]);
    }

    /**
     * Muestra la vista de compra de dispositivos.
     * Si el usuario no está logueado, lo redirige a la página de registro/login.
     * Endpoint: GET /comprar
     */
    public function comprar()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            // Si el usuario no está logueado, guarda la URL de retorno y redirige al registro
            $session->setFlashdata('redirect_after_login', '/comprar');
            log_message('debug', 'Home::comprar() - Usuario no logueado, redirigiendo a registro.');
            return redirect()->to('/register')->with('info', 'Para comprar un dispositivo, por favor, regístrate o inicia sesión.');
        }

        log_message('debug', 'Home::comprar() - Usuario logueado, mostrando vista de compra.');
        return view('comprar');
    }

    /**
     * Este método registra una compra automáticamente después de una transacción exitosa (ej. PayPal webhook).
     * ASUME que el usuario ya está logueado cuando se dispara esta lógica.
     *
     * @param string $macComprada La MAC del dispositivo comprado
     * @param string|null $transaccionId Opcional: ID de la transacción de PayPal
     * @return \CodeIgniter\HTTP\Response
     */
    public function registrarCompraAutomatica($macComprada, $transaccionId = null)
    {
        $session = session();

        // 1. Asegurarse de que el usuario esté logueado
        if (!$session->get('logged_in')) {
            log_message('error', 'Intento de registrar compra automática sin usuario logueado.');
            return $this->failUnauthorized('Debe iniciar sesión para registrar una compra.');
        }

        $idUsuarioComprador = $session->get('id'); // Obtener el ID del usuario logueado

        // --- VALIDACIÓN Y VERIFICACIÓN ---
        // 1. Validar formato de MAC
        if (!preg_match('/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i', $macComprada)) {
            log_message('error', 'Formato de MAC inválido al registrar compra automática: ' . $macComprada);
            return $this->fail('Formato de MAC inválido.', 400);
        }
        $macComprada = strtoupper(str_replace(['-', ' ', ':'], '', $macComprada)); // Normalizar y limpiar MAC

        // 2. Validar que la MAC existe en tu tabla `dispositivos`
        $dispositivoExiste = $this->dispositivoModel->where('MAC', $macComprada)->first();
        if (!$dispositivoExiste) {
            log_message('error', 'Intento de registrar compra para MAC inexistente en `dispositivos`: ' . $macComprada);
            return $this->fail('MAC del dispositivo no registrada en el sistema.', 404);
        }

        // 3. Verificar si esta compra ya fue registrada para ESTE USUARIO Y MAC
        $compraExistente = $this->compraDispositivoModel
                                 ->where('id_usuario_comprador', $idUsuarioComprador)
                                 ->where('MAC_dispositivo', $macComprada)
                                 ->first();

        if ($compraExistente) {
            log_message('info', 'Compra para usuario ' . $idUsuarioComprador . ' y MAC ' . $macComprada . ' ya registrada.');
            return $this->respond(['status' => 'info', 'message' => 'Esta MAC ya ha sido comprada por su cuenta.'], 200);
        }
        // --- FIN VALIDACIÓN Y VERIFICACIÓN ---

        $dataCompra = [
            'id_usuario_comprador'  => $idUsuarioComprador,
            'MAC_dispositivo'       => $macComprada,
            'transaccion_paypal_id' => $transaccionId // Puede ser NULL
        ];

        if ($this->compraDispositivoModel->insert($dataCompra)) {
            log_message('info', 'Compra de dispositivo ' . $macComprada . ' registrada automáticamente para usuario ' . $idUsuarioComprador . '.');
            return $this->respondCreated(['status' => 'success', 'message' => 'Compra registrada automáticamente.']);
        } else {
            $errors = $this->compraDispositivoModel->errors();
            log_message('error', 'Error al registrar compra automática: ' . json_encode($errors));
            return $this->failServerError('Error al registrar la compra automáticamente: ' . implode(', ', $errors));
        }
    }

    // --- Métodos relacionados con recuperación de contraseña (mantener si son necesarios) ---
    // (Asegúrate de que estos métodos existan si tienes rutas para ellos en Routes.php)

    public function forgotpassword()
    {
        // Lógica para mostrar el formulario de 'olvidé mi contraseña'
        return view('forgot_password'); // Asegúrate de tener esta vista
    }

    public function forgotPPassword()
    {
        // Lógica para procesar el envío del formulario de 'olvidé mi contraseña'
        // (Enviar email con token, etc.)
        return redirect()->back()->with('info', 'Instrucciones enviadas a tu correo.');
    }

    public function showResetPasswordForm($token)
    {
        // Lógica para mostrar el formulario de restablecimiento de contraseña
        // (Validar token y cargar vista)
        return view('reset_password_form', ['token' => $token]); // Asegúrate de tener esta vista
    }

    public function resetPassword()
    {
        // Lógica para procesar el restablecimiento de contraseña
        // (Validar nueva contraseña, actualizar en DB)
        return redirect()->to('/loginobtener')->with('success', 'Contraseña restablecida correctamente.');
    }
}
