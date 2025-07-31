<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ComprasModel;

class Compras extends BaseController
{
    use ResponseTrait;

    class Compras extends CI_Controller {

    public function guardar_compra() {
        // Verificar si es una solicitud AJAX y POST
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
    }

        // Obtener datos del POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id_usuario']) || empty($data['monto']) || empty($data['direccion'])) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Preparar datos para la inserción
        $compra_data = [
            'id_usuario' => $data['id_usuario'],
            'monto' => $data['monto'],
            'moneda' => $data['moneda'] ?? 'USD',
            'direccion_envio' => $data['direccion'],
            'paypal_order_id' => $data['paypal_order_id'] ?? null,
            'estado_pago' => $data['estado_pago'] ?? 'pendiente'
        ];

        // Insertar en la base de datos
        $this->load->database();
        $this->db->insert('compras', $compra_data);
        
        if ($this->db->affected_rows() > 0) {
            $compra_id = $this->db->insert_id();
            
            // Aquí podrías agregar lógica adicional como enviar un email de confirmación
            
            echo json_encode([
                'success' => true,
                'message' => 'Compra registrada correctamente',
                'compra_id' => $compra_id
            ]);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'Error al guardar la compra']);
        }
    }

    // Método para mostrar las compras del usuario
    public function mis_compras() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        $this->load->database();
        $this->load->model('Compra_model');
        
        $data['compras'] = $this->Compra_model->obtener_compras_usuario($_SESSION['user_id']);
        
        $this->load->view('header');
        $this->load->view('mis_compras', $data);
        $this->load->view('footer');
    }
}