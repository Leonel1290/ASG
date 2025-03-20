<?php

namespace App\Controllers;

use App\Models\Userlogin;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Home extends Controller
{
    public function index()
    {
        return view('inicio');
    }

    public function inicio()
    {
        $session = session();
        var_dump($session->get()); // Verifica el contenido de la sesión
        if (!$session->get('logged_in')) {
            return view('/login'); // Redirigir si no está logueado
        }
        return view('inicio'); // Mostrar la vista de inicio si está logueado
    }

    public function login()
{
    $session = session();
    $userlogin = new Userlogin();
    $lecturasGasModel = new \App\Models\LecturasGasModel();

    // Captura los datos enviados desde el formulario
    $nombre = $this->request->getPost('nombre');
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    // Buscar el usuario en la base de datos
    $user = $userlogin->where('nombre', $nombre)
                      ->where('email', $email)
                      ->first();

// Verificar si el usuario existe y si la contraseña es correcta
if ($user) {
    if (password_verify($password, $user['password'])) {
        // Obtener la última lectura de gas y la fecha asociada
        $ultimaLectura = $lecturasGasModel->orderBy('id', 'DESC')->where(['usuario_id' => $user['id']])->first();

        if ($ultimaLectura) {
            // Si hay una última lectura, obtén el nivel de gas y la fecha
            $nivel_gas = $ultimaLectura['nivel_gas'];
            $fecha = $ultimaLectura['created_at']; // Asumiendo que tienes una columna `created_at`
        } else {
            // Si no hay registros de lecturas, asigna valores predeterminados
            $nivel_gas = null;
            $fecha = null;
            $mensaje = 'No tienes registros de lecturas';
        }

        // Guardar datos en la sesión y redirigir al perfil con datos adicionales
        $session->set([
            'id'        => $user['id'],
            'nombre'    => $user['nombre'],
            'email'     => $user['email'],
            'logged_in' => true,
        ]);

        return view('perfil', [
            'nivel_gas' => $nivel_gas,
            'fecha'     => $fecha,
            'mensaje'   => $mensaje ?? null
        ]);
    } else {
        // Contraseña incorrecta
        $session->setFlashdata('error', 'Contraseña incorrecta');
        return redirect()->back();
    }
} else {
    // Usuario no encontrado
    $session->setFlashdata('error', 'Usuario no encontrado');
    return redirect()->back();

}
}

    public function register()
    {
        return view('register');
    }
    public function logout()
    {
        // Destruir la sesión
        session()->destroy();
        return redirect()->to('/');
    }
    public function loginobtener()
    {
        return view('login');
    }
    public function configview()
    {
        return view('configuracion');
    }
    public function salirdelconfig()
    {
        return view('inicio');
    }
    public function volveralinicio()
    {
        return view('inicio');
    }
    public function volveralperfil()
    {
        return view('perfil');
    }
    
    

    //ACA EMPIEZA LA VALIDACIÓN



    public function forgotPPassword()
    {
        $session = session();
        $userlogin = new UserModel();

        // Captura el email ingresado
        $email = $this->request->getPost('email');

        // Verifica si el usuario existe
        $user = $userlogin->where('email', $email)->first();

        if ($user) {

            // Verifica los valores antes de hacer la actualización
            $token = bin2hex(random_bytes(50));
            $expires = Time::now()->addHours(1);

        
            
            $updated = $userlogin->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires->toDateTimeString(),
            ]);

                  // Genera el enlace de restablecimiento de contraseña
        $resetLink = base_url("/reset-password/$token");

            $email = \Config\Services::email();
            
            $email->setTo($user['email']);
            $email->setFrom('againsafegas.ascii@gmail.com', 'ASG');
            $email->setSubject('Recuperación de contraseña');
            $email->setMessage("Haz clic en este enlace para recuperar tu contraseña: " . $resetLink);

            if ($email->send()) {
              
            // El correo se envió correctamente
            } else {
            // Error en el envío
            $data = $email->printDebugger(['headers']);
            log_message('error', 'Error enviando correo: ' . $data);
            }
            
            
            if ($updated) {
                echo "Actualización exitosa";
            } else {
                echo "Actualización fallida";
            }
            

            // Enviar un correo electrónico con el enlace de recuperación
            $resetLink = base_url("/reset-password/$token");
            // Aquí podrías enviar el correo con tu sistema de email

            $session->setFlashdata('success', 'Se ha enviado un enlace de recuperación a tu correo.');
            return redirect()->back();
        } else {
            $session->setFlashdata('error', 'Correo electrónico no encontrado.');
            return redirect()->back();
        }       
    }
    public function loginobtenerforgot()
    {
        return view('login');
    }



    
    public function forgotpassword()
    {
        return view('forgotpassword');
    }

    public function showResetPasswordForm($token)
    {
        $userlogin = new UserModel();
        $user = $userlogin->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
            return view('reset_password', ['token' => $token]);
        } else {
            session()->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->to('/forgotpassword');
        }
    }

    public function resetPassword()
    {
        $session = session();
        $userlogin = new UserModel();

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Busca el usuario por el token
        $user = $userlogin->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
            // Actualiza la contraseña y elimina el token
            $userlogin->update($user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_expires' => null,
            ]);

            $session->setFlashdata('success', 'Tu contraseña ha sido actualizada.');
            return redirect()->to('/');
        } else {
            $session->setFlashdata('error', 'Token de recuperación inválido o expirado.');
            return redirect()->back();
        }
    }
    public function inicioresetpass()
    {
        return view('reset_password');
    }
    public function obtenerperfil(){
        return view('perfilobtener');
    }

    //función para la vista
    public function dispositivos()
{
    return view('dispositivos');
}
}