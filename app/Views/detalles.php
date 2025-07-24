<?php
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[0]['nivel_gas']) ? htmlspecialchars($lecturas[0]['nivel_gas'], ENT_QUOTES, 'UTF-8') . ' PPM' : 'Sin datos';
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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-gauge@0.4.2/dist/chartjs-gauge.min.js"></script>
    <style>
        body {
            background-color: #2d3e50;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .container {
            padding: 2rem;
        }
        .gauge-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
        }
        .card {
            background-color: #34495e;
            color: #fff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .card-title {
            font-size: 2rem;
            font-weight: bold;
            color: #f6e05e;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .current-gas-value {
            font-size: 2rem;
            font-weight: bold;
            color: #f6e05e;
            margin-top: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
    </a>
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h2 class="card-title"><i class="fas fa-gas-pump"></i> Nivel de Gas Actual</h2>
            <div class="gauge-container">
                <canvas id="gaugeChart" width="350" height="350"></canvas>
            </div>
            <div class="current-gas-value" id="currentGasLevelDisplay">
                <?= $nivelGasActualDisplay ?>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener el último valor de gas (o 0 si no hay datos)
        const ultimoValor = <?= isset($data) && count($data) > 0 ? floatval(end($data)) : 0 ?>;
        const gaugeCtx = document.getElementById('gaugeChart').getContext('2d');
        const gaugeChart = new Chart(gaugeCtx, {
            type: 'gauge',
            data: {
                datasets: [{
                    value: ultimoValor,
                    minValue: 0,
                    data: [200, 500, 1000], // Cambia los cortes según tus rangos
                    backgroundColor: [
                        'rgba(72, 187, 120, 0.8)', // Seguro
                        'rgba(246, 224, 94, 0.8)', // Precaución
                        'rgba(229, 62, 62, 0.8)'   // Peligro
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: false,
                needle: {
                    radiusPercentage: 2,
                    widthPercentage: 3.2,
                    lengthPercentage: 80,
                    color: 'rgba(45,55,72,1)'
                },
                valueLabel: {
                    display: true,
                    formatter: (value) => value + ' PPM',
                    color: '#fff',
                    font: {
                        size: 24,
                        weight: 'bold'
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
</body>
</html>