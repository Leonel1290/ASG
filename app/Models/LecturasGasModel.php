<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'MAC', 'nivel_gas', 'fecha'];
    protected $useTimestamps = false;

    // Método para migrar datos a la nueva tabla registros_gas
    public function migrarARegistrosGas()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('registros_gas');
        
        // Obtener todas las lecturas
        $lecturas = $this->findAll();
        $migrados = 0;
        
        foreach ($lecturas as $lectura) {
            // Verificar si ya existe en registros_gas
            $existe = $builder->where('dispositivo_id', $lectura['MAC'])
                            ->where('fecha', $lectura['fecha'])
                            ->countAllResults();
            
            if (!$existe) {
                // Obtener datos del dispositivo
                $dispositivoModel = new DispositivoModel();
                $dispositivo = $dispositivoModel->getDispositivoByMac($lectura['MAC']);
                
                $data = [
                    'dispositivo_id' => $lectura['MAC'],
                    'nombre_dispositivo' => $dispositivo['nombre'] ?? $lectura['MAC'],
                    'ubicacion' => $dispositivo['ubicacion'] ?? 'Desconocida',
                    'nivel_gas' => $lectura['nivel_gas'],
                    'fecha' => $lectura['fecha'],
                    'estado' => $this->determinarEstado($lectura['nivel_gas'])
                ];
                
                $builder->insert($data);
                $migrados++;
            }
        }
        
        return $migrados;
    }
    
    // Método para determinar el estado
    protected function determinarEstado($nivelGas)
    {
        if ($nivelGas >= 500) return 'peligro';
        if ($nivelGas >= 200) return 'precaucion';
        return 'seguro';
    }

    // Resto de tus métodos existentes...
    public function getLecturasPorMac($mac)
    {
        return $this->db->table($this->table)
            ->where('MAC', $mac)
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getLecturasPorUsuario($id_usuario)
    {
        return $this->db->table($this->table)
            ->select('lecturas_gas.*, dispositivos.MAC, dispositivos.nombre as dispositivo_nombre, dispositivos.ubicacion as dispositivo_ubicacion')
            ->join('dispositivos', 'lecturas_gas.MAC = dispositivos.MAC', 'left')
            ->join('enlace', 'dispositivos.MAC = enlace.MAC', 'inner')
            ->where('enlace.id_usuario', $id_usuario)
            ->where('lecturas_gas.MAC IS NOT NULL')
            ->orderBy('lecturas_gas.fecha', 'DESC')
            ->get()
            ->getResultArray();
    }
}