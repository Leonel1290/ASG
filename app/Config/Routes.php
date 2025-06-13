<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN GENERALES ---
$routes->get('/register', 'registerController::index');
$routes->post('/register/store', 'registerController::store');
$routes->get('/register/check-email', 'registerController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

$routes->post('/login', 'Home::login');
$routes->get('/login', 'Home::login'); // Cambiado de 'loginobtener' a 'login' para consistencia

$routes->post('/logout', 'Home::logout');


// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

// --- FIN RUTAS DE REGISTRO Y LOGIN GENERALES ---


// --- RUTAS ESPECÍFICAS PARA COMPRA/PAYPAL ---
$routes->get('/login/paypal', 'Home::loginPaypal'); // Muestra el formulario de login para PayPal
$routes->post('/login/paypal', 'Home::processLoginPaypal'); // Procesa el login para PayPal

$routes->get('/register/paypal', 'registerController::registerPaypal'); // Muestra el formulario de registro para PayPal
$routes->post('/register/paypal/store', 'registerController::storePaypal'); // Procesa el registro para PayPal
// --- FIN RUTAS ESPECÍFICAS PARA COMPRA/PAYPAL ---


// Perfil (Agrupamos rutas relacionadas al perfil)
$routes->group('perfil', function ($routes) {
    $routes->get('/', 'PerfilController::index');
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('actualizar-perfil', 'PerfilController::actualizarPerfil');
    $routes->post('cambiar-contrasena', 'PerfilController::cambiarContrasena');
    $routes->get('verificar-email', 'PerfilController::verificarEmail'); // Para solicitar un nuevo email de verificación de perfil
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacionEmail');
    $routes->get('verificar-email-token/(:segment)', 'PerfilController::verificarEmailConfiguracion/$1');
    $routes->get('dispositivo/editar/(:any)', 'PerfilController::editarDispositivo/$1');
    $routes->post('dispositivo/actualizar/(:any)', 'PerfilController::actualizarDispositivo/$1');
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');
});


// Rutas de enlace de MACs
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');


// Endpoints de la API para dispositivos (usado por ESP32)
$routes->group('dispositivos', function ($routes) {
    $routes->post('registrar', 'DispositivoController::registrarDispositivo');
});


// Endpoints de la API para lecturas de gas (usado por ESP32)
$routes->group('lecturas_gas', function ($routes) {
    $routes->post('guardar', 'LecturasController::guardar');
});

// Detalles del dispositivo (mostrar lecturas, etc.)
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');

// Otras rutas
$routes->get('/comprar', 'Home::comprar');

// Asumo que esta es la ruta a la que redirige PayPal después de una compra exitosa
$routes->get('/paypal/success', 'PaypalController::success');
$routes->get('/paypal/cancel', 'PaypalController::cancel'); // Si tienes una ruta de cancelación

// Ruta para la página de compra exitosa después de un restablecimiento de contraseña
$routes->get('/cambio_exitoso', 'Home::cambioExitoso'); // Asumo que tienes este método en Home.php
// Método dummy para cambioExitoso (si no lo tienes en Home.php)
// En Home.php:
// public function cambioExitoso() {
//     return view('cambio_exitoso'); // Crea esta vista si no existe
// }
