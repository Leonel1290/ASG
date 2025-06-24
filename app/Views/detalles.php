<?php
// Initialize variables to prevent errors if they are not passed from the controller
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Use MAC as default name if no name is passed
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$lecturas = $lecturas ?? []; // Array of readings for the table
$labels = $labels ?? [];     // Date/time labels for the chart
$data = $data ?? [];         // Gas levels for the chart
$message = $message ?? null; // Optional messages

// Get the latest gas level to display in the simple card
// It's assumed that the $lecturas array is ordered in DESCENDING order (most recent first)
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? esc($lecturas[0]['nivel_gas']) . ' PPM' : 'Sin datos';

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
    <title>Detalle del Dispositivo</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* General reset to ensure no default margins/paddings */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; /* Ensure margin is 0 */
            padding: 0; /* Ensure padding is 0 */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
            padding: 2rem;
        }

        /* Style for the "Volver" button */
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; /* Add hover transition */
        }
        .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }


        .page-title {
            color: #f7fafc;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem; /* Reduced bottom margin */
            text-align: center;
        }

        /* Style for device details line (MAC and Location) */
        .device-details-line {
            text-align: center;
            color: #a0aec0; /* Secondary text color */
            font-size: 1rem;
            margin-bottom: 1.5rem; /* Space below details */
        }


        .section-title {
            color: #edf2f7;
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 0.5rem;
        }

        .table-responsive {
            margin-bottom: 1.5rem;
        }

        .table {
            width: 100%;
            color: #e2e8f0;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            border-radius: 0.5rem;
            overflow: hidden; /* Ensure rounded corners are visible */
            background-color: #2d3748;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #4a5568;
        }

        .table th {
            background-color: #4a5568;
            color: #f7fafc;
            font-weight: bold;
        }

        .table tbody tr:nth-child(even) {
            background-color: #374151;
        }

        .table tbody tr:hover {
            background-color: #4a5568;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        /* Bootstrap classes for badge/span background colors */
        .bg-success {
            background-color: #48bb78 !important;
            color: #2d3748; /* Ensure text is readable on background */
        }

        .bg-warning {
            background-color: #f6e05e !important;
            color: #2d3748; /* Ensure text is readable on background */
        }

        .bg-danger {
            background-color: #e53e3e !important;
            color: #edf2f7;
        }


        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            transition: box-shadow 0.3s ease; /* Shadow animation */
        }

        .card:hover {
             /* Neon and wide shadow - Adjust colors and blur as needed */
             box-shadow: 0 0 20px rgba(102, 126, 234, 0.6), 0 0 40px rgba(102, 126, 234, 0.4), 0 0 60px rgba(102, 126, 234, 0.2);
        }

        .card-body {
            padding: 1.5rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }

        /* Specific styles for the Current Gas Level card (from the first code) */
        .current-gas-level-card-simple {
            background-color: #2d3748; /* Use general card color */
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 2rem;
            transition: box-shadow 0.3s ease;
            padding: 1.5rem; /* Same as card-body */
            text-align: center; /* Center content */
        }

        .current-gas-level-card-simple:hover {
             box-shadow: 0 0 20px rgba(102, 126, 234, 0.6), 0 0 40px rgba(102, 126, 234, 0.4), 0 0 60px rgba(102, 126, 234, 0.2);
        }

        .current-gas-level-card-simple .card-title {
            font-size: 1.75rem; /* Same as general card-title */
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .current-gas-level-card-simple .current-gas-level-value {
            font-size: 2rem;
            font-weight: bold;
            color: #f6e05e; /* Yellow/gold color */
        }


        .progress {
            height: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #4a5568;
        }

        .progress-bar {
            background-color: #48bb78; /* Default color, changed with JS */
            transition: width 0.5s ease; /* Smoother animation */
            color: #fff;
            text-align: center;
            line-height: 1rem; /* Vertically center text if any */
        }

        .security-level-text {
            font-weight: bold;
            margin-top: 0.5rem;
            color: #f7fafc;
        }

        .current-gas-level-card {
            background-color: #4a5568;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .current-gas-level-title {
            font-size: 1.25rem;
            color: #edf2f7;
            margin-bottom: 0.5rem;
        }

        .current-gas-level {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea; /* Default color, can be changed with JS if desired */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.75rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .card-title {
                font-size: 1.5rem;
            }
            .current-gas-level-card-simple .card-title {
                font-size: 1.5rem;
            }
            .current-gas-level-card-simple .current-gas-level-value {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

<div class="container my-5">
    <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
    </a>

    <h1 class="page-title"><i class="fas fa-microchip me-2"></i> Detalle del Dispositivo: <span class="text-primary"><?= esc($nombreDispositivo) ?></span></h1>
    <p class="device-details-line">
        MAC: <?= esc($mac) ?> | Ubicación: <?= esc($ubicacionDispositivo) ?>
    </p>

    <div class="current-gas-level-card-simple">
        <h3 class="card-title"><i class="fas fa-gas-pump me-2"></i>Nivel de Gas Actual</h3>
        <p class="current-gas-level-value"><?= $nivelGasActualDisplay ?></p>
    </div>

    <h2 class="section-title"><i class="fas fa-list-alt me-2"></i> Registros de Lecturas de Gas</h2>
    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th><i class="fas fa-calendar-alt me-2"></i> Fecha</th>
                    <th><i class="fas fa-thermometer-half me-2"></i> Nivel de Gas (PPM)</th>
                    <th class="text-center"><i class="fas fa-exclamation-triangle me-2"></i> Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lecturas)): ?>
                    <tr><td colspan="3" class="text-center">No hay lecturas registradas.</td></tr>
                <?php else: ?>
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
                                    } elseif ($nivel >= 200) {
                                        $estado = 'Precaución';
                                        $class = 'bg-warning';
                                    } elseif ($nivel >= 0) {
                                        $estado = 'Seguro';
                                        $class = 'bg-success';
                                    }
                                ?>
                                <span class="badge <?= $class ?>"><?= $estado ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card mt-5">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-chart-line me-2"></i> Gráfico de Niveles de Gas</h5>
            <div class="chart-container" style="max-width: 700px; margin: 0 auto;">
                <canvas id="gasChart" width="400" height="200"></canvas>
            </div>

            <div class="mt-4">
                <p class="security-level-text"><i class="fas fa-shield-alt me-2"></i> Nivel de Seguridad (PPM): <span id="securityLevel">Sin datos</span></p>
                <div class="progress">
                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="current-gas-level-card mt-4">
                <h5 class="current-gas-level-title"><i class="fas fa-tachometer-alt me-2"></i> Última Lectura (PPM)</h5>
                <p class="current-gas-level" id="nivelGas">Cargando...</p>
            </div>
        </div>
    </div>

