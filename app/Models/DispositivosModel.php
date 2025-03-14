namespace App\Models;

use CodeIgniter\Model;

class DispositivosModel extends Model
{
    protected $table = 'dispositivos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'nombre', 'numero_serie', 'estado', 'ultima_conexion', 'created_at'];
}
