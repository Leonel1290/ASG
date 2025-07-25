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
$nivelGasActualValue = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? (float)$lecturas[0]['nivel_gas'] : null;

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --text-dark: #343a40;
            --text-light: #f8f9fa;
            --text-lighter: #e2e6ea;
            --bg-light: #f8f9fa;
            --bg-dark: #343a40;
            --card-bg-light: #ffffff;
            --card-bg-dark: #4a5568;
            --border-color-light: #dee2e6;
            --border-color-dark: #6c757d;
        }

        body.light-mode {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        body.dark-mode {
            background-color: #1a202c; /* Darker background for dark mode */
            color: var(--text-light);
        }

        .card {
            background-color: var(--card-bg-light);
            color: var(--text-dark);
            border-color: var(--border-color-light);
        }

        body.dark-mode .card {
            background-color: var(--card-bg-dark); /* Darker card background */
            color: var(--text-light);
            border-color: var(--border-color-dark);
        }

        .navbar {
            background-color: var(--primary-color);
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: var(--text-light) !important;
        }

        .table {
            color: var(--text-dark);
        }

        body.dark-mode .table {
            color: var(--text-light);
        }

        body.dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05); /* Ligeramente más claro para filas impares en dark mode */
        }

        body.dark-mode .table thead {
            background-color: var(--dark-color);
            color: var(--text-light);
        }

        .form-control, .btn {
            border-radius: 0.375rem; /* Bootstrap default */
        }

        /* Estilos específicos para ECharts en modo oscuro */
        .echarts-dark {
            background-color: #1a202c; /* Fondo del contenedor del gráfico en dark mode */
            color: var(--text-light);
        }

        .modal-content {
            background-color: var(--card-bg-light);
            color: var(--text-dark);
        }
        body.dark-mode .modal-content {
            background-color: var(--card-bg-dark);
            color: var(--text-light);
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color-light);
        }
        body.dark-mode .modal-header, body.dark-mode .modal-footer {
            border-color: var(--border-color-dark);
        }

        /* Estilo para el contenedor del velocímetro */
        #gasGaugeChart {
            width: 100%;
            height: 300px; /* Ajusta la altura según sea necesario */
            margin-top: 20px;
        }
    </style>
