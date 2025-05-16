<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnlaceModel;
use App\Models\LecturasGasModel;
use App\Models\UserModel;
use App\Models\DispositivoModel; // Importar el modelo de dispositivo
use CodeIgniter\I18n\Time;

// Asumo que extiendes de tu BaseController. Si no, cambia 'extends BaseController' a 'extends Controller'.
class PerfilController extends BaseController
{
    protected $userModel;
    protected $enlaceModel;
    protected $lecturasGasModel;
    protected $dispositivoModel; // Propiedad para el modelo de dispositivo


    public function __construct()
    {
        // Llama al constructor de la clase padre (BaseController) si es necesario
        // parent::__construct();

        $this->userModel = new UserModel();
        $this->enlaceModel = new EnlaceModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->dispositivoModel = new DispositivoModel(); // Instancia el modelo de dispositivo


        // --- CARGAR HELPERS ---
        helper(['form', 'url', 'text', 'email']);
        // --- FIN CARGAR HELPERS ---
    }

    // Método para mostrar la página principal del perfil (GET /perfil)
    // Modificado para obtener detalles completos del dispositivo
    public function index()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Obtener las direcciones MAC enlazadas al usuario desde la tabla 'enlace'
        $enlaces = $this->enlaceModel
                        ->select('MAC')
                        ->where('id_usuario', $usuarioId)
                        ->findAll(); // Obtiene un array de arrays, ej: [['MAC' => '...'], ['MAC' => '...']]

        $macs = array_column($enlaces, 'MAC'); // Extraer solo los valores de MAC en un array simple

        $dispositivosEnlazados = [];
        if (!empty($macs)) {
            // Obtener los detalles completos de los dispositivos (nombre y ubicacion)
            // usando las MACs obtenidas.
            // Usamos whereIn para buscar múltiples MACs.
            $dispositivosEnlazados = $this->dispositivoModel
                                        ->whereIn('MAC', $macs)
                                        ->findAll(); // Obtiene un array de arrays con detalles del dispositivo
        }

        // Obtener lecturas por usuario (usando tu método existente en LecturasGasModel)
        $allLecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        // Procesar las lecturas para agruparlas por MAC (esto ya lo tenías)
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

