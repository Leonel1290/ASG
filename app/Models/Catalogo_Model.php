<?php namespace App\Models;

use CodeIgniter\Model;

class Catalogo_Model extends Model
{
    // Nombre de la tabla de catálogo que creamos
    protected $table = 'catalogo_dispositivos'; 

    // Campo de clave primaria
    protected $primaryKey = 'id';

    // Campos que se pueden modificar
    protected $allowedFields = ['nombre_modelo', 'url_imagen', 'activo']; 
    
    // Indica si se usan timestamps (ajustar si es necesario)
    protected $useTimestamps = false; 

    /**
     * Obtiene todos los modelos de dispositivos activos para el catálogo.
     * @return array|object Los resultados de la consulta.
     */
    public function obtenerModelosCatalogo()
    {
        // Excluímos el modelo 'ASG Sentinel' de la lista de "Otros Dispositivos",
        // ya que el usuario ya está viendo ese dispositivo en la tarjeta.
        return $this->select('nombre_modelo, url_imagen')
                    ->where('activo', true)
                    ->where('nombre_modelo !=', 'ASG Sentinel') 
                    ->orderBy('nombre_modelo', 'ASC')
                    ->findAll();
    }
}