<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ===================================================================
// 🌐 RUTAS DE LA APLICACIÓN WEB 🌐
// ===================================================================

// --- HOME / SIMULACIÓN ---
$routes->get('/', 'Home::index');
$routes->get('simulacion', 'Home::simulacion');

// --- REGISTRATION AND LOGIN ---
$routes->get('/register', 'RegisterController::index');
$routes->post('/register/store', 'RegisterController::store');
$routes->get('/register/check-email', 'RegisterController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'RegisterController::verifyEmailToken/$1');

$routes->get('/login', 'Home::login'); // Vista de Login
$routes->post('/login', 'Home::login'); // Procesar Login
$routes->get('/loginobtener', 'Home::loginobtener'); // Si la usas como alternativa a /login
$routes->post('/logout', 'Home::logout');

// --- PASSWORD RECOVERY ---
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword'); // Asumo 'forgotPPassword' es correcto
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

// --- PERFIL (AGRUPADAS) ---
$routes->group('perfil', function($routes) {
    $routes->get('/', 'PerfilController::index');
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');
    $routes->post('cambiar-contrasena', 'PerfilController::cambiarContrasena');
    $routes->post('eliminar-cuenta', 'PerfilController::eliminarCuenta');
    $routes->get('config_form', 'PerfilController::configForm');
    $routes->post('actualizar', 'PerfilController::actualizar');
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');
});

// --- ENLACE DE DISPOSITIVOS ---
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');

// --- DETALLES DE DISPOSITIVO (Usamos (.+) para aceptar la MAC completa) ---
$routes->get('/detalles/(.+)', 'DetalleController::detalles/$1'); // Más robusto para la MAC

// --- LECTURAS (Solo la ruta web, la API se define más abajo) ---
$routes->get('lecturas/obtenerUltimaLectura/(.+)', 'Lecturas::obtenerUltimaLectura/$1');

// --- REGISTROS DE GAS (AGRUPADAS) ---
$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    $routes->get('(.+)', 'RegistrosGasController::verDispositivo/$1');
});

// --- CONTROL DE VÁLVULA DESDE WEB (UI) ---
$routes->post('valve/control', 'ValveController::controlValve');

// --- SERVOS (AGRUPADAS) ---
$routes->group('servo', function($routes) {
    $routes->get('/', 'ServoController::index');
    $routes->post('abrir', 'ServoController::abrir');
    $routes->post('cerrar', 'ServoController::cerrar');
    $routes->get('obtenerEstado/(.+)', 'ServoController::obtenerEstado/$1'); // Más robusto para la MAC
    $routes->post('actualizarEstado', 'ServoController::actualizarEstado'); // Actualización desde la UI
});

// --- COMPRA / PWA / OTROS ---
$routes->get('/comprar', 'Home::comprar');
$routes->get('/instalar-pwa', 'Home::instalarPWA');
$routes->post('paypal/create-order', 'CompraController::createOrder');
$routes->post('paypal/capture-order/(:any)', 'CompraController::captureOrder/$1');
$routes->post('/cambiar-idioma', 'LanguageController::changeLanguage');
$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});


// ===================================================================
// 🤖 RUTAS DE API PARA EL ESP32 (Solución al problema) 🤖
// ===================================================================

// 1. SOLUCIÓN CLAVE (Error 404): La ruta que el ESP32 estaba buscando manualmente.
// Apunta a tu controlador de API seguro.
$routes->get('api/get_valve_status', 'App\\Controllers\\ApiEspController::estadoValvula');

// 2. Ruta para OBTENER el estado de la válvula (Alternativa/Oficial de tu código).
// Mantenemos esta ya que el controlador ApiEspController está diseñado para esto.
$routes->get('api/valve_status', 'App\\Controllers\\ApiEspController::estadoValvula');

// 3. Ruta para ENVIAR la lectura de gas (Alineada con el código Python).
$routes->post('api/send_gas_data', 'App\\Controllers\\LecturasController::guardar');
// Eliminamos la ruta duplicada o antigua: /lecturas_gas/guardar

// ===================================================================
// FIN DE RUTAS
// ===================================================================