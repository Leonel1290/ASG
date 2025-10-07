<?php
// Initialize variables to prevent errors if they are not passed from the controller
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Use MAC as default name if no name is passed
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$lecturas = $lecturas ?? []; // Array of readings for the table (expected DESC order)
$labels = $labels ?? [];     // Date/time labels for the chart (expected ASC order)
$data = $data ?? [];         // Gas levels for the chart (expected ASC order)
$message = $message ?? null; // Optional messages
$request = service('request'); // Assuming CodeIgniter context to get request object

// Get the latest gas level to display in the simple card
// It's assumed that the $lecturas array is ordered in DESCENDING order (most recent first)
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? esc($lecturas[0]['nivel_gas']) . ' PPM' : 'Sin datos';
$nivelGasActualValue = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? (float)$lecturas[0]['nivel_gas'] : 0; // Default to 0 if no data

// Helper function to escape HTML data (similar to CodeIgniter's esc())
if (!function_exists('esc')) {
    function esc($str) {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
   
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
            --navbar-height: 70px;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: var(--navbar-height); /* Espacio para el navbar fijo */
        }

        /* --- Navbar Styles --- */
        .navbar-custom {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            height: var(--navbar-height);
        }
        .navbar-custom .navbar-brand {
            color: var(--text-darker);
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .navbar-custom .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        .navbar-custom .nav-link {
            color: var(--text-light);
            transition: color 0.2s ease-in-out;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 0.375rem;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--text-darker);
            background-color: rgba(102, 126, 234, 0.2);
        }
        .navbar-custom .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }
        .navbar-custom .dropdown-item {
            color: var(--text-light);
        }
        .navbar-custom .dropdown-item:hover {
            background-color: var(--border-color);
            color: var(--text-darker);
        }
        .navbar-custom .navbar-toggler {
            border-color: var(--border-color);
        }
        .navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(203, 213, 224, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
       
        /* --- General Styles --- */
        .container {
            padding: 2rem;
        }

        .btn-outline-secondary {
            color: var(--text-light); border-color: var(--text-light); transition: all 0.2s ease-in-out;
        }
        .btn-outline-secondary:hover {
            color: var(--bg-dark); background-color: var(--text-light); border-color: var(--text-light);
        }

        .page-header {
            text-align: center; margin-bottom: 2.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);
        }
        .page-title {
            color: var(--text-darker); font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;
        }
        .page-title .text-primary {
            color: var(--primary-color) !important;
        }
        .device-info {
            color: var(--secondary-color); font-size: 1.1rem;
        }

        .card {
            background-color: var(--card-bg); color: var(--text-lighter); border: none; border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .card-body { padding: 2rem; }
        .card-header {
            background-color: #374151;
            border-bottom: 1px solid var(--border-color);
            font-weight: bold;
        }
        .card-title {
            font-size: 1.75rem; font-weight: bold; color: var(--primary-color); margin-bottom: 1.5rem;
            display: flex; align-items: center; justify-content: center;
        }
        .card-title .fas { margin-right: 0.75rem; }

        .current-gas-value {
            font-size: 3rem; font-weight: bold; color: var(--text-darker); margin-top: 1rem;
            animation: pulse 1.5s infinite alternate;
        }
        @keyframes pulse {
            from { transform: scale(1); } to { transform: scale(1.03); }
        }

        .section-title {
            color: var(--text-darker); font-size: 1.8rem; margin-top: 3rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color); display: flex; align-items: center;
        }
        .section-title .fas { margin-right: 0.75rem; }

        .table-responsive {
            margin-bottom: 2rem; border-radius: 0.75rem; overflow: hidden;
        }
        .table {
            width: 100%; color: var(--text-lighter); border-collapse: collapse; background-color: var(--card-bg);
        }
        .table th, .table td {
            padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color);
        }
        .table th {
            background-color: var(--border-color); color: var(--text-darker); font-weight: bold; text-transform: uppercase; font-size: 0.9rem;
        }
        .table tbody tr:nth-child(even) { background-color: #374151; }
        .table tbody tr:hover { background-color: #4a5568; transition: background-color 0.2s ease; cursor: default; }

        .badge {
            padding: 0.5em 0.8em; border-radius: 0.5rem; font-weight: bold; font-size: 0.85rem;
        }
        .badge.bg-success { background-color: var(--success-color) !important; color: var(--bg-dark); }
        .badge.bg-warning { background-color: var(--warning-color) !important; color: var(--bg-dark); }
        .badge.bg-danger { background-color: var(--danger-color) !important; color: var(--text-darker); }

        .chart-container {
            height: 400px;
        }

        .progress {
            height: 1.25rem; border-radius: 0.625rem; background-color: var(--border-color); margin-bottom: 1rem;
        }
        .progress-bar {
            transition: width 0.6s ease-in-out; color: var(--text-darker); font-weight: bold; line-height: 1.25rem;
        }
        .security-level-display {
            font-size: 1.25rem; font-weight: bold; color: var(--text-darker); margin-top: 1rem;
            display: flex; align-items: center; justify-content: center;
        }
       
        .modal-content {
            background-color: var(--card-bg); color: var(--text-lighter); border: 1px solid var(--border-color); border-radius: 0.75rem;
        }
        .modal-header, .modal-footer { border-color: var(--border-color); }
        .modal-title { color: var(--text-darker); }
        .btn-close { filter: invert(1); }
        .form-control, .form-select {
            background-color: var(--bg-dark);
            color: var(--text-light);
            border-color: var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-dark);
            color: var(--text-light);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        .form-control::placeholder {
            color: var(--secondary-color);
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('/dashboard') ?>">
            <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo">
            GasGuardian
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/dashboard') ?>"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil') ?>"><i class="fas fa-microchip me-1"></i> Mis Dispositivos</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                 <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> Mi Cuenta
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('/perfil') ?>"><i class="fas fa-user-edit me-2"></i> Mi Perfil</a></li>
                        <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                        <li><a class="dropdown-item" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container">
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
                    <h2 class="card-title"><i class="fas fa-shield-alt"></i> Nivel de Seguridad</h2>
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
            <i class="fas fa-filter me-2"></i>Filtrar Lecturas por Período
        </div>
        <div class="card-body">
            <form action="<?= base_url('detalles/' . esc($mac)) ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="fechaInicio" class="form-label">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= esc($request->getGet('fechaInicio') ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="fechaFin" class="form-label">Fecha Fin:</label>
                    <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= esc($request->getGet('fechaFin') ?? '') ?>">
                </div>
                <div class="col-md-4 d-flex justify-content-start justify-content-md-end pt-3 pt-md-0">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-check me-2"></i>Aplicar</button>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalLecturas"><i class="fas fa-table me-2"></i>Ver Registros</button>
                </div>
            </form>
        </div>
    </div>

    <section>
        <div class="card">
             <div class="card-header">
                <h2 class="section-title" style="margin: 0; padding: 0; border: none; font-size: 1.25rem;"><i class="fas fa-chart-line"></i> Histórico de Niveles de Gas</h2>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="gasLineChart"></canvas>
                </div>
                <?php if (empty($labels) || empty($data)): ?>
                    <p class="text-center text-muted mt-3">No hay datos suficientes para mostrar el gráfico en el período seleccionado.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalLecturas" tabindex="-1" aria-labelledby="modalLecturasLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLecturasLabel"><i class="fas fa-history me-2"></i>Registros Detallados de Lecturas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar-alt me-2"></i> Fecha y Hora</th>
                                    <th><i class="fas fa-thermometer-half me-2"></i> Nivel de Gas (PPM)</th>
                                    <th class="text-center"><i class="fas fa-exclamation-triangle me-2"></i> Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($lecturas)): ?>
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
                                                    $estado = 'Peligro'; $class = 'bg-danger';
                                                } elseif ($nivel >= 200) {
                                                    $estado = 'Precaución'; $class = 'bg-warning text-dark';
                                                } elseif ($nivel >= 0) {
                                                    $estado = 'Seguro'; $class = 'bg-success';
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = <?= json_encode($labels ?? []) ?>;
    const data = <?= json_encode($data ?? []) ?>;
    const ultimoValor = data.length > 0 ? (parseFloat(data[data.length - 1]) || 0) : <?= $nivelGasActualValue ?>;

    const THRESHOLD_WARNING = 200;
    const THRESHOLD_DANGER = 500;

    // --- Colores del CSS para usarlos en JS ---
    const styles = getComputedStyle(document.documentElement);
    const colorSuccess = styles.getPropertyValue('--success-color').trim();
    const colorWarning = styles.getPropertyValue('--warning-color').trim();
    const colorDanger = styles.getPropertyValue('--danger-color').trim();
    const colorPrimary = styles.getPropertyValue('--primary-color').trim();
    const colorTextLight = styles.getPropertyValue('--text-light').trim();
    const colorTextDarker = styles.getPropertyValue('--text-darker').trim();
    const colorBorder = styles.getPropertyValue('--border-color').trim();

    // --- Actualizar Barra de Progreso de Seguridad ---
    function updateSecurityProgressBar(value) {
        const progressBar = document.getElementById('progressBar');
        const securityLevelText = document.getElementById('securityLevelText');
        if (!progressBar || !securityLevelText) return;

        let width = 0, levelText = 'Sin Datos', barClass = '';
        const safeValue = Math.max(0, value);

        if (safeValue < THRESHOLD_WARNING) {
            width = (safeValue / THRESHOLD_WARNING) * 33;
            levelText = '<i class="fas fa-check-circle me-1"></i> Seguro'; barClass = 'bg-success';
        } else if (safeValue < THRESHOLD_DANGER) {
            width = 33 + ((safeValue - THRESHOLD_WARNING) / (THRESHOLD_DANGER - THRESHOLD_WARNING)) * 33;
            levelText = '<i class="fas fa-exclamation-triangle me-1"></i> Precaución'; barClass = 'bg-warning text-dark';
        } else {
            width = 66 + Math.min(34, ((safeValue - THRESHOLD_DANGER) / 500) * 34); // Limita el crecimiento para que no sea tan brusco
            levelText = '<i class="fas fa-skull-crossbones me-1"></i> Peligro'; barClass = 'bg-danger';
        }

        width = Math.min(100, Math.max(0, width));
        progressBar.style.width = `${width}%`;
        progressBar.className = `progress-bar ${barClass}`;
        progressBar.setAttribute('aria-valuenow', width);
        securityLevelText.innerHTML = levelText;
    }

    updateSecurityProgressBar(ultimoValor);
   
    // --- Configuración del Gráfico Mejorado con Chart.js ---
    const lineChartDom = document.getElementById('gasLineChart');
    if (lineChartDom && labels.length > 0 && data.length > 0) {
        const ctx = lineChartDom.getContext('2d');

        // Creación del gradiente para el área bajo la línea
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(102, 126, 234, 0.5)');
        gradient.addColorStop(1, 'rgba(102, 126, 234, 0)');

        const gasLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nivel de Gas (PPM)',
                    data: data,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: colorPrimary, // Color base
                    borderWidth: 2.5,
                    tension: 0.4, // Curvas más suaves
                    pointRadius: 2,
                    pointBackgroundColor: colorPrimary,
                    pointHoverRadius: 6,
                    // MEJORA: Color dinámico del segmento de la línea
                    segment: {
                        borderColor: ctx => {
                            if (ctx.p1.raw >= THRESHOLD_DANGER) return colorDanger;
                            if (ctx.p1.raw >= THRESHOLD_WARNING) return colorWarning;
                            return colorPrimary;
                        },
                    },
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: colorTextDarker, font: { size: 14 } }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: colorTextDarker,
                        bodyColor: colorTextLight,
                        borderColor: colorPrimary,
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: (context) => `Nivel de Gas: ${context.parsed.y.toFixed(2)} PPM`,
                        }
                    },
                    // MEJORA: Líneas de umbral de advertencia y peligro
                    annotation: {
                        annotations: {
                            warningLine: {
                                type: 'line',
                                yMin: THRESHOLD_WARNING,
                                yMax: THRESHOLD_WARNING,
                                borderColor: colorWarning,
                                borderWidth: 2,
                                borderDash: [6, 6],
                                label: {
                                    content: `Precaución (${THRESHOLD_WARNING} PPM)`,
                                    enabled: true,
                                    position: 'start',
                                    backgroundColor: colorWarning,
                                    color: '#000',
                                    font: { weight: 'bold' }
                                }
                            },
                            dangerLine: {
                                type: 'line',
                                yMin: THRESHOLD_DANGER,
                                yMax: THRESHOLD_DANGER,
                                borderColor: colorDanger,
                                borderWidth: 2,
                                borderDash: [6, 6],
                                label: {
                                    content: `Peligro (${THRESHOLD_DANGER} PPM)`,
                                    enabled: true,
                                    position: 'start',
                                    backgroundColor: colorDanger,
                                    color: colorTextDarker,
                                    font: { weight: 'bold' }
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { display: false },
                        ticks: { color: colorTextLight, maxRotation: 20, autoSkip: true, maxTicksLimit: 10 },
                        grid: { color: 'rgba(74, 85, 104, 0.2)' }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nivel de Gas (PPM)',
                            color: colorTextDarker,
                            font: { size: 14, weight: 'bold' }
                        },
                        ticks: {
                            color: colorTextLight,
                            callback: (value) => value + ' PPM'
                        },
                        grid: { color: 'rgba(74, 85, 104, 0.4)' }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }
});
</script>
</body>
</html>