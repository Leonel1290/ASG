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
$routes->get('/loginobtener', 'Home::loginobtener'); // GET para mostrar el formulario
$routes->post('/logout', 'Home::logout');


// --- RUTAS DE RECUPERACIÓN DE CONTRASEÑA ---
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');


// --- RUTAS DE LA APLICACIÓN PRINCIPAL (PROTEGIDAS) ---

// Ruta para el inicio de la aplicación principal (protegida por SessionAdmin)
// Nota: Si /inicio es solo para usuarios logueados, este filtro es correcto.
$routes->get('/inicio', 'Home::inicio', ['filter' => 'SessionAdmin']);

// Rutas del PANEL DE USUARIO (PERFIL) - ¡Este es el objetivo principal de la redirección!
// APLICA EL FILTRO 'SessionAdmin' AQUÍ
$routes->get('/perfil', 'PerfilController::index', ['filter' => 'SessionAdmin']);

// Rutas de configuración de perfil (protegidas por SessionAdmin)
$routes->group('perfil', ['filter' => 'SessionAdmin'], function($routes){
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');
    $routes->get('config_form', 'PerfilController::configForm');
    $routes->post('actualizar', 'PerfilController::actualizar');
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');

    // Rutas de gestión de dispositivos (protegidas por SessionAdmin)
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');
    $routes->post('dispositivo/eliminar', 'PerfilController::eliminarDispositivos');
});

// Rutas de enlace de dispositivos (protegidas por SessionAdmin)
$routes->group('enlace', ['filter' => 'SessionAdmin'], function($routes){
    $routes->get('/', 'EnlaceController::index');
    $routes->post('store', 'EnlaceController::store');
});

// Otras rutas principales (puedes protegerlas con SessionAdmin si es necesario)
$routes->get('/dispositivos', 'DispositivoController::index', ['filter' => 'SessionAdmin']);
$routes->get('/comprar', 'Home::comprar'); // OJO: Si es una página pública, no necesita filtro

// --- RUTAS DE LA API/ESP32 (NO DEBEN LLEVAR FILTROS DE SESIÓN DE USUARIO WEB) ---
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');
$routes->post('/dispositivos/registrar', 'DispositivoController::registrarDispositivo');