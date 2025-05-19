<?php
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Usar la MAC como nombre por defecto si no se pasa nombre
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$lecturas = $lecturas ?? [];
$labels = $labels ?? [];
$data = $data ?? [];
$message = $message ?? null;

// Obtener el último nivel de gas para mostrarlo en la tarjeta simple,
// tal como se hacía en el primer código (usando el primer elemento si está ordenado descendente)
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? esc($lecturas[0]['nivel_gas']) . ' PPM' : 'Sin datos';

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

        /* Clases de Bootstrap para colores de fondo de badges/spans */
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

         /* Estilos específicos para la tarjeta de Nivel de Gas Actual (del primer código) */
         .current-gas-level-card-simple {
            background-color: #2d3748; /* Usar el color de la tarjeta general */
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 2rem;
            transition: box-shadow 0.3s ease;
            padding: 1.5rem; /* Igual que card-body */
            text-align: center; /* Centrar contenido */
         }

         .current-gas-level-card-simple:hover {
              box-shadow: 0 0 20px rgba(102, 126, 234, 0.6), 0 0 40px rgba(102, 126, 234, 0.4), 0 0 60px rgba(102, 126, 234, 0.2);
         }

         .current-gas-level-card-simple .card-title {
             font-size: 1.75rem; /* Igual que card-title general */
             font-weight: bold;
             color: #667eea;
             margin-bottom: 1rem;
         }

         .current-gas-level-card-simple .current-gas-level-value {
             font-size: 2rem;
             font-weight: bold;
             color: #f6e05e; /* Color amarillo/oro */
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
                                        $estado = 'Seguro'; // O 'Normal' como en el código 1
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
                <p class="security-level-text"><i class="fas fa-shield-alt me-2"></i> Nivel de Seguridad (JS): <span id="securityLevel">Sin datos</span></p>
                <div class="progress">
                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="current-gas-level-card mt-4">
                <h5 class="current-gas-level-title"><i class="fas fa-tachometer-alt me-2"></i> Última Lectura (JS)</h5>
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

    // Encontrar el último valor válido de nivel_gas en el array lecturas,
    // asumiendo que el array "lecturas" para el JS y el gráfico
    // está ordenado de más antiguo a más reciente, y el último elemento es el más reciente.
    // Si tu array $lecturas en PHP (usado para la tabla) está ordenado de MÁS RECIENTE a MÁS ANTIGUO,
    // y quieres que el JS use el dato más reciente, entonces deberías acceder al primer elemento:
    // const ultimoValor = lecturas.length > 0 && typeof lecturas[0]['nivel_gas'] !== 'undefined' ? (parseFloat(lecturas[0]['nivel_gas']) || 0) : null;
    // Mantengo la lógica original del JS del segundo código, que usa el último elemento del array.
    const ultimoValor = lecturas.length > 0 && typeof lecturas[lecturas.length - 1]['nivel_gas'] !== 'undefined'
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
        // let textColorClass = ''; // Para cambiar color del texto de seguridad si quieres

        // Ajusta estos umbrales según la lógica de tu aplicación
        // Asegúrate de que el valor no sea negativo si vino un dato inesperado
        const safeValue = Math.max(0, value); // Usar 0 si el valor es negativo

        if (safeValue >= 0 && safeValue < 200) {
             // Escala: 0-199 PPM -> 0-33% de la barra
             width = Math.min(100, (safeValue / 200) * 33);
             levelText = 'Seguro';
             barClass = 'bg-success';
             // textColorClass = 'text-success';
         } else if (safeValue >= 200 && safeValue < 500) {
             // Escala: 200-499 PPM -> 33%-66% de la barra (rango de 300 PPM)
             width = Math.min(100, 33 + ((safeValue - 200) / 300) * 33);
             width = Math.min(100, width); // Asegurarse de no pasar del 100%
             levelText = 'Precaución';
             barClass = 'bg-warning';
             // textColorClass = 'text-warning';
         } else if (safeValue >= 500) {
             // Escala: 500+ PPM -> 66%-100% de la barra
             // Aquí puedes ajustar el divisor (500) si tienes un valor máximo razonable
             // Por ejemplo, si el máximo esperado es 1000 PPM: ((safeValue - 500) / 500) * 34
             // Si no hay máximo definido, la barra simplemente llegará al 100% para cualquier valor >= 500
             width = Math.min(100, 66 + ((safeValue - 500) / 500) * 34); // Ejemplo con divisor 500
             width = Math.min(100, width); // Asegurarse de no pasar del 100%
             levelText = 'Peligro';
             barClass = 'bg-danger';
             // textColorClass = 'text-danger';
         } else { // Caso de valor inválido o no numérico manejado por el Math.max(0, value)
             width = 0;
             levelText = 'Inválido';
             barClass = '';
             // textColorClass = 'text-muted';
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
                // Si los arrays labels y data están ordenados de más antiguo a más reciente, úsalos directamente:
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
                // Si tus arrays labels y data están ordenados de MÁS RECIENTE a MÁS ANTIGUO (como podría sugerir la tabla),
                // deberías revertirlos para el gráfico:
                // labels: labels.slice().reverse(), // Clona y revierte
                // datasets: [{
                //     label: 'Nivel de Gas (PPM)',
                //     data: data.slice().reverse(), // Clona y revierte
                //     borderColor: 'rgba(54, 162, 235, 1)',
                //     backgroundColor: 'rgba(54, 162, 235, 0.2)',
                //     borderWidth: 2,
                //     fill: true,
                //     tension: 0.3
                // }]
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
    // console.log('Labels:', labels);   // Descomenta para depurar
    // console.log('Data:', data);     // Descomenta para depurar
</script>

</body>
</html>