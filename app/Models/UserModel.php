<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens


class UserModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'usuarios'; // Nombre de tu tabla de usuarios (usado en controladores)
    protected $primaryKey  = 'id';  // Llave primaria de la tabla usuarios (usado en controladores)
    protected $useAutoIncrement = true;
    protected $returnType = 'array'; // Usaremos 'array' para obtener los resultados
    protected $useSoftDeletes  = false; // No usas soft deletes
    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'password',
        'is_active', // Columna para el estado de activación (0 = inactivo, 1 = activo)
        'reset_token', // Columna para almacenar tokens de verificación/reseteo
        'reset_expires', 
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime'; // O 'int' si guardas timestamps como enteros
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation  = false; // Cambia a true para omitir la validación del modelo
    protected $cleanValidationRules = true;
    protected $allowCallbacks = true;
    
    public function getUserByToken(string $token)
    {
        return $this->where('reset_token', $token)->first();
    }
}