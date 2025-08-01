<?php

namespace App\Controllers;

use App\Models\PagoModel; 
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class Payment extends Controller
{
    use ResponseTrait; // Permite usar métodos para respuestas API (e.g., respondCreated, fail)

    /**
     * Guarda la información de una compra en la base de datos.
     * Espera un JSON con los detalles del pago desde el frontend.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function guardarCompra()
    {
        // Asegúrate de que la solicitud sea POST y que el cuerpo sea JSON
        if ($this->request->getMethod() !== 'post' || !$this->request->isAJAX()) {
            return $this->failUnauthorized('Acceso no autorizado.');
        }

        // Obtener los datos del cuerpo JSON de la solicitud
        $input = $this->request->getJSON();

        // Validar que los datos esenciales estén presentes
        // Aquí puedes añadir más validaciones según tus necesidades (ej. tipo de dato, formato)
        if (
            !isset($input->id_usuario) || empty($input->id_usuario) ||
            !isset($input->monto) || empty($input->monto) ||
            !isset($input->moneda) || empty($input->moneda) ||
            !isset($input->direccion) || empty($input->direccion) ||
            !isset($input->paypal_order_id) || empty($input->paypal_order_id)
        ) {
            return $this->fail(['message' => 'Faltan datos requeridos para registrar la compra.'], 400);
        }

        // Asignar los valores a variables, con valores por defecto si es necesario
        $dataToSave = [
            'id_usuario'      => $input->id_usuario,
            'paypal_order_id' => $input->paypal_order_id,
            'monto'           => $input->monto,
            'moneda'          => $input->moneda,
            'direccion_envio' => $input->direccion,
            'estado_pago'     => $input->estado_pago ?? 'completado', // El frontend ya envía 'completado' si llega aquí
        ];

        try {
            // Cargar el modelo de Pago
            $pagoModel = new PagoModel();

            // Guardar los datos en la base de datos a través del modelo
            if ($pagoModel->insert($dataToSave)) {
                // Si la inserción fue exitosa
                return $this->respondCreated(['success' => true, 'message' => 'Compra registrada exitosamente.']);
            } else {
                // Si la inserción falló (ej. por reglas de validación del modelo)
                // Puedes obtener los errores de validación del modelo si los has configurado
                return $this->fail(['success' => false, 'message' => 'Error al registrar la compra en la base de datos.', 'errors' => $pagoModel->errors()], 500);
            }
        } catch (\Exception $e) {
            // Capturar cualquier excepción inesperada (ej. error de conexión a DB)
            log_message('error', 'Error en guardarCompra: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error interno al intentar registrar la compra.');
        }
    }
}