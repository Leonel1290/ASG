<?php

namespace App\Models;

use CodeIgniter\Model;

class HorariosModel extends Model
{
    protected $table = 'horarios'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'idhorario'; // Llave primaria de la tabla
    protected $allowedFields = [
        'ventana_apertura', 
        'ventana_cierre', 
        'cortina_apertura', 
        'cortina_cierre', 
        'postigon_apertura', 
        'postigon_cierre',
        'usuario_id']; // Campos permitidos para insertar o actualizar
}
