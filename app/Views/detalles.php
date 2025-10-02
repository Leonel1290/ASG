<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Dispositivo - ASG</title>
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
        .chart-container {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .device-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .alert-banner {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/perfil" class="back-button">← Volver al Perfil</a>
        
        <h1>Detalles del Dispositivo</h1>
        
        <?php if (isset($dispositivo)): ?>
            <!-- Banner de alerta según nivel de gas -->
            <?php if ($dispositivo->ultimo_nivel_gas >= 400): ?>
                <div class="alert-banner alert-danger">
                    ⚠️ ALERTA: Nivel de gas peligroso detectado
                </div>
            <?php elseif ($dispositivo->ultimo_nivel_gas >= 300): ?>
                <div class="alert-banner alert-warning">
                    ⚠️ Advertencia: Nivel de gas elevado
                </div>
            <?php else: ?>
                <div class="alert-banner alert-success">
                    ✅ Nivel de gas seguro
                </div>
            <?php endif; ?>

            <div class="device-info">
                <div class="info-card">
                    <h4>Información del Dispositivo</h4>
                    <p><strong>Nombre:</strong> <?= esc($dispositivo->nombre) ?></p>
                    <p><strong>MAC:</strong> <?= esc($dispositivo->MAC) ?></p>
                    <p><strong>Ubicación:</strong> <?= esc($dispositivo->ubicacion) ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="status <?= $dispositivo->estado_dispositivo === 'en_uso' ? 'status-open' : 'status-closed' ?>">
                            <?= esc(ucfirst(str_replace('_', ' ', $dispositivo->estado_dispositivo))) ?>
                        </span>
                    </p>
                </div>

                <div class="info-card">
                    <h4>Estado Actual</h4>
                    <div class="valve-control">
                        <span>Válvula:</span>
                        <span class="status <?= $dispositivo->estado_valvula ? 'status-closed' : 'status-open' ?>" id="valve-status">
                            <?= $dispositivo->estado_valvula ? 'CERRADA' : 'ABIERTA' ?>
                        </span>
                    </div>
                    
                    <div class="gas-level <?= getGasLevelClass($dispositivo->ultimo_nivel_gas) ?>" id="gas-level">
                        Nivel de Gas: <?= $dispositivo->ultimo_nivel_gas ?> ppm
                    </div>
                    
                    <div class="last-update" id="last-update">
                        Última actualización: <?= $dispositivo->ultima_actualizacion ?>
                    </div>
                </div>
            </div>

            <!-- Control de Válvula -->
            <div class="device-card">
                <h3>Control de Válvula</h3>
                
                <div class="valve-control">
                    <button class="btn btn-open" 
                            onclick="controlValvula(0)"
                            id="btn-open"
                            <?= $dispositivo->estado_valvula == 0 ? 'disabled' : '' ?>>
                        Abrir Válvula
                    </button>
                    <button class="btn btn-close" 
                            onclick="controlValvula(1)"
                            id="btn-close"
                            <?= $dispositivo->estado_valvula == 1 ? 'disabled' : '' ?>>
                        Cerrar Válvula
                    </button>
                </div>

                <div id="message-valve"></div>
            </div>

            <!-- Gráfico de Lecturas -->
            <div class="chart-container">
                <h3>Historial de Niveles de Gas</h3>
                <canvas id="gasChart" width="400" height="200"></canvas>
            </div>

            <!-- Lista de Lecturas Recientes -->
            <div class="device-card">
                <h3>Lecturas Recientes</h3>
                <?php if (!empty($lecturas)): ?>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Fecha</th>
                                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Nivel de Gas (ppm)</th>
                                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lecturas as $lectura): ?>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                                            <?= date('d/m/Y H:i', strtotime($lectura->fecha ?? $lectura['fecha'])) ?>
                                        </td>
                                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                                            <?= $lectura->nivel_gas ?? $lectura['nivel_gas'] ?>
                                        </td>
                                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                                            <span class="<?= getGasLevelClass($lectura->nivel_gas ?? $lectura['nivel_gas']) ?>">
                                                <?= getGasStatusText($lectura->nivel_gas ?? $lectura['nivel_gas']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No hay lecturas disponibles para este dispositivo.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-danger">
                Dispositivo no encontrado o no tienes acceso a este dispositivo.
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Inicializar gráfico
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($datosGrafico['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Nivel de Gas (ppm)',
                    data: <?= json_encode($datosGrafico['niveles'] ?? []) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nivel de Gas (ppm)'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hora'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });

        // Función para controlar la válvula
        async function controlValvula(accion) {
            const mac = '<?= $dispositivo->MAC ?? '' ?>';
            const endpoint = accion === 1 ? '/servo/cerrar' : '/servo/abrir';
            const messageDiv = document.getElementById('message-valve');
            
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
                    updateUI(data.estado_valvula);
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
                    updateUI(data.estado_valvula, data.nivel_gas, data.ultima_actualizacion);
                    
                    // Actualizar banner de alerta
                    updateAlertBanner(data.nivel_gas);
                }
            } catch (error) {
                console.error('Error al obtener estado:', error);
            }
        }

        // Función para actualizar la interfaz
        function updateUI(estadoValvula, nivelGas = null, ultimaActualizacion = null) {
            // Actualizar estado de la válvula
            const statusElement = document.getElementById('valve-status');
            const btnOpen = document.getElementById('btn-open');
            const btnClose = document.getElementById('btn-close');
            
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
                const gasLevelElement = document.getElementById('gas-level');
                gasLevelElement.textContent = `Nivel de Gas: ${nivelGas} ppm`;
                gasLevelElement.className = `gas-level ${getGasLevelClass(nivelGas)}`;
            }
            
            // Actualizar última actualización si se proporciona
            if (ultimaActualizacion) {
                document.getElementById('last-update').textContent = 
                    `Última actualización: ${ultimaActualizacion}`;
            }
        }

        // Función para actualizar el banner de alerta
        function updateAlertBanner(nivelGas) {
            const alertContainer = document.querySelector('.alert-banner');
            if (!alertContainer) return;

            if (nivelGas >= 400) {
                alertContainer.innerHTML = '⚠️ ALERTA: Nivel de gas peligroso detectado';
                alertContainer.className = 'alert-banner alert-danger';
            } else if (nivelGas >= 300) {
                alertContainer.innerHTML = '⚠️ Advertencia: Nivel de gas elevado';
                alertContainer.className = 'alert-banner alert-warning';
            } else {
                alertContainer.innerHTML = '✅ Nivel de gas seguro';
                alertContainer.className = 'alert-banner alert-success';
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
            const mac = '<?= $dispositivo->MAC ?? '' ?>';
            if (mac) {
                obtenerEstado(mac);
                
                setInterval(() => {
                    obtenerEstado(mac);
                }, 10000);
            }
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

// Función helper para obtener el texto del estado del gas
function getGasStatusText($nivel) {
    if ($nivel < 300) return 'Seguro';
    if ($nivel < 400) return 'Advertencia';
    return 'Peligroso';
}
?>