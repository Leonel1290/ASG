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

        // Log en vez de var_dump para evitar errores de headers
        log_message('debug', json_encode($session->get()));

        if (!$session->get('logged_in')) {
            return view('login'); // corregido: sin "/"
        }

        return view('inicio');
    }

    public function login()
{
    $session = session();
    $userlogin = new Userlogin();
    $lecturasGasModel = new \App\Models\LecturasGasModel();

    $nombre = $this->request->getPost('nombre');
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $user = $userlogin->where('nombre', $nombre)
                      ->where('email', $email)
                      ->first();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $ultimaLectura = $lecturasGasModel
                ->orderBy('id', 'DESC')
                ->where(['usuario_id' => $user['id']])
                ->asArray()
                ->first();

            $nivel_gas = $ultimaLectura['nivel_gas'] ?? null;
            $mensaje = $ultimaLectura ? null : 'No tienes registros de lecturas';

            $session->set([
                'id'        => $user['id'],
                'nombre'    => $user['nombre'],
                'email'     => $user['email'],
                'logged_in' => true,
            ]);

            return redirect()->to('/perfil');
        } else {
            $session->setFlashdata('error', 'Contraseña incorrecta');
            return redirect()->back();
        }
    } else {
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

    // Recuperación de contraseña

    public function forgotPPassword()
    {
        $session = session();
        $userlogin = new UserModel();
        $emailInput = $this->request->getPost('email');
        $user = $userlogin->where('email', $emailInput)->first();

        if ($user) {
            $token = bin2hex(random_bytes(50));
            $expires = Time::now()->addHours(1);

            $updated = $userlogin->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires->toDateTimeString(),
            ]);

            $resetLink = base_url("/reset-password/$token");

            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setFrom('againsafegas.ascii@gmail.com', 'ASG');
            $email->setSubject('Recuperación de contraseña');
            $email->setMessage("Haz clic en este enlace para recuperar tu contraseña: " . $resetLink);

            if (!$email->send()) {
                $data = $email->printDebugger(['headers']);
                log_message('error', 'Error enviando correo: ' . $data);
            }

            if ($updated) {
                log_message('debug', 'Actualización de token exitosa');
            } else {
                log_message('error', 'Actualización de token fallida');
            }

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

        $user = $userlogin->where('reset_token', $token)
                          ->where('reset_expires >=', Time::now()->toDateTimeString())
                          ->first();

        if ($user) {
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
    public function storeMac()
{
    $session = session();
    $mac = $this->request->getPost('mac');

    if (!$session->get('logged_in')) {
        return redirect()->to('/login');
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

    return redirect()->to('/perfil');
}
    public function inicioresetpass()
    {
        return view('reset_password');
    }

    public function obtenerperfil()
    {
        return view('perfilobtener');
    }

    // Vistas varias

    public function dispositivos()
    {
        return view('dispositivos');
    }

    public function perfil()
{
    $session = session();

    if (!$session->get('logged_in')) {
        return redirect()->to('/login');
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
        'lecturas' => $lecturas
    ]);
}

    public function comprar()
    {
        return view('comprar');
    }
}