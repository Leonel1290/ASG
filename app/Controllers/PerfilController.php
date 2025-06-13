<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;
use App\Models\UserModel;
use App\Models\DispositivoModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PerfilController extends BaseController
{
    protected $userModel;
    protected $enlaceModel;
    protected $lecturasGasModel;
    protected $dispositivoModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->userModel = new UserModel();
        $this->enlaceModel = new EnlaceModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel();

        // --- CARGAR HELPERS ---
        helper(['form', 'url', 'text', 'email']);
        // --- FIN CARGAR HELPERS ---
    }

    // Método para mostrar la página principal del perfil (GET /perfil)
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

        // Obtener las direcciones MAC enlazadas al usuario actual desde la tabla 'enlace'
        $enlaces = $this->enlaceModel
                        ->select('MAC')
                        ->where('id_usuario', $usuarioId)
                        ->findAll();

        $macs = array_column($enlaces, 'MAC');

        $dispositivosEnlazados = [];
        if (!empty($macs)) {
            // Obtener detalles de los dispositivos enlazados
            $dispositivosEnlazados = $this->dispositivoModel
                                            ->whereIn('MAC', $macs)
                                            ->findAll();
        }

        // Obtener lecturas por usuario (usando tu método existente en LecturasGasModel)
        $allLecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        // Procesar las lecturas para agruparlas por MAC
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

        // Pasar los datos a la vista.
        return view('perfil', [
            'dispositivosEnlazados' => $dispositivosEnlazados,
            'lecturasPorMac' => $lecturasPorMac
        ]);
    }

    // MÉTODOS PARA EL FLUJO DE VERIFICACIÓN Y CONFIGURACIÓN DEL PERFIL

    /**
     * Muestra el formulario de configuración o redirige a la verificación si es necesario.
     * Corresponde a GET /perfil/configuracion
     */
    public function configuracion()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if (!$loggedInUserId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Si ya se ha verificado el email para la configuración, mostrar el formulario directamente
        if ($session->get('email_verified_for_config')) {
            $userData = $this->userModel->find($loggedInUserId);
            if (!$userData) {
                $session->destroy();
                return redirect()->to('/login')->with('error', 'Usuario no encontrado. Por favor, inicia sesión de nuevo.');
            }
            // CAMBIO: Asegúrate de que esta vista también esté en la subcarpeta 'perfil'
            return view('perfil/configuracion_form', ['userData' => $userData]);
        } else {
            // Si no se ha verificado para la configuración, redirigir a la página de solicitud de verificación
            return redirect()->to('/perfil/verificar-email');
        }
    }

    /**
     * Muestra la vista para solicitar el envío de un email de verificación para el perfil.
     * Corresponde a GET /perfil/verificar-email
     */
    public function verificarEmail()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if (!$loggedInUserId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        $user = $this->userModel->find($loggedInUserId);
        if (!$user) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Usuario no encontrado.');
        }

        // CAMBIO: La vista se busca en la subcarpeta 'perfil'
        return view('perfil/verificar_email', ['userEmail' => $user['email']]);
    }

    /**
     * Procesa la solicitud para enviar un email de verificación para la configuración del perfil.
     * Corresponde a POST /perfil/enviar-verificacion
     */
    public function enviarVerificacionEmail()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if (!$loggedInUserId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para enviar el correo de verificación.');
        }

        $user = $this->userModel->find($loggedInUserId);
        if (!$user) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Usuario no encontrado.');
        }

        // Generar un token de verificación específico para la configuración del perfil
        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(1)->toDateTimeString(); // Token válido por 1 hora

        $this->userModel->update($loggedInUserId, [
            'verification_token' => $token, // Usamos el mismo campo que para el registro inicial
            'token_expires_at' => $expires,
        ]);

        $emailService = \Config\Services::email();
        // Configura el remitente en app/Config/Email.php o .env
        $emailService->setFrom(getenv('EMAIL_FROM_ADDRESS') ?? 'no-reply@tudominio.com', getenv('EMAIL_FROM_NAME') ?? 'Sistema ASG');
        $emailService->setTo($user['email']);
        $emailService->setSubject('Verificación de Email para Acceso a Configuración de Perfil - ASG');
        $verificationLink = base_url('perfil/verificar-email-token/' . $token);
        $message = "Hola " . esc($user['nombre']) . ",\n\n"
                 . "Has solicitado verificar tu email para acceder a la configuración de tu perfil. Haz clic en el siguiente enlace:\n"
                 . $verificationLink . "\n\n"
                 . "Este enlace expirará en 1 hora. Si no solicitaste esto, ignora este correo.\n\n"
                 . "Gracias,\nSistema ASG";
        $emailService->setMessage($message);

        if ($emailService->send()) {
            log_message('debug', 'Correo de verificación de configuración enviado a: ' . $user['email']);
            return redirect()->to('/perfil/verificar-email')->with('success', 'Se ha enviado un correo de verificación a tu email. Por favor, revisa tu bandeja de entrada.');
        } else {
            $error = $emailService->printDebugger(['headers']);
            log_message('error', 'Error al enviar email de verificación para perfil: ' . $error);
            return redirect()->back()->with('error', 'No se pudo enviar el correo de verificación. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Procesa el token de verificación de email recibido por el usuario.
     * Corresponde a GET /perfil/verificar-email-token/(:segment)
     */
    public function verificarEmailConfiguracion($token = null)
    {
        if (is_null($token)) {
            return redirect()->to('/perfil/verificar-email')->with('error', 'Token de verificación no proporcionado.');
        }

        $user = $this->userModel->where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->to('/perfil/verificar-email')->with('error', 'Token de verificación inválido o ya utilizado.');
        }

        $expires = Time::parse($user['token_expires_at']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['verification_token' => null, 'token_expires_at' => null]);
            return redirect()->to('/perfil/verificar-email')->with('error', 'El token de verificación ha expirado. Por favor, solicita uno nuevo.');
        }

        // Marcar que el email ha sido verificado para acceder a la configuración
        $session = session();
        $session->set('email_verified_for_config', true);
        // Limpiar el token una vez usado
        $this->userModel->update($user['id'], [
            'verification_token' => null,
            'token_expires_at' => null,
        ]);

        return redirect()->to('/perfil/configuracion')->with('success', '¡Email verificado exitosamente! Ahora puedes actualizar tu perfil.');
    }

    /**
     * Procesa la actualización del perfil (nombre y email).
     * Corresponde a POST /perfil/actualizar-perfil
     */
    public function actualizarPerfil() // Renombrado de 'actualizar'
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if (!$loggedInUserId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para actualizar tu perfil.');
        }

        // Asegurarse de que el email ha sido verificado para acceder a esta función
        if (!$session->get('email_verified_for_config')) {
            return redirect()->to('/perfil/verificar-email')->with('error', 'Por favor, verifica tu email antes de actualizar tu perfil.');
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/perfil/configuracion');
        }

        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'El campo Nombre es obligatorio.',
                    'min_length' => 'El Nombre debe tener al menos 3 caracteres.',
                    'max_length' => 'El Nombre no puede exceder los 100 caracteres.'
                ]
            ],
            'email'  => [
                'rules' => "required|valid_email|max_length[100]|is_unique[usuarios.email,id,{$loggedInUserId}]",
                'errors' => [
                    'required' => 'El campo Email es obligatorio.',
                    'valid_email' => 'Por favor, ingresa un Email válido.',
                    'max_length' => 'El Email no puede exceder los 100 caracteres.',
                    'is_unique' => 'Este Email ya está registrado por otro usuario.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/perfil/configuracion')->withInput()->with('errors', $this->validator->getErrors());
        }

        $nombre = $this->request->getPost('nombre');
        $email = $this->request->getPost('email');

        $currentUserData = $this->userModel->find($loggedInUserId);
        $updateData = [
            'nombre' => $nombre,
            'email'  => $email,
        ];

        $emailChanged = ($currentUserData['email'] !== $email);

        if ($emailChanged) {
            // Si el email cambia, invalidar la cuenta hasta que el nuevo email sea verificado
            $updateData['is_active'] = 0; // Marcar como inactivo hasta verificar el nuevo email
            $updateData['verification_token'] = bin2hex(random_bytes(32)); // Nuevo token para el nuevo email
            $updateData['token_expires_at'] = date('Y-m-d H:i:s', strtotime('+1 hour'));

            if ($this->userModel->update($loggedInUserId, $updateData)) {
                // Enviar correo de verificación para el nuevo email
                $emailService = \Config\Services::email();
                $emailService->setFrom(getenv('EMAIL_FROM_ADDRESS') ?? 'no-reply@tudominio.com', getenv('EMAIL_FROM_NAME') ?? 'Sistema ASG');
                $emailService->setTo($email);
                $emailService->setSubject('Verifica tu nuevo Email - ASG');
                // Reutilizamos la ruta de verificación de registro para la activación del nuevo email
                $verificationLink = base_url('register/verify-email/' . $updateData['verification_token']);
                $message = "Hola " . esc($nombre) . ",\n\n"
                                 . "Has actualizado tu email en el Sistema ASG. Por favor, verifica tu nuevo email haciendo clic en el siguiente enlace:\n"
                                 . $verificationLink . "\n\n"
                                 . "Este enlace expirará en 1 hora. Si no realizaste este cambio, ignora este correo.\n\n"
                                 . "Gracias,\nSistema ASG";
                $emailService->setMessage($message);

                if (!$emailService->send()) {
                    log_message('error', 'Error al enviar email de verificación de nuevo email de perfil: ' . $emailService->printDebugger(['headers']));
                    // Si falla el envío de correo, considerar revertir la actualización o dejar un estado consistente
                    // Para simplificar aquí, se asume que el envío de correo es crítico para el cambio de email.
                    // Si el email no se puede enviar, es mejor que el usuario no pueda cambiar su email.
                    // Podrías revertir: $this->userModel->update($loggedInUserId, ['is_active' => $currentUserData['is_active'], 'email' => $currentUserData['email'], 'verification_token' => null, 'token_expires_at' => null]);
                    return redirect()->back()->withInput()->with('error', 'Error al enviar el correo de verificación para el nuevo email. Inténtalo de nuevo.');
                }

                $session->setFlashdata('success', '¡Perfil actualizado! Se ha enviado un correo de verificación a tu nuevo email. Por favor, actívalo para recuperar el acceso.');
                $session->destroy(); // Forzar logout para que el usuario se re-autentique con el nuevo estado
                return redirect()->to('/login')->with('info', 'Tu email ha sido actualizado. Por favor, verifica tu nuevo email y vuelve a iniciar sesión.');
            } else {
                return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el perfil. Inténtalo de nuevo.');
            }
        } else {
            // Si el email no cambia, solo actualizar el nombre
            if ($this->userModel->update($loggedInUserId, $updateData)) {
                $session->set('nombre', $nombre);
                $session->set('email', $email);
                $session->remove('email_verified_for_config'); // Limpiar la bandera ya que los cambios se aplicaron

                // CAMBIO: Si tienes una vista para el éxito del cambio de contraseña, asegúrate de que también esté en 'perfil/'
                return redirect()->to('/perfil/configuracion')->with('success', '¡Perfil actualizado exitosamente!');
            } else {
                return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el perfil. Inténtalo de nuevo.');
            }
        }
    }

    /**
     * Procesa el cambio de contraseña del usuario.
     * Corresponde a POST /perfil/cambiar-contrasena
     */
    public function cambiarContrasena()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if (!$loggedInUserId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para cambiar tu contraseña.');
        }

        // Asegurarse de que el email ha sido verificado para acceder a esta función
        if (!$session->get('email_verified_for_config')) {
            return redirect()->to('/perfil/verificar-email')->with('error', 'Por favor, verifica tu email antes de cambiar la contraseña.');
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/perfil/configuracion');
        }

        $rules = [
            'current_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'La contraseña actual es obligatoria.'
                ]
            ],
            'new_password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La nueva contraseña es obligatoria.',
                    'min_length' => 'La nueva contraseña debe tener al menos 6 caracteres.'
                ]
            ],
            'confirm_new_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'La confirmación de la nueva contraseña es obligatoria.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/perfil/configuracion')->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        $user = $this->userModel->find($loggedInUserId);

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->to('/perfil/configuracion')->withInput()->with('error', 'La contraseña actual es incorrecta.');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($this->userModel->update($loggedInUserId, ['password' => $hashedPassword])) {
            $session->remove('email_verified_for_config'); // Limpiar la bandera después de un cambio importante

            // Si tienes una vista de "cambio exitoso" para la contraseña y está en 'perfil/', cárgala así
            // return view('perfil/cambio_exitoso'); // EJEMPLO
            return redirect()->to('/perfil/configuracion')->with('success', '¡Contraseña actualizada exitosamente!');
        } else {
            return redirect()->to('/perfil/configuracion')->withInput()->with('error', 'No se pudo actualizar la contraseña. Inténtalo de nuevo.');
        }
    }

    /**
     * MÉTODOS PARA GESTIÓN DE DISPOSITIVOS
     */

    /**
     * Muestra el formulario para editar un dispositivo enlazado.
     * Corresponde a GET /perfil/dispositivo/editar/(:any)
     */
    public function editarDispositivo($mac = null)
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        if ($mac === null) {
            return redirect()->to('/perfil')->with('error', 'MAC del dispositivo no especificada.');
        }

        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();

        if (!$dispositivo) {
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        // CAMBIO: La vista se busca en la subcarpeta 'perfil'
        return view('perfil/edit_device', [
            'dispositivo' => $dispositivo
        ]);
    }

    /**
     * Procesa la actualización de la información de un dispositivo.
     * Corresponde a POST /perfil/dispositivo/actualizar
     */
    public function actualizarDispositivo()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/perfil');
        }

        $mac = $this->request->getPost('MAC'); // Obtener MAC del input oculto
        $nombre = $this->request->getPost('nombre');
        $ubicacion = $this->request->getPost('ubicacion');

        $rules = [
            'MAC' => [ // Cambiado de 'mac' a 'MAC' para coincidir con el campo de POST
                'rules' => 'required|exact_length[17]',
                'errors' => [
                    'required' => 'La MAC es obligatoria.',
                    'exact_length' => 'El formato de la MAC es incorrecto.'
                ]
            ],
            'nombre' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'El Nombre del dispositivo es obligatorio.',
                    'max_length' => 'El Nombre no puede exceder los 255 caracteres.'
                ]
            ],
            'ubicacion' => [
                'rules' => 'max_length[255]',
                'errors' => [
                    'max_length' => 'La Ubicación no puede exceder los 255 caracteres.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            // Si la validación falla, redirigir de nuevo al formulario de edición con errores
            return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('errors', $this->validator->getErrors());
        }

        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para actualizar este dispositivo.');
        }

        $updateData = [
            'nombre' => $nombre,
            'ubicacion' => $ubicacion,
        ];

        // Obtener el ID del dispositivo para usar el método update() del modelo
        $dispositivo = $this->dispositivoModel->where('MAC', $mac)->first();
        if (!$dispositivo) {
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        if ($this->dispositivoModel->update($dispositivo['id'], $updateData)) { // Usar update por ID
            return redirect()->to('/perfil')->with('success', "¡Dispositivo '{$nombre}' actualizado exitosamente!");
        } else {
            return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('error', 'Hubo un error al intentar actualizar el dispositivo.');
        }
    }

    /**
     * Elimina uno o varios dispositivos enlazados al usuario.
     * Corresponde a POST /perfil/eliminar-dispositivos
     */
    public function eliminarDispositivos()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/perfil');
        }

        $macs_a_eliminar = $this->request->getPost('macs');

        if (!empty($macs_a_eliminar) && is_array($macs_a_eliminar)) {
            $this->db->transBegin(); // Iniciar transacción

            try {
                // Desvincular de la tabla 'enlace'
                $this->enlaceModel->where('id_usuario', $usuarioId)
                                  ->whereIn('MAC', $macs_a_eliminar)
                                  ->delete();

                // Actualizar el estado de los dispositivos a 'disponible'
                foreach ($macs_a_eliminar as $mac) {
                    // Asegúrate de que updateDeviceStatusByMac exista en DispositivoModel
                    // o usa update($dispositivoId, ['estado_dispositivo' => 'disponible'])
                    if (!$this->dispositivoModel->updateDeviceStatusByMac($mac, 'disponible')) {
                        throw new \Exception("No se pudo actualizar el estado del dispositivo MAC: {$mac}");
                    }
                }

                $this->db->transCommit(); // Confirmar transacción
                return redirect()->to('/perfil')->with('success', 'Dispositivos desvinculados correctamente y marcados como disponibles.');

            } catch (\Exception $e) {
                $this->db->transRollback(); // Revertir transacción en caso de error
                log_message('error', 'Error al eliminar/desvincular dispositivos: ' . $e->getMessage());
                return redirect()->to('/perfil')->with('error', 'Ocurrió un error al desvincular los dispositivos. Por favor, inténtalo de nuevo.');
            }
        } else {
            return redirect()->to('/perfil')->with('error', 'No se seleccionaron dispositivos para desvincular.');
        }
    }
}