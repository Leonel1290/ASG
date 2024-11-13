<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dispositivo y Lecturas de Gas</title>
    <style>
        body {
            background-color: #1E3D59;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #162B4E;
        }

        .navbar-brand, .nav-link {
            color: #fff !important;
        }

        .card {
            background-color: #FFFFFF;
            color: #333;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-bottom: 1rem;
        }

        .progress {
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background-color: #28a745;
            transition: width 0.6s ease;
        }

        .animate {
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ASG Cliente</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('logout') ?>">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <h1> Bienvenido <?= session()->get('nombre'); ?></h1>

    <div class="container my-5 animate">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Detalles del Dispositivo</h5>
                        <p><strong>Nombre del Dispositivo:</strong> Detector de Gas Inteligente</p>
                        <p><strong>Número de Serie:</strong> 123456789</p>
                        <p><strong>Estado:</strong> Activo</p>
                        <p><strong>Última Conexión:</strong> 02/10/2024</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Lecturas de Gas</h5>
                        <canvas id="gasChart" width="400" height="200"></canvas>

                        <div class="mt-4">
                            <p>Nivel de Seguridad: <span id="securityLevel">Seguro</span></p>
                            <div class="progress">
                                <div class="progress-bar" id="progressBar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- Nivel de Gas Actual -->
                        <div class="card text-center mt-4">
                            <div class="card-body">
                                <h5 class="card-title">Nivel de Gas Actual</h5>
                                <?php if (isset($nivel_gas) && $nivel_gas !== null): ?>
                                    <p id="nivelGas"><?= $nivel_gas ?> PPM</p>
                                <?php else: ?>
                                    <p id="nivelGas">No tienes registros de lecturas</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        const nivelGas = <?= isset($nivel_gas) ? $nivel_gas : 'null' ?>;

        if (nivelGas !== null) {
            // Actualizar gráfico y barra de progreso
            const ctx = document.getElementById('gasChart').getContext('2d');
            const gasChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['10:00', '10:05', '10:10', '10:15', '10:20'],
                    datasets: [{
                        label: 'Nivel de Gas (PPM)',
                        data: [nivelGas, nivelGas, nivelGas, nivelGas, nivelGas],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Actualizar barra de progreso con el nivel de gas
            updateProgressBar(nivelGas);
        }

        function updateProgressBar(value) {
            let progressBar = document.getElementById('progressBar');
            let securityLevel = document.getElementById('securityLevel');

            if (value <= 50) {
                progressBar.style.width = '100%';
                progressBar.classList.remove('bg-warning', 'bg-danger');
                progressBar.classList.add('bg-success');
                securityLevel.textContent = 'Seguro';
            } else if (value > 50 && value <= 75) {
                progressBar.style.width = '75%';
                progressBar.classList.remove('bg-success', 'bg-danger');
                progressBar.classList.add('bg-warning');
                securityLevel.textContent = 'Precaución';
            } else {
                progressBar.style.width = '50%';
                progressBar.classList.remove('bg-success', 'bg-warning');
                progressBar.classList.add('bg-danger');
                securityLevel.textContent = 'Peligro';
            }
        }
    </script>

</body>
</html>
