<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturaModel extends Model
{
    protected $table = 'lecturas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'nivel_gas', 'updated_at'];

    // Función para actualizar la lectura existente o insertar si no hay ninguna
    public function updateLectura($usuario_id, $nivel_gas)
    {
        // Intenta obtener el registro del usuario especificado
        $lectura = $this->where('usuario_id', $usuario_id)->first();

        if ($lectura) {
            // Si existe, actualiza el nivel de gas y la fecha
            return $this->update($lectura['id'], ['nivel_gas' => $nivel_gas, 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            // Si no existe, inserta un nuevo registro
            return $this->insert(['usuario_id' => $usuario_id, 'nivel_gas' => $nivel_gas, 'updated_at' => date('Y-m-d H:i:s')]);
        }
    }
}
