<?php

namespace App\Models;
use CodeIgniter\Model;
use App\Models\DispositivoModel; // Asegúrate de que esta línea esté presente

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
     * @param string|null $fechaInicio Fecha de inicio para el filtro (YYYY-MM-DD).
     * @param string|null $fechaFin Fecha de fin para el filtro (YYYY-MM-DD).
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

        // --- INICIO DE LOGGING PARA DEBUGGING ---
        // Para obtener la consulta compilada, necesitas usar getCompiledSelect()
        // log_message('debug', 'LecturasGasModel: getLecturasPorMac - Query: ' . $builder->getCompiledSelect());
        // --- FIN DE LOGGING ---

        $results = $builder
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();

        // **NUEVO CÓDIGO:** Añadir el estado a cada lectura
        foreach ($results as &$lectura) {
            $lectura['estado'] = $this->determinarEstado((float)$lectura['nivel_gas']);
        }
        unset($lectura); // Romper la referencia del último elemento

        return $results;
    }

    /**
     * Recupera la última lectura de gas para una MAC específica.
     *
     * @param string $mac La dirección MAC del dispositivo.
     * @return array|null Un array con la última lectura o null si no se encuentra.
     */
    public function getLatestLecturaPorMac(string $mac): ?array
    {
        $lectura = $this->db->table($this->table)
                    ->where('MAC', $mac)
                    ->orderBy('fecha', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray(); // getRowArray() devuelve un solo registro como array

        // Añadir el estado si se encuentra una lectura
        if ($lectura) {
            $lectura['estado'] = $this->determinarEstado((float)$lectura['nivel_gas']);
        }

        return $lectura;
    }


    /**
     * Recupera las lecturas de gas para un usuario específico.
     * Útil para la vista de perfil donde se listan todas las lecturas de sus dispositivos.
     *
     * @param int $usuarioId El ID del usuario.
     * @return array Array de lecturas.
     */
    public function getLecturasPorUsuario(int $usuarioId): array
    {
        // Une con la tabla 'dispositivos' y 'enlace' para obtener MACs asociadas al usuario
        $results = $this->db->table('lecturas_gas AS lg')
                            ->select('lg.*, d.nombre AS nombre_dispositivo, d.ubicacion')
                            ->join('enlace AS e', 'lg.MAC = e.MAC')
                            ->join('dispositivos AS d', 'lg.MAC = d.MAC', 'left') // LEFT JOIN para dispositivos, ya que MAC podría no tener nombre aún
                            ->where('e.id_usuario', $usuarioId)
                            ->orderBy('lg.fecha', 'DESC')
                            ->get()
                            ->getResultArray();

        // Añadir el estado a cada lectura
        foreach ($results as &$lectura) {
            $lectura['estado'] = $this->determinarEstado((float)$lectura['nivel_gas']);
        }
        unset($lectura); // Romper la referencia del último elemento

        return $results;
    }

    /**
     * Migra datos de la tabla 'lecturas_gas' a la nueva tabla 'registros_gas'.
     * Esta función asume que la nueva tabla 'registros_gas' ya existe y tiene la estructura adecuada.
     * Incluye lógica para evitar duplicados y para obtener datos del dispositivo.
     *
     * @return int El número de registros migrados.
     */
    public function migrarARegistrosGas(): int
    {
        // Este método asume que 'fecha' es una clave compuesta junto con 'dispositivo_id' para evitar duplicados.

        $migrados = 0;
        $builder = $this->db->table('registros_gas'); // Asegúrate de que esta tabla exista.

        // Obtener todas las lecturas de la tabla original
        $lecturasOriginales = $this->findAll();

        foreach ($lecturasOriginales as $lectura) {
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
        if ($nivelGas >= 450) return 'peligro';
        if ($nivelGas >= 300) return 'precaucion';
        return 'seguro';
    }
}