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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        :root {
            --bg-body: #2C3E50; /* Dark blue-grey */
            --bg-card: #34495E; /* Slightly lighter blue-grey for cards */
            --text-light: #ECF0F1; /* Light grey for most text */
            --text-lighter: #BDC3C7; /* Even lighter grey for titles/headers */
            --border-color: #4A5D70; /* Border for cards and elements */
            --primary-color: #3498DB; /* Blue for primary actions/highlights */
            --danger-color: #E74C3C; /* Red for danger/alerts */
            --success-color: #2ECC71; /* Green for success */
            --warning-color: #F39C12; /* Orange for warning */
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-brand, .nav-link {
            color: var(--text-lighter) !important;
        }

        .card {
            background-color: var(--bg-card);
            color: var(--text-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        .card-header {
            background-color: rgba(0,0,0,0.1);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-lighter);
        }

        .table {
            color: var(--text-light);
        }

        .table th {
            color: var(--text-lighter);
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            border-top: 1px solid var(--border-color);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,0.05);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2186c4;
            border-color: #2186c4;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .alert-info {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert-warning {
            background-color: rgba(243, 156, 18, 0.2);
            color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .form-control {
            background-color: #4A5D70;
            color: var(--text-light);
            border: 1px solid var(--border-color);
        }
        .form-control:focus {
            background-color: #4A5D70;
            color: var(--text-light);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-light);
            border: 1px solid var(--border-color);
        }
        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }
        .modal-footer {
            border-top: 1px solid var(--border-color);
        }
        .close {
            color: var(--text-light);
        }

        /* Custom styles for status badges */
        .badge-safe {
            background-color: var(--success-color);
            color: white;
        }
        .badge-caution {
            background-color: var(--warning-color);
            color: white;
        }
        .badge-danger {
            background-color: var(--danger-color);
            color: white;
        }
        .badge-no-data {
            background-color: #7F8C8D; /* Grey for no data */
            color: white;
        }
        .no-data-message {
            text-align: center;
            padding: 20px;
            color: var(--text-light);
        }

        /* Styles for date range input */
        .drp-selected {
            background-color: var(--primary-color);
            color: white;
        }
        .calendar-table .active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        .calendar-table td.active:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        .drp-calendar .table-condensed th,
        .drp-calendar .table-condensed td {
            color: var(--text-light);
        }
        .daterangepicker {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-light);
        }
        .daterangepicker td.active, .daterangepicker td.active:hover {
            background-color: var(--primary-color);
        }
        .daterangepicker .drp-buttons .btn {
            border: 1px solid var(--border-color);
            color: var(--text-light);
        }
        .daterangepicker .drp-buttons .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .daterangepicker .calendar thead tr:first-child th {
            background-color: var(--bg-card);
        }
        .daterangepicker .calendar-table {
            background-color: var(--bg-card);
        }
        .daterangepicker .month select,
        .daterangepicker .year select {
            background-color: var(--bg-card);
            color: var(--text-light);
            border: 1px solid var(--border-color);
        }

        /* Chart.js tooltips for dark theme */
        .chartjs-tooltip {
            background-color: var(--bg-card) !important;
            color: var(--text-light) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 5px !important;
        }
        .chartjs-tooltip-key {
            background-color: var(--primary-color) !important; /* Example color for key */
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/perfil">
                <i class="fas fa-arrow-left"></i> Volver a Perfil
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <form action="/logout" method="post" class="d-inline">
                            <button type="submit" class="nav-link btn btn-link" style="color: var(--text-lighter) !important;">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4 text-center text-lighter">Detalle del Dispositivo: <?= esc($nombreDispositivo) ?></h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-center mb-3">Información del Dispositivo</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-light"><strong>MAC:</strong> <?= esc($mac) ?></li>
                    <li class="list-group-item bg-transparent text-light"><strong>Nombre:</strong> <?= esc($nombreDispositivo) ?></li>
                    <li class="list-group-item bg-transparent text-light"><strong>Ubicación:</strong> <?= esc($ubicacionDispositivo) ?></li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <h5 class="card-title text-center mb-3">Nivel de Gas Actual</h5>
                <div class="gauge-container d-flex justify-content-center align-items-center" style="height: 250px;">
                    <canvas id="gasGauge" data-type="radial-gauge"
                            data-width="250" data-height="250"
                            data-units="PPM"
                            data-min-value="0"
                            data-max-value="1000"
                            data-major-ticks="0,100,200,300,400,500,600,700,800,900,1000"
                            data-minor-ticks="5"
                            data-stroke-ticks="true"
                            data-highlights='[
                                {"from": 0, "to": 199, "color": "rgba(0,128,0,0.5)"},      {"from": 200, "to": 499, "color": "rgba(255,165,0,0.5)"}, {"from": 500, "to": 1000, "color": "rgba(255,0,0,0.5)"}   ]'
                            data-color-plate="transparent"
                            data-border-inner-width="0"
                            data-border-outer-width="0"
                            data-value="<?= $nivelGasActualDisplay !== 'Sin datos' ? floatval(str_replace(' PPM', '', $nivelGasActualDisplay)) : 0; ?>"
                            data-animation-rule="linear"
                            data-animation-duration="500"
                            data-title="Gas"
                            data-value-box="true"
                            data-font-value="bold 20px Arial"
                            data-color-value-box="var(--bg-card)"
                            data-color-value-box-rect="var(--bg-card)"
                            data-color-value-box-border="transparent"
                            data-animated-value="true"
                            data-animation-target="value"
                            data-color-major-ticks="var(--text-light)"
                            data-color-minor-ticks="var(--text-light)"
                            data-color-title="var(--text-lighter)"
                            data-color-units="var(--text-light)"
                            data-color-numbers="var(--text-light)"
                            data-color-needle-shadow-down="transparent"
                            data-color-needle-start="rgba(255, 100, 100, 1)"
                            data-color-needle-end="rgba(255, 0, 0, 1)"
                            data-color-needle-circle-inner="var(--text-light)"
                            data-color-needle-circle-outer="var(--bg-card)"
                            data-color-needle-circle-border="var(--text-light)"
                            data-needle-start="20"
                            data-needle-end="80"
                            data-needle-type="line"
                            data-needle-width="3"
                            data-value-box-border-radius="5"
                            data-value-box-stroke="0"
                            data-value-box-shadow="false"
                            data-font-units="bold 16px Arial"
                            data-font-title="bold 24px Arial"
                            data-font-tick-labels="14px Arial"
                            data-font-major-ticks="14px Arial"
                            data-font-minor-ticks="10px Arial"
                            data-border-shadow-width="0"
                            data-borders="false"
                            data-shadow-items="0"
                            data-shadow-inner="false"
                            data-shadow-outer="false"
                            data-shadow-from-level="false"
                            data-glow="false"
                            data-animation-delay="0"
                            data-animation-rule="linear"
                            data-animation-target="value"
                    ></canvas>
                </div>
                <?php if ($nivelGasActualDisplay === 'Sin datos'): ?>
                    <p class="text-danger mt-3">No hay datos de nivel de gas recientes para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>


        <?php if ($message): ?>
            <div class="alert alert-info shadow-sm mb-4" role="alert">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                Histórico de Nivel de Gas
                <button type="button" class="btn btn-primary btn-sm" id="btnMostrarCalendario">
                    <i class="fas fa-calendar-alt"></i> Filtrar por Fecha
                </button>
            </div>
            <div class="card-body">
                <canvas id="gasChart"></canvas>
                <div id="noChartDataMessage" class="no-data-message" style="display: none;">
                    No hay datos de gas para el rango de fechas seleccionado.
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                Registros Detallados
                <button type="button" class="btn btn-primary btn-sm float-end" id="btnVerRegistros">
                    <i class="fas fa-table"></i> Ver Tabla
                </button>
            </div>
            <div class="card-body">
                <p>Haz click en "Ver Tabla" para ver todos los registros del rango seleccionado.</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCalendario" tabindex="-1" aria-labelledby="modalCalendarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCalendarioLabel">Seleccionar Rango de Fechas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/detalles/<?= esc($mac) ?>" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dateRangePicker" class="form-label">Rango de Fechas:</label>
                            <input type="text" id="dateRangePicker" name="dateRange" class="form-control" readonly>
                            <input type="hidden" id="fechaInicio" name="fechaInicio">
                            <input type="hidden" id="fechaFin" name="fechaFin">
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLecturasLabel">Todos los Registros de Gas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($lecturas)): ?>
                        <div class="table-responsive">
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
                                                    $estado = 'Sin Datos'; // Default
                                                    $badgeClass = 'badge-no-data';
                                                    if (isset($lectura['estado'])) {
                                                        $estado = ucfirst(esc($lectura['estado']));
                                                        switch ($lectura['estado']) {
                                                            case 'seguro':
                                                                $badgeClass = 'badge-safe';
                                                                break;
                                                            case 'precaucion':
                                                                $badgeClass = 'badge-caution';
                                                                break;
                                                            case 'peligro':
                                                                $badgeClass = 'badge-danger';
                                                                break;
                                                        }
                                                    }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= $estado ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No hay registros detallados para mostrar en este rango de fechas.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-gauges@2.1.7/gauge.min.js"></script>

    <script>
        // Initialize Date Range Picker
        $(function() {
            var start = moment("<?= esc($request->getGet('fechaInicio') ?? '') ?>");
            var end = moment("<?= esc($request->getGet('fechaFin') ?? '') ?>");

            // Si las fechas no son válidas (e.g., primera carga sin filtros), inicializar con fechas de ejemplo o en blanco
            if (!start.isValid() || !end.isValid()) {
                start = moment().subtract(29, 'days');
                end = moment();
            }

            $('#dateRangePicker').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
                    'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                    'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' a ',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'Desde',
                    toLabel: 'Hasta',
                    customRangeLabel: 'Rango Personalizado',
                    weekLabel: 'S',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    firstDay: 1
                }
            }, function(start, end, label) {
                $('#fechaInicio').val(start.format('YYYY-MM-DD'));
                $('#fechaFin').val(end.format('YYYY-MM-DD'));
            });

            // Establecer los valores ocultos al cargar la página si ya hay fechas en el GET
            $('#fechaInicio').val(start.format('YYYY-MM-DD'));
            $('#fechaFin').val(end.format('YYYY-MM-DD'));
        });


        // Chart.js Configuration
        const labels = <?= json_encode($labels) ?>;
        const data = <?= json_encode($data) ?>;
        const message = <?= json_encode($message) ?>;

        if (data.length > 0 && !message) {
            const ctx = document.getElementById('gasChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nivel de Gas (PPM)',
                        data: data,
                        borderColor: 'var(--primary-color)',
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: 'var(--primary-color)',
                        pointBorderColor: 'var(--text-light)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'var(--text-light)',
                        pointHoverBorderColor: 'var(--primary-color)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'var(--text-lighter)'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw + ' PPM';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(74, 85, 104, 0.3)', // Lighter grid lines
                                drawBorder: true
                            },
                            ticks: {
                                color: 'var(--text-light)',
                                maxRotation: 45,
                                minRotation: 45
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
            const noDataMessage = document.getElementById('noChartDataMessage');
            if (chartCanvas) {
                chartCanvas.style.display = 'none';
            }
            if (noDataMessage) {
                noDataMessage.style.display = 'block'; // Show "No hay datos" message
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

            // --- Código para actualizar el Velocímetro en tiempo real (opcional) ---
            // Solo descomentar si tienes la ruta y el método en el controlador configurados
            const gasGaugeCanvas = document.getElementById('gasGauge');
            const macAddress = "<?= esc($mac); ?>"; // Asegúrate de que $mac esté disponible

            function updateGasGauge() {
                fetch(`/detalles/latest-gas/${macAddress}`) // Ajusta la URL si es diferente
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.nivel_gas !== null) {
                            let gaugeInstance = undefined;
                            if (window.Gauges) { // Check if Gauges global object exists
                                // Find the gauge instance in the global Gauges array by its canvas element
                                gaugeInstance = window.Gauges.find(g => g.options.renderTo === gasGaugeCanvas);
                            }

                            if (gaugeInstance) {
                                gaugeInstance.value = data.nivel_gas; // Set the new value
                                const noDataMessage = document.querySelector('.gauge-container + p.text-danger');
                                if (noDataMessage) {
                                    noDataMessage.style.display = 'none'; // Hide "Sin datos" message
                                }
                            } else {
                                console.error("Gauge instance not found for ID 'gasGauge'. It might not have been initialized yet.");
                                // Re-render if not found, useful if gauge was initialized later or if the object isn't directly accessible.
                                // This assumes the gauge is initialized from data attributes on page load.
                                // If you dynamically create gauges with JS, you'd store the instance.
                                // For simplicity with data-attributes, a page refresh or re-initialization might be needed.
                                // For true dynamic updates, it's better to initialize the gauge via JS and store its instance.
                                // E.g., var gasGaugeInstance = new RadialGauge({ renderTo: 'gasGauge', ... });
                            }
                        } else {
                            console.warn("No latest gas data received from API.");
                            const noDataMessage = document.querySelector('.gauge-container + p.text-danger');
                            if (noDataMessage) {
                                noDataMessage.style.display = 'block'; // Show "Sin datos" message
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching latest gas data:', error));
            }

            // Uncomment the following lines if you want real-time updates
            // Initial update when the page loads
            // updateGasGauge();
            // Update every 10 seconds (adjust as needed)
            // setInterval(updateGasGauge, 10000); // 10 seconds
        });
    </script>

</body>
</html>