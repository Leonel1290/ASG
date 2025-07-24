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
    <style>
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #2d3748 !important;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff !important;
        }
        .navbar-nav .nav-link.active {
            color: #4299e1 !important;
        }
        .navbar-nav .nav-link:hover {
            color: #fff !important;
        }
        .container {
            flex: 1;
            padding: 2rem;
        }
        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #4a5568;
            color: #edf2f7;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2d3748;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        .card-body {
            padding: 1.5rem;
        }
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }
        .alert-info {
            background-color: #bee3f8;
            color: #1a202c;
            border-color: #90cdf4;
        }
        .alert-danger {
            background-color: #fed7d7;
            color: #1a202c;
            border-color: #fbcbcb;
        }
        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }
        .btn-secondary {
            background-color: #6b7280;
            border-color: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #4a5568;
            border-color: #4a5568;
        }
        .btn-success {
            background-color: #48bb78;
            border-color: #48bb78;
            color: white;
        }
        .btn-success:hover {
            background-color: #38a169;
            border-color: #38a169;
        }
        .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c53030;
            border-color: #c53030;
        }
        .table-dark {
            --bs-table-bg: #2d3748;
            --bs-table-striped-bg: #2d3748; /* Same as background for a consistent dark look */
            --bs-table-striped-color: #edf2f7;
            --bs-table-active-bg: #4a5568;
            --bs-table-active-color: #edf2f7;
            --bs-table-hover-bg: #4a5568;
            --bs-table-hover-color: #edf2f7;
            color: #edf2f7;
            border-color: #4a5568;
        }
        .table-dark th, .table-dark td {
            border-color: #4a5568;
        }
        /* Custom scrollbar for table-responsive */
        .table-responsive::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #4a5568;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #63b3ed;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #4299e1;
        }

        /* Modal specific styles for dark theme */
        .modal-content {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
        }
        .modal-header {
            background-color: #4a5568;
            border-bottom: 1px solid #2d3748;
            color: #edf2f7;
        }
        .modal-footer {
            border-top: 1px solid #2d3748;
        }
        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/perfil') ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil') ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion') ?>">Configuración</a>
                        </li>
                    </ul>
                    <a href="<?= base_url('/logout') ?>" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container my-5">
        <?php if (session('success')): ?>
            <div class="alert alert-success"><?= session('success') ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>
        <?php if (session('info')): ?>
            <div class="alert alert-info"><?= session('info') ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-microchip me-2"></i> Detalles del Dispositivo: <?= esc($nombreDispositivo) ?>
                </h5>
                <span class="badge bg-primary">MAC: <?= esc($mac) ?></span>
            </div>
            <div class="card-body">
                <p><strong>Ubicación:</strong> <?= esc($ubicacionDispositivo) ?></p>
                <p><strong>Última Lectura:</strong> <span class="badge bg-info"><?= $nivelGasActualDisplay ?></span></p>

                <div class="d-flex justify-content-start gap-2 mt-3">
                    <button type="button" class="btn btn-primary" id="btnMostrarCalendario">
                        <i class="fas fa-calendar-alt me-2"></i> Seleccionar Periodo
                    </button>

                    <button type="button" class="btn btn-info" id="btnVerRegistros">
                        <i class="fas fa-list-alt me-2"></i> Ver Registros
                    </button>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i> <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($data)): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-chart-line me-2"></i> Historial de Nivel de Gas</h5>
                </div>
                <div class="card-body">
                    <canvas id="gasChart"></canvas>
                </div>
            </div>
        <?php endif; ?>


    </div>

    <div class="modal fade" id="modalCalendario" tabindex="-1" aria-labelledby="modalCalendarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCalendarioLabel"><i class="fas fa-calendar-alt me-2"></i> Seleccionar Periodo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filtroFechaForm" method="GET" action="<?= base_url('detalles/' . esc($mac)) ?>">
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio:</label>
                            <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= esc($this->request->getGet('fechaInicio') ?? date('Y-m-01')) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="fechaFin" class="form-label">Fecha de Fin:</label>
                            <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= esc($this->request->getGet('fechaFin') ?? date('Y-m-d')) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i> Filtrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLecturas" tabindex="-1" aria-labelledby="modalLecturasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLecturasLabel"><i class="fas fa-list-alt me-2"></i> Registros de Lecturas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($lecturas)): ?>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-dark table-striped table-hover">
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
                                            <td><?= esc($lectura['estado']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No hay registros de lecturas para mostrar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Configuración global de Chart.js para el tema oscuro
    Chart.defaults.color = 'var(--text-light)'; // Color por defecto del texto para las etiquetas del gráfico
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"; // Fuente por defecto

    const chartLabels = <?= json_encode($labels) ?>;
    const chartData = <?= json_encode($data) ?>;
    const hasChartData = chartLabels.length > 0 && chartData.length > 0;

    if (hasChartData) {
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Nivel de Gas (PPM)',
                    data: chartData,
                    borderColor: 'rgb(66, 153, 225)', // Blue color for the line
                    backgroundColor: 'rgba(66, 153, 225, 0.2)', // Light blue fill
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'var(--text-lighter)', // Legend text color
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' PPM';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha y Hora',
                            color: 'var(--text-lighter)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            color: 'var(--text-light)' // X-axis ticks color
                        },
                        grid: {
                            color: 'rgba(74, 85, 104, 0.3)', // Lighter grid lines
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