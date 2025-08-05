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
        // Llama al constructor de la clase padre (BaseController) si es necesario
        // parent::__construct();

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

        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'PerfilController::index() - Estado de la sesión al inicio: ' . json_encode($session->get()));
        log_message('debug', 'PerfilController::index() - Usuario ID de la sesión: ' . ($usuarioId ?? 'null'));
        // --- FIN LOGGING ---

        // Redirigir si el usuario no está logueado
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
            $dispositivosEnlazados = $this->dispositivoModel
                                        ->whereIn('MAC', $macs)
                                        ->findAll();
        }

        // Obtener lecturas por usuario (usando tu método existente en LecturasGasModel)
        // CAMBIO: Se ha corregido el nombre del método de LecturasGasModel
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

        // --- LOGGING PARA DEBUGGING ---
        log_message('debug', 'PerfilController::index() - Dispositivos enlazados obtenidos: ' . json_encode($dispositivosEnlazados));
        log_message('debug', 'PerfilController::index() - Lecturas por MAC procesadas: ' . json_encode($lecturasPorMac));
        // --- FIN LOGGING ---


        // Pasar los datos a la vista.
        return view('perfil', [
            'dispositivosEnlazados' => $dispositivosEnlazados,
            'lecturasPorMac' => $lecturasPorMac
        ]);
    }


    // --- MÉTODOS PARA EL FLUJO DE VERIFICACIÓN Y CONFIGURACIÓN ---

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
        $expires = Time::now()->addMinutes(15); // Token válido por 15 minutos

        // Guardar el token y su expiración en la base de datos para el usuario
        $this->userModel->update($loggedInUserId, [
            'reset_token' => $token,
            'reset_expires' => $expires->toDateTimeString(),
        ]);

        // Cargar el servicio de email
        $emailService = \Config\Services::email();

        $emailService->setFrom('no-reply@tudominio.com', 'Sistema de Monitoreo de Gas');
        $emailService->setTo($email);
        $emailService->setSubject('Verifica tu cuenta de Sistema de Monitoreo de Gas');

        $verificationLink = site_url('register/verify-email/' . $token); // Usa site_url para generar la URL base

        $message = view('emails/verify_email', ['verificationLink' => $verificationLink]); // Crea una vista para el contenido del email
        $emailService->setMessage($message);
        $emailService->setAltMessage(strip_tags($message)); // Versión de texto plano

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Correo de verificación enviado. Revisa tu bandeja de entrada.');
        } else {
            $error = $emailService->printDebugger(['headers']);
            log_message('error', 'Fallo al enviar correo de verificación a ' . $email . ': ' . $error);
            return redirect()->back()->with('error', 'No se pudo enviar el correo de verificación. Por favor, inténtalo de nuevo más tarde.');
        }
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

        // Validar el formato de la MAC si es necesario
        if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac)) {
            return redirect()->back()->with('error', 'Formato de MAC inválido.');
        }

        // Verificar que la MAC realmente pertenece al usuario logueado
        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para actualizar este dispositivo.');
        }

        $updateData = [
            'nombre' => $nombre,
            'ubicacion' => $ubicacion
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

    public function cambiarIdioma()
    {
        $idioma = $this->request->getPost('idioma');

        if (in_array($idioma, ['en', 'es'])) {
            session()->set('locale', $idioma);
        }

        return redirect()->back();
    }
}