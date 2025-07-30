<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens

class UserModel extends Model
{
    // Configuración de la base de datos
    protected $DBGroup          = 'default';
    protected $table            = 'usuarios'; // Nombre de tu tabla de usuarios (usado en controladores)
    protected $primaryKey       = 'id';       // Llave primaria de la tabla usuarios (usado en controladores)
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Usaremos 'array' para obtener los resultados
    protected $useSoftDeletes   = false; // No usas soft deletes

    // Campos permitidos para insertar o actualizar.
    // Asegúrate de que estos campos coincidan exactamente con las columnas de tu tabla 'usuarios'.
    // Basado en el uso en tus controladores, deberías tener:
    // id (auto-increment), nombre, apellido, email, password, is_active, reset_token, reset_expires, created_at, updated_at
    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'password',
        'is_active', // Columna para el estado de activación (0 = inactivo, 1 = activo)
        'reset_token', // Columna para almacenar tokens de verificación/reseteo
        'reset_expires', // Columna para almacenar la expiración del token
        // 'created_at' y 'updated_at' se manejan automáticamente si useTimestamps es true
    ];

    // Configuración de Timestamps (created_at y updated_at)
    // Habilita esto si tu tabla 'usuarios' tiene columnas `created_at` y `updated_at`
    // y quieres que CodeIgniter las gestione automáticamente en inserciones y actualizaciones.
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // O 'int' si guardas timestamps como enteros
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Solo si useSoftDeletes es true

    // Validación (las reglas específicas de registro están en registerController)
    // Puedes añadir reglas de validación por defecto aquí si aplica a todas las operaciones con el modelo.
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false; // Cambia a true para omitir la validación del modelo
    protected $cleanValidationRules = true;

    // Callbacks (ej. para hashear contraseña, formatear datos antes de guardar, etc.)
    // Tu registerController ya hashea la contraseña antes de guardar, así que no necesitas beforeInsert/beforeUpdate para eso aquí.
    protected $allowCallbacks = true;
    // protected $beforeInsert   = ['hashPassword']; // Ejemplo de callback
    // protected $beforeUpdate   = ['hashPassword']; // Ejemplo de callback

    // Método para encontrar un usuario por su token de verificación/reseteo
    public function getUserByToken(string $token)
    {
        return $this->where('reset_token', $token)->first();
    }
    
public function isCommonPassword($password)
{
    $commonPasswords = [
        'password', '123456', '123456789', 'qwerty', 'password1', 
        '12345678', '111111', '123123', '1234567890', '1234567',
        'letmein', 'admin', 'welcome', 'monkey', 'sunshine',
        'football', 'iloveyou', 'starwars', 'dragon', 'trustno1'
        // Puedes agregar más contraseñas comunes según sea necesario
    ];
    
    return in_array(strtolower($password), $commonPasswords);
}
}
