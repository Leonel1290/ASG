<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN (AJUSTADAS PARA VERIFICACIÓN) ---

// Ruta para mostrar el formulario de registro
$routes->get('/register', 'registerController::index');

// Ruta para procesar el formulario de registro
// POST /register/store (Coincide con la action del formulario en register.php)
$routes->post('/register/store', 'registerController::store');

// Ruta para mostrar la página que le dice al usuario que revise su email después del registro
$routes->get('/register/check-email', 'registerController::checkEmail');

// Ruta para verificar el token recibido por email para REGISTRO
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Ruta para procesar el formulario de login
$routes->post('/login', 'Home::login');

// Ruta para mostrar el formulario de login (si usas loginobtener para esto)
$routes->get('/loginobtener', 'Home::loginobtener');

// Ruta para cerrar sesión (usando POST para mayor seguridad)
$routes->post('/logout', 'Home::logout');


// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword'); // Asumo que esta es la ruta que procesa el formulario de forgot password
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword'); // Asumo que esta es la ruta que procesa el formulario de reset password

// --- FIN RUTAS DE REGISTRO Y LOGIN ---


// Perfil (Agrupamos rutas relacionadas con PerfilController)
$routes->group('perfil', function($routes) {
    // Ruta para la página principal del perfil
    // GET /perfil
    $routes->get('/', 'PerfilController::index');

    // --- RUTAS PARA EL FLUJO DE VERIFICACIÓN Y CONFIGURACIÓN DE PERFIL ---

    // Ruta para mostrar la página INICIAL de configuración (pide verificar email)
    // GET /perfil/configuracion
    $routes->get('configuracion', 'PerfilController::configuracion');

    // Ruta para procesar el envío del correo de verificación para CONFIGURACIÓN
    // POST /perfil/enviar-verificacion
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');

    // Ruta para verificar el token recibido por email para CONFIGURACIÓN
    // GET /perfil/verificar-email/EL_TOKEN_GENERADO
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');

    // Ruta para mostrar el formulario de configuración REAL (accesible después de verificar email)
    // GET /perfil/config_form
    $routes->get('config_form', 'PerfilController::configForm');

    // Ruta para procesar la actualización del perfil (nombre/email)
    // POST /perfil/actualizar
    $routes->post('actualizar', 'PerfilController::actualizar');

    // Ruta para la página de éxito después de actualizar el perfil
    // GET /perfil/cambio-exitoso
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');

    // --- FIN RUTAS VERIFICACIÓN Y CONFIGURACIÓN DE PERFIL ---


    // --- RUTAS PARA GESTIÓN DE DISPOSITIVOS DESDE EL PERFIL ---

    // Ruta para mostrar el formulario de edición de un dispositivo
    // GET /perfil/dispositivo/editar/MAC_DEL_DISPOSITIVO
    // (:segment) captura la MAC de la URL
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');

    // Ruta para procesar el formulario de edición de un dispositivo
    // POST /perfil/dispositivo/actualizar
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');

    // Ruta para eliminar dispositivos seleccionados (desenlazar del usuario)
    // POST /perfil/eliminar-dispositivos
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');


    // --- FIN RUTAS GESTIÓN DE DISPOSITIVOS DESDE EL PERFIL ---

});


// Recibir los datos de la ESP32:
// POST /lecturas_gas/guardar
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

// Vista para enlazar dispositivos (formulario para ingresar MAC)
// GET /enlace
$routes->get('/enlace', 'EnlaceController::index');
// Acción para enlazar MACs (procesa el formulario de /enlace)
// POST /enlace/store
$routes->post('/enlace/store', 'EnlaceController::store');

// --- RUTAS DE DISPOSITIVOS Y REGISTROS DE GAS ---

// Ruta para la nueva vista que lista todos los dispositivos (el "menú")
// GET /dispositivos
$routes->get('/dispositivos', 'DeviceController::listDevices');

// Ruta para la vista de detalle de un dispositivo específico (con gráfico y última lectura)
// GET /dispositivo/LA_MAC_DEL_DISPOSITIVO
$routes->get('/dispositivo/(:segment)', 'DeviceController::showDeviceDetail/$1');

// Nueva ruta para la vista de todos los registros de gas de un dispositivo específico
// GET /registros-gas/LA_MAC_DEL_DISPOSITIVO
$routes->get('/registros-gas/(:segment)', 'DeviceController::showGasRecords/$1');

// --- FIN RUTAS DE DISPOSITIVOS Y REGISTROS DE GAS ---


// --- RUTAS DE VISTA DIRECTA ---

// Ruta para la vista de "comprar"
// Descomentamos esta ruta para que la vista sea accesible
$routes->get('/comprar', 'Home::comprar');

$routes->get('dispositivo/(:segment)/lecturas', 'DeviceController::showAllGasReadings/$1'); // Nueva ruta para el historial completo

// --- RUTAS PREVIAS (COMENTADAS PARA CLARIDAD Y EVITAR DUPLICADOS) ---
// La ruta '/detalles/(:any)' ha sido reemplazada por '/dispositivo/(:segment)' y manejada por DeviceController.
// Si aún la necesitas para otra función de DetalleController, descoméntala y ajusta.
// $routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');

// Estas rutas parecen estar manejadas por otros controladores o no tienen una función clara en Home.
// Si necesitas alguna, descoméntala y asegúrate de que el método exista en Home.php
// $routes->get('/inicioobtener', 'Home::inicioobtener'); // Duplicada con '/' o '/inicio'
// $routes->get('/loginobtenerforgot', 'Home::loginobtenerforgot'); // Duplicada con /forgotpassword
// $routes->get('/inicioresetpass', 'Home::inicioresetpass'); // Duplicada con /reset-password/(:any)
// $routes->get('/obtenerperfil', 'Home::obtenerperfil'); // Parece una vista directa, no una acción de controlador

// NOTA: También tienes un método `perfil()` y `storeMac()` en Home.php
// que parecen duplicados con PerfilController::index y EnlaceController::store.
// Es recomendable usar solo los controladores dedicados (PerfilController y EnlaceController)
// para estas funcionalidades y eliminar los métodos duplicados en Home.php.

// --- RUTAS NO ENCONTRADAS EN CONTROLADORES ADJUNTOS (COMENTADAS) ---
// Estas rutas estaban en tu Routes.php original pero no vimos métodos correspondientes
// en los controladores que proporcionaste.
// $routes->get('/mac/(:segment)', 'Home::verLecturas/$1'); // Método verLecturas no encontrado en Home.php
// $routes->post('/actualizar-dispositivo', 'DispositivoController::actualizarDispositivo'); // Método actualizarDispositivo no encontrado en DispositivoController.php