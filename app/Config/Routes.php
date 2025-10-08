<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ===================================================================
// 🌐 RUTAS DE LA APLICACIÓN WEB (Limpias y Agrupadas) 🌐
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
$routes->get('/loginobtener', 'Home::loginobtener');
$routes->post('/logout', 'Home::logout');

// --- PASSWORD RECOVERY ---
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
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

// --- DETALLES DE DISPOSITIVO (Usamos (.+) para aceptar la MAC) ---
$routes->get('/detalles/(.+)', 'DetalleController::detalles/$1');

// --- HISTORIAL DE ALERTAS ---
$routes->get('/alertas', 'AlertasController::index');

// --- LECTURAS ---
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
    $routes->get('obtenerEstado/(.+)', 'ServoController::obtenerEstado/$1');
    $routes->post('actualizarEstado', 'ServoController::actualizarEstado');
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
// 🤖 RUTAS DE API PARA EL ESP32 (¡CORREGIDAS!) 🤖
// ===================================================================

// CORRECCIÓN CLAVE 1: Ruta para ENVIAR la lectura de gas (POST /api/send_gas_data)
// Se eliminó el namespace 'App\\Controllers\\' para evitar la duplicación que causaba el 404.
$routes->post('api/send_gas_data', 'LecturasController::guardar');


$routes->get('api/get_valve_status', 'ApiEspController::estadoValvula');


$routes->get('api/valve_status', 'ApiEspController::estadoValvula');

