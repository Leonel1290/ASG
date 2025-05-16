<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time; // Para manejar expiración de tokens

class UserModel extends Model
{
    // Configuración de la base de datos
    protected $DBGroup          = 'default';
    protected $table            = 'usuarios'; // Nombre de tu tabla de usuarios
    protected $primaryKey       = 'id';       // Llave primaria de la tabla usuarios
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Usaremos 'array'
    protected $useSoftDeletes   = false;

    // Campos permitidos para insertar o actualizar.
    // Añadimos 'is_active'. Asegúrate de que todos los campos relevantes estén aquí.
    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'password',
        'is_active', // <-- NUEVA COLUMNA añadida manualmente a la DB
        'reset_token', // Reutilizado para el token de verificación de registro y reset pass
        'reset_expires', // Reutilizado para la expiración del token
    ];

    // Configuración de Timestamps (created_at y updated_at)
    // Basado en tu script SQL, estas columnas existen y se manejan automáticamente.
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // No aplica

    // Validación (las reglas específicas de registro están en registerController)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks (ej. para hashear contraseña).
    // Tu registerController ya hashea la contraseña antes de guardar.
    protected $allowCallbacks = true;
    // protected $beforeInsert   = ['hashPassword'];
    // protected $beforeUpdate   = ['hashPassword'];

    // Método para encontrar un usuario por su token de verificación/reseteo
    public function getUserByToken(string $token)
    {
        return $this->where('reset_token', $token)->first();
    }
}
