<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse; // Asegúrate de que esta línea esté presente

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // CAMBIO 1: Verifica 'logged_in' (el nombre que estableces en el controlador Home::login())
        // CAMBIO 2: Redirige a '/loginobtener' (la ruta GET para mostrar el formulario)
        if (!session()->get('logged_in')) {
            // Opcional: Para depuración, puedes añadir un log aquí
            log_message('debug', 'AuthFilter: Usuario no autenticado. Redirigiendo a /loginobtener.');
            return redirect()->to('/loginobtener')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Si está logueado, deja pasar al controlador
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitas hacer nada aquí para este caso
    }
}
