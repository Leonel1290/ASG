<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASG - Demostración Interactiva</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-bg: #0A192F;
            --secondary-bg: #0D203B;
            --card-bg: #122137;
            --text-color: #AFB3B7;
            --accent-color: #557A95;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }

        body {
            background: linear-gradient(135deg, var(--primary-bg), var(--secondary-bg));
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            padding: 2rem;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }

        .btn-custom {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-light, .btn-warning {
            border: 2px solid;
        }

        .btn-warning {
            color: var(--primary-bg);
        }

        .btn-circle {
            width: 80px;
            height: 80px;
            padding: 0;
            border-radius: 50%;
            background-color: var(--success-color);
            color: #fff;
            border: none;
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-circle:hover {
            background-color: #218838;
            transform: scale(1.1);
        }

        .image-container {
            position: relative;
            width: 100%;
            height: 400px;
            margin: 20px auto;
            border-radius: 10px;
            overflow: hidden;
        }

        .image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            object-fit: cover;
        }

        .image.active {
            opacity: 1;
        }

        .alerta-btn {
            background-color: var(--secondary-bg);
            border: 1px solid var(--accent-color);
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
        }

        .alerta-btn:hover, .alerta-btn.active {
            background-color: var(--accent-color);
            transform: translateY(-5px);
            color: #fff;
        }
        
        .alerta-btn.active span {
            font-weight: bold;
        }

        .alerta-btn input[type="radio"] {
            display: none;
        }

        .modal-content {
            background-color: var(--primary-bg);
            color: var(--text-color);
            border: none;
        }

        .modal-header, .modal-footer {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .list-group-item {
            font-family: 'Roboto', sans-serif;
            background-color: transparent !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Estilo para el botón de simulación cuando está en pausa/listo */
        #animationButton i.fa-check {
            transition: transform 0.5s ease;
        }
        #animationButton.simulating i.fa-check {
            transform: scale(0); /* Oculta el check */
        }
        #animationButton:not(.simulating) i.fa-bolt {
            transform: scale(0); /* Oculta el rayo */
        }

        /* Se invierte el color del ícono si el fondo es oscuro */
        .btn-close {
            filter: invert(1);
        }

    </style>
