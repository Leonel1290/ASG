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
$routes->post('/forgotpassword1', 'Home::forgotpassword1');
$routes->get('/resetpassword/(:segment)', 'Home::resetpassword/$1');
$routes->post('/resetpassword1', 'Home::resetpassword1');


// --- PERFIL ROUTES ---
$routes->group('perfil', function ($routes) {
    $routes->get('/', 'PerfilController::index');
    $routes->get('configuracion', 'PerfilController::configuracion'); // La vista que pide el email

    // Rutas para Dispositivos
    $routes->get('registrar-dispositivo', 'PerfilController::registerLink');
    $routes->post('registrar-dispositivo/store', 'PerfilController::storeLink');
    $routes->get('mis-compras', 'PerfilController::misCompras');
    $routes->get('direccion-envio/(:segment)', 'PerfilController::direccionEnvio/$1');
    $routes->post('direccion-envio/guardar', 'PerfilController::guardarDireccion');
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');

    // === NUEVAS RUTAS DE VERIFICACIÓN DE PERFIL ===
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');
    $routes->get('confirmar-acceso/(:segment)', 'PerfilController::confirmarAcceso/$1');
    // =============================================
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
    $routes->get('(:any)', 'RegistrosGasController::verDispositivo/$1');
});


$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});


$routes->post('paypal/create-order', 'CompraController::createOrder');
$routes->post('paypal/capture-order/(:any)', 'CompraController::captureOrder/$1');