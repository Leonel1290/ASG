<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('simulacion', 'Home::simulacion');
// --- REGISTRATION AND LOGIN ROUTES (ADJUSTED FOR VERIFICATION) ---

// Route to display the registration form
$routes->get('/register', 'registerController::index');

// Route to process the registration form
// POST /register/store (Matches the form action in register.php)
$routes->post('/register/store', 'registerController::store');

// Route to display the page telling the user to check their email after registration
$routes->get('/register/check-email', 'registerController::checkEmail');

// Route to verify the token received by email for REGISTRATION
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Route to process the login form
$routes->post('/login', 'Home::login');

$routes->get('/login', 'Home::login');

// Route to display the login form (if you use loginobtener for this)
$routes->get('/loginobtener', 'Home::loginobtener');

// Route to log out (using POST for better security)
$routes->post('/logout', 'Home::logout');


// PASSWORD RECOVERY
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1'); 
$routes->post('/reset-password', 'Home::resetPassword'); 

// Perfil (Agrupamos rutas relacionadas con PerfilController)
$routes->group('perfil', function($routes) {
    $routes->get('/', 'PerfilController::index'); // Esta es la ruta principal para el perfil
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


// Ruta para cambiar el idioma
$routes->post('/cambiar-idioma', 'LanguageController::changeLanguage');

// Ruta para guardar lecturas de gas
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');


// Vista para enlazar dispositivos (formulario para ingresar MAC)
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');

// Detalles del dispositivo (mostrar lecturas, etc.)
// CORRECCIÓN CLAVE 1: Usamos (.+) para permitir la MAC con ':'
$routes->get('/detalles/(.+)', 'DetalleController::detalles/$1');

// Ruta para la vista de "comprar"
$routes->get('/comprar', 'Home::comprar');

// Ruta para la PWA
$routes->get('/instalar-pwa', 'Home::instalarPWA');


// NUEVAS RUTAS AÑADIDAS (registros-gas)
$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    // CORRECCIÓN CLAVE 2: Usar (.+) para la MAC
    $routes->get('(.+)', 'RegistrosGasController::verDispositivo/$1'); 
});


$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});


$routes->post('paypal/create-order', 'CompraController::createOrder');
$routes->post('paypal/capture-order/(:any)', 'CompraController::captureOrder/$1');


// CORRECCIÓN CLAVE 3: Usar (.+) para la MAC en obtenerUltimaLectura
$routes->get('lecturas/obtenerUltimaLectura/(.+)', 'Lecturas::obtenerUltimaLectura/$1');


// RUTAS DE SERVOS (Interfaz de Usuario/AJAX - Agrupadas y Corregidas)
$routes->group('servo', function($routes) {
    $routes->get('/', 'ServoController::index');
    $routes->post('abrir', 'ServoController::abrir');
    $routes->post('cerrar', 'ServoController::cerrar');
    // CORRECCIÓN CLAVE 4: Usamos (.+) para la MAC en obtenerEstado
    $routes->get('obtenerEstado/(.+)', 'ServoController::obtenerEstado/$1'); 
    $routes->post('actualizarEstado', 'ServoController::actualizarEstado');
});

// RUTA DEDICADA PARA EL DISPOSITIVO (ESP32) 🔑
// CORRECCIÓN CLAVE 5: Apuntamos la API al controlador seguro ApiEspController
$routes->get('/api/valve_status', 'ApiEspController::estadoValvula');