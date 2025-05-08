<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/register', 'Home::register');
$routes->get('/logout', 'Home::logout');

$routes->get('/register', 'Home::register'); // Vista de registro
$routes->get('/loginobtener', 'Home::loginobtener');
$routes->post('/registerController/store', 'RegisterController::store'); // Acción del formulario de registro

// RECUPERACION DE CONTRASEÑA
$routes->get('/login', 'Home::loginobtenerforgot');
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->get('/inicioobtener', 'Home::inicioobtener');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

$routes->post('/login', 'Home::login');

// Perfil
$routes->get('/perfil', 'PerfilController::index'); // Ruta correcta para el perfil
$routes->post('/perfil/eliminar-dispositivos', 'PerfilController::eliminarDispositivos'); // Nueva ruta

// Recibir los datos de la ESP32:
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

// Vista para dispositivos
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');

// Detalles del dispositivo
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');  // Ruta correcta para detalles

// Otras rutas
$routes->get('/comprar', 'Home::comprar');
$routes->post('/enlazar-mac', 'EnlaceController::store');
$routes->get('/mac/(:segment)', 'Home::verLecturas/$1'); // Si esta ruta existe en tu controlador 'Home'
$routes->post('/actualizar-dispositivo', 'DispositivoController::actualizarDispositivo');
