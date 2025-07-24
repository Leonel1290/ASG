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

// --- END REGISTRATION AND LOGIN ROUTES ---


// Profile (Group routes related to PerfilController)
$routes->group('perfil', function($routes) {
    // Route for the main profile page
    // GET /perfil
    $routes->get('/', 'PerfilController::index');

    // --- ROUTES FOR PROFILE VERIFICATION AND CONFIGURATION FLOW ---

    // Route to display the INITIAL configuration page (asks to verify email)
    // GET /perfil/configuracion
    $routes->get('configuracion', 'PerfilController::configuracion');

    // Route to process sending the verification email for CONFIGURATION
    // POST /perfil/enviar-verificacion
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');

    // Route to verify the token received by email for CONFIGURATION
    // GET /perfil/verificar-email/THE_GENERATED_TOKEN
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');

    // Route to display the REAL configuration form (accessible after email verification)
    // GET /perfil/config_form
    $routes->get('config_form', 'PerfilController::configForm');

    // Route to process profile update (name/email)
    // POST /perfil/actualizar
    $routes->post('actualizar', 'PerfilController::actualizar');

    // Route for the success page after profile update
    // GET /perfil/cambio-exitoso
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');

    // Route to change language
    $routes->match(['get', 'post'], 'cambiar-idioma', 'PerfilController::cambiarIdioma');

    // --- END PROFILE VERIFICATION AND CONFIGURATION ROUTES ---


    // --- ROUTES FOR DEVICE MANAGEMENT FROM PROFILE ---

    // Route to display the device editing form
    // GET /perfil/dispositivo/editar/DEVICE_MAC
    // (:segment) captures the MAC from the URL
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');

    // Route to process the device editing form
    // POST /perfil/dispositivo/actualizar
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');

    // Route to delete selected devices (unlink from user)
    // POST /perfil/eliminar-dispositivos
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');


    // --- END DEVICE MANAGEMENT ROUTES FROM PROFILE ---

});


// Receive data from ESP32:
// POST /lecturas_gas/guardar
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

// View to link devices (form to enter MAC)
// GET /enlace
$routes->get('/enlace', 'EnlaceController::index');
// Action to link MACs (processes the /enlace form)
// POST /enlace/store
$routes->post('/enlace/store', 'EnlaceController::store');

// --- DEVICE AND GAS RECORD ROUTES ---

// CRUCIAL ROUTE: Route for the detail view of a specific device (with chart and latest reading)
// Esta ruta ahora apunta a DetalleController::detalles para manejar el filtrado de fechas.
// Si tenías una ruta similar para LecturasController::detalle, asegúrate de que no haya conflictos.
$routes->get('detalles/(:any)', 'DetalleController::detalles/$1');

// Ruta original comentada para evitar conflictos, ya que ahora 'detalles' la manejará
// $routes->get('/dispositivo/(:segment)', 'LecturasController::detalle/$1');


// New route for the view of all gas records of a specific device
// If you want a separate view just for historical records without the large chart,
// you can point this to another method in LecturasController or a new controller.
// Commented out to avoid potential conflicts with the /dispositivo route if not distinct.
// $routes->get('/registros-gas/(:segment)', 'DeviceController::showGasRecords/$1');

// --- END DEVICE AND GAS RECORD ROUTES ---


// --- DIRECT VIEW ROUTES ---

// Route for the "comprar" view
$routes->get('/comprar', 'Home::comprar');

// Temporary migration route (delete after use)
// $routes->get('migrar-datos', 'DetalleController::migrarDatos'); // Comment or delete after migration.

// Routes for the new system (if RegistrosGasController exists for other functionality)
$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    $routes->get('(:any)', 'RegistrosGasController::verDispositivo/$1');
});


// --- PREVIOUS ROUTES (COMMENTED FOR CLARITY AND TO AVOID DUPLICATES) ---
// The following routes are commented because they are likely redundant or do not correspond
// to methods in the current controllers, or are already covered by the routes above.

// $routes->get('/inicioobtener', 'Home::inicioobtener');
// $routes->get('/loginobtenerforgot', 'Home::loginobtenerforgot');
// $routes->get('/inicioresetpass', 'Home::inicioresetpass');
// $routes->get('/obtenerperfil', 'Home::obtenerperfil');
// $routes->get('/mac/(:segment)', 'Home::verLecturas/$1');
// $routes->post('/actualizar-dispositivo', 'DispositivoController::actualizarDispositivo');
// If Home::perfil() and EnlaceController::store() duplicate PerfilController::index() and EnlaceController::store(),
// remove the duplicate methods in Home.php.

$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});