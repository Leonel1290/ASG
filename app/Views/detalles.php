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
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <style>
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #2d3748;
        }
        .navbar-brand, .nav-link {
            color: #cbd5e0 !important;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #ffffff !important;
        }
        .container {
            padding: 20px;
        }
        .card {
            background-color: #2d3748;
            color: #cbd5e0;
            border: 1px solid #4a5568;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #4a5568;
            color: #ffffff;
            font-weight: bold;
        }
        .table {
            color: #cbd5e0;
        }
        .table th, .table td {
            border-color: #4a5568;
        }
        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
        }
        .btn-primary:hover {
            background-color: #3182ce;
            border-color: #3182ce;
        }
        .form-control {
            background-color: #2d3748;
            color: #cbd5e0;
            border-color: #4a5568;
        }
        .form-control:focus {
            background-color: #2d3748;
            color: #cbd5e0;
            border-color: #63b3ed;
            box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25);
        }
        /* Style for the chart container */
        #gasGaugeChart, #gasLineChart { /* Added gasLineChart ID for Chart.js */
            width: 100%;
            height: 400px;
            background-color: #2d3748; /* Dark background for the chart area */
            border-radius: 8px;
            margin-top: 20px;
        }
        .alert {
            margin-top: 20px;
        }
        .modal-content { /* Kept for general modal styling, though specific modal is removed */
            background-color: #2d3748;
            color: #cbd5e0;
            border: 1px solid #4a5568;
        }
        .modal-header, .modal-footer {
            border-color: #4a5568;
        }
        .modal-title {
            color: #ffffff;
        }
        .btn-close {
            filter: invert(1); /* Makes the close button white for dark background */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('inicio'); ?>">
                <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo" width="40" height="40" class="d-inline-block align-text-top">
                ASG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('perfil'); ?>">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('comprar'); ?>">Comprar Dispositivo</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form action="<?= base_url('logout'); ?>" method="post">
                            <button type="submit" class="btn btn-link nav-link">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4 text-center">Detalle del Dispositivo</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info" role="alert">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Información del Dispositivo
                    </div>
                    <div class="card-body">
                        <p><strong>MAC:</strong> <?= esc($mac) ?></p>
                        <p><strong>Nombre:</strong> <?= esc($nombreDispositivo) ?></p>
                        <p><strong>Ubicación:</strong> <?= esc($ubicacionDispositivo) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-header">
                        Nivel de Gas Actual
                    </div>
                    <div class="card-body">
                        <h3><?= esc($nivelGasActualDisplay) ?></h3>
                        <p class="text-muted">Última lectura disponible</p>
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
                        <!-- El botón "Ver Registros" y el modal han sido eliminados, la tabla se muestra directamente -->
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Gráfico de Historial de Nivel de Gas
            </div>
            <div class="card-body">
                <canvas id="gasLineChart"></canvas> <!-- Chart.js line chart -->
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Nivel de Gas (Última Lectura del Período Seleccionado)
            </div>
            <div class="card-body">
                <div id="gasGaugeChart"></div> <!-- ECharts Gauge Chart -->
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Registros de Lecturas del Período
            </div>
            <div class="card-body">
                <?php if (!empty($lecturas)): ?>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Nivel de Gas (PPM)</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lecturas as $lectura): ?>
                                    <tr>
                                        <td><?= esc($lectura['fecha']) ?></td>
                                        <td><?= esc($lectura['nivel_gas']) ?></td>
                                        <td>
                                            <?php
                                                $estado = '';
                                                $clase_badge = '';
                                                if (isset($lectura['estado'])) {
                                                    $estado = esc($lectura['estado']);
                                                    switch ($estado) {
                                                        case 'seguro':
                                                            $clase_badge = 'bg-success';
                                                            break;
                                                        case 'precaucion':
                                                            $clase_badge = 'bg-warning text-dark';
                                                            break;
                                                        case 'peligro':
                                                            $clase_badge = 'bg-danger';
                                                            break;
                                                        default:
                                                            $clase_badge = 'bg-secondary';
                                                            break;
                                                    }
                                                } else {
                                                    $estado = 'Desconocido';
                                                    $clase_badge = 'bg-secondary';
                                                }
                                            ?>
                                            <span class="badge <?= $clase_badge ?>"><?= $estado ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay lecturas disponibles para el período seleccionado.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ECharts Gauge Chart
            const gaugeChartDom = document.getElementById('gasGaugeChart');
            if (gaugeChartDom) {
                const myGaugeChart = echarts.init(gaugeChartDom, 'dark'); // Initialize with 'dark' theme
                const nivelGasActual = <?= json_encode($nivelGasActualValue) ?>;

                const gaugeOption = {
                    series: [
                        {
                            type: 'gauge',
                            min: 0,
                            max: 1000, // Max value for the gauge (adjust as needed for your sensor's range)
                            axisLine: {
                                lineStyle: {
                                    width: 30,
                                    color: [
                                        [0.3, '#67e0e3'], // 0-300 PPM (safe)
                                        [0.7, '#37a2da'], // 300-700 PPM (caution)
                                        [1, '#fd666d']    // 700-1000 PPM (danger)
                                    ]
                                }
                            },
                            pointer: {
                                itemStyle: {
                                    color: 'auto'
                                }
                            },
                            axisTick: {
                                distance: -30,
                                length: 8,
                                lineStyle: {
                                    color: '#fff',
                                    width: 2
                                }
                            },
                            splitLine: {
                                distance: -30,
                                length: 30,
                                lineStyle: {
                                    color: '#fff',
                                    width: 4
                                }
                            },
                            axisLabel: {
                                color: 'inherit',
                                distance: 40,
                                fontSize: 20
                            },
                            detail: {
                                valueAnimation: true,
                                formatter: '{value} PPM',
                                color: 'inherit'
                            },
                            data: [
                                {
                                    value: nivelGasActual
                                }
                            ]
                        }
                    ]
                };
                myGaugeChart.setOption(gaugeOption);

                // Handle chart resizing
                window.addEventListener('resize', function() {
                    myGaugeChart.resize();
                });
            }

            // Chart.js Line Chart (Historial de Lecturas)
            const lineChartDom = document.getElementById('gasLineChart');
            if (lineChartDom) {
                const labels = <?= json_encode($labels) ?>;
                const data = <?= json_encode($data) ?>;

                const ctx = lineChartDom.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nivel de Gas (PPM)',
                            data: data,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.4, // Smooths the line
                            fill: true, // Fills the area under the line
                            pointRadius: 3, // Size of data points
                            pointBackgroundColor: 'rgb(75, 192, 192)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Allows height to be set by CSS
                        scales: {
                            x: {
                                ticks: {
                                    color: '#cbd5e0' // X-axis label color
                                },
                                grid: {
                                    color: 'rgba(203, 213, 224, 0.1)' // X-axis grid line color
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#cbd5e0' // Y-axis label color
                                },
                                grid: {
                                    color: 'rgba(203, 213, 224, 0.1)' // Y-axis grid line color
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#cbd5e0' // Legend label color
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y + ' PPM';
                                    }
                                }
                            }
                        }
                    }
                });

                // Handle chart resizing for Chart.js
                window.addEventListener('resize', function() {
                    // Chart.js handles responsiveness automatically with responsive: true and maintainAspectRatio: false
                    // No manual resize call needed for Chart.js usually.
                });
            }
        });
    </script>
</body>
</html>
