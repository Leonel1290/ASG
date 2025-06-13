<?php namespace App\Controllers;

use App\Models\ComprasDispositivosModel;
use App\Models\EnlaceModel; // Si este modelo está siendo usado aquí, es un indicador

class PaypalController extends BaseController
{
    public function success()
    {
        // ... (Tu código para obtener los parámetros de PayPal y capturar la orden) ...

        if (/* la captura de PayPal es exitosa */) {
            // ... (Código para obtener $mac_dispositivo_comprado y $id_usuario_comprador) ...

            // Esto DEBE QUEDARSE: Registrar la compra
            $comprasModel = new ComprasDispositivosModel();
            $comprasModel->insert([
                'id_usuario_comprador' => $id_usuario_comprador,
                'MAC_dispositivo' => $mac_dispositivo_comprado,
                'fecha_compra' => date('Y-m-d H:i:s'),
                'transaccion_paypal_id' => $transaccionId, // Reemplaza con el ID real
                'monto' => $monto, // Reemplaza con el monto real
                'estado' => 'Completado'
            ]);

            // ESTO ES LO QUE PROBABLEMENTE QUIERES ELIMINAR O COMENTAR:
            // Lógica que asocia automáticamente la MAC en la tabla 'enlace'
            // Podría ser algo así:
            /*
            $enlaceModel = new EnlaceModel();
            $enlaceModel->insert([
                'usuario_id' => $id_usuario_comprador,
                'MAC' => $mac_dispositivo_comprado,
                'nombre_dispositivo' => 'Dispositivo Recién Comprado', // O similar
                'ubicacion' => 'Pendiente', // O similar
                'fecha_enlace' => date('Y-m-d H:i:s')
            ]);
            */
            // FIN DE LA SECCIÓN A ELIMINAR/COMENTAR

            // Redirigir al usuario a una página de confirmación de compra, no de enlace
            return redirect()->to(base_url('/compra-exitosa'))->with('success', '¡Tu compra se ha completado con éxito!');

        } else {
            // ... (Manejo de errores de PayPal) ...
            return redirect()->to(base_url('/paypal/cancel'))->with('error', 'Hubo un problema al procesar tu pago.');
        }
    }
}
