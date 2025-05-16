<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- RUTAS DE REGISTRO Y LOGIN (AJUSTADAS PARA VERIFICACIÓN) ---

// Ruta para mostrar el formulario de registro
$routes->get('/register', 'registerController::index');

// Ruta para procesar el formulario de registro
// POST /registerController/store (Coincide con la action del formulario en register.php)
$routes->post('/register/store', 'registerController::store'); 

// Ruta para mostrar la página que le dice al usuario que revise su email
$routes->get('/register/check-email', 'registerController::checkEmail');

// Ruta para verificar el token recibido por email para REGISTRO
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Ruta para procesar el formulario de login
$routes->post('/login', 'Home::login');

// Ruta para mostrar el formulario de login (si usas loginobtener para esto)
$routes->get('/loginobtener', 'Home::loginobtener');

// Ruta para cerrar sesión
$routes->post('/logout', 'Home::logout');


// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword');
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');

// --- FIN RUTAS DE REGISTRO Y LOGIN ---


// Perfil (Agrupamos rutas relacionadas con PerfilController)
$routes->group('perfil', function($routes) {
    // Ruta para la página principal del perfil
    // GET /perfil
    $routes->get('/', 'PerfilController::index');

    // --- RUTAS PARA EL FLUJO DE VERIFICACIÓN Y CONFIGURACIÓN DE PERFIL ---

    // Ruta para mostrar la página INICIAL de configuración (pide verificar email)
    // GET /perfil/configuracion
    $routes->get('configuracion', 'PerfilController::configuracion');

    // Ruta para procesar el envío del correo de verificación para CONFIGURACIÓN
    // POST /perfil/enviar-verificacion
    $routes->post('enviar-verificacion', 'PerfilController::enviarVerificacion');

    // Ruta para verificar el token recibido por email para CONFIGURACIÓN
    // GET /perfil/verificar-email/EL_TOKEN_GENERADO
    $routes->get('verificar-email/(:segment)', 'PerfilController::verificarEmailToken/$1');

    // Ruta para mostrar el formulario de configuración REAL (accesible después de verificar email)
    // GET /perfil/config_form
    $routes->get('config_form', 'PerfilController::configForm');

    // Ruta para procesar la actualización del perfil (nombre/email)
    // POST /perfil/actualizar
    $routes->post('actualizar', 'PerfilController::actualizar');

    // Ruta para la página de éxito después de actualizar el perfil
    // GET /perfil/cambio-exitoso
    $routes->get('cambio-exitoso', 'PerfilController::cambioExitoso');

    // --- FIN RUTAS VERIFICACIÓN Y CONFIGURACIÓN DE PERFIL ---


    // --- NUEVAS RUTAS PARA GESTIÓN DE DISPOSITIVOS ---

    // Ruta para mostrar el formulario de edición de un dispositivo
    // GET /perfil/dispositivo/editar/MAC_DEL_DISPOSITIVO
    // (:segment) captura la MAC de la URL
    $routes->get('dispositivo/editar/(:segment)', 'PerfilController::editDevice/$1'); // NUEVA RUTA

    // Ruta para procesar el formulario de edición de un dispositivo
    // POST /perfil/dispositivo/actualizar
    $routes->post('dispositivo/actualizar', 'PerfilController::updateDevice'); // NUEVA RUTA

    // Ruta para eliminar dispositivos seleccionados (desenlazar del usuario)
    // POST /perfil/eliminar-dispositivos
    $routes->post('eliminar-dispositivos', 'PerfilController::eliminarDispositivos'); // Ya existía


    // --- FIN NUEVAS RUTAS GESTIÓN DE DISPOSITIVOS ---

});


// Recibir los datos de la ESP32:
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

// Vista para enlazar dispositivos (formulario para ingresar MAC)
$routes->get('/enlace', 'EnlaceController::index');
// Acción para enlazar MACs (procesa el formulario de /enlace)
$routes->post('/enlace/store', 'EnlaceController::store');

// Detalles del dispositivo (mostrar lecturas, etc.)
// Asumo que esta ruta usa la MAC para identificar el dispositivo
$routes->get('/detalles/(:any)', 'DetalleController::detalles/$1');

// Otras rutas (mantengo las que tenías)
$routes->get('/comprar', 'Home::comprar');