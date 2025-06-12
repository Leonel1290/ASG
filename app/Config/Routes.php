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

// Ruta para procesar el formulario de login
$routes->post('/login', 'Home::login');

// Ruta para mostrar el formulario de login
$routes->get('/loginobtener', 'Home::loginobtener');

// Ruta para cerrar sesión (usando POST para mayor seguridad)
$routes->post('/logout', 'Home::logout');

// --- RUTAS DE RECUPERACIÓN DE CONTRASEÑA ---
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');


// --- RUTAS DE HOME PARA USUARIOS LOGUEADOS ---

// ¡¡¡ ESTA ES LA RUTA QUE FALTABA Y CAUSABA EL ERROR !!!
$routes->get('/inicio', 'Home::inicio'); // <--- ¡ASEGÚRATE QUE ESTA LÍNEA ESTÉ PRESENTE!

// Ruta para mostrar el perfil del usuario logueado
// NOTA: Esta ruta ya está agrupada en el bloque /perfil, pero si necesitas acceso directo, la mantienes aquí.
// Si ya accedes a ella con /perfil, puedes eliminar esta línea para evitar duplicidad.
// $routes->get('/perfil', 'Home::perfil');


// Ruta para mostrar la vista de compra de dispositivos
$routes->get('/comprar', 'Home::comprar');

// Ruta para registrar una compra de dispositivo (para ser llamada por webhooks o lógica interna)
// Requiere MAC y opcionalmente un ID de transacción
$routes->post('/registrar-compra-automatica', 'Home::registrarCompraAutomatica');


// --- RUTAS AGRUPADAS (RECOMENDADO PARA ORGANIZACIÓN) ---

// Perfil (Agrupamos rutas relacionadas con el perfil del usuario)
$routes->group('/perfil', function ($routes) {
    // Ruta principal del perfil (GET /perfil)
    $routes->get('/', 'PerfilController::index'); // Esta es la ruta preferida para /perfil

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
    // GET /perfil/dispositivo/editar/(:any)
    $routes->get('dispositivo/editar/(:any)', 'PerfilController::editarDispositivo/$1');
    // POST /perfil/dispositivo/actualizar
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
// Asumo que esta ruta usa la MAC para identificar el dispositivo
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');


// --- RUTAS NO UTILIZADAS O DUPLICADAS (COMENTADAS) ---
// Las siguientes rutas están comentadas porque parecen ser redundantes o no corresponden
// con los métodos de controlador que hemos estado utilizando. Es buena práctica mantener
// un Routes.php limpio y sin duplicidades.

// $routes->post('/enlazar-mac', 'EnlaceController::store'); // Duplicada con /enlace/store
// $routes->get('/mac/(:segment)', 'Home::verLecturas/$1'); // Método verLecturas no se encuentra en Home.php
// $routes->post('/actualizar-dispositivo', 'DispositivoController::actualizarDispositivo'); // Duplicada con /perfil/dispositivo/actualizar si se usa PerfilController
// $routes->get('/inicioobtener', 'Home::inicioobtener'); // Duplicada con /loginobtener
// $routes->get('/loginobtenerforgot', 'Home::loginobtenerforgot'); // Duplicada con /forgotpassword
// $routes->get('/inicioresetpass', 'Home::inicioresetpass'); // Duplicada con /reset-password/(:any)
// $routes->get('/obtenerperfil', 'Home::obtenerperfil'); // Duplicada con /perfil
// $routes->get('/dispositivos', 'Home::dispositivos'); // Si es para listar, debería ir en DispositivoController
// $routes->get('/home', 'Home::home'); // Si usas Home::inicio, esta puede ser redundante.
// $routes->get('/dispositivos', 'DispositivoController::index'); // Si tienes un método index en DispositivoController para listar
