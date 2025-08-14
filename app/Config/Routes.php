<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN ---
$routes->get('/register', 'registerController::index');
$routes->post('/register/store', 'registerController::store');
$routes->get('/register/check-email', 'registerController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');
$routes->post('/login', 'Home::login');
$routes->get('/loginobtener', 'Home::loginobtener');
$routes->post('/logout', 'Home::logout');

// RECUPERACION DE CONTRASEÑA
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
    $routes->get('config_form', 'PerfilController::configForm');
    $routes->post('actualizar', 'PerfilController::actualizar');
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');
});
// --- FIN RUTAS DE LA API ---


// --- OTRAS RUTAS DE LA APLICACIÓN ---

// La siguiente ruta ha sido comentada, ya que receiveSensorData está en ServoController.
// $routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');


// Vista para enlazar dispositivos (formulario para ingresar MAC)
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');

// Detalles del dispositivo (mostrar lecturas, etc.)
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');

// Ruta para la vista de "comprar"
$routes->get('/comprar', 'Home::comprar');

// Ruta para la PWA
$routes->get('/instalar-pwa', 'Home::instalarPWA');



$routes->get('servo/obtenerEstado/(:any)', 'ServoController::obtenerEstado/$1');
$routes->post('servo/actualizarEstado', 'ServoController::actualizarEstado');

$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');

// RUTA CORREGIDA: Cambiado 'estado_valvula' a 'valve_status' para que coincida con la solicitud del ESP
$routes->get('api/valve_status', 'ApiEspController::estadoValvula');

$routes->get('/simulacion', 'Home::simulacion');