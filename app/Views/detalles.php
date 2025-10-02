<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Dispositivo - ASG</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* BASE & TIPOGRAFÍA */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f7f9; /* Fondo muy claro */
            color: #333;
            line-height: 1.6;
            margin: 0;
        }
        h1, h3, h4 {
            color: #2c3e50; /* Color oscuro para títulos */
            margin-top: 0;
        }

        /* LAYOUT PRINCIPAL */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px; /* Más padding */
        }

        /* TARJETAS GENERALES */
        .device-card {
            background: #fff; /* Fondo blanco para las tarjetas */
            border-radius: 12px; /* Bordes más redondeados */
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Sombra más pronunciada */
            transition: box-shadow 0.3s ease;
        }
        .device-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        /* BOTONES */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px; /* Bordes redondeados modernos */
            cursor: pointer;
            font-weight: 600; /* Un poco más de peso a la fuente */
            transition: background-color 0.3s ease, transform 0.1s ease;
        }
        .btn:active {
            transform: translateY(1px);
        }
        .btn-open {
            background-color: #1abc9c; /* Verde aqua moderno */
            color: white;
        }
        .btn-open:hover {
            background-color: #16a085;
        }
        .btn-close {
            background-color: #e74c3c; /* Rojo más saturado */
            color: white;
        }
        .btn-close:hover {
            background-color: #c0392b;
        }
        .btn:disabled {
            background-color: #bdc3c7; /* Gris claro */
            color: #7f8c8d;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ESTADOS DE VÁLVULA Y DISPOSITIVO */
        .status {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85em;
        }
        .status-open {
            background-color: #d1f2eb;
            color: #1abc9c;
        }
        .status-closed {
            background-color: #f7e0e0;
            color: #e74c3c;
        }
        
        /* TARJETAS DE INFORMACIÓN (INFO-CARDS) */
        .device-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Mínimo 300px */
            gap: 20px;
            margin-bottom: 25px;
        }
        .info-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #3498db; /* Azul corporativo */
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .info-card h4 {
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: #3498db;
        }

        /* CONTROL DE VÁLVULA */
        .valve-control {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 15px 0;
            flex-wrap: wrap; /* Para responsividad */
        }
        .valve-control > span {
            font-weight: 600;
            color: #2c3e50;
        }

        /* NIVEL DE GAS */
        .gas-level {
            font-size: 1.6em; /* Más grande y destacado */
            font-weight: 700;
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px dashed #ecf0f1;
        }
        .gas-safe { color: #1abc9c; }
        .gas-warning { color: #f39c12; } /* Amarillo-naranja */
        .gas-danger { color: #e74c3c; }

        .last-update {
            font-size: 0.85em;
            color: #7f8c8d;
            font-style: italic;
        }

        /* GRÁFICO */
        .chart-container {
            margin-top: 30px;
            /* Hereda estilos de .device-card */
        }

        /* BOTÓN DE VOLVER */
        .back-button {
            display: inline-flex; /* Usar flex para centrar el icono/texto */
            align-items: center;
            gap: 5px;
            margin-bottom: 30px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #2980b9;
        }

        /* BANNER DE ALERTA */
        .alert-banner {
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger {
            background-color: #fbecec;
            color: #c0392b;
            border: 1px solid #e74c3c;
        }
        .alert-warning {
            background-color: #fcf8e3;
            color: #f39c12;
            border: 1px solid #f1c40f;
        }
        .alert-success {
            background-color: #e8f8f5;
            color: #16a085;
            border: 1px solid #1abc9c;
        }
        
        /* TABLA DE LECTURAS */
        .lecturas-table-container {
            max-height: 400px; /* Más espacio para la tabla */
            overflow-y: auto;
            border: 1px solid #ecf0f1;
            border-radius: 8px;
        }
        .lecturas-table {
            width: 100%;
            border-collapse: collapse;
        }
        .lecturas-table thead tr {
            background-color: #ecf0f1; /* Fondo de la cabecera */
            color: #2c3e50;
            position: sticky; /* Fija la cabecera al hacer scroll */
            top: 0;
            z-index: 10;
        }
        .lecturas-table th, .lecturas-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        .lecturas-table tbody tr:hover {
            background-color: #f9f9f9; /* Efecto hover */
        }

    </style>
</head>
<body>
    <div class="container">
        <a href="/perfil" class="back-button">
            <span style="font-size: 1.2em;">&larr;</span> Volver al Perfil
        </a>
        
        <h1>Detalles del Dispositivo</h1>
        
        <?php if (isset($dispositivo)): ?>
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
                    <h4>Información General</h4>
                    <p><strong>Nombre:</strong> <?= esc($dispositivo->nombre) ?></p>
                    <p><strong>MAC:</strong> <code style="background-color: #ecf0f1; padding: 2px 5px; border-radius: 3px;"><?= esc($dispositivo->MAC) ?></code></p>
                    <p><strong>Ubicación:</strong> <?= esc($dispositivo->ubicacion) ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="status <?= $dispositivo->estado_dispositivo === 'en_uso' ? 'status-open' : 'status-closed' ?>">
                            <?= esc(ucfirst(str_replace('_', ' ', $dispositivo->estado_dispositivo))) ?>
                        </span>
                    </p>
                </div>

                <div class="info-card">
                    <h4>Estado Operacional</h4>
                    <div class="valve-control" style="margin-top: 5px; margin-bottom: 5px;">
                        <span>Estado de Válvula:</span>
                        <span class="status <?= $dispositivo->estado_valvula ? 'status-closed' : 'status-open' ?>" id="valve-status">
                            <?= $dispositivo->estado_valvula ? 'CERRADA' : 'ABIERTA' ?>
                        </span>
                    </div>
                    
                    <div class="gas-level <?= getGasLevelClass($dispositivo->ultimo_nivel_gas) ?>" id="gas-level">
                        Nivel de Gas: <?= $dispositivo->ultimo_nivel_gas ?> <small>ppm</small>
                    </div>
                    
                    <div class="last-update" id="last-update">
                        Última actualización: <?= $dispositivo->ultima_actualizacion ?>
                    </div>
                </div>
            </div>

            <div class="device-card">
                <h3>Control de Válvula</h3>
                
                <div class="valve-control">
                    <button class="btn btn-open" 
                            onclick="controlValvula(0)"
                            id="btn-open"
                            <?= $dispositivo->estado_valvula == 0 ? 'disabled' : '' ?>>
                        <span style="margin-right: 5px;">&#x2714;</span> Abrir Válvula
                    </button>
                    <button class="btn btn-close" 
                            onclick="controlValvula(1)"
                            id="btn-close"
                            <?= $dispositivo->estado_valvula == 1 ? 'disabled' : '' ?>>
                        <span style="margin-right: 5px;">&#x2716;</span> Cerrar Válvula
                    </button>
                </div>

                <div id="message-valve" style="margin-top: 15px; font-weight: 600;"></div>
            </div>

            <div class="device-card chart-container">
                <h3>Historial de Niveles de Gas</h3>
                <canvas id="gasChart" width="400" height="200"></canvas>
            </div>

            <div class="device-card">
                <h3>Lecturas Recientes</h3>
                <?php if (!empty($lecturas)): ?>
                    <div class="lecturas-table-container">
                        <table class="lecturas-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nivel de Gas (ppm)</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lecturas as $lectura): ?>
                                    <tr>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($lectura->fecha ?? $lectura['fecha'])) ?>
                                        </td>
                                        <td>
                                            <?= $lectura->nivel_gas ?? $lectura['nivel_gas'] ?>
                                        </td>
                                        <td>
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
            <div class="alert-banner alert-danger">
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
                    borderColor: '#3498db', /* Color de línea ajustado */
                    backgroundColor: 'rgba(52, 152, 219, 0.2)', /* Color de fondo ajustado */
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
                            color: '#ecf0f1' /* Líneas de grid más suaves */
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hora'
                        },
                        grid: {
                            color: '#ecf0f1'
                        }
                    }
                }
            }
        });

        // Función para determinar la clase CSS del nivel de gas (Duplicada desde PHP, pero necesaria en JS)
        function getGasLevelClass(nivel) {
            if (nivel < 300) return 'gas-safe';
            if (nivel < 400) return 'gas-warning';
            return 'gas-danger';
        }

        // Función para actualizar la interfaz (Se mantiene la lógica, se actualizan las clases CSS)
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
                gasLevelElement.innerHTML = `Nivel de Gas: ${nivelGas} <small>ppm</small>`;
                gasLevelElement.className = `gas-level ${getGasLevelClass(nivelGas)}`;
            }
            
            // Actualizar última actualización si se proporciona
            if (ultimaActualizacion) {
                document.getElementById('last-update').textContent = 
                    `Última actualización: ${ultimaActualizacion}`;
            }
        }

        // Función para actualizar el banner de alerta (Se mantiene la lógica, se actualizan las clases CSS)
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

        // --- LAS FUNCIONES controlValvula() y obtenerEstado() SE MANTIENEN LITERALMENTE SIN CAMBIOS DE LÓGICA ---

        // Función para controlar la válvula (Lógica inalterada)
        async function controlValvula(accion) {
            const mac = '<?= $dispositivo->MAC ?? '' ?>';
            const endpoint = accion === 1 ? '/servo/cerrar' : '/servo/abrir';
            const messageDiv = document.getElementById('message-valve');
            
            try {
                messageDiv.innerHTML = '<div style="color: #3498db;">Procesando...</div>'; /* Color azul corporativo */
                
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `mac=${encodeURIComponent(mac)}`
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    messageDiv.innerHTML = `<div style="color: #1abc9c;">${data.message}</div>`; /* Color verde success */
                    updateUI(data.estado_valvula);
                    // Actualizar estado automáticamente después de 2 segundos
                    setTimeout(() => obtenerEstado(mac), 2000);
                } else {
                    messageDiv.innerHTML = `<div style="color: #e74c3c;">Error: ${data.message}</div>`; /* Color rojo error */
                }
            } catch (error) {
                messageDiv.innerHTML = `<div style="color: #e74c3c;">Error de conexión: ${error.message}</div>`;
            }
        }

        // Función para obtener el estado actual (Lógica inalterada)
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
        
        // Ejecución al cargar (Lógica inalterada)
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
// Función helper para determinar la clase del nivel de gas (Lógica inalterada)
function getGasLevelClass($nivel) {
    if ($nivel < 300) return 'gas-safe';
    if ($nivel < 400) return 'gas-warning';
    return 'gas-danger';
}

// Función helper para obtener el texto del estado del gas (Lógica inalterada)
function getGasStatusText($nivel) {
    if ($nivel < 300) return 'Seguro';
    if ($nivel < 400) return 'Advertencia';
    return 'Peligroso';
}
?>