</head>
<body class="light-mode">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Sistema de Monitoreo de Gas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/perfil">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/enlace">Enlazar Dispositivo</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-dark" id="darkModeToggle">
                            <i class="bi bi-moon-fill"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <form action="/logout" method="post" class="d-flex ms-2">
                            <button type="submit" class="btn btn-outline-light">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Detalles del Dispositivo: <?= esc($nombreDispositivo) ?> (MAC: <?= esc($mac) ?>)</h1>
        <p class="text-muted">Ubicación: <?= esc($ubicacionDispositivo) ?></p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info" role="alert">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Nivel de Gas Actual</h5>
                        <p class="card-text display-4"><?= $nivelGasActualDisplay ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Nivel de Gas (Histórico)</h5>
                        <?php if (!empty($data)): ?>
                            <div id="gasChart" style="width: 100%; height: 300px;"></div>
                        <?php else: ?>
                            <p class="text-center">No hay datos de lecturas disponibles para el gráfico en el rango de fechas seleccionado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Nivel de Gas (Velocímetro)</h5>
                        <?php if ($nivelGasActualValue !== null): ?>
                            <div id="gasGaugeChart"></div>
                        <?php else: ?>
                            <p class="text-center">No hay datos de nivel de gas disponibles para el velocímetro.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Lecturas de Gas</h3>
                    <div>
                        <button type="button" class="btn btn-secondary me-2" id="btnMostrarCalendario">
                            <i class="bi bi-calendar-check"></i> Filtrar por Fecha
                        </button>
                        <?php if (!empty($lecturas)): ?>
                            <button type="button" class="btn btn-info" id="btnVerRegistros">
                                <i class="bi bi-list-columns-reverse"></i> Ver Registros
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal fade" id="modalCalendario" tabindex="-1" aria-labelledby="modalCalendarioLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCalendarioLabel">Seleccionar Rango de Fechas</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?= site_url('detalles/' . esc($mac)) ?>" method="get">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="fechaInicio" class="form-label">Fecha de Inicio:</label>
                                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= $request->getGet('fechaInicio') ?? '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fechaFin" class="form-label">Fecha de Fin:</label>
                                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= $request->getGet('fechaFin') ?? '' ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Aplicar Filtro</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalLecturas" tabindex="-1" aria-labelledby="modalLecturasLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLecturasLabel">Registros de Lecturas</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <?php if (!empty($lecturas)): ?>
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Fecha y Hora</th>
                                                    <th>Nivel de Gas (PPM)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($lecturas as $lectura): ?>
                                                    <tr>
                                                        <td><?= esc($lectura['fecha']) ?></td>
                                                        <td><?= esc($lectura['nivel_gas']) ?> PPM</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p class="text-center">No hay lecturas disponibles para mostrar.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;

        // Load theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.classList.add(savedTheme);
        } else {
            // Default to light mode if no preference saved
            body.classList.add('light-mode');
        }

        darkModeToggle.addEventListener('click', function() {
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                body.classList.add('light-mode');
                localStorage.setItem('theme', 'light-mode');
            } else {
                body.classList.remove('light-mode');
                body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark-mode');
            }
        });
    });

    // Chart.js for historical data
    const labels = <?= json_encode($labels) ?>;
    const data = <?= json_encode($data) ?>;
    const hasChartData = data.length > 0;

    if (hasChartData) {
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nivel de Gas (PPM)',
                    data: data,
                    borderColor: 'var(--primary-color)',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Histórico de Nivel de Gas',
                        color: 'var(--text-dark)'
                    },
                    legend: {
                        labels: {
                            color: 'var(--text-dark)'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(74, 85, 104, 0.3)',
                            drawBorder: true
                        },
                        ticks: {
                            color: 'var(--text-dark)'
                        },
                        title: {
                            display: true,
                            text: 'Fecha y Hora',
                            color: 'var(--text-dark)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
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
                            color: 'rgba(74, 85, 104, 0.3)', // Lighter grid lines
                            drawBorder: true
                        }
                    }
                }
            }
        });
    } else {
        // If no chart data, hide the canvas and display the message provided in HTML
        const chartCanvas = document.getElementById('gasChart');
        if (chartCanvas) {
            chartCanvas.style.display = 'none';
        }
    }

    // ECharts Gauge for current gas level
    const nivelGasActualValue = <?= json_encode($nivelGasActualValue) ?>;
    if (nivelGasActualValue !== null) {
        var gaugeChartDom = document.getElementById('gasGaugeChart');
        var myGaugeChart = echarts.init(gaugeChartDom, document.body.classList.contains('dark-mode') ? 'dark' : 'light'); // Initialize with theme
        var gaugeOption;

        gaugeOption = {
            series: [
                {
                    type: 'gauge',
                    min: 0, // Minimum value for the gauge
                    max: 1000, // Adjust max value based on typical gas levels (e.g., 1000 PPM)
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
                        fontSize: 16 // Adjusted font size for better fit
                    },
                    detail: {
                        valueAnimation: true,
                        formatter: '{value} PPM', // Changed to PPM
                        color: 'inherit',
                        fontSize: 20
                    },
                    data: [
                        {
                            value: nivelGasActualValue // Use the actual latest gas value
                        }
                    ]
                }
            ]
        };
        myGaugeChart.setOption(gaugeOption);

        // Update gauge theme on dark mode toggle
        darkModeToggle.addEventListener('click', function() {
            if (document.body.classList.contains('dark-mode')) {
                myGaugeChart.dispose(); // Dispose old chart
                myGaugeChart = echarts.init(gaugeChartDom, 'dark'); // Init with dark theme
            } else {
                myGaugeChart.dispose(); // Dispose old chart
                myGaugeChart = echarts.init(gaugeChartDom, 'light'); // Init with light theme
            }
            myGaugeChart.setOption(gaugeOption); // Set option again for new chart
        });

    } else {
        const gaugeChartCanvas = document.getElementById('gasGaugeChart');
        if (gaugeChartCanvas) {
            gaugeChartCanvas.style.display = 'none';
        }
    }


    // Solo mostrar el modal al hacer click en el botón
    document.addEventListener('DOMContentLoaded', function() {
        const btnMostrarCalendario = document.getElementById('btnMostrarCalendario');
        const modalCalendario = new bootstrap.Modal(document.getElementById('modalCalendario'));
        btnMostrarCalendario.addEventListener('click', function() {
            modalCalendario.show();
        });

        // Código para el nuevo modal de lecturas
        const btnVerRegistros = document.getElementById('btnVerRegistros');
        const modalLecturas = new bootstrap.Modal(document.getElementById('modalLecturas'));
        if (btnVerRegistros) { // Asegúrate de que el botón exista antes de añadir el listener
            btnVerRegistros.addEventListener('click', function() {
                modalLecturas.show();
            });
        }
    });
</script>

</body>
</html>