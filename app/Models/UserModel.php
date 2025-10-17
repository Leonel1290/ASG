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
    // **CORRECCIÓN: Se añade 'activation_token' para el flujo de reenvío de verificación.**
    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'password',
        'is_active', // Columna para el estado de activación (0 = inactivo, 1 = activo)
        'reset_token', // Columna para almacenar tokens de verificación/reseteo (usado en recuperación de contraseña)
        'reset_expires', // Columna para almacenar la expiración del token (usado en recuperación de contraseña)
        'activation_token', // <--- CAMPO AÑADIDO PARA LA VERIFICACIÓN DE EMAIL DE CONFIGURACIÓN
        // 'created_at' y 'updated_at' se manejan automáticamente si useTimestamps es true
    ];

    // Configuración de Timestamps (created_at y updated_at)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // O 'int' si guardas timestamps como enteros
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Solo si useSoftDeletes es true

    // Validación (las reglas específicas de registro están en registerController)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false; // Cambia a true para omitir la validación del modelo
    protected $cleanValidationRules = true;

    // Callbacks (ej. para hashear contraseña, formatear datos antes de guardar, etc.)
    protected $allowCallbacks = true;
    // protected $beforeInsert   = ['hashPassword']; // Ejemplo de callback
    // protected $beforeUpdate   = ['hashPassword']; // Ejemplo de callback

    // Método para encontrar un usuario por su token de verificación/reseteo
    public function getUserByToken(string $token)
    {
        return $this->where('reset_token', $token)->first();
    }
}