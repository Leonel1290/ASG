<?php namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_usuario', 'monto', 'moneda', 'direccion_envio', 'paypal_order_id', 'estado_pago'];

    public function obtenerComprasUsuario($id_usuario)
    {
        return $this->where('id_usuario', $id_usuario)
                   ->orderBy('fecha_compra', 'DESC')
                   ->findAll();
    }

    public function crearCompra($data)
    {
        return $this->insert($data);
    }
}