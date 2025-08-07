<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('simulacion', 'Home::simulacion');

// Rutas de login y registro que ahora apuntan a la vista unificada
$routes->get('/register', 'Home::showLoginRegister');
$routes->get('/login', 'Home::showLoginRegister');

// Rutas POST para el procesamiento de los formularios (no cambian)
$routes->post('/register/store', 'registerController::store');
$routes->post('/login', 'Home::login');

// Otras rutas de registro
$routes->get('/register/check-email', 'registerController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Rutas de login (si existen)
$routes->get('/loginobtener', 'Home::loginobtener');

// Ruta para cerrar sesión
$routes->post('/logout', 'Home::logout');

// RECUPERACIÓN DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotpassword1');

// PERFIL
$routes->group('perfil', function($routes) {
    $routes->get('form', 'PerfilController::form');
    $routes->get('cambiar-email', 'PerfilController::cambiarEmail');
    $routes->post('cambiar-email-proceso', 'PerfilController::cambiarEmailProceso');
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

// OTRAS RUTAS
$routes->post('/cambiar-idioma', 'LanguageController::changeLanguage');
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');
$routes->get('/dispositivo/(:segment)', 'LecturasController::detalle/$1');
$routes->get('/comprar', 'Home::comprar');

// NUEVAS RUTAS AÑADIDAS
$routes->group('registros-gas', function($routes) {
    $routes->get('/', 'RegistrosGasController::index');
    $routes->get('(:any)', 'RegistrosGasController::index');
});