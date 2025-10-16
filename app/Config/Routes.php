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
    $routes->get('logout', 'PerfilController::logout');
});

// --- DISPOSITIVOS (AGRUPADAS) ---
$routes->group('dispositivos', function($routes) {
    $routes->get('/', 'DispositivoController::index');
    $routes->post('registrar', 'DispositivoController::registrarDispositivo');
    $routes->post('eliminar', 'DispositivoController::eliminarDispositivo');
});

// --- DETALLES DE LECTURAS (UNIFICADA) ---
$routes->get('detalles/(:any)', 'DetalleController::detalles/$1'); // Esta ruta llama a la vista detalles.php


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
// 💧 RUTAS DE VÁLVULA (CONTROL Y ESTADO UNIFICADO) 💧
// ===================================================================

// Ruta principal para enviar la acción de control (POST /valve/control)
$routes->post('valve/control', 'ValveController::controlValve');

// ✅ RUTA REEMPLAZADA: Obtener estado (reemplaza /servo/obtenerEstado/{MAC})
$routes->get('valve/obtenerEstado/(.+)', 'ValveController::obtenerEstado/$1');

// ✅ RUTA REEMPLAZADA: Actualizar estado (reemplaza /servo/actualizarEstado)
$routes->post('valve/actualizarEstado', 'ValveController::actualizarEstado');


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
// 🤖 RUTAS DE API PARA EL ESP32 (MANTENER) 🤖
// ===================================================================

// Ruta para ENVIAR la lectura de gas (POST /api/send_gas_data)
$routes->post('api/send_gas_data', 'LecturasController::guardar'); 

// Ruta para CONSULTAR el estado de la válvula (GET /api/valve_status)
// Esta ruta es manejada por el archivo get_valve_status.php y NO por un controlador de CodeIgniter.
// La ruta DEBE seguir apuntando al archivo directamente.
// Si esta ruta se define en Routes.php, CI intentará buscar un controlador. 
// Asumo que tu configuración de CI en public/index.php permite que el archivo PHP se ejecute directamente.