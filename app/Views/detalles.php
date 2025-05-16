<?php
// Esta vista espera las variables $mac, $lecturas, $labels, $data, $message (opcional),
// y las nuevas variables $nombreDispositivo y $ubicacionDispositivo del controlador.

// Asegurarse de que las variables existan y tengan valores por defecto si son nulas
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Usar la MAC como nombre por defecto si no se pasa nombre
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$lecturas = $lecturas ?? [];
$labels = $labels ?? [];
$data = $data ?? [];
$message = $message ?? null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo: <?= esc($nombreDispositivo) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Añadimos un reset general para asegurar que no haya márgenes/paddings por defecto */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; /* Aseguramos que el margen sea 0 */
            padding: 0; /* Aseguramos que el padding sea 0 */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Eliminamos padding-top ya que la navbar no es fija por defecto (si la haces fija, descomenta y ajusta) */
            /* padding-top: 56px; */
        }

        .container {
            flex: 1;
            padding: 2rem;
        }

        /* Estilo para el botón Volver */
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; /* Añadir transición para hover */
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
            margin-bottom: 0.5rem; /* Reducido el margen inferior */
            text-align: center;
        }

        /* Estilo para la línea de detalles (MAC y Ubicación) */
        .device-details-line {
            text-align: center;
            color: #a0aec0; /* Color de texto secundario */
            font-size: 1rem;
            margin-bottom: 1.5rem; /* Espacio debajo de los detalles */
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
            overflow: hidden; /* Asegura que las esquinas redondeadas se vean */
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
            color: #2d3748; /* Asegura que el texto sea legible sobre el fondo */
        }

        .bg-warning {
            background-color: #f6e05e !important;
             color: #2d3748; /* Asegura que el texto sea legible sobre el fondo */
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
             /* Sombra neón y ancha - Puedes ajustar los colores y el desenfoque */
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

        .progress {
            height: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #4a5568;
        }

        .progress-bar {
            background-color: #48bb78; /* Color por defecto, se cambia con JS */
            transition: width 0.5s ease; /* Animación más suave */
            color: #fff;
            text-align: center;
            line-height: 1rem; /* Centrar texto verticalmente si lo hubiera */
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
            color: #667eea; /* Color por defecto, se puede cambiar con JS si quieres */
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
    <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
    </a>

    <h1 class="page-title"><i class="fas fa-microchip me-2"></i> Detalle del Dispositivo: <span class="text-primary"><?= esc($nombreDispositivo) ?></span></h1>
    <p class="device-details-line">
        MAC: <?= esc($mac) ?> | Ubicación: <?= esc($ubicacionDispositivo) ?>
    </p>
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
                        // Asegúrate de que 'nivel_gas' existe y es numérico
                        $nivel_gas = isset($lectura['nivel_gas']) ? (float) $lectura['nivel_gas'] : -1; // Usar -1 o similar para indicar dato inválido si no existe

                        if ($nivel_gas >= 500) {
                            $rowClass = 'table-danger'; // Usar clases de tabla de Bootstrap
                            $estado = 'Peligro';
                        } elseif ($nivel_gas >= 200) {
                            $rowClass = 'table-warning'; // Usar clases de tabla de Bootstrap
                            $estado = 'Precaución';
                        } elseif ($nivel_gas >= 0) { // Considerar 0 o más como seguro si no supera los umbrales
                            $rowClass = 'table-success'; // Usar clases de tabla de Bootstrap
                            $estado = 'Seguro';
                        } else {
                            // Caso de dato inválido o faltante
                            $rowClass = 'table-secondary'; // O alguna otra clase para indicar estado desconocido/error
                            $estado = 'Desconocido';
                        }
                    ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= esc($lectura['fecha'] ?? 'Fecha desconocida') ?></td>
                            <td><?= esc($lectura['nivel_gas'] ?? 'N/D') ?></td>
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
    // Asegúrate de que $lecturas, $labels y $data se pasen correctamente desde PHP
    // Usar ?? [] para manejar caso null/undefined si no hay datos
    const lecturas = <?= json_encode($lecturas ?? []) ?>;
    const labels = <?= json_encode(isset($labels) ? $labels : []) ?>; // Mantener orden original para JS/Chart.js
    const data = <?= json_encode(isset($data) ? $data : []) ?>; // Mantener orden original para JS/Chart.js


    const ultimoValor = lecturas.length > 0 && lecturas[lecturas.length - 1] && typeof lecturas[lecturas.length - 1]['nivel_gas'] !== 'undefined'
        ? (parseFloat(lecturas[lecturas.length - 1]['nivel_gas']) || 0) // Asegurar que sea número, default 0 si falla parse
        : null; // Usar null si no hay lecturas o el campo no existe


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
        const securityLevelSpan = document.getElementById('securityLevel'); // Referencia al span
        let width = 0;
        let levelText = 'Sin datos';
        let barClass = '';
         let textColorClass = ''; // Para cambiar color del texto de seguridad si quieres

        // Ajusta estos umbrales según la lógica de tu aplicación
        if (value >= 0 && value < 200) {
            width = Math.min(100, (value / 200) * 33); // Escala dentro del primer tercio
            levelText = 'Seguro';
            barClass = 'bg-success';
             textColorClass = 'text-success'; // O una clase personalizada
        } else if (value >= 200 && value < 500) {
            width = Math.min(100, 33 + ((value - 200) / 300) * 33); // Escala en el segundo tercio
            width = Math.min(100, width); // Asegurarse de no pasar del 100%
            levelText = 'Precaución';
            barClass = 'bg-warning';
             textColorClass = 'text-warning'; // O una clase personalizada
        } else if (value >= 500) {
            width = Math.min(100, 66 + ((value - 500) / 500) * 34); // Escala en el último tercio (ejemplo, ajusta el divisor si tienes un valor máximo esperado)
            width = Math.min(100, width); // Asegurarse de no pasar del 100%
            levelText = 'Peligro';
            barClass = 'bg-danger';
             textColorClass = 'text-danger'; // O una clase personalizada
        } else { // Valor negativo o no numérico inesperado manejado antes, pero como fallback
             width = 0;
             levelText = 'Inválido';
             barClass = '';
             textColorClass = 'text-muted';
        }


        progressBar.style.width = `${width}%`;
        progressBar.className = `progress-bar ${barClass}`; // Aplicar clase de color
        progressBar.setAttribute('aria-valuenow', width);
        securityLevelSpan.textContent = levelText;
        // Opcional: cambiar el color del texto del nivel de seguridad
        // securityLevelSpan.className = ''; // Limpiar clases anteriores
        // securityLevelSpan.classList.add(textColorClass); // Añadir clase de color
    }


    // Solo intentar crear el gráfico si hay datos de labels y data
    if (labels.length > 0 && data.length > 0) {
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
            type: 'line',
            data: {
                // Si los labels están en orden descendente (reciente a antiguo)
                // labels: labels.slice().reverse(), // Clona y revierte para Chart.js si es necesario
                // data: data.slice().reverse(),   // Clona y revierte para Chart.js si es necesario
                labels: labels, // Asumiendo que labels ya está en el orden correcto (antiguo a reciente)
                data: data, // Asumiendo que data ya está en el orden correcto
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
                maintainAspectRatio: false, // Permite controlar el tamaño con el div contenedor
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#f7fafc' // Color para el texto de la leyenda
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
                            color: '#e2e8f0' // Color para el título del eje
                        },
                        ticks: {
                            color: '#cbd5e0', // Color para las etiquetas del eje
                            display: false // Ocultar las etiquetas del eje X si hay muchas
                        },
                         // Si labels está en orden descendente (reciente a antiguo) para Chart.js, descomenta:
                         // reverse: true,
                        grid: {
                            display: false, // Ocultar las líneas de la cuadrícula del eje X
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
    // console.log('Labels:', labels);   // Descomenta para depurar
    // console.log('Data:', data);     // Descomenta para depurar
</script>

</body>
</html>
