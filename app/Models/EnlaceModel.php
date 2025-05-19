<?php

namespace App\Models;

use CodeIgniter\Model;

class EnlaceModel extends Model
{
    protected $table = 'enlace'; // Nombre de tu tabla de enlaces usuario-dispositivo
    protected $primaryKey = 'id'; // Llave primaria de la tabla
    // Campos permitidos para insertar o actualizar.
    // Asegúrate de que estos campos coincidan exactamente con las columnas de tu tabla 'enlace'.
    protected $allowedFields = ['id_usuario', 'MAC'];
    // Habilita el uso de timestamps si tu tabla tiene created_at y updated_at
    protected $useTimestamps = false; // Tu modelo actual tiene useTimestamps = false
    // public $timestamps = false; // Esta propiedad es redundante si useTimestamps es false
}
