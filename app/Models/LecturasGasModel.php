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

        $results = $builder
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();

        // **NUEVO CÓDIGO:** Añade el 'estado' a cada resultado
        foreach ($results as &$row) { // Usar '&' para modificar el array directamente
            $row['estado'] = $this->determinarEstado((float)$row['nivel_gas']);
        }
        unset($row); // Romper la referencia del último elemento

        return $results;
    }

    /**
     * Recupera las lecturas de gas más recientes para todos los dispositivos de un usuario.
     *
     * @param int $userId ID del usuario.
     * @return array Array asociativo [mac => ultima_lectura].
     */
    public function getLatestLecturasPorUsuario(int $userId): array
    {
        // Obtener todas las MACs asociadas a este usuario
        $dispositivoModel = new DispositivoModel();
        $dispositivos = $dispositivoModel->getDispositivosPorUsuario($userId);

        $latestLecturas = [];
        foreach ($dispositivos as $dispositivo) {
            $mac = $dispositivo['MAC'];
            $lectura = $this->db->table($this->table)
                                ->where('MAC', $mac)
                                ->orderBy('fecha', 'DESC')
                                ->limit(1)
                                ->get()
                                ->getRowArray();

            if ($lectura) {
                // Agregar el estado a la última lectura obtenida
                $lectura['estado'] = $this->determinarEstado((float)$lectura['nivel_gas']);
                $latestLecturas[$mac] = $lectura;
            } else {
                $latestLecturas[$mac] = [
                    'MAC' => $mac,
                    'nivel_gas' => 'N/A',
                    'fecha' => 'Sin datos',
                    'estado' => 'sin_datos' // O un estado por defecto si no hay datos
                ];
            }
        }
        return $latestLecturas;
    }


    /**
     * Migra datos de la tabla 'lecturas_gas' a la nueva tabla 'registros_gas'.
     *
     * @return int Cantidad de registros migrados.
     */
    public function migrarARegistrosGas(): int
    {
        $lecturasAntiguas = $this->findAll(); // Recuperar todos los registros de la tabla antigua
        $migrados = 0;

        $db      = \Config\Database::connect();
        $builder = $db->table('registros_gas'); // La nueva tabla

        foreach ($lecturasAntiguas as $lectura) {
            // Verificar si el registro ya existe para evitar duplicados
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