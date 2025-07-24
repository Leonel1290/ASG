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
$routes->get('/reset-password/(:any)', 'Home::resetPassword/$1');
$routes->post('/reset-password', 'Home::processResetPassword'); // Route to process the password reset form


// --- USER PROFILE AND DEVICE MANAGEMENT ROUTES ---

// Route to display the user profile (main dashboard after login)
$routes->get('/perfil', 'PerfilController::index', ['filter' => 'auth']); // Apply auth filter if needed

// Route to handle device linking (POST request)
$routes->post('/enlace/store', 'EnlaceController::store', ['filter' => 'auth']); // Apply auth filter

// Route to display the device linking form (GET request)
$routes->get('/enlace', 'EnlaceController::index', ['filter' => 'auth']); // Apply auth filter

// Routes for editing and deleting devices from the profile
$routes->get('/perfil/dispositivo/editar/(:any)', 'PerfilController::editarDispositivo/$1', ['filter' => 'auth']);
$routes->post('/perfil/dispositivo/actualizar/(:any)', 'PerfilController::actualizarDispositivo/$1', ['filter' => 'auth']);
$routes->post('/perfil/eliminar-dispositivos', 'PerfilController::eliminarDispositivos', ['filter' => 'auth']);

// Route for changing language
$routes->post('/cambiar-idioma', 'PerfilController::cambiarIdioma', ['filter' => 'auth']);


// --- DEVICE DETAIL AND GAS RECORD ROUTES ---

// Route for displaying device details with MAC (and optional date filters)
// THIS IS THE MAIN ROUTE FOR THE 'detalles' VIEW
$routes->get('detalles/(:any)', 'DetalleController::detalles/$1', ['filter' => 'auth']);

// NEW: Route to get the latest gas level for a MAC as JSON
$routes->get('detalles/latest-gas/(:any)', 'DetalleController::getLatestGasLevel/$1', ['filter' => 'auth']);


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
// If Home::perfil() and EnlaceController::store() duplicate PerfilController::index() and EnlaceController::store(), remove the duplicates.

// Example of a route to receive sensor data (if not already handled by LecturasController::guardar)
// $routes->post('/api/gas-reading', 'LecturasController::guardar');