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
        // Para obtener la consulta compilada, necesitas usar getCompiledSelect() antes de get()
        // Nota: Una vez que llamas a getCompiledSelect(), el builder se resetea para el get() subsiguiente.
        // Por eso, para debugging, es útil separarlo o reconstruir el builder.
        // Para simplificar, te doy una forma de obtener la consulta.
        $tempBuilder = clone $builder; // Clonamos el builder para que no afecte la consulta original
        $query = $tempBuilder->orderBy('fecha', 'DESC')->getCompiledSelect();
        log_message('debug', "LecturasGasModel: Consulta SQL para getLecturasPorMac: " . $query);
        // --- FIN DE LOGGING ---

        $results = $builder
            ->orderBy('fecha', 'DESC')
            ->get()
            ->getResultArray();

        // --- INICIO DE LOGGING PARA DEBUGGING ---
        log_message('debug', "LecturasGasModel: Resultados de getLecturasPorMac: " . count($results) . " registros.");
        if (empty($results)) {
            log_message('debug', "LecturasGasModel: No se encontraron lecturas en la base de datos para la MAC y el rango de fechas.");
        }
        // --- FIN DE LOGGING ---

        // **NUEVO CÓDIGO (del cambio anterior):** Añadir el campo 'estado' a cada lectura
        foreach ($results as $key => $lectura) {
            // Asegúrate de que 'nivel_gas' sea un float antes de pasarlo a determinarEstado
            $results[$key]['estado'] = $this->determinarEstado((float)$lectura['nivel_gas']);
        }

        return $results;
    }

    /**
     * Recupera las últimas lecturas de gas para cada dispositivo enlazado a un usuario.
     *
     * @param int $userId El ID del usuario.
     * @return array Array de las últimas lecturas.
     */
    public function getLatestLecturasPorUsuario(int $userId): array
    {
        // Une la tabla 'lecturas_gas' con la tabla 'enlace' para filtrar por usuario
        // Y luego con una subconsulta para obtener solo la lectura más reciente por MAC.
        $subquery = $this->db->table('lecturas_gas')
                            ->selectMax('fecha', 'max_fecha')
                            ->select('MAC')
                            ->groupBy('MAC')
                            ->getCompiledSelect();

        return $this->db->table('lecturas_gas AS lg')
                        ->select('lg.*, e.id_usuario') // Selecciona todas las columnas de lecturas_gas y el id_usuario de enlace
                        ->join('enlace AS e', 'lg.MAC = e.MAC')
                        ->join("({$subquery}) AS latest_readings", 'lg.MAC = latest_readings.MAC AND lg.fecha = latest_readings.max_fecha')
                        ->where('e.id_usuario', $userId)
                        ->orderBy('lg.fecha', 'DESC')
                        ->get()
                        ->getResultArray();
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

    /**
     * Método para migrar datos de 'lecturas_gas' a una nueva tabla 'registros_gas'.
     *
     * @return int El número de registros migrados.
     */
    public function migrarARegistrosGas(): int
    {
        // Este método asume que tienes una tabla 'registros_gas' con las columnas:
        // 'dispositivo_id' (MAC), 'nombre_dispositivo', 'ubicacion', 'nivel_gas', 'fecha', 'estado'
        // Y que 'fecha' es una clave compuesta junto con 'dispositivo_id' para evitar duplicados.

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
}