</div>

<script>
    // Ensure $lecturas, $labels, and $data are correctly passed from PHP
    // Use ?? [] to handle null/undefined cases if no data
    const lecturas = <?= json_encode($lecturas ?? []) ?>;
    const labels = <?= json_encode($labels ?? []) ?>; // Labels for the chart (dates in ascending order)
    const data = <?= json_encode($data ?? []) ?>;     // Data for the chart (gas levels in ascending order)

    // Find the last valid gas level value for the "Última Lectura" card
    // Since Chart.js data (labels and data) are in ASC order (oldest to newest),
    // the last element in 'data' will be the most recent.
    const ultimoValor = data.length > 0 ? (parseFloat(data[data.length - 1]) || 0) : null;

    if (ultimoValor !== null) {
        document.getElementById('nivelGas').textContent = `${ultimoValor} PPM`;
        updateProgressBar(ultimoValor);
    } else {
        document.getElementById('nivelGas').textContent = 'Sin datos';
        document.getElementById('securityLevel').textContent = 'Sin datos';
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = '0%';
        progressBar.className = 'progress-bar';
        progressBar.setAttribute('aria-valuenow', 0);
    }

    function updateProgressBar(value) {
        const progressBar = document.getElementById('progressBar');
        const securityLevelSpan = document.getElementById('securityLevel');
        let width = 0;
        let levelText = 'Sin datos';
        let barClass = '';

        const safeValue = Math.max(0, value); // Ensure value is not negative

        if (safeValue >= 0 && safeValue < 200) {
            // Scale: 0-199 PPM -> 0-33% of the bar
            width = Math.min(100, (safeValue / 200) * 33);
            levelText = 'Seguro';
            barClass = 'bg-success';
        } else if (safeValue >= 200 && safeValue < 500) {
            // Scale: 200-499 PPM -> 33%-66% of the bar (range of 300 PPM)
            width = Math.min(100, 33 + ((safeValue - 200) / 300) * 33);
            width = Math.min(100, width);
            levelText = 'Precaución';
            barClass = 'bg-warning';
        } else if (safeValue >= 500) {
            // Scale: 500+ PPM -> 66%-100% of the bar
            width = Math.min(100, 66 + ((safeValue - 500) / 500) * 34); // Example with divisor 500
            width = Math.min(100, width);
            levelText = 'Peligro';
            barClass = 'bg-danger';
        } else {
            width = 0;
            levelText = 'Inválido';
            barClass = '';
        }

        progressBar.style.width = `${width}%`;
        progressBar.className = `progress-bar ${barClass}`;
        progressBar.setAttribute('aria-valuenow', width);
        securityLevelSpan.textContent = levelText;
    }

    // Only try to create the chart if there is labels and data
    if (labels.length > 0 && data.length > 0) {
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
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
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#f7fafc'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                if (context.parsed.y !== null) {
                                    return `${label}: ${context.parsed.y} PPM`;
                                }
                                return label;
                            },
                            title: function(context) {
                                if (context && context[0] && context[0].label) {
                                    return `Fecha: ${context[0].label}`;
                                }
                                return null;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha',
                            color: '#e2e8f0'
                        },
                        ticks: {
                            color: '#cbd5e0',
                            display: false // Hide X-axis labels if there are too many
                        },
                        grid: {
                            display: false,
                            color: '#4a5568'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nivel de Gas (PPM)',
                            color: '#e2e8f0'
                        },
                        beginAtZero: true,
                        ticks: {
                            color: '#cbd5e0'
                        },
                        grid: {
                            color: '#4a5568'
                        }
                    }
                }
            }
        });
        console.log('Gráfico creado');
    } else {
        // Display a message if there is not enough data for the chart
        const chartContainer = document.querySelector('.chart-container');
        if(chartContainer) {
            chartContainer.innerHTML = '<p class="text-center text-muted">No hay datos suficientes para mostrar el gráfico.</p>';
            chartContainer.style.height = '150px';
            chartContainer.style.display = 'flex';
            chartContainer.style.alignItems = 'center';
            chartContainer.style.justifyContent = 'center';
        }
        console.log('No hay datos para crear el gráfico');
    }

    console.log('Lecturas (para tabla, DESC):', lecturas);
    console.log('Labels (para gráfico, ASC):', labels);
    console.log('Data (para gráfico, ASC):', data);
</script>

</body>
</html>
