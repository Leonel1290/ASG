<?php
/**
 * Vista profesional para el control de una válvula de seguridad de GAS.
 * * Requiere la variable $estado_valvula:
 * - 1: Válvula Cerrada (Seguro)
 * - 0: Válvula Abierta (Peligro/Normal en uso)
 * * Se asume el uso de un framework PHP (como CodeIgniter o Laravel) para base_url().
 */
 
 // Se asegura que la variable exista y establece un valor predeterminado seguro (abierta=0)
 $estado_valvula = $estado_valvula ?? 0;
 
 // Definición de variables para la presentación basadas en el estado
 // NOTA: En seguridad de gas, CERRADA es usualmente el estado SEGURO.
 $esta_cerrada = ($estado_valvula == 1);
 $estado_texto = $esta_cerrada ? 'CERRADA (Seguro)' : 'ABIERTA (Fluyendo)';
 $estado_clase = $esta_cerrada ? 'bg-success' : 'bg-danger'; // Verde para Cerrado/Seguro; Rojo para Abierto/Peligro
 $icono_estado = $esta_cerrada ? 'bi-shield-lock-fill' : 'bi-exclamation-triangle-fill';
 $porcentaje_medidor = $esta_cerrada ? 0 : 100; // 0% flujo (cerrado), 100% flujo (abierto)
 
 // Variables para los botones
 $accion_abrir_url = base_url('/servo/abrir'); 
 $accion_cerrar_url = base_url('/servo/cerrar');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Seguridad - Detector y Válvula de Gas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .control-panel { max-width: 800px; }
        .card-header-custom { font-size: 1.25rem; font-weight: 600; }
        .estado-badge {
            min-width: 180px; 
            padding: 0.75em 1.25em;
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem; /* Aumentado para mejor visibilidad */
        }

        /* Estilos del Medidor (Adaptado para Flujo de Gas) */
        .circular-progress-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .circular-progress-fill {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: var(--bs-warning); /* Amarillo/Naranja para Gas */
            transition: transform 1s ease-in-out;
            transform: rotate(calc(var(--fill-angle) * 1deg));
        }
        .circular-progress-circle {
            position: absolute;
            width: 130px;
            height: 130px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
        }
        .circular-progress-circle i {
            font-size: 2.5rem;
            color: var(--bs-warning); /* Icono del medidor en color Gas */
        }
        .circular-progress-circle span.label {
            top: 25px;
            color: #6c757d;
        }

    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="container control-panel">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning text-center card-header-custom">
            <i class="bi bi-gas-tank-fill me-2"></i> Panel de Seguridad de Gas - **VÁLVULA DE EMERGENCIA**
        </div>
        <div class="card-body p-4">
            
            <div class="row mb-4 align-items-center">
                <div class="col-md-5 text-center">
                    <p class="text-muted mb-1">Estado de Seguridad del Suministro:</p>
                    <div class="estado-display mb-3">
                        <span class="badge estado-badge <?php echo $estado_clase; ?> text-uppercase shadow-sm">
                            <i class="bi <?php echo $icono_estado; ?> me-2"></i>
                            <?php echo $estado_texto; ?>
                        </span>
                    </div>

                    
                    <div class="circular-progress-container">
                        <div class="circular-progress-fill" style="--fill-angle: <?php echo $porcentaje_medidor * 3.6; ?>deg;"></div>
                        <div class="circular-progress-circle">
                            <span class="label">Flujo de Gas</span>
                            <i class="bi bi-fire"></i> <span class="percentage"><?php echo $porcentaje_medidor; ?>%</span>
                        </div>
                    </div>
                    <small class="text-muted">Nivel de Flujo de Gas (0% Cerrado)</small>
                </div>

                <div class="col-md-7">
                    <h6 class="text-center text-secondary mb-3"><i class="bi bi-bell-fill me-2"></i>Historial de Alertas y Activación</h6>
                    <div class="bg-light p-3 border rounded">
                        <canvas id="activityChart"></canvas>
                        <small class="text-muted mt-2 d-block text-end">Lecturas de Nivel de Gas (PPM) - Últimas 24 horas</small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h6 class="text-center text-dark mb-3"><i class="bi bi-radioactive me-2"></i>Control Manual de la Válvula</h6>

            <div class="row g-3">
                <div class="col-md-6">
                    <form method="post" action="<?php echo htmlspecialchars($accion_abrir_url); ?>">
                        <button 
                            type="submit" 
                            class="btn btn-warning btn-lg w-100 text-dark"
                            <?php echo $esta_cerrada ? '' : 'disabled'; ?>
                            name="action" 
                            value="abrir"
                        >
                            <i class="bi bi-play-circle-fill me-2"></i>
                            Abrir Suministro
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="post" action="<?php echo htmlspecialchars($accion_cerrar_url); ?>">
                        <button 
                            type="submit" 
                            class="btn btn-danger btn-lg w-100"
                            <?php echo $esta_cerrada ? 'disabled' : ''; ?>
                            name="action" 
                            value="cerrar"
                        >
                            <i class="bi bi-stop-circle-fill me-2"></i>
                            CERRAR VÁLVULA **(Emergencia)**
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
        <div class="card-footer text-muted text-end bg-white">
            <small>Sistema de Monitoreo de Seguridad | Estado crítico: **NO** se detectó fuga.</small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Animación del Medidor Circular (CSS y JS)
        const progressBarFill = document.querySelector('.circular-progress-fill');
        const percentage = <?php echo $porcentaje_medidor; ?>; 
        progressBarFill.style.transition = 'none';
        progressBarFill.style.transform = `rotate(${percentage * 3.6}deg)`;
        void progressBarFill.offsetWidth;
        progressBarFill.style.transition = 'transform 1s ease-in-out';

        // 2. Gráfico de Actividad (Simulado con Chart.js - Representa PPM de Gas)
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['0h', '2h', '4h', '6h', '8h', '10h', '12h', '14h', '16h', '18h', '20h', '22h', '24h'],
                datasets: [{
                    label: 'Nivel de Gas (PPM)',
                    data: [15, 14, 16, 15, 17, 18, 17, 16, 18, 15, 14, 16, 15], // Datos simulados de PPM bajo
                    borderColor: 'rgb(255, 193, 7)', // Color warning de Bootstrap
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 50, // Escala máxima relevante para niveles de gas seguros/alerta
                        title: { display: true, text: 'Partes Por Millón (PPM)' }
                    }
                }
            }
        });
    });
</script>
</body>
</html>