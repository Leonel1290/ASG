<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LecturasGasModel;
use App\Models\CompraDispositivoModel;
use App\Models\DispositivoModel;
use App\Models\EnlaceModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Home extends BaseController
{
    protected $userModel;
    protected $lecturasGasModel;
    protected $compraDispositivoModel;
    protected $dispositivoModel;
    protected $enlaceModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->lecturasGasModel = new LecturasGasModel();
        $this->compraDispositivoModel = new CompraDispositivoModel();
        $this->dispositivoModel = new DispositivoModel();
        $this->enlaceModel = new EnlaceModel();
    }

    public function index()
    {
        return view('inicio');
    }

    public function inicio()
    {
        $session = session();
        log_message('debug', 'Home::inicio() - Estado de la sesión: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::inicio() - Usuario no logueado, redirigiendo a login.');
            return view('login');
        }

        log_message('debug', 'Home::inicio() - Usuario logueado, mostrando vista inicio.');
        return view('inicio');
    }

    public function loginobtener()
    {
        return view('login');
    }

    public function login()
    {
        $session = session();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0) {
                return redirect()->back()->with('error', 'Tu cuenta no ha sido verificada. Por favor, revisa tu correo electrónico para el enlace de verificación.');
            }

            $ses_data = [
                'id'        => $user['id'],
                'nombre'    => $user['nombre'],
                'apellido'  => $user['apellido'],
                'email'     => $user['email'],
                'logged_in' => TRUE,
                'is_admin'  => $user['is_admin'] ?? 0 // <-- CORRECCIÓN APLICADA AQUÍ
            ];
            $session->set($ses_data);

            $redirectUrl = session()->getFlashdata('redirect_after_login');
            if ($redirectUrl) {
                session()->removeFlashdata('redirect_after_login');
                return redirect()->to($redirectUrl)->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '! Por favor, completa tu acción.');
            } else {
                return redirect()->to('/inicio')->with('success', '¡Bienvenido de nuevo, ' . $user['nombre'] . '!');
            }
        } else {
            return redirect()->back()->with('error', 'Email o contraseña incorrectos.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/loginobtener')->with('info', 'Has cerrado sesión correctamente.');
    }

    public function perfil()
    {
        $session = session();
        log_message('debug', 'Home::perfil() - Estado de la sesión al inicio: ' . json_encode($session->get()));

        if (!$session->get('logged_in')) {
            log_message('debug', 'Home::perfil() - Usuario no logueado, redirigiendo a login.');
            return redirect()->to('/loginobtener');
        }

        $usuarioId = $session->get('id');

        $macs = $this->enlaceModel
                    ->select('MAC')
                    ->where('id_usuario', $usuarioId)
                    ->groupBy('MAC')
                    ->findAll();

        $lecturas = $this->lecturasGasModel->getLecturasPorUsuario($usuarioId);

        log_message('debug', 'Home::perfil() - Lecturas por usuario obtenidas: ' . print_r($lecturas, true));

        return view('perfil', [
            'macs' => $macs,
            'lecturas' => $lecturas
        ]);
    }

    public function comprar()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            $session->setFlashdata('redirect_after_login', '/comprar');
            log_message('debug', 'Home::comprar() - Usuario no logueado, redirigiendo a registro.');
            return redirect()->to('/register')->with('info', 'Para comprar un dispositivo, por favor, regístrate o inicia sesión.');
        }

        log_message('debug', 'Home::comprar() - Usuario logueado, mostrando vista de compra.');
        return view('comprar');
    }

    public function registrarCompraAutomatica($macComprada, $transaccionId = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            log_message('error', 'Intento de registrar compra automática sin usuario logueado.');
            return $this->failUnauthorized('Debe iniciar sesión para registrar una compra.');
        }

        $idUsuarioComprador = $session->get('id');

        if (!preg_match('/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i', $macComprada)) {
            log_message('error', 'Formato de MAC inválido al registrar compra automática: ' . $macComprada);
            return $this->fail('Formato de MAC inválido.', 400);
        }
        $macComprada = strtoupper(str_replace(['-', ' ', ':'], '', $macComprada));

        $dispositivoExiste = $this->dispositivoModel->where('MAC', $macComprada)->first();
        if (!$dispositivoExiste) {
            log_message('error', 'Intento de registrar compra para MAC inexistente en `dispositivos`: ' . $macComprada);
            return $this->fail('MAC del dispositivo no registrada en el sistema.', 404);
        }

        $compraExistente = $this->compraDispositivoModel
                                 ->where('id_usuario_comprador', $idUsuarioComprador)
                                 ->where('MAC_dispositivo', $macComprada)
                                 ->first();

        if ($compraExistente) {
            log_message('info', 'Compra para usuario ' . $idUsuarioComprador . ' y MAC ' . $macComprada . ' ya registrada.');
            return $this->respond(['status' => 'info', 'message' => 'Esta MAC ya ha sido comprada por su cuenta.'], 200);
        }

        $dataCompra = [
            'id_usuario_comprador'  => $idUsuarioComprador,
            'MAC_dispositivo'       => $macComprada,
            'transaccion_paypal_id' => $transaccionId
        ];

        if ($this->compraDispositivoModel->insert($dataCompra)) {
            log_message('info', 'Compra de dispositivo ' . $macComprada . ' registrada automáticamente para usuario ' . $idUsuarioComprador . '.');
            return $this->respondCreated(['status' => 'success', 'message' => 'Compra registrada automáticamente.']);
        } else {
            $errors = $this->compraDispositivoModel->errors();
            log_message('error', 'Error al registrar compra automática: ' . json_encode($errors));
            return $this->failServerError('Error al registrar la compra automáticamente: ' . implode(', ', $errors));
        }
    }

    public function forgotpassword()
    {
        return view('forgot_password');
    }

    public function forgotPPassword()
    {
        return redirect()->back()->with('info', 'Instrucciones enviadas a tu correo.');
    }

    public function showResetPasswordForm($token)
    {
        return view('reset_password_form', ['token' => $token]);
    }

    public function resetPassword()
    {
        return redirect()->to('/loginobtener')->with('success', 'Contraseña restablecida correctamente.');
    }
}
