<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASG - Panel de Pruebas</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #0A192F, #0D203B);
            font-family: 'Poppins', sans-serif;
            color: #AFB3B7;
            padding: 2rem;
        }

        .btn-circle {
            width: 60px;
            height: 60px;
            padding: 0;
            border-radius: 50%;
            background-color: #28a745;
            color: #fff;
            border: none;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .btn-circle:hover {
            background-color: #218838;
        }

        .image-container {
            position: relative;
            width: 700px;
            height: 500px;
            margin: 20px auto;
        }

        .image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .image.active {
            opacity: 1;
        }

        /* Estilo para los botones de selección de alerta */
        .alerta-btn {
            background-color: #122137;
            border: 1px solid #334255;
            color: #AFB3B7;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .alerta-btn:hover, .alerta-btn.active {
            background-color: #213247;
            border-color: #557A95;
            transform: translateY(-5px);
            color: #fff;
        }
        .alerta-btn input[type="radio"] {
            display: none;
        }
    </style>
</head>
<body class="text-center">

    <h1 class="mb-4">Panel de Pruebas</h1>

    <div class="d-flex justify-content-center flex-wrap gap-2">
        <button class="btn btn-outline-light" onclick="history.back()">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </button>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#simulacionModal">⚡ Simular Actuación</button>
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#alertaModal"> Configurar Alerta</button>
    </div>

    <div class="container mt-5">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card bg-dark text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Estado del Sistema</h5>
                        <p id="status-display" class="card-text fs-4 mt-3">
                            <span class="text-success"><i class="fas fa-check-circle"></i> Normal</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-dark text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Registro de Actividad</h5>
                        <ul id="activity-log" class="list-group list-group-flush text-start">
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <audio id="alerta1Audio" src="<?= base_url('/audio/alarma.mp3') ?>" preload="auto"></audio>
    <audio id="alerta2Audio" src="<?= base_url('/audio/alarma1.mp3') ?>" preload="auto"></audio>
    <audio id="alerta3Audio" src="<?= base_url('/audio/alarma2.mp3') ?>" preload="auto"></audio>

    <div class="modal fade" id="alertaModal" tabindex="-1" aria-labelledby="alertaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color:#0A192F; color:#AFB3B7;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" id="alertaModalLabel">Seleccionar Tono de Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="alerta1">
                                <div class="btn alerta-btn w-100 py-3 rounded">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                    <span>Sirena</span>
                                    <input class="form-check-input" type="radio" name="alertaSeleccionada" id="alerta1" value="alerta1Audio" checked>
                                </div>
                            </label>
                        </div>
                        <div class="col-4">
                            <label for="alerta2">
                                <div class="btn alerta-btn w-100 py-3 rounded">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                    <span>Timbre</span>
                                    <input class="form-check-input" type="radio" name="alertaSeleccionada" id="alerta2" value="alerta2Audio">
                                </div>
                            </label>
                        </div>
                        <div class="col-4">
                            <label for="alerta3">
                                <div class="btn alerta-btn w-100 py-3 rounded">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                    <span>Emergencia</span>
                                    <input class="form-check-input" type="radio" name="alertaSeleccionada" id="alerta3" value="alerta3Audio">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #333;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="probarAlertaBtn" data-bs-dismiss="modal">
                        <i class="fas fa-play me-2"></i> Probar Alerta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="simulacionModal" tabindex="-1" aria-labelledby="simulacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color:#000; color:#fff;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" id="simulacionModalLabel">Simulación de Fuga de Gas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                </div>
                <div class="modal-body text-center d-flex flex-column justify-content-between align-items-center">
                    <div class="image-container">
                        <img id="frame-1" class="image active" src="<?= base_url('/imagenes/frame_1.jpg'); ?>" alt="Garrafa sin fuga">
                        <img id="frame-2" class="image" src="<?= base_url('/imagenes/frame_2.jpg'); ?>" alt="Fuga de gas">
                        <img id="frame-3" class="image" src="<?= base_url('/imagenes/frame_3.jpg'); ?>" alt="Detector activo">
                    </div>
                    <button id="animationButton" class="btn btn-circle mt-3">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Funciones para el registro y estado
        function logActivity(message) {
            const logList = document.getElementById('activity-log');
            const now = new Date();
            const timeString = `${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
            const li = document.createElement('li');
            li.className = 'list-group-item bg-transparent text-white-50 border-secondary d-flex justify-content-between align-items-center';
            li.innerHTML = `<span>${message}</span><small class="text-white-50">${timeString}</small>`;
            logList.prepend(li);
        }

        function setStatus(status, text) {
            const statusDisplay = document.getElementById('status-display');
            statusDisplay.innerHTML = `<span class="text-${status}"><i class="fas fa-${status === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${text}</span>`;
        }

        // Selección de tarjeta para la alerta
        document.querySelectorAll('.alerta-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.alerta-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                button.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Inicializar la selección de la alerta
        document.addEventListener('DOMContentLoaded', () => {
            const selectedAlerta = document.querySelector('input[name="alertaSeleccionada"]:checked');
            if (selectedAlerta) {
                document.querySelector(`label[for="${selectedAlerta.id}"] .alerta-btn`).classList.add('active');
            }
        });

        // Escuchar el clic en el nuevo botón de probar alerta
        const probarAlertaBtn = document.getElementById('probarAlertaBtn');
        probarAlertaBtn.addEventListener('click', () => {
            const selectedAlerta = document.querySelector('input[name="alertaSeleccionada"]:checked');
            if (selectedAlerta) {
                const audioId = selectedAlerta.value;
                const audio = document.getElementById(audioId);
                audio.play();
                logActivity('Alerta probada: ' + selectedAlerta.id);
            }
        });

        // Animación del modal de simulación
        const simulacionModal = document.getElementById('simulacionModal');
        const animationButton = document.getElementById('animationButton');
        const frame1 = document.getElementById('frame-1');
        const frame2 = document.getElementById('frame-2');
        const frame3 = document.getElementById('frame-3');

        let isAnimating = false;
        let animationTimeout;
        let currentAudio;

        simulacionModal.addEventListener('hidden.bs.modal', resetAnimation);
        simulacionModal.addEventListener('show.bs.modal', () => {
            logActivity('Simulación iniciada');
        });

        function resetAnimation() {
            isAnimating = false;
            clearTimeout(animationTimeout);
            frame1.classList.add('active');
            frame2.classList.remove('active');
            frame3.classList.remove('active');
            if (currentAudio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
            }
            setStatus('success', 'Normal');
        }

        animationButton.addEventListener('mousedown', () => {
            if (isAnimating) return;
            isAnimating = true;

            const selectedAlerta = document.querySelector('input[name="alertaSeleccionada"]:checked');
            if (selectedAlerta) {
                const audioId = selectedAlerta.value;
                currentAudio = document.getElementById(audioId);
                
                frame1.classList.remove('active');
                frame2.classList.add('active');
                setStatus('warning', 'Fuga de Gas Detectada');

                currentAudio.loop = true;
                currentAudio.play();
                logActivity('Alerta de fuga de gas activada');

                animationTimeout = setTimeout(() => {
                    frame2.classList.remove('active');
                    frame3.classList.add('active');
                    currentAudio.pause();
                    currentAudio.currentTime = 0;
                    setStatus('danger', 'Alerta en curso');
                }, 2000);
            }
        });

        animationButton.addEventListener('mouseup', resetAnimation);
    </script>

</body>
</html>