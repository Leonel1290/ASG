<?php

namespace App\Controllers;

use App\Models\PagoModel; // Asegúrate de que el namespace y el nombre del modelo sean correctos
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class Payment extends Controller
{
    use ResponseTrait;

    /**
     * Guarda la información de una compra en la base de datos.
     * Espera un JSON con los detalles del pago desde el frontend.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function guardarCompra()
    {
        // ***** CAMBIO AQUÍ: Eliminada la verificación isAJAX() temporalmente *****
        if ($this->request->getMethod() !== 'post') {
            return $this->failUnauthorized('Acceso no autorizado: Método no permitido.');
        }


        // Obtener los datos del cuerpo JSON de la solicitud
        $input = $this->request->getJSON();

        log_message('debug', 'Datos recibidos en guardarCompra: ' . json_encode($input));


        if (
            !isset($input->id_usuario) || empty($input->id_usuario) ||
            !isset($input->monto) || empty($input->monto) ||
            !isset($input->moneda) || empty($input->moneda) ||
            !isset($input->paypal_order_id) || empty($input->paypal_order_id)
        ) {
            log_message('error', 'Faltan datos requeridos. Datos: ' . json_encode($input));
            return $this->fail(['message' => 'Faltan datos requeridos para registrar la compra.'], 400);
        }

        $dataToSave = [
            'id_usuario'      => $input->id_usuario,
            'paypal_order_id' => $input->paypal_order_id,
            'monto'           => $input->monto,
            'moneda'          => $input->moneda,
            'estado_pago'     => $input->estado_pago ?? 'completado', // El frontend ya envía 'completado' si llega aquí
        ];


        log_message('debug', 'Datos a guardar en el modelo: ' . json_encode($dataToSave));

        try {
            // Cargar el modelo de Pago
            $pagoModel = new PagoModel();

            // Guardar los datos en la base de datos a través del modelo
            if ($pagoModel->insert($dataToSave)) {
                // Si la inserción fue exitosa
                return $this->respondCreated(['success' => true, 'message' => 'Compra registrada exitosamente.']);
            } else {
                // Si la inserción falló (ej. por reglas de validación del modelo)
                $errors = $pagoModel->errors();
                log_message('error', 'Error al insertar en la BD. Errores del modelo: ' . json_encode($errors));
                return $this->fail(['success' => false, 'message' => 'Error al registrar la compra en la base de datos.', 'errors' => $errors], 500);
            }
        } catch (\Exception $e) {
            // Capturar cualquier excepción inesperada (ej. error de conexión a DB)
            log_message('error', 'Excepción en guardarCompra: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return $this->failServerError('Ocurrió un error interno al intentar registrar la compra.');
        }
    }
}