</head>
<body class="text-center">

    <div class="container">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="display-6 fw-bold">ASG - Demostración Interactiva</h1>
            <a href="javascript:history.back()" class="btn btn-outline-light btn-custom">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </header>

        <section class="mb-5">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100 p-4">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase fw-bold text-white mb-3">Estado del Sistema</h5>
                            <p id="status-display" class="card-text fs-4 fw-bold mt-4">
                                <span class="text-success"><i class="fas fa-check-circle me-2"></i> Normal</span>
                            </p>
                            <hr class="border-secondary my-4">
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning btn-custom" data-bs-toggle="modal" data-bs-target="#simulacionModal">
                                    <i class="fas fa-bolt me-2"></i> Iniciar Simulación
                                </button>
                                <button class="btn btn-outline-light btn-custom" data-bs-toggle="modal" data-bs-target="#alertaModal">
                                    <i class="fas fa-cog me-2"></i> Configurar Alerta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100 p-4">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase fw-bold text-white mb-3">Registro de Actividad</h5>
                            <ul id="activity-log" class="list-group list-group-flush text-start">
                                </ul>
                            <div class="text-center mt-3">
                                <small class="text-white-50">El registro se actualiza en tiempo real durante la simulación.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <audio id="alerta1Audio" src="<?= base_url('/audio/alarma.mp3') ?>" preload="auto"></audio>
    <audio id="alerta2Audio" src="<?= base_url('/audio/alarma1.mp3') ?>" preload="auto"></audio>
    <audio id="alerta3Audio" src="<?= base_url('/audio/alarma2.mp3') ?>" preload="auto"></audio>

    <div class="modal fade" id="alertaModal" tabindex="-1" aria-labelledby="alertaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertaModalLabel">Seleccionar Tono de Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-white-50 mb-4">Elige el tono que prefieres para la alerta de gas. Podrás probarlo antes de guardar.</p>
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="alerta1">
                                <div class="btn alerta-btn w-100 py-4 rounded">
                                    <i class="fas fa-bullhorn fa-2x mb-2 d-block"></i>
                                    <span>Sirena</span>
                                    <input class="form-check-input" type="radio" name="alertaSeleccionada" id="alerta1" value="alerta1Audio" checked>
                                </div>
                            </label>
                        </div>
                        <div class="col-4">
                            <label for="alerta2">
                                <div class="btn alerta-btn w-100 py-4 rounded">
                                    <i class="fas fa-bell fa-2x mb-2 d-block"></i>
                                    <span>Timbre</span>
                                    <input class="form-check-input" type="radio" name="alertaSeleccionada" id="alerta2" value="alerta2Audio">
                                </div>
                            </label>
                        </div>
                        <div class="col-4">
                            <label for="alerta3">
                                <div class="btn alerta-btn w-100 py-4 rounded">
                                    <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                                    <span>Emergencia</span>
                                    <input class="form-check-input" type="radio" name="alertaSeleccionada" id="alerta3" value="alerta3Audio">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="probarAlertaBtn">
                        <i class="fas fa-play me-2"></i> Probar Tono
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="simulacionModal" tabindex="-1" aria-labelledby="simulacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="simulacionModalLabel">Simulación de Fuga de Gas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center d-flex flex-column justify-content-between align-items-center">
                    <p class="text-white-50 mb-4" id="sim-text">Haz clic en el botón para iniciar la simulación de una fuga de gas y observar cómo reacciona el dispositivo.</p>
                    <div class="image-container shadow-lg">
                        <img id="frame-1" class="image active" src="<?= base_url('/imagenes/frame_1.jpg'); ?>" alt="Garrafa en estado normal">
                        <img id="frame-2" class="image" src="<?= base_url('/imagenes/frame_2.jpg'); ?>" alt="Fuga de gas detectada">
                        <img id="frame-3" class="image" src="<?= base_url('/imagenes/frame_3.jpg'); ?>" alt="Dispositivo de alarma activo">
                    </div>
                    <button id="animationButton" class="btn btn-circle mt-4">
                        <i class="fas fa-bolt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Funciones auxiliares para el registro y el estado
        function logActivity(message, type = 'info') {
            const logList = document.getElementById('activity-log');
            const now = new Date();
            const timeString = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
            const li = document.createElement('li');

            let iconClass = 'fas fa-info-circle';
            let textColor = 'text-white-50';

            if (type === 'warning') {
                iconClass = 'fas fa-exclamation-triangle text-warning';
                textColor = 'text-warning';
            } else if (type === 'danger') {
                iconClass = 'fas fa-bell text-danger';
                textColor = 'text-danger';
            }

            li.className = `list-group-item bg-transparent ${textColor} border-secondary d-flex justify-content-between align-items-center`;
            li.innerHTML = `<span><i class="${iconClass} me-2"></i>${message}</span><small class="text-white-50">${timeString}</small>`;
            logList.prepend(li);
        }

        function setStatus(status, text) {
            const statusDisplay = document.getElementById('status-display');
            let iconClass = 'fas fa-check-circle';
            let textColor = 'text-success';

            if (status === 'warning') {
                iconClass = 'fas fa-exclamation-triangle';
                textColor = 'text-warning';
            } else if (status === 'danger') {
                iconClass = 'fas fa-bell';
                textColor = 'text-danger';
            }

            statusDisplay.innerHTML = `<span class="${textColor}"><i class="${iconClass} me-2"></i> ${text}</span>`;
        }
        
        // Función principal para controlar la simulación
        function runSimulation() {
            const animationButton = document.getElementById('animationButton');
            const simText = document.getElementById('sim-text');
            const frame1 = document.getElementById('frame-1');
            const frame2 = document.getElementById('frame-2');
            const frame3 = document.getElementById('frame-3');
            let currentAudio = null;
            let timeout1, timeout2;

            // Al hacer clic en el botón de simulación
            animationButton.addEventListener('click', () => {
                if (animationButton.classList.contains('simulating')) {
                    // Si ya está simulando, no hacer nada
                    return;
                }
                
                animationButton.classList.add('simulating');
                animationButton.innerHTML = `<i class="fas fa-spinner fa-spin"></i>`;
                logActivity('Simulación iniciada: preparando...', 'info');
                simText.textContent = 'Simulando un aumento de la concentración de gas...';

                // Paso 1: Fuga de gas (activación del sensor)
                timeout1 = setTimeout(() => {
                    frame1.classList.remove('active');
                    frame2.classList.add('active');
                    setStatus('warning', 'Fuga de Gas Detectada');
                    logActivity('¡Fuga de gas detectada!', 'warning');
                    simText.textContent = 'El sensor ha detectado una fuga. El sistema se prepara para la alerta.';
                }, 2000);

                // Paso 2: Alerta activada
                timeout2 = setTimeout(() => {
                    frame2.classList.remove('active');
                    frame3.classList.add('active');
                    
                    const selectedAlerta = document.querySelector('input[name="alertaSeleccionada"]:checked');
                    if (selectedAlerta) {
                        currentAudio = document.getElementById(selectedAlerta.value);
                        currentAudio.loop = true;
                        currentAudio.play();
                    }
                    
                    setStatus('danger', 'Alerta en Curso');
                    logActivity('Alerta sonora activada. ¡Acción requerida!', 'danger');
                    simText.textContent = '¡Alerta máxima! El dispositivo está sonando para avisar del peligro.';

                    // Cambiar el icono del botón para detener la simulación
                    animationButton.innerHTML = `<i class="fas fa-stop"></i>`;

                }, 4000);
            });

            // Función para detener y resetear la simulación
            function stopSimulation() {
                clearTimeout(timeout1);
                clearTimeout(timeout2);
                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio.currentTime = 0;
                }
                
                frame1.classList.add('active');
                frame2.classList.remove('active');
                frame3.classList.remove('active');
                setStatus('success', 'Normal');
                animationButton.classList.remove('simulating');
                animationButton.innerHTML = `<i class="fas fa-bolt"></i>`;
                simText.textContent = 'Haz clic en el botón para iniciar la simulación de una fuga de gas.';
                logActivity('Simulación finalizada', 'info');
            }

            // Detectar el cierre del modal para resetear
            const simulacionModal = document.getElementById('simulacionModal');
            simulacionModal.addEventListener('hidden.bs.modal', stopSimulation);
        }

        // Inicializar la interacción del usuario
        document.addEventListener('DOMContentLoaded', () => {
            // Manejar la selección de alertas
            document.querySelectorAll('.alerta-btn').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('.alerta-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    button.querySelector('input[type="radio"]').checked = true;
                });
            });

            const probarAlertaBtn = document.getElementById('probarAlertaBtn');
            probarAlertaBtn.addEventListener('click', () => {
                const selectedAlerta = document.querySelector('input[name="alertaSeleccionada"]:checked');
                if (selectedAlerta) {
                    const audio = document.getElementById(selectedAlerta.value);
                    audio.play();
                    logActivity('Alerta probada con éxito.', 'info');
                }
            });

            // Iniciar la lógica de la simulación
            runSimulation();
        });
    </script>

</body>
</html>