<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verifica si el usuario está logueado (puedes cambiar 'isLoggedIn' por tu variable de sesión)
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Si está logueado, deja pasar al controlador
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitas hacer nada aquí para este caso
    }
}
