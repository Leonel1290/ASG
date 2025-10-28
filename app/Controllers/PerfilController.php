<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;
use App\Models\UserModel;
use App\Models\DispositivoModel;
use CodeIgniter\I18n\Time;
use Config\Email;

class PerfilController extends BaseController
{
    protected $userModel;
    protected $enlaceModel;
    protected $lecturasGasModel;
    protected $dispositivoModel;


    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->enlaceModel = new EnlaceModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel();

        helper(['form', 'url', 'text', 'email']);
    }
public function enviarVerificacion()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para verificar tu perfil.');
        }

        $user = $this->userModel->find($usuarioId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Usuario no encontrado.');
        }

        // 1. Generar Token y actualizar en la DB
        $verificationToken = bin2hex(random_bytes(32));
        $expirationTime = Time::now()->addMinutes(60)->toDateTimeString(); // 1 hora de validez

        // Asumiendo que tu modelo tiene una columna 'reset_token' y 'reset_expires'
        $this->userModel->update($usuarioId, [
            'reset_token' => $verificationToken,
            'reset_expires' => $expirationTime,
        ]);

        // 2. Preparar el correo
        $verificationLink = base_url('perfil/confirmar-acceso/' . $verificationToken);
        // NOTA: Asume que tienes una vista 'emails/email_verificacion_perfil.php'
        $mensaje = view('emails/email_verificacion_perfil', ['link' => $verificationLink, 'nombre' => $user['nombre']]);

        $datosEmail = [
            'email' => $user['email'],
            'asunto' => 'Verificación de Acceso a Configuración de Perfil',
            'mensaje' => $mensaje,
        ];
        
        // 🛑 LÍNEA DE PRUEBA CRÍTICA (TEMPORAL): BORRAR INMEDIATAMENTE DESPUÉS DE LA PRUEBA 🛑
        // Esto registrará la clave real que Render está usando.
        log_message('error', 'API_KEY_LEIDA_RENDER: ' . getenv('SENDGRID_API_KEY'));
        // 🛑 FIN DE LÍNEA DE PRUEBA 🛑

        // 3. Enviar el correo usando el servicio SendGrid
        $emailService = new \Config\Email();
        $resultadoEnvio = $emailService->enviarEmail($datosEmail);

        if ($resultadoEnvio['success']) {
            log_message('info', 'Correo de verificación de perfil enviado a: ' . $user['email']);
            return redirect()->back()->with('success', 'Se ha enviado un enlace de verificación a tu email para acceder a la configuración. Revisa tu bandeja de entrada.');
        } else {
            // Fallo: Loggea el error y muestra un mensaje al usuario con el detalle del error
            $errorMensaje = $resultadoEnvio['message'];
            log_message('error', 'Error al enviar correo de verificación de perfil (SendGrid): ' . $errorMensaje);

            // Suavizar el mensaje para el usuario final (si es un 403, es un problema de clave)
            $displayMessage = 'Error al enviar el correo de verificación. Razón: ' . $errorMensaje;
            if (strpos($errorMensaje, 'Código: 403') !== false) {
                 $displayMessage = 'Error crítico: Problema de autorización de la API de SendGrid. Por favor, verifica la clave en Render y sus permisos en SendGrid.';
            }

            return redirect()->back()->with('error', $displayMessage);
        }
    }
    public function index()
    {
        $session = session();
        $usuarioId = $session->get('id');

        log_message('debug', 'PerfilController::index() - Estado de la sesión al inicio: ' . json_encode($session->get()));
        log_message('debug', 'PerfilController::index() - Usuario ID de la sesión: ' . ($usuarioId ?? 'null'));

        if (!$usuarioId) {
            log_message('debug', 'PerfilController::index() - Usuario ID no encontrado en sesión, redirigiendo a login.');
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $enlaces = $this->enlaceModel
                        ->select('MAC')
                        ->where('id_usuario', $usuarioId)
                        ->findAll();

        $macs = array_column($enlaces, 'MAC');

        $dispositivosEnlazados = [];
        if (!empty($macs)) {
            $dispositivosEnlazados = $this->dispositivoModel
                                        ->whereIn('MAC', $macs)
                                        ->findAll();
        }

        $allLecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        $lecturasPorMac = [];
        if (!empty($allLecturas)) {
             foreach ($allLecturas as $lectura) {
                if (isset($lectura['MAC']) && $lectura['MAC']) {
                    $currentMac = $lectura['MAC'];
                    if (!isset($lecturasPorMac[$currentMac])) {
                        $lecturasPorMac[$currentMac] = [];
                    }
                    $lecturasPorMac[$currentMac][] = $lectura;
                }
            }
        }

        log_message('debug', 'PerfilController::index() - Dispositivos enlazados obtenidos: ' . json_encode($dispositivosEnlazados));
        log_message('debug', 'PerfilController::index() - Lecturas por MAC procesadas: ' . json_encode($lecturasPorMac));


        return view('perfil', [
            'dispositivosEnlazados' => $dispositivosEnlazados,
            'lecturasPorMac' => $lecturasPorMac
        ]);
    }

    public function configuracion()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if (!$loggedInUserId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $userData = $this->userModel->find($loggedInUserId);

         if (!$userData) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Usuario no encontrado. Por favor, inicia sesión de nuevo.');
        }

        $data['userEmail'] = $userData['email'] ?? 'No disponible';

        // Muestra la vista de verificación de email antes de la configuración
        return view('perfil/verificar_email', $data);
    }
    
    // =========================================================================
    // === NUEVOS MÉTODOS PARA VERIFICACIÓN DE PERFIL (SendGrid) ===
    // =========================================================================

    // Método que maneja el envío del correo de verificación (POST /perfil/enviar-verificacion)
    public function enviarVerificacion()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        $user = $this->userModel->find($usuarioId);

        if (!$user) {
             return redirect()->back()->with('error', 'Usuario no encontrado.');
        }

        // 1. Generar nuevo token y expiración
        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(2)->toDateTimeString();

        // 2. Guardar el nuevo token en la base de datos para verificación de perfil
        $this->userModel->update($usuarioId, [
            'reset_token' => $token, // Reutilizamos el campo de token
            'reset_expires' => $expires,
        ]);


        // --- Lógica de Envío de Email con SendGrid (CI4) ---
        $emailService = \Config\Services::email();

        // **NOTA: Dependemos de que Email.php esté usando getenv()**
        $emailService->setFrom(getenv('SENDGRID_FROM_EMAIL'), getenv('SENDGRID_FROM_NAME'));

        // Configurar destinatario
        $emailService->setTo($user['email']);
        $emailService->setSubject('Verificación de Seguridad de Perfil ASG');

        // Crear el enlace de verificación (Usa la nueva ruta: /perfil/confirmar-acceso/$token)
        $verificationLink = base_url('/perfil/confirmar-acceso/' . $token); 
        
        // Crear el mensaje HTML
        $message = "<h2>Verificación de Seguridad de Perfil</h2>"
            . "<p>Alguien ha solicitado un acceso a la configuración de tu perfil.</p>"
            . "<p>Para continuar y acceder a la sección de configuración, haz clic en el siguiente enlace:</p>"
            . "<p><a href=\"{$verificationLink}\" style=\"display: inline-block; padding: 10px 20px; color: white; background-color: #007bff; text-decoration: none; border-radius: 5px;\">Acceder a la Configuración de Perfil</a></p>"
            . "<p>Si no solicitaste este acceso, ignora este correo. El enlace expirará en 2 horas.</p>"
            . "<p>Saludos,<br>El equipo de ASG</p>";

        $emailService->setMessage($message);

        // Enviar el correo
        if ($emailService->send()) {
            log_message('info', 'Correo de verificación de perfil enviado a: ' . $user['email']);
            // Redirigir de vuelta al perfil o a una página de confirmación
            return redirect()->to('/perfil')->with('success', 'Se ha enviado un enlace de verificación de seguridad a tu correo. Por favor, revísalo para continuar con los cambios.');
        } else {
            // Manejo de error para debug
            $error = $emailService->printDebugger(['headers']);
            log_message('error', 'Error al enviar email de verificación de perfil (SendGrid): ' . $error);
            
            return redirect()->back()->with('error', 'Error al enviar el correo de verificación. Por favor, revisa la configuración de SendGrid y los logs de Render.');
        }
    }

    // Método para manejar la confirmación (GET /perfil/confirmar-acceso/$token)
    public function confirmarAcceso($token)
    {
        if (empty($token)) {
             return redirect()->to('/perfil')->with('error', 'Token de acceso no proporcionado.');
        }

        // 1. Buscar usuario por el token
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/perfil')->with('error', 'El enlace de acceso no es válido o ya ha sido utilizado.');
        }

        // 2. Verificar Expiración
        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/perfil')->with('error', 'El enlace de acceso ha expirado. Por favor, solicita uno nuevo.');
        }

        // 3. Token válido: Limpiar el token y crear una sesión temporal para permitir el acceso al perfil
        $this->userModel->update($user['id'], [
            'reset_token' => null, 
            'reset_expires' => null,
        ]);

        // Establecer una bandera en la sesión para permitir el acceso temporal a la configuración
        // Acceso temporal de 10 minutos
        session()->set('perfil_verified_until', Time::now()->addMinutes(10)->getTimestamp());
        
        // Redirigir a la vista de configuración (configuracion()) con éxito
        return redirect()->to('/perfil/configuracion')->with('success', 'Acceso a la configuración verificado. Tienes 10 minutos para realizar tus cambios.');
    }
    
    // =========================================================================
    // === FIN DE MÉTODOS AÑADIDOS/MODIFICADOS ===
    // =========================================================================
    
    // ... (El resto de funciones originales del PerfilController.php continúan aquí) ...
    /* public function misCompras() { ... }
    public function direccionEnvio($paymentId) { ... }
    public function guardarDireccion() { ... }
    public function registerLink() { ... }
    public function storeLink() { ... }
    public function editDevice($mac) { ... }
    public function updateDevice() { ... }
    public function eliminarDispositivos() { ... }
    public function cambioExitoso() { ... }
    public function guardarDireccion() { ... }
    */
    
    // ... (Cualquier otra función que tengas debe ir aquí) ...
}