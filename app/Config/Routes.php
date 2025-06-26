<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN (Las que ya tenías) ---
$routes->get('/register', 'registerController::index');
$routes->post('/register/store', 'registerController::store');
$routes->get('/register/check-email', 'registerController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');
$routes->post('/login', 'Home::login');
$routes->get('/loginobtener', 'Home::loginobtener');
$routes->post('/logout', 'Home::logout');

// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

// Perfil (Agrupamos rutas relacionadas con PerfilController)
$routes->group('perfil', function($routes) {
    $routes->get('/', 'PerfilController::index');
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');
    $routes->get('config_form', 'PerfilController::configForm');
    $routes->post('actualizar', 'PerfilController::actualizar');
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');
});

// --- RUTAS DE LA API (EXISTENTES - para ESP32) ---
$routes->group('api', function($routes){
    // Endpoint para controlar la válvula (abrir/cerrar) desde la web
    // Este es el endpoint que tu anterior ValveController usaba para la PWA.
    // Si ya no lo usas, puedes eliminarlo, o mantenerlo si otras partes lo referencian.
    // $routes->post('controlValve', 'ValveController::controlValve');

    // Endpoint para que el ESP32 (o la web) consulte el estado de la válvula y el nivel de gas
    $routes->get('getValveState/(:segment)', 'ValveController::getValveState/$1');

    // Endpoint para que el ESP32 envíe las lecturas del sensor de gas
    $routes->post('receiveSensorData', 'ValveController::receiveSensorData');
});
// --- FIN RUTAS DE LA API EXISTENTES ---


// --- NUEVAS RUTAS ESPECÍFICAS PARA EL CONTROL DEL SERVO DESDE LA WEB ---
// Creando un nuevo grupo 'web' para estos endpoints para mayor claridad.
$routes->group('web', function($routes){
    // Endpoint para que la PWA envíe el comando de abrir/cerrar servo
    // POST /web/controlServo
    $routes->post('controlServo', 'ServoController::controlServoFromWeb');

    // Endpoint para que la PWA consulte el estado actual del servo (opcional, si necesitas mostrarlo en la UI)
    // GET /web/getServoState/MAC_DEL_DISPOSITIVO
    $routes->get('getServoState/(:segment)', 'ServoController::getServoStateFromWeb/$1');
});
// --- FIN NUEVAS RUTAS DE CONTROL DE SERVO ---


// --- OTRAS RUTAS DE LA APLICACIÓN (Las que ya tenías) ---

// La siguiente ruta ha sido comentada ya que su funcionalidad
// de recibir lecturas de gas del ESP32 ahora es manejada por
// el endpoint 'api/receiveSensorData' bajo el 'ValveController'.
// Si otros módulos la utilizan, deberías migrar su uso al nuevo endpoint de la API.
// $routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');


// Vista para enlazar dispositivos (formulario para ingresar MAC)
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');

// Detalles del dispositivo (mostrar lecturas, etc.)
// Nota: La vista 'detalles' cargará la interfaz PWA para el control de la válvula.
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');

// Ruta para la vista de "comprar"
$routes->get('/comprar', 'Home::comprar');

// Ruta para la PWA
$routes->get('/instalar-pwa', 'Home::instalarPWA');
