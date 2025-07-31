<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

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

// Route to display the login form (if you use loginobtener for this)
$routes->get('/loginobtener', 'Home::loginobtener');

// Route to log out (using POST for better security)
$routes->post('/logout', 'Home::logout');


// PASSWORD RECOVERY
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword'); // Assume this is the route that processes the forgot password form
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword'); // Assume this is the route that processes the reset password form
$routes->get('detalles/(:any)', 'DetalleController::detalles/$1');


// Profile (Group routes related to PerfilController)
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

$routes->post('/cambiar-idioma', 'LanguageController::changeLanguage');

$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

$routes->get('/enlace', 'EnlaceController::index');

$routes->post('/enlace/store', 'EnlaceController::store');


$routes->get('/dispositivo/(:segment)', 'LecturasController::detalle/$1');


$routes->get('/comprar', 'Home::comprar');


$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    $routes->get('(:any)', 'RegistrosGasController::verDispositivo/$1');
});


$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});

$routes->post('guardar_compra', 'Compras::guardarCompra');