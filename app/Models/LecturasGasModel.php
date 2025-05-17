<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas'; // Nombre de tu tabla de lecturas de gas
    protected $primaryKey = 'id'; // Llave primaria de la tabla

    // Campos permitidos para insertar o actualizar.
    // Asegúrate de que estos campos coincidan exactamente con las columnas de tu tabla 'lecturas_gas'.
    // --- CORRECCIÓN: Cambiar 'id_usuario' a 'usuario_id' para que coincida con el uso en getLecturasPorUsuario y asumiendo el nombre de la columna ---
    // También asegúrate de incluir 'fecha' y 'MAC'. Si tienes 'update_at' en la DB, inclúyelo aquí.
    protected $allowedFields = ['usuario_id', 'MAC', 'nivel_gas', 'fecha'];
    // --- FIN CORRECCIÓN ---

    // Habilita el uso de timestamps si tu tabla tiene created_at y updated_at
    // Tu modelo actual tiene useTimestamps = false. Si necesitas 'update_at', debes gestionarlo manualmente
    // o cambiar useTimestamps a true y definir $updatedField.
    protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';


    // Método para obtener lecturas de gas por la dirección MAC
    public function getLecturasPorMac($mac)
    {
        // Consulta a la tabla 'lecturas_gas' filtrando por MAC
        return $this->db->table($this->table) // Usamos $this->table para referencia al nombre de la tabla del modelo
            ->where('MAC', $mac)
            ->orderBy('fecha', 'DESC') // Ordenar por fecha descendente (más reciente primero)
            ->get()
            ->getResultArray(); // Obtener resultados como array de arrays
    }

    // Método para obtener lecturas de gas por el id del usuario
    // Este método ya usa 'enlace.id_usuario' (correcto para 'enlace')
    // y 'lecturas_gas.usuario_id' (correcto para 'lecturas_gas').
    // Realiza un JOIN para relacionar lecturas, dispositivos y enlaces.
    public function getLecturasPorUsuario($id_usuario)
    {
        return $this->db->table($this->table) // Consulta principal a 'lecturas_gas'
            ->select('lecturas_gas.*, dispositivos.MAC, dispositivos.nombre as dispositivo_nombre, dispositivos.ubicacion as dispositivo_ubicacion') // Selecciona campos, incluyendo nombre y ubicación del dispositivo
            ->join('dispositivos', 'lecturas_gas.MAC = dispositivos.MAC', 'left') // JOIN con 'dispositivos' por MAC (LEFT JOIN para incluir lecturas sin dispositivo si las hubiera)
            ->join('enlace', 'dispositivos.MAC = enlace.MAC', 'inner') // JOIN con 'enlace' por MAC (INNER JOIN para solo lecturas de dispositivos enlazados)
            ->where('enlace.id_usuario', $id_usuario) // Filtra por el ID del usuario en la tabla 'enlace'
            ->where('lecturas_gas.MAC IS NOT NULL') // Asegura que la MAC en lecturas_gas no sea nula
            ->orderBy('lecturas_gas.fecha', 'DESC') // Ordenar por fecha descendente
            ->get()
            ->getResultArray(); // Obtener resultados como array de arrays
    }

    // Método para insertar una nueva lectura (ya lo tienes en LecturasController, pero es común tenerlo en el modelo)
    // public function addLectura($data)
    // {
    //     return $this->insert($data);
    // }
}
