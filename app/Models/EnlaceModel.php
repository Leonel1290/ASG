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

    /**
     * Obtiene el ID de usuario asociado a una dirección MAC específica.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return int|null El ID de usuario si se encuentra un enlace, de lo contrario, null.
     */
    public function getUsuarioIdByMac(string $mac): ?int
    {
        // Busca el primer registro en la tabla 'enlace' donde la columna 'MAC' coincida con la MAC proporcionada.
        $enlace = $this->where('MAC', $mac)->first();

        // Si se encuentra un enlace, devuelve el 'id_usuario'.
        // De lo contrario, devuelve null.
        return $enlace ? (int)$enlace['id_usuario'] : null;
    }
}