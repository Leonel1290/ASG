<?php namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_compras_usuario($id_usuario) {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->order_by('fecha_compra', 'DESC')
            ->get('compras')
            ->result_array();
    }

    public function crear_compra($data) {
        $this->db->insert('compras', $data);
        return $this->db->insert_id();
    }
}