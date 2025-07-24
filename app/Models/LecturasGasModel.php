<?php

namespace App\Models;
use CodeIgniter\Model;

class LecturasGasModel extends Model
{
    protected $table = 'lecturas_gas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'MAC', 'nivel_gas', 'fecha'];
    protected $useTimestamps = false; // Si no usas 'created_at' y 'updated_at' automáticos

    /**
     * Recupera las lecturas de gas para una MAC específica.
     * Ordena las lecturas de la más reciente a la más antigua (DESC).
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return array Array de lecturas.
     */
    public function getLecturasPorMac(string $mac, ?string $fechaInicio = null, ?string $fechaFin = null): array
{
    $builder = $this->db->table($this->table)
        ->where('MAC', $mac);

    if ($fechaInicio) {
        $builder->where('fecha >=', $fechaInicio . ' 00:00:00');
    }
    if ($fechaFin) {
        $builder->where('fecha <=', $fechaFin . ' 23:59:59');
    }

    return $builder
        ->orderBy('fecha', 'DESC')
        ->get()
        ->getResultArray();
}

    /**
     * Recupera las lecturas de gas asociadas a un usuario específico.
     * Realiza un join con las tablas 'dispositivos' y 'enlace'.
     *
     * @param int $id_usuario El ID del usuario.
     * @return array Array de lecturas con detalles del dispositivo.
     */
    public function getLecturasPorUsuario(int $id_usuario): array
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

    /**
     * Migra datos de 'lecturas_gas' a la nueva tabla 'registros_gas'.
     * Este método solo es necesario si estás realizando una migración única.
     * Después de la migración, se puede eliminar o comentar.
     *
     * @return int Número de registros migrados.
     */
    public function migrarARegistrosGas(): int
    {
        $db = \Config\Database::connect();
        $builder = $db->table('registros_gas'); // Asegúrate de que esta tabla exista y tenga los campos adecuados

        // Obtener todas las lecturas de la tabla original
        $lecturas = $this->findAll();
        $migrados = 0;

        foreach ($lecturas as $lectura) {
            // Verificar si ya existe en registros_gas para evitar duplicados
            $existe = $builder->where('dispositivo_id', $lectura['MAC'])
                              ->where('fecha', $lectura['fecha'])
                              ->countAllResults();

            if (!$existe) {
                // Obtener datos del dispositivo para la nueva tabla
                $dispositivoModel = new DispositivoModel();
                $dispositivo = $dispositivoModel->getDispositivoByMac($lectura['MAC']);

                $data = [
                    'dispositivo_id' => $lectura['MAC'],
                    'nombre_dispositivo' => $dispositivo['nombre'] ?? $lectura['MAC'],
                    'ubicacion' => $dispositivo['ubicacion'] ?? 'Desconocida',
                    'nivel_gas' => $lectura['nivel_gas'],
                    'fecha' => $lectura['fecha'],
                    'estado' => $this->determinarEstado((float)$lectura['nivel_gas'])
                ];

                $builder->insert($data);
                $migrados++;
            }
        }

        return $migrados;
    }

    /**
     * Determina el estado de seguridad basado en el nivel de gas.
     *
     * @param float $nivelGas El nivel de gas en PPM.
     * @return string El estado ('peligro', 'precaucion', 'seguro').
     */
    protected function determinarEstado(float $nivelGas): string
    {
        if ($nivelGas >= 500) return 'peligro';
        if ($nivelGas >= 200) return 'precaucion';
        return 'seguro';
    }
}
