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
$routes->post('/forgotpassword1', 'Home::forgotPPassword'); // Assume this is the route that processes the forgot password form

// CORRECCIÓN 1: No es necesario cambiar (:any) ya que el token no lleva ':'
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword'); // Assume this is the route that processes the reset password form

// CORRECCIÓN 2: Cambiado (:any) a (.+) para permitir la MAC con ':'
$routes->get('detalles/(.+)', 'DetalleController::detalles/$1');


// Rutas para el perfil y dispositivos (PerfilController)
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

$routes->post('/cambiar-idioma', 'LanguageController::changeLanguage');

$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

$routes->get('/enlace', 'EnlaceController::index');

$routes->post('/enlace/store', 'EnlaceController::store');


$routes->get('/dispositivo/(:segment)', 'LecturasController::detalle/$1');


$routes->get('/comprar', 'Home::comprar');

// NUEVAS RUTAS AÑADIDAS
$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    // CORRECCIÓN 3: Cambiado (:any) a (.+) para permitir la MAC con ':'
    $routes->get('(.+)', 'RegistrosGasController::verDispositivo/$1');
});


$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});


$routes->post('paypal/create-order', 'CompraController::createOrder');
$routes->post('paypal/capture-order/(:any)', 'CompraController::captureOrder/$1');

// CORRECCIÓN 4: Cambiado (:any) a (.+) para permitir la MAC con ':'
$routes->get('lecturas/obtenerUltimaLectura/(.+)', 'Lecturas::obtenerUltimaLectura/$1');

// RUTAS DE SERVOS (AGREGAMOS LAS FALTANTES QUE CAUSABAN 404 EN AJAX)
$routes->get('/servo', 'ServoController::index');
$routes->post('/servo/abrir', 'ServoController::abrir');
$routes->post('/servo/cerrar', 'ServoController::cerrar');
// RUTA AÑADIDA: Para obtener el estado (el 404 que reportaste en tu último mensaje)
// CORRECCIÓN 5: Usamos (.+) para aceptar la MAC con ':'
$routes->get('servo/obtenerEstado/(.+)', 'ServoController::obtenerEstado/$1'); 
// RUTA AÑADIDA: Para actualizar el estado (el 404 que reportaste en tu penúltimo mensaje)
$routes->post('servo/actualizarEstado', 'ServoController::actualizarEstado'); 
$routes->get('/api/valve_status', 'ServoController::obtenerEstadoValvula');