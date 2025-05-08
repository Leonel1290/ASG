<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo: <?= esc($mac) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
            padding: 2rem;
        }

        .page-title {
            color: #f7fafc;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra sutil */
            border-radius: 0.5rem;
            overflow: hidden;
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

        .bg-success {
            background-color: #48bb78 !important;
            color: #2d3748;
        }

        .bg-warning {
            background-color: #f6e05e !important;
            color: #2d3748;
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
            transition: box-shadow 0.3s ease; /* Animación para la sombra */
        }

        .card:hover {
            box-shadow: 0 0 20px #667eea, 0 0 40px #667eea, 0 0 60px #667eea; /* Sombra neón y ancha */
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

        .progress {
            height: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #4a5568;
        }

        .progress-bar {
            background-color: #48bb78;
            transition: width 0.3s ease;
            color: #fff;
            text-align: center;
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
            color: #667eea;
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
        }
    </style>
</head>
<body>

<div class="container my-5">
    <h1 class="page-title"><i class="fas fa-microchip me-2"></i> Detalle del Dispositivo: <span class="text-primary"><?= esc($mac) ?></span></h1>

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
                    <tr><td colspan="3" class="text-center">No hay lecturas disponibles para este dispositivo.</td></tr>
                <?php else: ?>
                    <?php foreach ($lecturas as $lectura):
                        $rowClass = '';
                        $estado = '';
                        if ($lectura['nivel_gas'] >= 500) {
                            $rowClass = 'bg-danger';
                            $estado = 'Peligro';
                        } elseif ($lectura['nivel_gas'] >= 200) {
                            $rowClass = 'bg-warning';
                            $estado = 'Precaución';
                        } else {
                            $rowClass = 'bg-success';
                            $estado = 'Seguro';
                        }
                    ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= esc($lectura['fecha']) ?></td>
                            <td><?= esc($lectura['nivel_gas']) ?></td>
                            <td class="text-center"><?= $estado ?></td>
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
                <p class="security-level-text"><i class="fas fa-shield-alt me-2"></i> Nivel de Seguridad: <span id="securityLevel">Sin datos</span></p>
                <div class="progress">
                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="current-gas-level-card mt-4">
                <h5 class="current-gas-level-title"><i class="fas fa-tachometer-alt me-2"></i> Nivel de Gas Actual</h5>
                <p class="current-gas-level" id="nivelGas">Cargando...</p>
            </div>
        </div>
    </div>

</div>

<script>
    const lecturas = <?= json_encode($lecturas) ?>;
    const labels = <?= json_encode(array_reverse($labels)) ?>;
    const data = <?= json_encode(array_reverse($data)) ?>;

    const ultimoValor = lecturas[lecturas.length - 1];

    if (ultimoValor) {
        const nivelGas = ultimoValor['nivel_gas'];
        document.getElementById('nivelGas').textContent = `${nivelGas} PPM`;
        updateProgressBar(nivelGas);
    }

    function updateProgressBar(value) {
        const progressBar = document.getElementById('progressBar');
        const securityLevel = document.getElementById('securityLevel');
        let width = 0;
        let levelText = 'Sin datos';
        let barClass = '';

        if (value <= 200) {
            width = 33;
            levelText = 'Seguro';
            barClass = 'bg-success';
        } else if (value <= 300) {
            width = 66;
            levelText = 'Precaución';
            barClass = 'bg-warning';
        } else {
            width = 100;
            levelText = 'Peligro';
            barClass = 'bg-danger';
        }

        progressBar.style.width = `${width}%`;
        progressBar.className = `progress-bar ${barClass}`;
        progressBar.setAttribute('aria-valuenow', width);
        securityLevel.textContent = levelText;
    }

    const ctx = document.getElementById('gasChart').getContext('2d');
    const gasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nivel de Gas (PPM)',
                data: data,
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
                        display: false // Ocultar las etiquetas del eje X
                    },
                    reverse: true,
                    grid: {
                        display: false // Ocultar las líneas de la cuadrícula del eje X
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
                    }
                }
            }
        }
    });

    console.log('Lecturas:', lecturas);
    console.log('Labels:', labels);
    console.log('Data:', data);
</script>

</body>
</html>