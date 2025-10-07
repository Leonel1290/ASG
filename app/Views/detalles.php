<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo: <?= esc($nombreDispositivo) ?></title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-darker: #0a0f1c;
            --text-light: #e2e8f0;
            --text-lighter: #f1f5f9;
            --text-darker: #f8fafc;
            --primary-color: #3b82f6;
            --primary-light: #60a5fa;
            --secondary-color: #94a3b8;
            --card-bg: #1e293b;
            --card-bg-light: #334155;
            --border-color: #475569;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gradient-primary: linear-gradient(135deg, #3b82f6, #1d4ed8);
            --gradient-success: linear-gradient(135deg, #10b981, #047857);
            --gradient-warning: linear-gradient(135deg, #f59e0b, #d97706);
            --gradient-danger: linear-gradient(135deg, #ef4444, #dc2626);
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
            padding: 2rem;
        }

        .btn-outline-secondary {
            color: var(--text-light);
            border-color: var(--text-light);
            transition: all 0.2s ease-in-out;
        }
        .btn-outline-secondary:hover {
            color: var(--bg-dark);
            background-color: var(--text-light);
            border-color: var(--text-light);
        }

        .page-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            color: var(--text-darker);
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .device-info {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .card {
            background-color: var(--card-bg);
            color: var(--text-lighter);
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.35);
        }
        .card-body {
            padding: 2rem;
        }
        .card-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-title .fas {
            margin-right: 0.75rem;
        }

        .current-gas-value {
            font-size: 3.5rem;
            font-weight: bold;
            margin-top: 1rem;
            animation: pulse 1.5s infinite alternate;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.05); opacity: 0.95; }
        }

        .section-title {
            color: var(--text-darker);
            font-size: 1.8rem;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        .section-title .fas {
            margin-right: 0.75rem;
        }

        .table-responsive {
            margin-bottom: 2rem;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .table {
            width: 100%;
            color: var(--text-lighter);
            border-collapse: collapse;
            background-color: var(--card-bg);
            margin-bottom: 0;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background-color: var(--card-bg-light);
            color: var(--text-darker);
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .table tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08);
            transition: background-color 0.2s ease;
            cursor: default;
        }

        .badge {
            padding: 0.5em 0.8em;
            border-radius: 0.5rem;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .badge.bg-success { 
            background: var(--gradient-success) !important; 
            color: var(--text-darker); 
        }
        .badge.bg-warning { 
            background: var(--gradient-warning) !important; 
            color: var(--text-darker); 
        }
        .badge.bg-danger { 
            background: var(--gradient-danger) !important; 
            color: var(--text-darker); 
        }

        .chart-container {
            max-width: 100%;
            height: 400px;
            margin: 0 auto 2rem auto;
            background-color: var(--card-bg-light);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .progress {
            height: 1.5rem;
            border-radius: 0.75rem;
            background-color: var(--border-color);
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .progress-bar {
            background: var(--gradient-success);
            transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            color: var(--text-darker);
            font-weight: bold;
            line-height: 1.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .security-level-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-darker);
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .security-level-display .fas {
            margin-right: 0.5rem;
        }

        /* Chart.js overrides for dark theme */
        .chartjs-render-monitor {
            background-color: transparent !important;
        }
        
        /* Modal specific styles */
        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-lighter);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color);
        }
        .modal-title {
            color: var(--text-darker);
        }
        .btn-close {
            filter: invert(1);
        }
        
        /* Status indicators */
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-safe {
            background-color: var(--success-color);
            box-shadow: 0 0 8px var(--success-color);
        }
        .status-warning {
            background-color: var(--warning-color);
            box-shadow: 0 0 8px var(--warning-color);
        }
        .status-danger {
            background-color: var(--danger-color);
            box-shadow: 0 0 8px var(--danger-color);
        }
        
        /* Filter card improvements */
        .filter-card .card-header {
            background: var(--gradient-primary);
            color: var(--text-darker);
            font-weight: bold;
            border-bottom: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            .current-gas-value {
                font-size: 2.5rem;
            }
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>

<div class="container my-5">
    <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
    </a>

    <header class="page-header">
        <h1 class="page-title"><i class="fas fa-microchip me-2"></i> Dispositivo: <span><?= esc($nombreDispositivo) ?></span></h1>
        <p class="device-info">
            <i class="fas fa-network-wired me-1"></i> MAC: <?= esc($mac) ?> | <i class="fas fa-map-marker-alt me-1"></i> Ubicación: <?= esc($ubicacionDispositivo) ?>
        </p>
    </header>

    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= esc($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-gas-pump"></i> Nivel de Gas Actual</h2>
                    <p class="current-gas-value" id="currentGasLevelDisplay"><?= $nivelGasActualDisplay ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-tachometer-alt"></i> Nivel de Seguridad</h2>
                    <p class="security-level-display">
                        <span id="securityLevelText">Cargando...</span>
                    </p>
                    <div class="progress mt-3">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 filter-card">
        <div class="card-header">
            <i class="fas fa-filter me-2"></i> Filtrar Lecturas por Período
        </div>
        <div class="card-body">
            <form action="<?= base_url('detalles/' . esc($mac)) ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="fechaInicio" class="form-label">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= $request->getGet('fechaInicio') ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label for="fechaFin" class="form-label">Fecha Fin:</label>
                    <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= $request->getGet('fechaFin') ?? '' ?>">
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-check me-2"></i>Aplicar Filtro</button>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalLecturas"><i class="fas fa-list me-2"></i>Ver Registros</button>
                </div>
            </form>
        </div>
    </div>

    <section>
        <h2 class="section-title"><i class="fas fa-chart-line"></i> Histórico de Niveles de Gas</h2>
        <div class="card">
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="gasLineChart"></canvas>
                </div>
                <?php if (empty($labels) || empty($data)): ?>
                    <p class="text-center text-muted mt-3">No hay datos suficientes para mostrar el gráfico de línea.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Modal para Registros Detallados de Lecturas -->
    <div class="modal fade" id="modalLecturas" tabindex="-1" aria-labelledby="modalLecturasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLecturasLabel"><i class="fas fa-list me-2"></i> Registros Detallados de Lecturas del Período</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($lecturas)): ?>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar-alt me-2"></i> Fecha y Hora</th>
                                        <th><i class="fas fa-thermometer-half me-2"></i> Nivel de Gas (PPM)</th>
                                        <th class="text-center"><i class="fas fa-exclamation-triangle me-2"></i> Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lecturas as $lectura): ?>
                                        <tr>
                                            <td><?= esc($lectura['fecha'] ?? 'Fecha desconocida') ?></td>
                                            <td><?= esc($lectura['nivel_gas'] ?? 'N/D') ?></td>
                                            <td class="text-center">
                                                <?php
                                                    $nivel = isset($lectura['nivel_gas']) ? (float) $lectura['nivel_gas'] : -1;
                                                    $estado = 'Desconocido';
                                                    $class = '';
                                                    $statusClass = '';

                                                    if ($nivel >= 500) {
                                                        $estado = 'Peligro';
                                                        $class = 'bg-danger';
                                                        $statusClass = 'status-danger';
                                                    } elseif ($nivel >= 200) {
                                                        $estado = 'Precaución';
                                                        $class = 'bg-warning text-dark';
                                                        $statusClass = 'status-warning';
                                                    } elseif ($nivel >= 0) {
                                                        $estado = 'Seguro';
                                                        $class = 'bg-success';
                                                        $statusClass = 'status-safe';
                                                    }
                                                ?>
                                                <span class="badge <?= $class ?>">
                                                    <span class="status-indicator <?= $statusClass ?>"></span>
                                                    <?= $estado ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-4">No hay lecturas registradas para este dispositivo en el período seleccionado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure $lecturas, $labels, and $data are correctly passed from PHP
        const lecturas = <?= json_encode($lecturas ?? []) ?>; // For the table (expected DESC order)
        const labels = <?= json_encode($labels ?? []) ?>;     // For the chart (expected ASC order)
        const data = <?= json_encode($data ?? []) ?>;         // For the chart (expected ASC order)

        // Get the last valid gas level for the "Nivel de Seguridad" card
        // Chart data is ASC (oldest to newest), so the last element is the most recent.
        const ultimoValor = data.length > 0 ? (parseFloat(data[data.length - 1]) || 0) : null;

        // Update the "Nivel de Seguridad" card and progress bar
        if (ultimoValor !== null) {
            updateSecurityProgressBar(ultimoValor);
        } else {
            document.getElementById('securityLevelText').textContent = 'Sin datos';
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = '0%';
            progressBar.className = 'progress-bar';
            progressBar.setAttribute('aria-valuenow', 0);
        }

        function updateSecurityProgressBar(value) {
            const progressBar = document.getElementById('progressBar');
            const securityLevelText = document.getElementById('securityLevelText');
            let width = 0;
            let levelText = 'Sin datos';
            let barClass = '';

            const safeValue = Math.max(0, value); // Ensure value is not negative

            if (safeValue < 200) {
                width = (safeValue / 200) * 33; // Scale 0-199 PPM to 0-33%
                levelText = 'Seguro';
                barClass = 'bg-success';
            } else if (safeValue < 500) {
                width = 33 + ((safeValue - 200) / 300) * 33; // Scale 200-499 PPM to 33-66%
                levelText = 'Precaución';
                barClass = 'bg-warning text-dark'; // Added text-dark for visibility
            } else { // 500 PPM and above
                width = 66 + ((safeValue - 500) / 500) * 34; // Scale 500+ PPM to 66-100%
                levelText = 'Peligro';
                barClass = 'bg-danger';
            }

            width = Math.min(100, Math.max(0, width)); // Cap width between 0 and 100

            progressBar.style.width = `${width}%`;
            progressBar.className = `progress-bar ${barClass}`;
            progressBar.setAttribute('aria-valuenow', width);
            securityLevelText.textContent = levelText;
        }

        // Chart.js Line Chart configuration
        const lineChartDom = document.getElementById('gasLineChart');
        if (lineChartDom && labels.length > 0 && data.length > 0) {
            const ctx = lineChartDom.getContext('2d');
            
            // Create gradient for the chart
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
            
            const gasLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // Already in ascending order (oldest to newest)
                    datasets: [{
                        label: 'Nivel de Gas (PPM)',
                        data: data, // Already in ascending order
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: 'rgba(255, 255, 255, 0.8)',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointHoverBorderColor: 'rgba(255, 255, 255, 1)',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: 'var(--text-darker)',
                                font: {
                                    size: 14,
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Nivel de Gas: ${context.parsed.y} PPM`;
                                },
                                title: function(context) {
                                    return `Fecha: ${context[0].label}`;
                                }
                            },
                            backgroundColor: 'rgba(30, 41, 59, 0.9)',
                            titleColor: 'var(--text-lighter)',
                            bodyColor: 'var(--text-light)',
                            borderColor: 'var(--primary-color)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha',
                                color: 'var(--text-lighter)',
                                font: {
                                    size: 14,
                                    weight: 'bold',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: {top: 10, bottom: 10}
                            },
                            ticks: {
                                color: 'var(--text-light)',
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10,
                                font: {
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                }
                            },
                            grid: {
                                color: 'rgba(71, 85, 105, 0.3)',
                                drawBorder: true
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Nivel de Gas (PPM)',
                                color: 'var(--text-lighter)',
                                font: {
                                    size: 14,
                                    weight: 'bold',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: {top: 10, bottom: 10}
                            },
                            beginAtZero: true,
                            ticks: {
                                color: 'var(--text-light)',
                                callback: function(value) {
                                    return value + ' PPM';
                                },
                                font: {
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                }
                            },
                            grid: {
                                color: 'rgba(71, 85, 105, 0.3)',
                                drawBorder: true
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animations: {
                        tension: {
                            duration: 1000,
                            easing: 'linear'
                        }
                    }
                }
            });
        } else {
            const chartCanvas = document.getElementById('gasLineChart');
            if (chartCanvas) {
                chartCanvas.style.display = 'none';
                const parentCardBody = chartCanvas.closest('.card-body');
                if (parentCardBody && !parentCardBody.querySelector('.no-data-message')) {
                    const messageElement = document.createElement('p');
                    messageElement.className = 'text-center text-muted mt-3 no-data-message';
                    messageElement.textContent = 'No hay datos suficientes para mostrar el gráfico de línea.';
                    parentCardBody.appendChild(messageElement);
                }
            }
        }
    });
</script>
</body>
</html>