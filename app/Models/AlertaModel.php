<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertaModel extends Model
{
    protected $table = 'lecturas_gas';

    /**
     * Obtiene todas las lecturas de gas que superan un umbral para un usuario específico.
     *
     * @param int $userId ID del usuario.
     * @param int $umbral El nivel de gas considerado como alerta.
     * @return array
     */
    public function getAlertasPorUsuario($userId, $umbral)
    {
        return $this->db->table('lecturas_gas as lg')
            ->select('lg.nivel_gas, lg.fecha, d.nombre as nombre_dispositivo, d.MAC')
            ->join('enlace as e', 'lg.MAC = e.MAC')
            ->join('dispositivos as d', 'lg.MAC = d.MAC')
            ->where('e.id_usuario', $userId)
            ->where('lg.nivel_gas >', $umbral)
            ->orderBy('lg.fecha', 'DESC')
            ->get()
            ->getResult();
    }
}
