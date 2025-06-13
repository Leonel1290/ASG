<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN GENERALES ---
// CAMBIO: Asegurarse de que el controlador de registro se referencia con PascalCase (R mayúscula)
$routes->get('/register', 'RegisterController::index');
$routes->post('/register/store', 'RegisterController::store');
$routes->get('/register/check-email', 'RegisterController::checkEmail');
$routes->get('/register/verify-email/(:segment)', 'RegisterController::verifyEmailToken/$1');
$routes->post('/login', 'Home::login');
$routes->get('/loginobtener', 'Home::loginobtener');
$routes->post('/logout', 'Home::logout');


// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

// --- FIN RUTAS DE REGISTRO Y LOGIN GENERALES ---


// --- RUTAS ESPECÍFICAS PARA COMPRA/PAYPAL ---
$routes->get('/login/paypal', 'Home::loginPaypal');
$routes->post('/login/paypal', 'Home::processLoginPaypal'); // Este maneja el POST del login para compra

// CAMBIO: Asegurarse de que el controlador de registro se referencia con PascalCase (R mayúscula)
$routes->get('/register/paypal', 'RegisterController::registerPaypal');
$routes->post('/register/paypal/store', 'RegisterController::storePaypal');
// --- FIN RUTAS ESPECÍFICAS PARA COMPRA/PAYPAL ---


// Perfil (Agrupamos rutas relacionadas al perfil)
$routes->group('perfil', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PerfilController::index');
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('actualizar', 'PerfilController::actualizarPerfil'); // Asumo que este es el método correcto para actualizar perfil
    $routes->post('cambiar-password', 'PerfilController::cambiarContrasena'); // Asumo que este es el método correcto para cambiar contraseña
    $routes->get('verificar-email', 'PerfilController::verificarEmail'); // Vista para solicitar verificación
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacionEmail'); // Para enviar el email
    // CAMBIO CLAVE: El método en PerfilController es verificarEmailConfiguracion
    $routes->get('verificar-email-token/(:segment)', 'PerfilController::verificarEmailConfiguracion/$1'); // Para procesar el token del email

    // Rutas para editar y eliminar dispositivos (en el perfil, no en la compra)
    // CAMBIO: Asegurarse de que estos métodos están en PerfilController o ajustar la referencia
    $routes->get('dispositivo/editar/(:any)', 'PerfilController::editarDispositivo/$1'); // Apunta a PerfilController
    $routes->post('dispositivo/actualizar', 'PerfilController::actualizarDispositivo'); // Apunta a PerfilController (ajustado el nombre de la ruta POST)
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos'); // Ajustado el nombre de la ruta POST para eliminar varios
    // La ruta GET para eliminar un solo dispositivo si es que aún se usa (siempre es mejor POST para eliminaciones)
    // $routes->get('dispositivo/eliminar/(:any)', 'PerfilController::eliminarDispositivos/$1');
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
// La ruta '/procesar-compra' ya no se usa para el flujo de PayPal, puedes comentarla o eliminarla.
// $routes->post('/procesar-compra', 'Home::procesarCompra'); // ¡Añadida la ruta para procesar la compra!

// Rutas de la API de PayPal (llamadas por el frontend JS y el backend)
$routes->post('/paypal/create-order', 'PayPalController::createOrder');
$routes->post('/paypal/capture-order/(:any)', 'PayPalController::captureOrder/$1');

// Asumo que esta es la ruta a la que redirige PayPal después de una compra exitosa
$routes->get('/paypal/success', 'PayPalController::success');
$routes->get('/paypal/cancel', 'PayPalController::cancel');