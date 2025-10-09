<?php
// Initialize variables to prevent errors if they are not passed from the controller
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Use MAC as default name if no name is passed
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$lecturas = $lecturas ?? []; // Array of readings for the table (expected DESC order)
$labels = $labels ?? [];     // Date/time labels for the chart (expected ASC order)
$data = $data ?? [];         // Gas levels for the chart (expected ASC order)
$message = $message ?? null; // Optional messages

// Get the latest gas level to display in the simple card
// It's assumed that the $lecturas array is ordered in DESCENDING order (most recent first)
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? esc($lecturas[0]['nivel_gas']) . ' PPM' : 'Sin datos';
$nivelGasActualValue = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? (float)$lecturas[0]['nivel_gas'] : 0; // Default to 0 if no data

// Helper function to escape HTML data (similar to CodeIgniter's esc())
if (!function_exists('esc')) {
    function esc($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>
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
    <!-- ECharts is no longer needed, removing script tag -->
    <style>
        :root {
            --bg-dark: #1a202c;
            --text-light: #cbd5e0;
            --text-lighter: #e2e8f0;
            --text-darker: #f7fafc;
            --primary-color: #667eea;
            --secondary-color: #a0aec0;
            --card-bg: #2d3748;
            --border-color: #4a5568;
            --success-color: #48bb78;
            --warning-color: #f6e05e;
            --danger-color: #e53e3e;
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
        }
        .page-title .text-primary {
            color: var(--primary-color) !important;
        }

        .device-info {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .card {
            background-color: var(--card-bg);
            color: var(--text-lighter);
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
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
            justify-content: center; /* Center horizontally */
        }
        .card-title .fas {
            margin-right: 0.75rem;
        }

        .current-gas-value {
            font-size: 3rem;
            font-weight: bold;
            color: var(--warning-color);
            margin-top: 1rem;
            animation: pulse 1.5s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.03); opacity: 0.95; }
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
            overflow: hidden; /* Ensures rounded corners on table */
        }

        .table {
            width: 100%;
            color: var(--text-lighter);
            border-collapse: collapse;
            background-color: var(--card-bg);
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background-color: var(--border-color);
            color: var(--text-darker);
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .table tbody tr:nth-child(even) {
            background-color: #374151; /* Slightly different shade for zebra striping */
        }

        .table tbody tr:hover {
            background-color: #4a5568;
            transition: background-color 0.2s ease;
            cursor: default; /* Change cursor for clarity */
        }

        .badge {
            padding: 0.5em 0.8em;
            border-radius: 0.5rem;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .badge.bg-success { background-color: var(--success-color) !important; color: var(--bg-dark); }
        .badge.bg-warning { background-color: var(--warning-color) !important; color: var(--bg-dark); }
        .badge.bg-danger { background-color: var(--danger-color) !important; color: var(--text-darker); }


        .chart-container {
            max-width: 100%;
            height: 350px; /* Fixed height for consistency */
            margin: 0 auto 2rem auto;
            background-color: rgba(0,0,0,0.1); /* Subtle background for chart area */
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .progress {
            height: 1.25rem;
            border-radius: 0.625rem;
            background-color: var(--border-color);
            margin-bottom: 1rem;
        }

        .progress-bar {
            background-color: var(--success-color); /* Default color */
            transition: width 0.6s ease-in-out; /* Smoother animation */
            color: var(--text-darker);
            font-weight: bold;
            line-height: 1.25rem; /* Vertically center text */
        }

        .security-level-display {
            font-size: 1.25rem;
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
            background-color: transparent !important; /* Ensure canvas background is transparent */
        }
        /* Modal specific styles */
        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-lighter);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color);
        }
        .modal-title {
            color: var(--text-darker);
        }
        .btn-close {
            filter: invert(1); /* Makes the close button white for dark background */
        }
    </style>
</head>
<body>

<div class="container my-5">
    <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
    </a>

    <header class="page-header">
        <h1 class="page-title"><i class="fas fa-microchip me-2"></i> Dispositivo: <span class="text-primary"><?= esc($nombreDispositivo) ?></span></h1>
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

    <div class="card mb-4">
        <div class="card-header">
            Filtrar Lecturas por Período
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
                    <button type="submit" class="btn btn-primary me-2">Aplicar Filtro</button>
                    <!-- Button to trigger the modal for detailed records -->
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalLecturas">Ver Registros</button>
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
                    <h5 class="modal-title" id="modalLecturasLabel">Registros Detallados de Lecturas del Período</h5>
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

                                                    if ($nivel >= 500) {
                                                        $estado = 'Peligro';
                                                        $class = 'bg-danger';
                                                    } elseif ($nivel >= 350) {
                                                        $estado = 'Precaución';
                                                        $class = 'bg-warning text-dark';
                                                    } elseif ($nivel >= 0) {
                                                        $estado = 'Seguro';
                                                        $class = 'bg-success';
                                                    }
                                                ?>
                                                <span class="badge <?= $class ?>"><?= $estado ?></span>
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
            } else if (safeValue < 350) {
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
            const gasLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // Already in ascending order (oldest to newest)
                    datasets: [{
                        label: 'Nivel de Gas (PPM)',
                        data: data, // Already in ascending order
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(54, 162, 235, 1)'
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
                                    size: 14
                                }
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
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: 'var(--text-lighter)',
                            bodyColor: 'var(--text-light)',
                            borderColor: 'var(--primary-color)',
                            borderWidth: 1
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
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: 'var(--text-light)',
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10
                            },
                            grid: {
                                color: 'rgba(74, 85, 104, 0.3)',
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
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            ticks: {
                                color: 'var(--text-light)',
                                callback: function(value) {
                                    return value + ' PPM';
                                }
                            },
                            grid: {
                                color: 'rgba(74, 85, 104, 0.3)',
                                drawBorder: true
                            }
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
