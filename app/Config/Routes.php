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
$routes->get('/login', 'Home::login');

$routes->post('/logout', 'Home::logout');


// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

// --- FIN RUTAS DE REGISTRO Y LOGIN GENERALES ---


// --- RUTAS ESPECÍFICAS PARA COMPRA/PAYPAL ---
$routes->get('/login/paypal', 'Home::loginPaypal');
$routes->post('/login/paypal', 'Home::processLoginPaypal');

$routes->get('/register/paypal', 'registerController::registerPaypal');
$routes->post('/register/paypal/store', 'registerController::storePaypal');
// --- FIN RUTAS ESPECÍFICAS PARA COMPRA/PAYPAL ---


// Perfil (Agrupamos rutas relacionadas al perfil)
$routes->group('perfil', function ($routes) {
    $routes->get('/', 'PerfilController::index'); // Esta será tu función perfil() principal
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('actualizar-perfil', 'PerfilController::actualizarPerfil');
    $routes->post('cambiar-contrasena', 'PerfilController::cambiarContrasena');
    $routes->get('verificar-email', 'PerfilController::verificarEmail');
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacionEmail');
    $routes->get('verificar-email-token/(:segment)', 'PerfilController::verificarEmailConfiguracion/$1');
    // Rutas para la gestión de dispositivos en el perfil
    $routes->get('dispositivo/editar/(:any)', 'PerfilController::editarDispositivo/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::actualizarDispositivo'); // No necesitas (:any) aquí si MAC va por POST
    $routes->get('dispositivo/eliminar/(:any)', 'PerfilController::eliminarDispositivo/$1');
});


// Rutas de enlace de MACs - ESTAS RUTAS YA NO SERÍAN NECESARIAS SI LA COMPRA ES AUTOMÁTICA
// Te recomiendo comentarlas o eliminarlas si no planeas que el usuario introduzca MACs manualmente.
// $routes->get('/enlace', 'EnlaceController::index');
// $routes->post('/enlace/store', 'EnlaceController::store');


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

// Rutas de compra
$routes->get('/comprar', 'Home::comprar');
$routes->post('/procesar-compra', 'Home::procesarCompra'); // ¡Añadida la ruta para procesar la compra!

// Asumo que esta es la ruta a la que redirige PayPal después de una compra exitosa
$routes->get('/paypal/success', 'PaypalController::success');
$routes->get('/paypal/cancel', 'PaypalController::cancel');

// Ruta para la página de cambio de contraseña exitoso
$routes->get('/cambio_exitoso', 'Home::cambioExitoso'); // Asumo que tienes este método en Home.php
