<?php
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Usar la MAC como nombre por defecto si no se pasa nombre
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$lecturas = $lecturas ?? [];
$labels = $labels ?? [];
$data = $data ?? [];
$message = $message ?? null;

// Obtener el último nivel de gas para mostrarlo en la tarjeta simple
// Se asume que el arreglo $lecturas está ordenado de forma ascendente (el más antiguo primero)
$ultimoIndice = count($lecturas) - 1;
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[$ultimoIndice]['nivel_gas']) ? esc($lecturas[$ultimoIndice]['nivel_gas']) . ' PPM' : 'Sin datos';

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
        /* Estilos generales y navbar */
        body {
            background-color: #1a202c; /* Dark mode background */
            color: #cbd5e0; /* Light text */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: #2d3748 !important; /* Darker header */
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #ccc !important;
        }

        .nav-link {
            color: #cbd5e0 !important;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .container {
            flex: 1; /* Allow container to grow */
            padding-top: 20px; /* Space for fixed navbar */
            padding-bottom: 20px;
        }

        .card {
            background-color: #2d3748; /* Card background */
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #4a5568; /* Card header background */
            border-bottom: none;
            color: #fff;
            font-weight: bold;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .alert {
            margin-top: 1rem;
        }

        .alert-info {
            background-color: #bee3f8;
            color: #2c5282;
            border-color: #90cdf4;
        }

        .chart-container {
            position: relative;
            height: 40vh; /* Responsive height */
            width: 100%;
        }

        .status-circle {
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-ok { background-color: #4CAF50; } /* Green */
        .status-danger { background-color: #f56565; } /* Red */
        .status-unknown { background-color: #ecc94b; } /* Yellow */

        .level-card {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px; /* Fixed height for consistent display */
            font-size: 2.2rem;
            font-weight: bold;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            border-radius: 0.5rem;
            background-color: #374151; /* Default background */
            transition: background-color 0.3s ease;
        }

        .level-card.status-ok {
            background-color: #4CAF50; /* Green */
        }
        .level-card.status-danger {
            background-color: #e53e3e; /* Red */
        }
        .level-card.status-unknown {
            background-color: #d69e2e; /* Yellow */
        }

        /* Adjustments for smaller screens */
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            .nav-link {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/perfil'); ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil'); ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion'); ?>">Configuración</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/logout'); ?>">Cerrar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container my-4">
        <?php if ($message): ?>
            <div class="alert alert-info text-center" role="alert">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Detalles del Dispositivo: <?= esc($nombreDispositivo) ?></h2>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i> Información General
                    </div>
                    <div class="card-body">
                        <p><strong>MAC:</strong> <?= esc($mac) ?></p>
                        <p><strong>Ubicación:</strong> <?= esc($ubicacionDispositivo) ?></p>
                        <p><strong>Última Lectura:</strong>
                            <?php if (!empty($lecturas)):
                                $ultimaLectura = end($lecturas);
                                echo esc($ultimaLectura['fecha_lectura']) . ' ' . esc($ultimaLectura['hora_lectura']);
                            else: ?>
                                Sin datos
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-gas-pump me-2"></i> Nivel de Gas Actual
                    </div>
                    <div class="card-body text-center">
                        <?php
                        $statusClass = 'status-unknown';
                        if (!empty($lecturas) && isset($lecturas[$ultimoIndice]['nivel_gas'])) {
                            $nivelGas = (float)$lecturas[$ultimoIndice]['nivel_gas'];
                            if ($nivelGas <= 500) {
                                $statusClass = 'status-ok';
                            } else {
                                $statusClass = 'status-danger';
                            }
                        }
                        ?>
                        <div class="level-card <?= $statusClass ?>">
                            <?= $nivelGasActualDisplay ?>
                        </div>
                        <?php if ($statusClass == 'status-ok'): ?>
                            <p class="text-success mt-2">Nivel de gas seguro.</p>
                        <?php elseif ($statusClass == 'status-danger'): ?>
                            <p class="text-danger mt-2">¡Nivel de gas peligroso! Se recomienda ventilar y revisar.</p>
                        <?php else: ?>
                            <p class="text-muted mt-2">Estado desconocido o sin datos.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i> Historial de Nivel de Gas (Últimas lecturas)
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="gasLevelChart"></canvas>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="<?= base_url('/perfil') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Volver a Mis Dispositivos</a>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const labels = <?= json_encode($labels); ?>;
    const data = <?= json_encode($data); ?>;
    const lecturas = <?= json_encode($lecturas); ?>; // Puedes usar esto para depuración o lógica adicional

    if (labels.length > 0 && data.length > 0) {
        const ctx = document.getElementById('gasLevelChart').getContext('2d');
        const gasLevelChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nivel de Gas (PPM)',
                    data: data,
                    borderColor: '#4CAF50', // Color de línea verde
                    backgroundColor: 'rgba(76, 175, 80, 0.2)', // Fondo de área suave
                    fill: true,
                    tension: 0.3, // Curva suave de la línea
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4CAF50',
                    pointHoverBackgroundColor: '#4CAF50',
                    pointHoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#cbd5e0' // Color para la leyenda
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
                        title: {
                            display: true,
                            text: 'Fecha y Hora',
                            color: '#e2e8f0' // Color para el título del eje
                        },
                        ticks: {
                            color: '#cbd5e0' // Color para las etiquetas del eje
                        },
                        grid: {
                             color: '#4a5568' // Color opcional para la cuadrícula si la muestras
                         }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nivel de Gas (PPM)',
                            color: '#e2e8f0' // Color para el título del eje
                        },
                        beginAtZero: true,
                        ticks: {
                            color: '#cbd5e0' // Color para las etiquetas del eje
                        },
                         grid: {
                             color: '#4a5568' // Color opcional para la cuadrícula si la muestras
                         }
                    }
                }
            }
        });
         console.log('Gráfico creado');
    } else {
        // Mostrar un mensaje si no hay datos para el gráfico
        const chartContainer = document.querySelector('.chart-container');
        if(chartContainer) {
            chartContainer.innerHTML = '<p class="text-center text-muted">No hay datos suficientes para mostrar el gráfico.</p>';
            chartContainer.style.height = '150px'; // Darle una altura al contenedor para que el mensaje se vea
            chartContainer.style.display = 'flex';
            chartContainer.style.alignItems = 'center';
            chartContainer.style.justifyContent = 'center';
        }
         console.log('No hay datos para crear el gráfico');
    }

    // console.log('Lecturas:', lecturas); // Descomenta para depurar
    // console.log('Labels:', labels);   // Descomenta para depurar
    // console.log('Data:', data);     // Descomenta para depurar
</script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Fallo el registro de ServiceWorker:', error);
                    });
            });
        }
    </script>

</body>
</html>
