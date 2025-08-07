<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('simulacion', 'Home::simulacion');

// --- RUTAS AJUSTADAS PARA LA VISTA UNIFICADA ---
// Ambas rutas (GET /login y GET /register) ahora apuntan al mismo método.
// Debes asegurarte de tener el método `showLoginRegister` en tu controlador Home.
$routes->get('/register', 'Home::showLoginRegister');
$routes->get('/login', 'Home::showLoginRegister');

// Las rutas POST para procesar los formularios permanecen iguales
$routes->post('/register/store', 'registerController::store');
$routes->post('/login', 'Home::login');


// --- OTRAS RUTAS (NO MODIFICADAS) ---

// Rutas de registro y verificación
$routes->get('/register/check-email', 'registerController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Rutas de login (si existen)
// Mantén esta si la sigues usando en algún otro lugar, pero el enlace principal ya no la usa.
$routes->get('/loginobtener', 'Home::loginobtener');

// Ruta para cerrar sesión
$routes->post('/logout', 'Home::logout');


// RECUPERACIÓN DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword'); 
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword'); 
$routes->get('detalles/(:any)', 'DetalleController::detalles/$1');


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
    $routes->get('(:any)', 'RegistrosGasController::verDispositivo/$1');
});


$routes->get('prueba', function() {
    return '¡Ruta de prueba funcionando!';
});


$routes->post('/home/guardar_compra', 'Home::guardar_compra');