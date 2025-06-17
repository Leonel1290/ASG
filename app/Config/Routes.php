<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
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

// Ruta para procesar el formulario de login (POST)
$routes->post('/login', 'Home::login');

// Ruta para mostrar el formulario de login (GET)
$routes->get('/loginobtener', 'Home::loginobtener'); // Mantengo esta para ser tu ruta GET del login

// Ruta para cerrar sesión (usando POST para mayor seguridad)
$routes->post('/logout', 'Home::logout');


// --- RUTAS DE RECUPERACIÓN DE CONTRASEÑA ---
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');


// --- RUTAS DE LA APLICACIÓN PRINCIPAL (Protegidas por SessionAdmin) ---

// Ruta para el inicio de la aplicación principal (protegida)
$routes->get('/inicio', 'Home::inicio', ['filter' => 'SessionAdmin']); // Aplicamos el filtro aquí

// Rutas del PANEL DE USUARIO (PERFIL) - Protegidas por SessionAdmin
$routes->get('/perfil', 'PerfilController::index', ['filter' => 'SessionAdmin']); // <--- AQUI EL CAMBIO CLAVE

// Rutas de configuración de perfil (protegidas)
$routes->group('perfil', ['filter' => 'SessionAdmin'], function($routes){
    $routes->get('configuracion', 'PerfilController::configuracion');
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion'); // Usar POST
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');
    $routes->get('config_form', 'PerfilController::configForm');
    $routes->post('actualizar', 'PerfilController::actualizar');
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');

    // Rutas de gestión de dispositivos (protegidas)
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1');
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice');
    $routes->post('dispositivo/eliminar', 'PerfilController::eliminarDispositivos');
});

// Rutas de enlace de dispositivos (protegidas)
$routes->group('enlace', ['filter' => 'SessionAdmin'], function($routes){
    $routes->get('/', 'EnlaceController::index'); // Ruta para mostrar el formulario de enlace de MACs
    $routes->post('store', 'EnlaceController::store'); // Ruta para procesar el formulario de enlace de MACs
});

// Otras rutas principales (puedes protegerlas con SessionAdmin si es necesario)
$routes->get('/dispositivos', 'DispositivoController::index', ['filter' => 'SessionAdmin']); // Ejemplo: si esta vista debe ser para logueados
$routes->get('/comprar', 'Home::comprar'); // Si es una página pública o no requiere SessionAdmin

// --- RUTAS DE LA API/ESP32 (NO DEBEN LLEVAR FILTROS DE SESIÓN DE USUARIO WEB) ---
// Endpoint: POST https://asg-leo.onrender.com/lecturas_gas/guardar
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

// NUEVA RUTA para registrar dispositivos desde la ESP32
// Endpoint: POST https://asg-leo.onrender.com/dispositivos/registrar
$routes->post('/dispositivos/registrar', 'DispositivoController::registrarDispositivo');


// --- RUTAS REDUNDANTES O NO MÁS NECESARIAS (ELIMINADAS) ---
// Eliminadas:
// $routes->get('/inicioobtener', 'Home::inicioobtener'); // Duplicada con '/inicio' y el filtro
// $routes->get('/loginobtenerforgot', 'Home::loginobtenerforgot'); // Duplicada con /forgotpassword
// $routes->get('/inicioresetpass', 'Home::inicioresetpass'); // Duplicada con /reset-password/(:any)
// $routes->get('/obtenerperfil', 'Home::obtenerperfil'); // Duplicada por /perfil

// Asegúrate de que estos métodos existan si los necesitas en algún lado
// $routes->get('/mac/(:segment)', 'Home::verLecturas/$1');
// $routes->post('/actualizar-dispositivo', 'DispositivoController::actualizarDispositivo');