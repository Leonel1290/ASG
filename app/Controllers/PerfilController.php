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
        // Asegúrate de que todos los helpers necesarios estén cargados aquí o en BaseController
        helper(['form', 'url', 'text', 'email']);
        // --- FIN CARGAR HELPERS ---
    }

    // Método para mostrar la página principal del perfil (GET /perfil)
    // Modificado para obtener detalles completos del dispositivo
    public function index()
    {
        $session = session();
        $usuarioId = $session->get('id');

        // Redirigir si el usuario no está logueado
        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Obtener las direcciones MAC enlazadas al usuario actual desde la tabla 'enlace'
        $enlaces = $this->enlaceModel
                        ->select('MAC')
                        ->where('id_usuario', $usuarioId)
                        ->findAll(); // Obtiene un array de arrays, ej: [['MAC' => '...'], ['MAC' => '...']]

        $macs = array_column($enlaces, 'MAC'); // Extraer solo los valores de MAC en un array simple

        $dispositivosEnlazados = [];
        if (!empty($macs)) {
            // Obtener los detalles completos de los dispositivos (nombre y ubicacion)
            // usando las MACs obtenidas de la tabla 'enlace'.
            // Usamos whereIn para buscar múltiples MACs en la tabla 'dispositivos'.
            $dispositivosEnlazados = $this->dispositivoModel
                                        ->whereIn('MAC', $macs)
                                        ->findAll(); // Obtiene un array de arrays con detalles del dispositivo
        }

        // Obtener lecturas por usuario (usando tu método existente en LecturasGasModel)
        $allLecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        // Procesar las lecturas para agruparlas por MAC
        $lecturasPorMac = [];
        if (!empty($allLecturas)) {
             foreach ($allLecturas as $lectura) {
                // Asegurarse de que la clave 'MAC' existe y no es nula
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
        // Ahora pasamos los detalles completos de los dispositivos enlazados y las lecturas procesadas.
        return view('perfil', [
            'dispositivosEnlazados' => $dispositivosEnlazados, // Pasar los detalles de los dispositivos
            'lecturasPorMac' => $lecturasPorMac // Pasar las lecturas procesadas
        ]);
    }


    // --- MÉTODOS PARA EL FLUJO DE VERIFICACIÓN Y CONFIGURACIÓN ---

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

        // Pasamos el email del usuario a la vista
        $data['userEmail'] = $userData['email'] ?? 'No disponible';

        // Carga la vista para solicitar verificación de email para configuración
        return view('perfil/verificar_email', $data);
    }

    // Método para enviar el correo de verificación para configuración (POST /perfil/enviar-verificacion)
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
        // Generar un nuevo token y definir su expiración (más corta que el de registro)
        $token = random_string('alnum', 32);
        $expires = Time::now()->addMinutes(15); // Token válido por 15 minutos para configuración

        // Guardar el token y la expiración en la base de datos para este usuario
        $this->userModel->update($loggedInUserId, [
            'reset_token' => $token,
            'reset_expires' => $expires->toDateTimeString(),
        ]);

        // Configurar y enviar el correo electrónico
        $emailService = \Config\Services::email();
        // Configura el remitente. Es mejor configurar esto en app/Config/Email.php o .env
        // Si no está configurado globalmente, descomenta y ajusta las siguientes líneas:
        // $emailService->setFrom('tu_correo@ejemplo.com', 'ASG'); // <-- CONFIGURA ESTO

        $emailService->setTo($email);
        $emailService->setSubject('Verificación de Email para Configuración de Perfil');
        $verificationLink = base_url("perfil/verificar-email/{$token}"); // Enlace de verificación para configuración
        $message = "Hola {$user['nombre']},\n\nHaz solicitado verificar tu email para acceder a la configuración de tu perfil.\n\nPor favor, haz clic en el siguiente enlace para verificar tu email:\n{$verificationLink}\n\nEste enlace expirará en 15 minutos.\n\nSi no solicitaste esta verificación, puedes ignorar este correo.\n\nAtentamente,\nEl equipo de ASG";
        $emailService->setMessage($message);

        // Intentar enviar el correo
        if ($emailService->send()) {
            log_message('debug', 'Correo de verificación de configuración enviado a: ' . $email);
            return redirect()->to('/perfil/configuracion')->with('success', 'Se ha enviado un correo de verificación a tu email actual. Por favor, revisa tu bandeja de entrada.');
        } else {
            // Loguear el error si el envío falla
            log_message('error', 'Error al enviar correo de verificación de configuración a ' . $email . ': ' . $emailService->printDebugger(['headers', 'subject', 'body']));
            return redirect()->to('/perfil/configuracion')->with('error', 'Hubo un error al enviar el correo de verificación. Por favor, inténtalo de nuevo.');
        }
    }

    // Método para verificar el token recibido por email para configuración (GET /perfil/verificar-email/(:segment))
    public function verificarEmailToken($token = null)
    {
        if ($token === null) {
            return redirect()->to('/perfil/configuracion')->with('error', 'Token de verificación no proporcionado.');
        }

        // Buscar al usuario por el token en la base de datos
        $user = $this->userModel->where('reset_token', $token)->first();

        // Verificar si se encontró un usuario con ese token
        if (!$user) {
            return redirect()->to('/perfil/configuracion')->with('error', 'Token de verificación inválido.');
        }

        // Verificar si el token ha expirado
        $expires = Time::parse($user['reset_expires']);
        if ($expires->isBefore(Time::now())) {
            // Token expirado, limpiar el token en la base de datos
            $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
            return redirect()->to('/perfil/configuracion')->with('error', 'El token de verificación ha expirado. Por favor, solicita uno nuevo.');
        }

        // Token válido: Limpiar el token y marcar el email como verificado temporalmente en la sesión
        $this->userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
        $session = session();
        // Usamos una bandera de sesión temporal para permitir el acceso al formulario de configuración
        $session->set('email_verified_for_config', true);

        // Redirigir al usuario al formulario de configuración real
        return redirect()->to('/perfil/config_form')->with('success', 'Email verificado exitosamente. Ahora puedes actualizar tu perfil.');
    }

    // Método para mostrar el formulario de configuración REAL (GET /perfil/config_form)
    public function configForm()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        // Verificar si el usuario está logueado y si ha verificado su email para configuración
         if (!$loggedInUserId || !$session->get('email_verified_for_config')) {
             if ($loggedInUserId) {
                 // Si está logueado pero no ha verificado, redirigir a la página de verificación
                 return redirect()->to('/perfil/configuracion')->with('error', 'Por favor, verifica tu email antes de acceder a la configuración.');
             } else {
                 // Si no está logueado, redirigir al login
                 return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
             }
        }

        // Obtener los datos actuales del usuario para prellenar el formulario
        $userData = $this->userModel->find($loggedInUserId);

         if (!$userData) {
            // Si el usuario no se encuentra, destruir sesión y redirigir al login
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Usuario no encontrado.');
        }

        // Prepara los datos del usuario para pasarlos a la vista
         $data['userData'] = [
            'nombre' => $userData['nombre'] ?? '',
            'email' => $userData['email'] ?? ''
        ];

        // Carga la vista del formulario de configuración
        return view('perfil/configuracion_form', $data);
    }


    // Método para procesar la actualización del perfil (POST /perfil/actualizar)
    public function actualizar()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

         // Verificar si el usuario está logueado y si ha verificado su email para configuración
         if (!$loggedInUserId || !$session->get('email_verified_for_config')) {
             if ($loggedInUserId) {
                 // Si está logueado pero no ha verificado, redirigir a la página de verificación
                 return redirect()->to('/perfil/configuracion')->with('error', 'Por favor, verifica tu email antes de actualizar tu perfil.');
             } else {
                 // Si no está logueado, redirigir al login
                 return redirect()->to('/login')->with('error', 'Debes iniciar sesión para actualizar tu perfil.');
             }
        }

        // Verificar que la solicitud sea POST
        $requestMethod = $this->request->getMethod();
        if (strcasecmp($requestMethod, 'post') !== 0) {
            // Si no es POST, redirigir a la página de configuración
            return redirect()->to('/perfil/configuracion');
        }

        // Definir reglas de validación para los campos del formulario
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
                // Regla is_unique con exclusión del ID del usuario actual para permitir actualizar sin cambiar email
                'rules' => "required|valid_email|max_length[100]|is_unique[usuarios.email,id,{$loggedInUserId}]",
                 'errors' => [
                    'required' => 'El campo Email es obligatorio.',
                    'valid_email' => 'Por favor, ingresa un Email válido.',
                    'max_length' => 'El Email no puede exceder los 100 caracteres.',
                    'is_unique' => 'Este Email ya está registrado por otro usuario.'
                ]
            ],
        ];

        // Ejecutar la validación
        if (! $this->validate($rules)) {
            // Si la validación falla, redirigir de vuelta al formulario con errores y datos antiguos
            return redirect()->to('/perfil/config_form')->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Error de validación. Por favor, revisa los datos.');
        }

        // Obtener los datos del formulario
        $nombre = $this->request->getPost('nombre');
        $email = $this->request->getPost('email');

        // Preparar los datos para la actualización en la base de datos
        $updateData = [
            'nombre' => $nombre,
            'email'  => $email,
            // 'updated_at' se actualizará automáticamente si useTimestamps es true en el modelo.
        ];

        // Actualizar los datos del usuario en la base de datos
        $updated = $this->userModel->update($loggedInUserId, $updateData);

        if ($updated) {
            // Si la actualización fue exitosa, actualizar los datos en la sesión
            $session->set('nombre', $nombre);
            $session->set('email', $email);
            // Eliminar la bandera temporal de verificación de email para configuración
            $session->remove('email_verified_for_config');

            // Redirigir a la página de éxito
            return redirect()->to('/perfil/cambio-exitoso')->with('success', '¡Configuración actualizada exitosamente!');
        } else {
             // Si el update() devuelve false, hubo un problema al guardar
             return redirect()->to('/perfil/config_form')->withInput()->with('error', 'Hubo un error al intentar actualizar la configuración.');
        }
    }

    // Método para mostrar la página de éxito después de actualizar el perfil (GET /perfil/cambio-exitoso)
    public function cambioExitoso()
    {
        $session = session();
        // Verificar si hay un mensaje de éxito en la sesión flashdata (para evitar acceso directo)
        if (!session('success')) {
             // Si no hay mensaje de éxito, redirigir a la página de perfil
             return redirect()->to('/perfil')->with('error', 'Acceso no autorizado a la página de éxito.');
        }
        // Carga la vista de cambio exitoso
        return view('perfil/cambio_exitoso');
    }

    // --- FIN MÉTODOS FLUJO VERIFICACIÓN Y CONFIGURACIÓN ---


    // --- MÉTODOS PARA GESTIÓN DE DISPOSITIVOS ---

    // Método para mostrar el formulario de edición de un dispositivo (GET /perfil/dispositivo/editar/(:segment))
    public function editDevice($mac = null)
    {
        $session = session();
        $usuarioId = $session->get('id');

        // Redirigir si el usuario no está logueado
        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        // Verificar si se proporcionó una MAC en la URL
        if ($mac === null) {
            return redirect()->to('/perfil')->with('error', 'MAC del dispositivo no especificada.');
        }

        // Verificar si la MAC proporcionada está enlazada al usuario actual
        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        // Si no hay enlace para esta MAC y este usuario, denegar el acceso
        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        // Obtener los detalles completos del dispositivo usando la MAC
        $dispositivo = $this->dispositivoModel->getDispositivoByMac($mac);

        // Verificar si se encontró el dispositivo (esto debería pasar si el enlace existe)
        if (!$dispositivo) {
            // Esto no debería pasar si el enlace existe, pero es una buena práctica verificar
            return redirect()->to('/perfil')->with('error', 'Dispositivo no encontrado.');
        }

        // Cargar la vista del formulario de edición y pasar los datos del dispositivo
        return view('perfil/edit_device', [
            'dispositivo' => $dispositivo // Pasar todos los datos del dispositivo encontrado
        ]);
    }

    // Método para procesar el formulario de edición de un dispositivo (POST /perfil/dispositivo/actualizar)
    public function updateDevice()
    {
        $session = session();
        $usuarioId = $session->get('id');

        // Redirigir si el usuario no está logueado
        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        // Obtener los datos del formulario (MAC oculta, nombre, ubicacion)
        $mac = $this->request->getPost('mac'); // Necesitamos la MAC para identificar el dispositivo a actualizar
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

        // Ejecutar la validación
        if (! $this->validate($rules)) {
            // Si la validación falla, redirigir de vuelta al formulario de edición con errores y datos antiguos
            // Redirigimos a la ruta de edición, pasando la MAC como segmento de URL para que se cargue el formulario correcto
            return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Error de validación. Por favor, revisa los datos.');
        }

        // Verificar si la MAC está enlazada al usuario actual antes de permitir la actualización
        $enlace = $this->enlaceModel
                        ->where('id_usuario', $usuarioId)
                        ->where('MAC', $mac)
                        ->first();

        // Si no hay enlace para esta MAC y este usuario, denegar la actualización
        if (!$enlace) {
            return redirect()->to('/perfil')->with('error', 'No tienes permiso para editar este dispositivo.');
        }

        // Preparar los datos para la actualización del dispositivo
        $updateData = [
            'nombre' => $nombre,
            'ubicacion' => $ubicacion,
            // 'updated_at' se actualizará automáticamente si useTimestamps es true en el modelo DispositivoModel.
        ];

        // Usar el modelo de dispositivo para realizar la actualización por MAC
        // El método updateDispositivoByMac ya maneja la condición WHERE MAC = $mac
        $updated = $this->dispositivoModel->updateDispositivoByMac($mac, $updateData);

        if ($updated) {
            // Si la actualización fue exitosa, redirigir de vuelta a la página principal del perfil con un mensaje de éxito
            return redirect()->to('/perfil')->with('success', "¡Dispositivo '{$nombre}' actualizado exitosamente!");
        } else {
             // Si el update() devuelve false, hubo un problema al guardar
             // Redirigir de vuelta al formulario de edición con un mensaje de error genérico
             return redirect()->to("/perfil/dispositivo/editar/{$mac}")->withInput()->with('error', 'Hubo un error al intentar actualizar el dispositivo.');
        }
    }


    // Método para eliminar dispositivos (desenlazar del usuario) (POST /perfil/eliminar-dispositivos)
     public function eliminarDispositivos()
    {
        $session = session();
        $usuarioId = $session->get('id');

        // Redirigir si el usuario no está logueado
        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        // Obtener el array de MACs seleccionadas para eliminar del formulario
        $macs_a_eliminar = $this->request->getPost('macs');

        // Verificar que se hayan seleccionado MACs y que sea un array válido
        if (!empty($macs_a_eliminar) && is_array($macs_a_eliminar)) {
            // Eliminar los enlaces del usuario actual a estas MACs de la tabla 'enlace'
            // Usamos whereIn para eliminar múltiples registros basados en la lista de MACs
            $this->enlaceModel->where('id_usuario', $usuarioId)
                              ->whereIn('MAC', $macs_a_eliminar)
                              ->delete();

            // NOTA IMPORTANTE sobre la eliminación:
            // Tu script SQL muestra que la tabla 'enlace' tiene una clave foránea a 'dispositivos' con ON DELETE CASCADE.
            // También 'lecturas_gas' tiene una clave foránea a 'dispositivos' con ON DELETE CASCADE.
            // Esto significa que si ELIMINAS UN REGISTRO de la tabla 'dispositivos',
            // los registros relacionados en 'enlace' y 'lecturas_gas' se eliminarán automáticamente.
            //
            // Tu lógica actual ELIMINA REGISTROS de la tabla 'enlace'.
            // Si la tabla 'enlace' tuviera una clave foránea a 'usuarios' con ON DELETE CASCADE,
            // eliminar un usuario eliminaría sus enlaces.
            //
            // La lógica actual de eliminar de 'enlace' SOLO DESENLAZA el dispositivo del usuario.
            // El dispositivo en la tabla 'dispositivos' y sus lecturas en 'lecturas_gas' PERMANECEN.
            // Esto es probablemente lo que quieres: desenlazar, no eliminar el dispositivo globalmente.
            // Si quisieras eliminar el dispositivo COMPLETO si ya no está enlazado a NINGÚN usuario,
            // necesitarías lógica adicional después de eliminar el enlace para verificar si quedan enlaces.
            //
            // Por ahora, la lógica de eliminar de 'enlace' es correcta para la funcionalidad de desenlazar.

            return redirect()->to('/perfil')->with('success', 'Dispositivos desenlazados correctamente.');
        } else {
            // Si no se seleccionaron MACs, redirigir con un mensaje de error
            return redirect()->to('/perfil')->with('error', 'No se seleccionaron dispositivos para desenlazar.');
        }
    }
}
