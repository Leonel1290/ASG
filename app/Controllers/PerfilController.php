<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;
use App\Models\UserModel;
use App\Models\DispositivoModel;
use CodeIgniter\I18n\Time;

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

        return view('perfil/verificar_email', $data);
    }

    public function enviarVerificacion()
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


        $email = $user['email'];
        $token = random_string('alnum', 32);
        $expires = Time::now()->addMinutes(15);

        $this->userModel->update($loggedInUserId, [
            'reset_token' => $token,
            'reset_expires' => $expires->toDateTimeString(),
        ]);

        $emailService = \Config\Services::email();

        $emailService->setTo($email);
        $emailService->setSubject('Verificación de Email para Configuración de Perfil');
        $verificationLink = base_url("perfil/verificar-email/{$token}");
        $message = "Hola {$user['nombre']},\n\nHaz solicitado verificar tu email para acceder a la configuración de tu perfil.\n\nPor favor, haz clic en el siguiente enlace para verificar tu email:\n{$verificationLink}\n\nEste enlace expirará en 15 minutos.\n\nSi no solicitaste esta verificación, puedes ignorar este correo.\n\nAtentamente,\nEl equipo de ASG";
        $emailService->setMessage($message);

        if ($emailService->send()) {
            log_message('debug', 'Correo de verificación de configuración enviado a: ' . $email);
            return redirect()->to('/perfil/configuracion')->with('success', 'Se ha enviado un correo de verificación a tu email actual. Por favor, revisa tu bandeja de entrada.');
        } else {
            log_message('error', 'Error al enviar correo de verificación de configuración a ' . $email . ': ' . $emailService->printDebugger(['headers', 'subject', 'body']));
            return redirect()->to('/perfil/configuracion')->with('error', 'Hubo un error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
        }
    }

    public function verificarEmailToken($token = null)
    {
        if ($token === null) {
            return redirect()->to('/perfil/configuracion')->with('error', 'Token de verificación no proporcionado.');
        }

        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/perfil/configuracion')->with('error', 'Token de verificación inválido.');
        }

        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/perfil/configuracion')->with('error', 'El token de verificación ha expirado. Por favor, solicita uno nuevo.');
        }

        $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
        $session = session();
        $session->set('email_verified_for_config', true);

        return redirect()->to('/perfil/config_form')->with('success', 'Email verificado exitosamente. Ahora puedes actualizar tu perfil.');
    }

    public function cambiarContrasena()
    {
        $session = session();
        $validation = \Config\Services::validation();

        $userId = $session->get('id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para cambiar tu contraseña.');
        }

        $rules = [
            'current_password' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'La contraseña actual es requerida.'
                ]
            ],
            'new_password' => [
                'rules' => [
                    'required',
                    'min_length[6]',
                    'max_length[255]',
                    'regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/]',
                    function ($password) {
                        return !$this->userModel->isCommonPassword($password);
                    }
            ],
            'errors' => [
                'required' => 'El campo contraseña es obligatorio.',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
                'max_length' => 'La contraseña no puede exceder los 255 caracteres.',
        'regex_match' => 'La contraseña debe incluir: mayúscula, minúscula, número y carácter especial.',
        'La contraseña es demasiado común. Elige una más segura.'
            ],
            'confirm_password' => [
                'rules' => [
                    'required',
                    'min_length[6]',
                    'max_length[255]',
                    'regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/]',
                    function ($password) {
                        return !$this->userModel->isCommonPassword($password);
                    }
            ],
            'errors' => [
                'required' => 'El campo contraseña es obligatorio.',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
                'max_length' => 'La contraseña no puede exceder los 255 caracteres.',
        'regex_match' => 'La contraseña debe incluir: mayúscula, minúscula, número y carácter especial.',
        'La contraseña es demasiado común. Elige una más segura.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        $user = $this->userModel->findWithPassword($userId);

        if ($user) {
            if (password_verify($currentPassword, $user['password'])) {
                $this->userModel->update($userId, [
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT)
                ]);
                return redirect()->to('/perfil/config_form')->with('success', 'Contraseña cambiada exitosamente.');
            } else {
                return redirect()->to('/perfil/config_form')->with('error', 'La contraseña actual es incorrecta.');
            }
        }

        return redirect()->to('/perfil/config_form')->with('error', 'Ocurrió un error inesperado al cambiar la contraseña.');
    }

    public function eliminarCuenta()
    {
        $session = session();
        $userId = $session->get('id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para eliminar tu cuenta.');
        }

        if ($this->userModel->delete($userId)) {
            $session->destroy();
            return redirect()->to('/')->with('success', 'Tu cuenta ha sido eliminada permanentemente.');
        } else {
            return redirect()->to('/perfil/config_form')->with('error', 'Ocurrió un error al intentar eliminar la cuenta.');
        }
    }

    public function configForm()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

         if (!$loggedInUserId || !$session->get('email_verified_for_config')) {
             if ($loggedInUserId) {
                 return redirect()->to('/perfil/configuracion')->with('error', 'Por favor, verifica tu email antes de acceder a la configuración.');
             } else {
                 return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
             }
        }
        
        $userData = $this->userModel->find($loggedInUserId);

         if (!$userData) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Usuario no encontrado.');
        }

         $data['userData'] = [
            'nombre' => $userData['nombre'] ?? '',
            'email' => $userData['email'] ?? ''
        ];

        return view('perfil/configuracion_form', $data);
    }

    public function actualizar()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

         if (!$loggedInUserId || !$session->get('email_verified_for_config')) {
             if ($loggedInUserId) {
                 return redirect()->to('/perfil/configuracion')->with('error', 'Por favor, verifica tu email antes de actualizar tu perfil.');
             } else {
                 return redirect()->to('/login')->with('error', 'Debes iniciar sesión para actualizar tu perfil.');
             }
        }

        $requestMethod = $this->request->getMethod();
        if (strcasecmp($requestMethod, 'post') !== 0) {
            return redirect()->to('/perfil/configuracion');
        }

        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'El campo Nombre es obligatorio.',
                    'min_length' => 'El Nombre debe tener al menos 3 caracteres.',
                    'max_length' => 'El Nombre no puede exceder los 50 caracteres.'
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

        if (! $this->validate($rules)) {
            return redirect()->to('/perfil/config_form')->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Error de validación. Por favor, revisa los datos.');
        }

        $nombre = $this->request->getPost('nombre');
        $email = $this->request->getPost('email');

        $updateData = [
            'nombre' => $nombre,
            'email'  => $email,
        ];

        $updated = $this->userModel->update($loggedInUserId, $updateData);

        if ($updated) {
            $session->set('nombre', $nombre);
            $session->set('email', $email);
            $session->remove('email_verified_for_config');

            return redirect()->to('/perfil/cambio-exitoso')->with('success', '¡Configuración actualizada exitosamente!');
        } else {
             return redirect()->to('/perfil/config_form')->withInput()->with('error', 'Hubo un error al intentar actualizar la configuración.');
        }
    }

    public function cambioExitoso()
    {
        $session = session();
        if (!session('success')) {
             return redirect()->to('/perfil')->with('error', 'Acceso no autorizado a la página de éxito.');
        }
        return view('perfil/cambio_exitoso');
    }

    public function editDevice($mac = null)
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

        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);

        if (!$dispositivo) {
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        return view('perfil/edit_device', [
            'dispositivo' => $dispositivo
        ]);
    }

    public function updateDevice()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        $mac = $this->request->getPost('mac');
        $nombre = $this->request->getPost('nombre');
        $ubicacion = $this->request->getPost('ubicacion');

        $rules = [
            'mac' => [
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

        if (! $this->validate($rules)) {
            return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Error de validación. Por favor, revisa los datos.');
        }

        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        $updateData = [
            'nombre' => $nombre,
            'ubicacion' => $ubicacion,
        ];

        $updated = $this->dispositivoModel->updateDispositivoByMac($mac, $updateData);

        if ($updated) {
            return redirect()->to('/perfil')->with('success', "¡Dispositivo '{$nombre}' actualizado exitosamente!");
        } else {
             return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('error', 'Hubo un error al intentar actualizar el dispositivo.');
        }
    }

    public function eliminarDispositivos()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        $macs_a_eliminar = $this->request->getPost('macs');

        if (!empty($macs_a_eliminar) && is_array($macs_a_eliminar)) {
            $this->enlaceModel->where('id_usuario', $usuarioId)
                              ->whereIn('MAC', $macs_a_eliminar)
                              ->delete();

            return redirect()->to('/perfil')->with('success', 'Dispositivos desenlazados correctamente.');
        } else {
            return redirect()->to('/perfil')->with('error', 'No se seleccionaron dispositivos para desenlazar.');
        }
    }
}