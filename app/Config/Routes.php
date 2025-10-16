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

$routes->get('/login', 'Home::login'); 
$routes->post('/login', 'Home::login'); 
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
    $routes->get('logout', 'PerfilController::logout');
});

// --- DISPOSITIVOS (AGRUPADAS) ---
$routes->group('dispositivos', function($routes) {
    $routes->get('/', 'DispositivoController::index');
    $routes->post('registrar', 'DispositivoController::registrarDispositivo');
    $routes->post('eliminar', 'DispositivoController::eliminarDispositivo');
});

// --- DETALLES DE LECTURAS (UNIFICADA) ---
$routes->get('detalles/(:any)', 'DetalleController::detalles/$1'); 


// --- LECTURAS (AGRUPADAS) ---
$routes->group('lecturas', function($routes) {
    $routes->get('/', 'Lecturas::index');
    $routes->get('obtenerUltimaLectura/(.+)', 'Lecturas::obtenerUltimaLectura/$1');
});

// --- REGISTROS DE GAS (AGRUPADAS) ---
$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    $routes->get('(.+)', 'RegistrosGasController::verDispositivo/$1');
});


// ===================================================================
// 💧 RUTAS DE VÁLVULA (CONTROL Y ESTADO UNIFICADO EN ValveController) 💧
// ===================================================================

// RUTA PARA CONTROL DESDE BOTONES WEB (POST)
$routes->post('valve/control', 'ValveController::controlValve');

// RUTA DE ACTUALIZACIÓN (JSON) - Usada por AJAX de botones (POST)
$routes->post('valve/actualizarEstado', 'ValveController::actualizarEstado');


// ===================================================================
// 🤖 RUTAS DE API PARA EL ESP32 y PWA (¡LA CORRECCIÓN DEL 404!) 🤖
// ===================================================================

// RUTA CRÍTICA: /api/valve_status (GET) - Soluciona el 404 para el ESP32 y la PWA
$routes->get('api/valve_status', 'ValveController::obtenerEstadoSimple'); 

// Ruta para ENVIAR la lectura de gas (POST /api/send_gas_data)
$routes->post('api/send_gas_data', 'LecturasController::guardar'); 


// --- COMPRA / PWA / OTROS ---
$routes->get('/comprar', 'Home::comprar');
$routes->get('/instalar-pwa', 'Home::instalarPWA');
$routes->post('paypal/create-order', 'CompraController::createOrder');
$routes->post('paypal/capture-order/(:any)', 'CompraController::captureOrder/$1');
$routes->post('/cambiar-idioma', 'LanguageController::changeLanguage');
$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});