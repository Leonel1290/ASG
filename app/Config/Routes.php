<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Ruta principal que carga la vista de inicio
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN ---

// Ruta para mostrar el formulario de registro
$routes->get('/register', 'registerController::index');

// Ruta para procesar el formulario de registro
$routes->post('/register/store', 'registerController::store');

// Ruta para mostrar la página que le dice al usuario que revise su email después del registro
$routes->get('/register/check-email', 'registerController::checkEmail');

// Ruta para verificar el token recibido por email para REGISTRO
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Ruta para mostrar y procesar el formulario de login (AHORA GESTIONA AMBOS MÉTODOS GET y POST)
$routes->match(['get', 'post'], '/login', 'Home::login');

// Ruta para cerrar sesión (usando POST para mayor seguridad)
$routes->post('/logout', 'Home::logout');


// --- RUTAS DE RECUPERACIÓN DE CONTRASEÑA ---
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');
$routes->get('/cambio_exitoso', function() { return view('cambio_exitoso'); }); // Ruta para la vista de cambio de contraseña exitoso


// --- RUTAS DE HOME PARA USUARIOS LOGUEADOS ---

// Ruta para mostrar la vista de inicio para usuarios logueados
$routes->get('/inicio', 'Home::inicio');

// Ruta para mostrar la vista de compra de dispositivos
$routes->get('/comprar', 'Home::comprar');

// Ruta para registrar una compra de dispositivo (usada internamente por PayPalController)
$routes->post('/registrar-compra-automatica', 'Home::registrarCompraAutomatica');


// --- RUTAS AGRUPADAS (RECOMENDADO PARA ORGANIZACIÓN) ---

// Perfil (Agrupamos rutas relacionadas con el perfil del usuario)
$routes->group('/perfil', function ($routes) {
    // Ruta principal del perfil (GET /perfil)
    $routes->get('/', 'PerfilController::index');

    // Ruta para mostrar el formulario de configuración del perfil (GET /perfil/configuracion)
    $routes->get('configuracion', 'PerfilController::configuracion');

    // Ruta para procesar la actualización de la configuración del perfil (POST /perfil/actualizar-configuracion)
    $routes->post('actualizar-configuracion', 'PerfilController::actualizarConfiguracion');

    // Ruta para mostrar el formulario de verificación de email antes de editar datos sensibles (GET /perfil/verificar-email)
    $routes->get('verificar-email', 'PerfilController::verificarEmail');

    // Ruta para enviar el correo de verificación de email (POST /perfil/enviar-verificacion)
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacionEmail');

    // Ruta para validar el código de verificación del email (POST /perfil/validar-codigo-verificacion)
    $routes->post('validar-codigo-verificacion', 'PerfilController::validarCodigoVerificacion');

    // Rutas para editar y actualizar un dispositivo específico del usuario
    $routes->get('dispositivo/editar/(:any)', 'PerfilController::editarDispositivo/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::actualizarDispositivo');

    // Ruta para eliminar (desenlazar) dispositivos del perfil del usuario (POST /perfil/eliminar-dispositivos)
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos');
});


// Rutas de Enlace de MAC a Usuario
$routes->get('/enlace', 'EnlaceController::index');
$routes->post('/enlace/store', 'EnlaceController::store');

// Rutas para la API de Dispositivos (ESP32)
$routes->post('/dispositivos/registrar', 'DispositivoController::registrarDispositivo');

// Rutas para la API de Lecturas de Gas (ESP32)
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');


// Detalles del dispositivo (mostrar lecturas, etc.)
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');


// --- NUEVAS RUTAS PARA INTEGRACIÓN DE PAYPAL ---
// Agrupamos las rutas de PayPal bajo '/paypal'
$routes->group('paypal', function ($routes) {
    // Endpoint para que el SDK de PayPal cree una orden en tu servidor
    $routes->post('create-order', 'PayPalController::createOrder');

    // Endpoint para que el SDK de PayPal capture la orden en tu servidor
    $routes->post('capture-order', 'PayPalController::captureOrder');

    // Vistas de redirección post-PayPal
    $routes->get('success', 'PayPalController::success');
    $routes->get('cancel', 'PayPalController::cancel');
});
