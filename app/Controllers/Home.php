<?php

namespace App\Controllers;

// use App\Models\Userlogin; // Eliminamos el uso de Userlogin
use App\Models\UserModel; // Usaremos UserModel para todo lo relacionado con usuarios
use App\Models\LecturasGasModel; // Si lo usas en login o perfil
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Home extends Controller
{
    // Puedes instanciar modelos aquí si los usas en múltiples métodos
    // protected $userModel; // Lo instanciamos dentro del método login()
    // protected $lecturasGasModel; // Lo instanciamos dentro del método login()

    public function __construct()
    {
        // Instanciar modelos si se usan en el constructor o en varios métodos
        // $this->userModel = new UserModel();
        // $this->lecturasGasModel = new LecturasGasModel(); // Si se usa aquí
    }


    public function index()
    {
        return view('inicio');
    }

    public function inicio()
    {
        $session = session();

        // Log en vez de var_dump para evitar errores de headers
        log_message('debug', json_encode($session->get()));

        if (!$session->get('logged_in')) {
            return view('login'); // corregido: sin "/"
        }

        return view('inicio');
    }

    // Método de Login (POST /login) - Modificado para usar UserModel y verificar is_active
    public function login()
    {
        // --- LÍNEA DE DEBUGGING TEMPORAL ---
        // Ahora depuramos justo antes de obtener la sesión. Quitamos el exit().
        echo "DEBUG: Iniciando metodo login(). Intentando obtener sesion.";
        // --- FIN LÍNEA DE DEBUGGING TEMPORAL ---

        $session = session();
        // Usamos UserModel para verificar el estado activo y credenciales
        $userModel = new UserModel();
        // Instancia LecturasGasModel si lo necesitas aquí (tu código original lo hacía)
        $lecturasGasModel = new \App\Models\LecturasGasModel();


        // Obtener datos del formulario de login
        // Asumo que tu formulario de login tiene campos 'email' y 'password'
        // Si también usa 'nombre', ajusta la búsqueda.
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        // $nombre = $this->request->getPost('nombre'); // Descomentar si buscas por nombre también


        // Buscar usuario por email (asumiendo que el email es único para login)
        $user = $userModel->where('email', $email)->first();
        // Si tu formulario usa nombre Y email, usa:
        // $user = $userModel->where('nombre', $nombre)->where('email', $email)->first();


        if ($user) {
            // 1. Comprobar si la cuenta está activa
            if (!$user['is_active']) {
                // Si la cuenta no está activa, redirigir con mensaje de error
                $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu email para activarla.');
                 // Opcional: Redirigir a la página que le dice que revise su email
                 // return redirect()->to('/register/check-email');
                return redirect()->back()->withInput(); // Mantener email en el campo
            }

            // 2. Si la cuenta está activa, verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Contraseña correcta, iniciar sesión

                // Tu lógica para obtener la última lectura (si la necesitas en la sesión)
                // Asegúrate de que la columna en lecturas_gas es 'usuario_id'
                $ultimaLectura = $lecturasGasModel
                    ->orderBy('id', 'DESC')
                    ->where(['usuario_id' => $user['id']]) // Usamos 'usuario_id' que es el nombre correcto en la tabla
                    ->asArray()
                    ->first();

                $nivel_gas = $ultimaLectura['nivel_gas'] ?? null;
                // $mensaje = $ultimaLectura ? null : 'No tienes registros de lecturas'; // Esto se manejaría en la vista de perfil

                // Establecer datos en la sesión
                $session->set([
                    'id'        => $user['id'],
                    'nombre'    => $user['nombre'],
                    'email'     => $user['email'],
                    'logged_in' => true, // Bandera de sesión iniciada
                ]);

                // Redirigir al perfil después del login exitoso
                return redirect()->to('/perfil');
            } else {
                // Contraseña incorrecta
                $session->setFlashdata('error', 'Contraseña incorrecta.');
                return redirect()->back()->withInput(); // Mantener email en el campo
            }
        } else {
            // Usuario no encontrado (email no registrado)
            $session->setFlashdata('error', 'Email no encontrado.');
            return redirect()->back()->withInput(); // Mantener email en el campo
        }
    }


    public function register()
    {
        // Este método simplemente muestra la vista de registro
        return view('register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/'); // Redirigir a la página de inicio o login
    }

    // Rutas que parecen vistas directas o redundantes (considera limpiarlas)
    public function loginobtener() { return view('login'); } // Duplicada con login() si solo muestra la vista
    public function configview() { return view('configuracion'); } // Ahora redirige a PerfilController::configuracion
    public function salirdelconfig() { return view('inicio'); }
    public function volveralinicio() { return view('inicio'); }
    public function volveralperfil() { return view('perfil'); } // Ahora redirige a PerfilController::index

    // Recuperación de contraseña (mantengo tu lógica existente, usa UserModel)

    public function forgotPPassword()
    {
        $session = session();
        // Usamos UserModel
        $userModel = new UserModel();
        $emailInput = $this->request->getPost('email');
        $user = $userModel->where('email', $emailInput)->first();

        if ($user) {
            // --- VERIFICACIÓN: Solo enviar si la cuenta está activa ---
            if (!$user['is_active']) {
                 $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. No se puede recuperar la contraseña.');
                 return redirect()->back()->withInput();
            }
            // --- FIN VERIFICACIÓN ---

            $token = bin2hex(random_bytes(50));
            $expires = Time::now()->addHours(1);

            $updated = $userModel->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires->toDateTimeString(),
            ]);

            $resetLink = base_url("/reset-password/$token");

            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setFrom('againsafegas.ascii@gmail.com', 'ASG'); // <-- CONFIGURA ESTO EN app/Config/Email.php
            $email->setSubject('Recuperación de contraseña');
            $email->setMessage("Haz clic en este enlace para recuperar tu contraseña: " . $resetLink);

            if (!$email->send()) {
                $data = $email->printDebugger(['headers']);
                log_message('error', 'Error enviando correo de recuperación: ' . $data);
                 $session->setFlashdata('error', 'Hubo un error al enviar el correo de recuperación.');
                 return redirect()->back()->withInput();
            }

            if ($updated) {
                log_message('debug', 'Actualización de token de recuperación exitosa');
            } else {
                log_message('error', 'Actualización de token de recuperación fallida');
            }

            $session->setFlashdata('success', 'Se ha enviado un enlace de recuperación a tu correo.');
            return redirect()->back()->withInput();
        } else {
            $session->setFlashdata('error', 'Correo electrónico no encontrado.');
            return redirect()->back()->withInput();
        }
    }

    public function loginobtenerforgot()
    {
        return view('login'); // Vista de login, quizás renombrada
    }

    public function forgotpassword()
    {
        return view('forgotpassword'); // Vista del formulario de forgot password
    }

    public function showResetPasswordForm($token)
    {
        $userModel = new UserModel(); // Usamos UserModel
        $user = $userModel->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
             // --- VERIFICACIÓN: Solo permitir reset si la cuenta está activa ---
            if (!$user['is_active']) {
                 // Limpiar el token si la cuenta no está activa
                 $userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
                 session()->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. No se puede resetear la contraseña.');
                 return redirect()->to('/register'); // O a una página de error/instrucciones
            }
            // --- FIN VERIFICACIÓN ---

            return view('reset_password', ['token' => $token]);
        } else {
            session()->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->to('/forgotpassword');
        }
    }

    public function resetPassword()
    {
        $session = session();
        $userModel = new UserModel(); // Usamos UserModel

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $userModel->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
             // --- VERIFICACIÓN: Solo permitir reset si la cuenta está activa ---
            if (!$user['is_active']) {
                 // Limpiar el token si la cuenta no está activa
                 $userModel->update($user['id'], ['reset_token' => null, 'reset_expires' => null]);
                 $session->setFlashdata('error', 'Tu cuenta aún no ha sido verificada. No se puede resetear la contraseña.');
                 return redirect()->to('/register'); // O a una página de error/instrucciones
            }
            // --- FIN VERIFICACIÓN ---

            $userModel->update($user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_expires' => null,
            ]);

            $session->setFlashdata('success', 'Tu contraseña ha sido actualizada.');
            return redirect()->to('/loginobtener'); // Redirigir a la página de login
        } else {
            $session->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->back()->withInput();
        }
    }

    // Método para enlazar MACs (parece que lo tienes duplicado con EnlaceController::store)
    // Considera eliminar este si usas EnlaceController::store
    public function storeMac()
    {
        $session = session();
        $mac = $this->request->getPost('mac');

        if (!$session->get('logged_in')) {
            return redirect()->to('/loginobtener'); // Redirigir a la página de login si no está logueado
        }

        $usuarioId = $session->get('id');
        $enlaceModel = new \App\Models\EnlaceModel();

        // Verificar si ya existe la MAC para este usuario
        $existe = $enlaceModel
                    ->where('id_usuario', $usuarioId)
                    ->where('MAC', $mac)
                    ->first();

        if ($existe) {
            $session->setFlashdata('error', 'Esta MAC ya está enlazada.');
        } else {
            $enlaceModel->insert([
                'id_usuario' => $usuarioId,
                'MAC' => $mac
            ]);
            $session->setFlashdata('success', 'MAC enlazada correctamente.');
        }

        return redirect()->to('/perfil'); // Redirigir al perfil
    }

    // Rutas que parecen vistas directas o redundantes (considera limpiarlas)
    public function inicioresetpass() { return view('reset_password'); } // Duplicada con showResetPasswordForm
    public function obtenerperfil() { return view('perfilobtener'); } // Vista no proporcionada

    // Vistas varias (mantengo las que tenías)

    public function dispositivos()
    {
        return view('dispositivos'); // Vista no proporcionada
    }

    // Método para mostrar la página de perfil (parece que PerfilController::index es la principal ahora)
    // Considera eliminar este si usas PerfilController::index
    public function perfil()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/loginobtener'); // Redirigir a la página de login
        }

        $usuarioId = $session->get('id');

        $enlaceModel = new \App\Models\EnlaceModel();
        $lecturasGasModel = new \App\Models\LecturasGasModel();

        // Obtener MACs del usuario
        $macs = $enlaceModel
                    ->select('MAC')
                    ->where('id_usuario', $usuarioId)
                    ->groupBy('MAC')
                    ->findAll();

        // Obtener lecturas por usuario
        $lecturas = $lecturasGasModel->getLecturasPorUsuario($usuarioId);

        // Agrega este log para ver los datos antes de pasarlos a la vista
        log_message('debug', 'Lecturas por usuario: ' . print_r($lecturas, true));

        return view('perfil', [
            'macs' => $macs,
            'lecturas' => $lecturas // Asegúrate de que tu vista 'perfil' espera 'lecturas' o 'lecturasPorMac'
        ]);
    }

    public function comprar()
    {
        return view('comprar'); // Vista no proporcionada
    }

    // Parece que tenías un método verLecturas, asegúrate de que exista si la ruta /mac/(:segment) lo usa
    // public function verLecturas($mac) { ... }
}
