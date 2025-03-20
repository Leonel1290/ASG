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

//RECUPERACION DE CONTRASEÑA
$routes->get('/login', 'Home::loginobtenerforgot');
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->get('/inicioobtener', 'Home::inicioobtener');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

$routes->post('/login', 'Home::login');

//Perfil
$routes->get('/perfilobtener', 'Home::obtenerperfil');

//Recibir los datos de la ESP32:
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

//Vista para dispositivos
$routes->get('/dispositivos', 'Home::dispositivos');