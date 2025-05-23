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
// POST /register/store (Coincide con la action del formulario en register.php)
$routes->post('/register/store', 'registerController::store');

// Ruta para mostrar la página que le dice al usuario que revise su email después del registro
$routes->get('/register/check-email', 'registerController::checkEmail');

// Ruta para verificar el token recibido por email para REGISTRO
$routes->get('/register/verify-email/(:segment)', 'registerController::verifyEmailToken/$1');

// Ruta para procesar el formulario de login
$routes->post('/login', 'Home::login');

// Ruta para mostrar el formulario de login (si usas loginobtener para esto)
$routes->get('/loginobtener', 'Home::loginobtener');

// Ruta para cerrar sesión (usando POST para mayor seguridad)
$routes->post('/logout', 'Home::logout');


// RECUPERACION DE CONTRASEÑA
$routes->get('/forgotpassword', 'Home::forgotpassword');
$routes->post('/forgotpassword1', 'Home::forgotPPassword'); // Asumo que esta es la ruta que procesa el formulario de forgot password
$routes->get('/reset-password/(:any)', 'Home::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Home::resetPassword');


// --- RUTAS DE LA APLICACIÓN PRINCIPAL ---

// Ruta para guardar lecturas de gas desde la ESP32
// Endpoint: POST https://asg-leo.onrender.com/lecturas_gas/guardar
$routes->post('/lecturas_gas/guardar', 'LecturasController::guardar');

// NUEVA RUTA para registrar dispositivos desde la ESP32
// Endpoint: POST https://asg-leo.onrender.com/dispositivos/registrar
$routes->post('/dispositivos/registrar', 'DispositivoController::registrarDispositivo');


// --- RUTAS RELACIONADAS CON ENLACE DE DISPOSITIVOS ---
// Ruta para mostrar el formulario de enlace de MACs (GET /enlace)
$routes->get('/enlace', 'EnlaceController::index');
// Ruta para procesar el formulario de enlace de MACs (POST /enlace/store)
$routes->post('/enlace/store', 'EnlaceController::store');


// --- RUTAS DEL PANEL DE USUARIO (PERFIL) ---
// Ruta para el perfil del usuario (GET /perfil)
$routes->get('/perfil', 'PerfilController::index', ['filter' => 'auth']); // Asumo que necesitas autenticación


// --- RUTAS PARA VISTAS GENÉRICAS (Ej. si PerfilController no maneja todo) ---
 $routes->get('/home', 'Home::home'); // Si Home::home es la página principal después del login
 $routes->get('/dispositivos', 'DispositivoController::index'); // Si hay una vista para listar dispositivos


// --- RUTAS NO ENCONTRADAS EN CONTROLADORES ADJUNTOS (COMENTADAS) ---
// Estas rutas estaban en tu Routes.php original pero no vimos métodos correspondientes
// en los controladores que proporcionaste.

 $routes->get('/inicioobtener', 'Home::inicioobtener'); // Duplicada con '/' o '/inicio'
 $routes->get('/loginobtenerforgot', 'Home::loginobtenerforgot'); // Duplicada con /forgotpassword
 $routes->get('/inicioresetpass', 'Home::inicioresetpass'); // Duplicada con /reset-password/(:any)
 $routes->get('/obtenerperfil', 'Home::obtenerperfil'); // Parece una vista directa, no una acción de controlador
 $routes->get('/dispositivos', 'Home::dispositivos'); // Parece una vista directa, no una acción de controlador

// NOTA: También tienes un método `perfil()` y `storeMac()` en Home.php
// que parecen duplicados con PerfilController::index y EnlaceController::store.
// Es recomendable usar solo los controladores dedicados (PerfilController y EnlaceController)
// para estas funcionalidades y eliminar los métodos duplicados en Home.php.

 $routes->get('/mac/(:segment)', 'Home::verLecturas/$1'); // Método verLecturas no encontrado en Home.php
 $routes->post('/actualizar-dispositivo', 'DispositivoController::actualizarDispositivo'); // Método actualizarDispositivo no encontrado en DispositivoController.php

