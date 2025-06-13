<?php



namespace App\Models;



use CodeIgniter\Model;

use CodeIgniter\I18n\Time; // Para manejar expiración de tokens



class UserModel extends Model

{

    // Configuración de la base de datos

    protected $DBGroup          = 'default';

    protected $table            = 'usuarios'; // Nombre de tu tabla de usuarios (usado en controladores)

    protected $primaryKey       = 'id';       // Llave primaria de la tabla usuarios (usado en controladores)

    protected $useAutoIncrement = true;

    protected $returnType       = 'array'; // Usaremos 'array' para obtener los resultados

    protected $useSoftDeletes   = false; // No usas soft deletes



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

    protected $dateFormat    = 'datetime'; // O 'int' si guardas timestamps como enteros

    protected $createdField  = 'created_at';

    protected $updatedField  = 'updated_at';

    // protected $deletedField  = 'deleted_at'; // Solo si useSoftDeletes es true



    // Validación (las reglas específicas de registro están en registerController)

    // Puedes añadir reglas de validación por defecto aquí si aplica a todas las operaciones con el modelo.

    protected $validationRules      = [];

    protected $validationMessages   = [];

    protected $skipValidation       = false; // Cambia a true para omitir la validación del modelo

    protected $cleanValidationRules = true;



    // Callbacks (ej. para hashear contraseña, formatear datos antes de guardar, etc.)

    // Tu registerController ya hashea la contraseña antes de guardar, así que no necesitas beforeInsert/beforeUpdate para eso aquí.

    protected $allowCallbacks = true;

    // protected $beforeInsert   = ['hashPassword']; // Ejemplo de callback

    // protected $beforeUpdate   = ['hashPassword']; // Ejemplo de callback



    // Método para encontrar un usuario por su token de verificación/reseteo

    public function getUserByToken(string $token)

    {

        return $this->where('reset_token', $token)->first();

    }



    // NOTA: Tienes otro modelo llamado Userlogin.php que parece apuntar a la misma tabla 'usuarios'.

    // Es recomendable usar UN SOLO modelo (UserModel) para interactuar con la tabla de usuarios

    // y eliminar el modelo Userlogin.php para evitar confusión y redundancia.



    // NOTA ADICIONAL: La migración 2024-09-11-023010_TUsuarios.php crea una tabla llamada 't_usuario'

    // con columnas 'id_usuario', 'usuario', 'password', 'type'.

    // Esto NO coincide con la tabla 'usuarios' y las columnas ('id', 'nombre', 'apellido', 'email', 'is_active', etc.)

    // que tus controladores y este modelo (UserModel) parecen estar utilizando.

    // Debes asegurarte de que la base de datos en Clever Cloud tenga la tabla 'usuarios' con la estructura correcta

    // que tus controladores y UserModel esperan, o ajustar tus controladores/Modelos/Migración

    // para que sean consistentes.

    // Si la migración TUsuarios es antigua o incorrecta, ignórala. Si es la correcta,

    // deberías ajustar tus modelos y controladores para usar 't_usuario', 'id_usuario', 'usuario', etc.

    // Basado en el uso en tus controladores, parece que la tabla 'usuarios' es la que estás usando activamente.

}