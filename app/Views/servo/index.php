<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvulas - ASG</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .device-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .valve-control {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 15px 0;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-open {
            background-color: #28a745;
            color: white;
        }
        .btn-close {
            background-color: #dc3545;
            color: white;
        }
        .btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-open {
            background-color: #d4edda;
            color: #155724;
        }
        .status-closed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .gas-level {
            font-size: 1.2em;
            font-weight: bold;
            margin: 10px 0;
        }
        .gas-safe { color: #28a745; }
        .gas-warning { color: #ffc107; }
        .gas-danger { color: #dc3545; }
        .last-update {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Control de Válvulas de Gas</h1>
        
        <?php if (empty($dispositivos)): ?>
            <div class="alert alert-warning">
                No tienes dispositivos enlazados. <a href="/enlace">Enlaza un dispositivo</a> para comenzar.
            </div>
        <?php else: ?>
            <div class="devices-grid">
                <?php foreach ($dispositivos as $dispositivo): ?>
                    <div class="device-card" id="device-<?= str_replace(':', '-', $dispositivo['MAC']) ?>">
                        <h3><?= esc($dispositivo['nombre']) ?></h3>
                        <p><strong>MAC:</strong> <?= esc($dispositivo['MAC']) ?></p>
                        <p><strong>Ubicación:</strong> <?= esc($dispositivo['ubicacion']) ?></p>
                        
                        <div class="valve-control">
                            <span>Estado de la válvula:</span>
                            <span class="status <?= $dispositivo['estado_valvula'] ? 'status-closed' : 'status-open' ?>" id="status-<?= str_replace(':', '-', $dispositivo['MAC']) ?>">
                                <?= $dispositivo['estado_valvula'] ? 'CERRADA' : 'ABIERTA' ?>
                            </span>
                        </div>

                        <div class="valve-control">
                            <button class="btn btn-open" 
                                    onclick="controlValvula('<?= $dispositivo['MAC'] ?>', 0)"
                                    id="btn-open-<?= str_replace(':', '-', $dispositivo['MAC']) ?>"
                                    <?= $dispositivo['estado_valvula'] == 0 ? 'disabled' : '' ?>>
                                Abrir Válvula
                            </button>
                            <button class="btn btn-close" 
                                    onclick="controlValvula('<?= $dispositivo['MAC'] ?>', 1)"
                                    id="btn-close-<?= str_replace(':', '-', $dispositivo['MAC']) ?>"
                                    <?= $dispositivo['estado_valvula'] == 1 ? 'disabled' : '' ?>>
                                Cerrar Válvula
                            </button>
                        </div>

                        <div class="gas-info">
                            <div class="gas-level <?= getGasLevelClass($dispositivo['ultimo_nivel_gas']) ?>" 
                                 id="gas-level-<?= str_replace(':', '-', $dispositivo['MAC']) ?>">
                                Nivel de Gas: <?= $dispositivo['ultimo_nivel_gas'] ?> ppm
                            </div>
                            <div class="last-update" id="last-update-<?= str_replace(':', '-', $dispositivo['MAC']) ?>">
                                Última actualización: <?= $dispositivo['ultima_actualizacion'] ?>
                            </div>
                        </div>

                        <div id="message-<?= str_replace(':', '-', $dispositivo['MAC']) ?>"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Función para controlar la válvula
        async function controlValvula(mac, accion) {
            const endpoint = accion === 1 ? '/servo/cerrar' : '/servo/abrir';
            const messageDiv = document.getElementById(`message-${mac.replace(/:/g, '-')}`);
            
            try {
                messageDiv.innerHTML = '<div style="color: blue;">Procesando...</div>';
                
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `mac=${encodeURIComponent(mac)}`
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    messageDiv.innerHTML = `<div style="color: green;">${data.message}</div>`;
                    updateUI(mac, data.estado_valvula);
                    // Actualizar estado automáticamente después de 2 segundos
                    setTimeout(() => obtenerEstado(mac), 2000);
                } else {
                    messageDiv.innerHTML = `<div style="color: red;">Error: ${data.message}</div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = `<div style="color: red;">Error de conexión: ${error.message}</div>`;
            }
        }

        // Función para obtener el estado actual
        async function obtenerEstado(mac) {
            try {
                const response = await fetch(`/servo/obtenerEstado/${encodeURIComponent(mac)}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    updateUI(mac, data.estado_valvula, data.nivel_gas, data.ultima_actualizacion);
                }
            } catch (error) {
                console.error('Error al obtener estado:', error);
            }
        }

        // Función para actualizar la interfaz
        function updateUI(mac, estadoValvula, nivelGas = null, ultimaActualizacion = null) {
            const macId = mac.replace(/:/g, '-');
            
            // Actualizar estado de la válvula
            const statusElement = document.getElementById(`status-${macId}`);
            const btnOpen = document.getElementById(`btn-open-${macId}`);
            const btnClose = document.getElementById(`btn-close-${macId}`);
            
            if (estadoValvula === 1) {
                statusElement.textContent = 'CERRADA';
                statusElement.className = 'status status-closed';
                btnOpen.disabled = false;
                btnClose.disabled = true;
            } else {
                statusElement.textContent = 'ABIERTA';
                statusElement.className = 'status status-open';
                btnOpen.disabled = true;
                btnClose.disabled = false;
            }
            
            // Actualizar nivel de gas si se proporciona
            if (nivelGas !== null) {
                const gasLevelElement = document.getElementById(`gas-level-${macId}`);
                gasLevelElement.textContent = `Nivel de Gas: ${nivelGas} ppm`;
                gasLevelElement.className = `gas-level ${getGasLevelClass(nivelGas)}`;
            }
            
            // Actualizar última actualización si se proporciona
            if (ultimaActualizacion) {
                document.getElementById(`last-update-${macId}`).textContent = 
                    `Última actualización: ${ultimaActualizacion}`;
            }
        }

        // Función para determinar la clase CSS del nivel de gas
        function getGasLevelClass(nivel) {
            if (nivel < 300) return 'gas-safe';
            if (nivel < 400) return 'gas-warning';
            return 'gas-danger';
        }

        // Actualizar estados automáticamente cada 10 segundos
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($dispositivos as $dispositivo): ?>
                obtenerEstado('<?= $dispositivo['MAC'] ?>');
            <?php endforeach; ?>
            
            setInterval(() => {
                <?php foreach ($dispositivos as $dispositivo): ?>
                    obtenerEstado('<?= $dispositivo['MAC'] ?>');
                <?php endforeach; ?>
            }, 10000);
        });
    </script>
</body>
</html>

<?php
// Función helper para determinar la clase del nivel de gas
function getGasLevelClass($nivel) {
    if ($nivel < 300) return 'gas-safe';
    if ($nivel < 400) return 'gas-warning';
    return 'gas-danger';
}
?>