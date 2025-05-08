<?php

namespace App\Models;

use CodeIgniter\Model;

class DispositivoModel extends Model
{
    protected $table      = 'dispositivos';
    protected $primaryKey = 'id';
    
    // Agregamos 'nombre' y 'ubicacion' a los campos permitidos para ser editados
    protected $allowedFields = ['MAC', 'nombre', 'ubicacion'];
    
    // Habilitamos el uso de timestamps si quieres manejar la fecha de creación o actualización de registros
    protected $useTimestamps = true;

    // Obtener dispositivo por su id
    public function getDispositivoById($id)
    {
        return $this->find($id);
    }

    // Método para actualizar dispositivo por su MAC (esto se usará en el controlador)
    public function updateDispositivoByMac($mac, $data)
    {
        return $this->where('MAC', $mac)->set($data)->update();
    }
    
    // Método para actualizar un dispositivo por su ID
    public function updateDispositivo($id, $data)
    {
        return $this->update($id, $data);
    }
}