        // Pasar los datos a la vista.
        // Ahora pasamos los detalles completos de los dispositivos enlazados.
        return view('perfil', [
            'dispositivosEnlazados' => $dispositivosEnlazados, // Pasar los detalles de los dispositivos
            'lecturasPorMac' => $lecturasPorMac // Pasar las lecturas procesadas
        ]);
    }


    // --- MÉTODOS PARA EL FLUJO DE VERIFICACIÓN Y CONFIGURACIÓN ---

    // ... (Mantén los métodos configuracion, enviarVerificacion, verificarEmailToken, configForm, actualizar) ...
    // Asegúrate de que estén en este archivo PerfilController.php

    // Método para mostrar la página de inicio de verificación (GET /perfil/configuracion)
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

    // Método para enviar el correo de verificación (POST /perfil/enviar-verificacion)
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
        $emailService->setFrom('tu_correo@ejemplo.com', 'ASG'); // <-- CONFIGURA ESTO
        $emailService->setSubject('Verificación de Email para Configuración de Perfil');
        $verificationLink = base_url("perfil/verificar-email/{$token}");
        $message = "Hola {$user['nombre']},\n\nHaz solicitado verificar tu email para acceder a la configuración de tu perfil.\n\nPor favor, haz clic en el siguiente enlace para verificar tu email:\n{$verificationLink}\n\nEste enlace expirará en 15 minutos.\n\nSi no solicitaste esta verificación, puedes ignorar este correo.\n\nAtentamente,\nEl equipo de ASG";
        $emailService->setMessage($message);

        if ($emailService->send()) {
            return redirect()->to('/perfil/configuracion')->with('success', 'Se ha enviado un correo de verificación a tu email actual. Por favor, revisa tu bandeja de entrada.');
        } else {
            log_message('error', 'Error al enviar correo de verificación: ' . $emailService->printDebugger(['headers', 'subject', 'body']));
            return redirect()->to('/perfil/configuracion')->with('error', 'Hubo un error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
        }
    }

    // Método para verificar el token recibido por email (GET /perfil/verificar-email/(:segment))
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

    // Método para mostrar el formulario de configuración REAL (GET /perfil/config_form)
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


    // Método para procesar la actualización del perfil (POST /perfil/actualizar)
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

    // Método para mostrar la página de éxito después de actualizar el perfil
    public function cambioExitoso()
    {
        $session = session();
        if (!session('success')) {
             return redirect()->to('/perfil')->with('error', 'Acceso no autorizado a la página de éxito.');
        }
        return view('perfil/cambio_exitoso');
    }

    // --- FIN MÉTODOS FLUJO VERIFICACIÓN Y CONFIGURACIÓN ---


    // --- NUEVOS MÉTODOS PARA GESTIÓN DE DISPOSITIVOS ---

    // Método para mostrar el formulario de edición de un dispositivo
    // GET /perfil/dispositivo/editar/(:segment)
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

        // Verificar si la MAC está enlazada al usuario actual
        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        // Obtener los detalles del dispositivo
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);

        if (!$dispositivo) {
            // Esto no debería pasar si el enlace existe, pero es una buena práctica verificar
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        // Cargar la vista del formulario de edición y pasar los datos del dispositivo
        return view('perfil/edit_device', [
            'dispositivo' => $dispositivo // Pasar todos los datos del dispositivo
        ]);
    }

    // Método para procesar el formulario de edición de un dispositivo
    // POST /perfil/dispositivo/actualizar
    public function updateDevice()
    {
        $session = session();
        $usuarioId = $session->get('id');

        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        // Obtener los datos del formulario
        $mac = $this->request->getPost('mac'); // Necesitamos la MAC para identificar el dispositivo
        $nombre = $this->request->getPost('nombre');
        $ubicacion = $this->request->getPost('ubicacion');

        // Validar los datos recibidos
        $rules = [
            'mac' => [
                'rules' => 'required|exact_length[17]', // Validación básica de formato MAC (XX:XX:XX:XX:XX:XX)
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
                'rules' => 'max_length[255]', // Ubicación puede ser opcional
                'errors' => [
                    'max_length' => 'La Ubicación no puede exceder los 255 caracteres.'
                ]
            ],
        ];

        if (! $this->validate($rules)) {
            // Si la validación falla, redirigir de vuelta al formulario con errores y datos antiguos
            // Redirigimos a la ruta de edición, pasando la MAC como segmento de URL
            return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Error de validación. Por favor, revisa los datos.');
        }

        // Verificar si la MAC está enlazada al usuario actual antes de actualizar
        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        // Preparar los datos para la actualización
        $updateData = [
            'nombre' => $nombre,
            'ubicacion' => $ubicacion,
            // 'updated_at' se actualizará automáticamente si useTimestamps es true en el modelo.
        ];

        // Usar el modelo de dispositivo para realizar la actualización por MAC
        $updated = $this->dispositivoModel->updateDispositivoByMac($mac, $updateData);

        if ($updated) {
            // Redirigir de vuelta a la página principal del perfil con un mensaje de éxito
            return redirect()->to('/perfil')->with('success', "¡Dispositivo '{$nombre}' actualizado exitosamente!");
        } else {
             // Si el update() devuelve false, hubo un problema
             // Redirigir de vuelta al formulario de edición con un mensaje de error genérico
             return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('error', 'Hubo un error al intentar actualizar el dispositivo.');
        }
    }


    // Método para eliminar dispositivos (mantén tu código existente)
     public function eliminarDispositivos()
    {
        $usuarioId = session()->get('id');
        // Asumo que el formulario de eliminación envía un array de MACs
        $macs_a_eliminar = $this->request->getPost('macs');

        if (!empty($macs_a_eliminar) && is_array($macs_a_eliminar)) {
            // Eliminar los enlaces del usuario a estas MACs
            $this->enlaceModel->where('id_usuario', $usuarioId)
                              ->whereIn('MAC', $macs_a_eliminar)
                              ->delete();

            // NOTA: Esto SOLO elimina el ENLACE. Los dispositivos en la tabla 'dispositivos'
            // y sus lecturas en 'lecturas_gas' (si tienen FK con CASCADE DELETE)
            // podrían eliminarse automáticamente si tienes configuradas las restricciones
            // FOREIGN KEY con ON DELETE CASCADE en tu base de datos.
            // Según tu script SQL, la tabla 'enlace' tiene FK a 'dispositivos' con ON DELETE CASCADE.
            // La tabla 'lecturas_gas' tiene FK a 'dispositivos' con ON DELETE CASCADE.
            // Esto significa que al eliminar una MAC de la tabla 'dispositivos',
            // sus entradas en 'enlace' y 'lecturas_gas' asociadas a esa MAC también se eliminarán.
            // Sin embargo, tu lógica actual elimina el enlace primero.
            // Si quieres eliminar el DISPOSITIVO COMPLETO (y no solo el enlace del usuario),
            // deberías eliminar de la tabla 'dispositivos' usando $this->dispositivoModel->whereIn('MAC', $macs_a_eliminar)->delete();
            // PERO ten cuidado, esto eliminará el dispositivo para TODOS los usuarios enlazados.
            // Si solo quieres desenlazar, la lógica actual de eliminar de 'enlace' es correcta.
            // Si quieres eliminar el dispositivo si NINGÚN usuario está enlazado a él después de desenlazar,
            // necesitarías lógica adicional.
            // Por ahora, mantendremos la lógica de solo eliminar el enlace, que es lo que hace tu código actual.

            return redirect()->to('/perfil')->with('success', 'Dispositivos desenlazados correctamente.');
        } else {
            return redirect()->to('/perfil')->with('error', 'No se seleccionaron dispositivos para desenlazar.');
        }
    }
}
