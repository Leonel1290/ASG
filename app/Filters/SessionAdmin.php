<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Si el usuario NO está logueado (verificando la variable 'logged_in')
        if (!$session->get('logged_in')) {
            log_message('debug', 'Filtro SessionAdmin: Usuario NO logueado. Redirigiendo a /loginobtener.');
            // Redirige al formulario de login y puedes añadir un mensaje de error
            return redirect()->to('/loginobtener')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Si el usuario SÍ está logueado, permite que la solicitud continúe.
        // No hay necesidad de una redirección aquí; el filtro simplemente otorga acceso.
        log_message('debug', 'Filtro SessionAdmin: Usuario logueado. Acceso concedido.');
        return true;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitamos realizar ninguna acción después de la ejecución de la ruta para este filtro.
    }
}