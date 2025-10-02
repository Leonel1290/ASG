<?php

namespace App\Controllers;

use App\Models\DispositivoModel;
use CodeIgniter\API\ResponseTrait; // Para manejar respuestas JSON si es necesario (opcional para AJAX)
use CodeIgniter\Controller;

class ValveController extends Controller
{
    protected $dispositivoModel;

    public function __construct()
    {
        $this->dispositivoModel = new DispositivoModel();
    }

    /**
     * Función principal para controlar la válvula (abrir/cerrar) desde la página web.
     * Esta función puede ser llamada vía POST desde un formulario o AJAX en la vista (ej. detalles.php).
     * 
     * @param string $action 'open' para abrir o 'close' para cerrar la válvula.
     * @param string $mac La MAC del dispositivo (se obtiene del request POST o sesión si no se proporciona).
     * 
     * Ejemplo de uso en rutas (agrega esto a Routes.php si no existe):
     * $routes->post('valve/control', 'ValveController::controlValve');
     * 
     * En la vista (detalles.php), usa un formulario POST con botones:
     * <form method="POST" action="/valve/control">
     *     <input type="hidden" name="mac" value="<?= $dispositivo->MAC ?>">
     *     <input type="hidden" name="action" value="open">
     *     <button type="submit">Abrir Válvula</button>
     * </form>
     * 
     * Similar para cerrar con action="close".
     * 
     * Para AJAX (opcional, en JavaScript):
     * fetch('/valve/control', { method: 'POST', body: new FormData(form) }).then(...);
     */
    public function controlValve()
    {
        $session = session();
        
        // Verificar si el usuario está logueado
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para controlar la válvula.');
        }

        // Obtener la acción (open/close) y la MAC del request POST
        $action = $this->request->getPost('action');
        $mac = $this->request->getPost('mac') ?? $session->get('MAC') ?? null; // Fallback a sesión si no se envía

        // Validar parámetros requeridos
        if (empty($mac) || !in_array($action, ['open', 'close'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Parámetros inválidos: MAC o acción (open/close) requeridos.'
            ]);
        }

        // Verificar permisos: El usuario debe tener enlace con esta MAC
        $enlaceModel = new \App\Models\EnlaceModel();
        $tieneAcceso = $enlaceModel->where('id_usuario', $session->get('id'))
                                  ->where('MAC', $mac)
                                  ->first();

        if (!$tieneAcceso) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'No tienes permiso para controlar este dispositivo.'
            ]);
        }

        // Actualizar el estado de la válvula en la DB
        $estado = ($action === 'open') ? 1 : 0; // 1 = abierta, 0 = cerrada (consistente con tu modelo)
        $updated = $this->dispositivoModel->updateDispositivoByMac($mac, [
            'estado_valvula' => $estado,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            // Respuesta exitosa (puede ser JSON para AJAX o redirección para formulario estándar)
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Válvula ' . ($action === 'open' ? 'abierta' : 'cerrada') . ' correctamente.',
                    'new_state' => $estado
                ]);
            } else {
                return redirect()->to('/detalles/' . $mac)->with('success', 'Válvula ' . ($action === 'open' ? 'abierta' : 'cerrada') . ' correctamente.');
            }
        } else {
            // Error en la actualización
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Error al actualizar el estado de la válvula.'
                ]);
            } else {
                return redirect()->to('/detalles/' . $mac)->with('error', 'Error al actualizar el estado de la válvula.');
            }
        }
    }
}