<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['form', 'url']; // Helpers globales

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
{
    parent::initController($request, $response, $logger);

    // 1. Configurar idioma por defecto si no está en sesión
    if (!session()->has('lang')) {
        $userModel = new \App\Models\UserModel();
        
        // 2. Intentar obtener idioma del usuario si está logueado
        if (session()->has('id')) {
            $user = $userModel->find(session()->get('id'));
            if ($user && !empty($user['idioma'])) {
                session()->set('lang', $user['idioma']);
            }
        }
        
        // 3. Fallback a español
        if (!session()->has('lang')) {
            session()->set('lang', 'es');
        }
    }

    // 4. Cargar archivo de idioma correspondiente
    $idioma = session('lang');
    $this->language = \Config\Services::language();
    $this->language->setLocale($idioma);
}
}