<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ASG - Simulador de Fugas de Gas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-background: #0B1A1E;
            --dark-secondary: #10262B;
            --dark-card-background: #1A2F34;
            --primary-highlight: #00A3A3;
            --primary-highlight-darker: #008D8D;
            --text-light: #C9D6DF;
            --text-lighter: #F0F4F8;
            --navbar-bg-opacity: rgba(11, 26, 30, 0.9);
            --danger-alert: #ff4444;
            --danger-alert-darker: #cc0000;
            --warning-alert: #ffbb33;
            --success-alert: #00C851;
        }

        body {
            background: linear-gradient(135deg, var(--dark-background), var(--dark-secondary));
            font-family: 'Poppins', sans-serif;
            color: var(--text-light);
            margin: 0;
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: var(--navbar-bg-opacity);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand, .nav-link {
            color: var(--text-lighter);
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: var(--primary-highlight);
        }

        .container-main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background-color: var(--dark-card-background);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--dark-background);
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .card-body {
            padding: 2rem;
        }

        h1, h2, h3, h4, h5 {
            color: var(--text-lighter);
            font-weight: 700;
        }

        .btn-primary {
            background-color: var(--primary-highlight);
            border: none;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-highlight-darker);
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger-alert);
            border: none;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: var(--danger-alert-darker);
            transform: translateY(-2px);
        }

        .simulation-panel {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .sensor-status {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background: var(--dark-background);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sensor-icon {
            font-size: 2.5rem;
            margin-right: 1.5rem;
            width: 60px;
            text-align: center;
        }

        .sensor-info {
            flex: 1;
        }

        .sensor-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .progress {
            height: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .progress-bar {
            transition: width 0.5s ease;
        }

        .countdown {
            text-align: center;
            padding: 2rem;
            background: var(--dark-background);
            border-radius: 10px;
            margin: 1rem 0;
        }

        .countdown-text {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .countdown-timer {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-highlight);
        }

        .test-results {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--dark-background);
            border-radius: 10px;
            border-left: 4px solid var(--primary-highlight);
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-active {
            background-color: var(--success-alert);
            box-shadow: 0 0 8px var(--success-alert);
        }

        .status-inactive {
            background-color: #888;
        }

        .log-entry {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-family: monospace;
            font-size: 0.9rem;
        }

        .log-time {
            color: var(--primary-highlight);
            margin-right: 10px;
        }

        .log-message {
            color: var(--text-light);
        }

        .permission-status {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: center;
            font-weight: 600;
        }

        .permission-granted {
            background-color: rgba(0, 200, 81, 0.1);
            border: 1px solid var(--success-alert);
            color: var(--success-alert);
        }

        .permission-denied {
            background-color: rgba(255, 68, 68, 0.1);
            border: 1px solid var(--danger-alert);
            color: var(--danger-alert);
        }

        .permission-default {
            background-color: rgba(255, 187, 51, 0.1);
            border: 1px solid var(--warning-alert);
            color: var(--warning-alert);
        }

        .notification-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--danger-alert);
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.4);
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .notification-badge:hover {
            transform: scale(1.1);
        }

        .audio-controls {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--dark-background);
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .sensor-status {
                flex-direction: column;
                text-align: center;
            }
            
            .sensor-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gas-pump"></i> ASG - Simulador de Fugas de Gas
            </a>
        </div>
    </nav>

    <div class="container-main">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-flask"></i> Simulación de Detección de Gas</h2>
                <p class="mb-0">El sistema simulará una fuga de gas después de 1 minuto y enviará notificaciones automáticas</p>
            </div>
            <div class="card-body">
                <div class="permission-status" id="permissionStatus">
                    Comprobando estado de permisos...
                </div>

                <button id="requestPermission" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Activar Notificaciones
                </button>

                <div class="countdown">
                    <div class="countdown-text">La simulación comenzará en:</div>
                    <div class="countdown-timer" id="countdown">01:00</div>
                </div>

                <div class="simulation-panel">
                    <div class="sensor-status">
                        <div class="sensor-icon text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="sensor-info">
                            <h4>Sensor de Gas - Cocina</h4>
                            <div class="sensor-value" id="gasValue">0 ppm</div>
                            <div class="progress">
                                <div class="progress-bar bg-success" id="gasProgress" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div>Estado: <span id="sensorStatus" class="text-success">Normal</span></div>
                        </div>
                    </div>
                </div>

                <div class="audio-controls">
                    <h4><i class="fas fa-volume-up"></i> Control de Audio</h4>
                    <p>Para que las alertas de sonido funcionen correctamente, interactúa con esta página:</p>
                    <button id="testAudio" class="btn btn-primary">
                        <i class="fas fa-play"></i> Probar Sonido de Alerta
                    </button>
                    <div id="audioStatus" class="mt-2"></div>
                </div>

                <div class="test-results">
                    <h4><i class="fas fa-clipboard-list"></i> Registro del Sistema</h4>
                    <div class="log-container" id="systemLog" style="max-height: 300px; overflow-y: auto; margin-top: 1rem;">
                        <div class="log-entry">
                            <span class="log-time" id="current-time"></span>
                            <span class="log-message">Sistema de monitoreo inicializado. Esperando inicio de simulación.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Información del Sistema</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Niveles de Gas</h4>
                        <ul>
                            <li><span class="text-success">0-100 ppm:</span> Nivel normal</li>
                            <li><span class="text-warning">100-300 ppm:</span> Fuga leve detectada</li>
                            <li><span class="text-warning">300-600 ppm:</span> Fuga moderada</li>
                            <li><span class="text-danger">600+ ppm:</span> Fuga crítica - ¡Peligro!</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Acciones Automáticas</h4>
                        <ul>
                            <li>Notificación en fuga leve</li>
                            <li>Alerta sonora en fuga moderada</li>
                            <li>Notificación crítica y simulación de cierre de válvula en fugas críticas</li>
                            <li>Registro de todos los eventos en el sistema</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="notification-badge d-none" id="notificationBadge">
        <i class="fas fa-bell"></i>
    </div>

    <!-- Audio de alerta: usando un archivo online para evitar problemas de ruta -->
    <audio id="alerta-audio" preload="auto">
        <source src="https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3" type="audio/mpeg">
        Tu navegador no soporta el elemento de audio.
    </audio>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const permissionStatus = document.getElementById('permissionStatus');
            const requestPermissionBtn = document.getElementById('requestPermission');
            const systemLog = document.getElementById('systemLog');
            const countdownElement = document.getElementById('countdown');
            const gasValueElement = document.getElementById('gasValue');
            const gasProgressElement = document.getElementById('gasProgress');
            const sensorStatusElement = document.getElementById('sensorStatus');
            const notificationBadge = document.getElementById('notificationBadge');
            const audioEl = document.getElementById('alerta-audio');
            const testAudioBtn = document.getElementById('testAudio');
            const audioStatus = document.getElementById('audioStatus');

            let simulationTimer;
            let countdownTimer;
            let secondsLeft = 60; // 1 minuto para comenzar la simulación
            let simulationActive = false;
            let audioUnlocked = false;

            // Actualizar la hora actual en el registro
            function updateCurrentTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                document.getElementById('current-time').textContent = '[' + timeString + ']';
            }

            setInterval(updateCurrentTime, 1000);
            updateCurrentTime();

            // Comprobar el estado de los permisos
            function checkNotificationPermission() {
                if (!('Notification' in window)) {
                    permissionStatus.textContent = 'Tu navegador no soporta notificaciones';
                    permissionStatus.className = 'permission-denied';
                    requestPermissionBtn.disabled = true;
                    return;
                }

                if (Notification.permission === 'granted') {
                    permissionStatus.innerHTML = '<span class="status-indicator status-active"></span> Notificaciones activadas: El sistema alertará sobre fugas de gas';
                    permissionStatus.className = 'permission-granted';
                    requestPermissionBtn.disabled = true;
                    startCountdown();
                } else if (Notification.permission === 'denied') {
                    permissionStatus.innerHTML = '<span class="status-indicator status-inactive"></span> Permisos denegados: El sistema no podrá enviar alertas';
                    permissionStatus.className = 'permission-denied';
                    requestPermissionBtn.disabled = true;
                    startCountdown();
                } else {
                    permissionStatus.innerHTML = '<span class="status-indicator status-inactive"></span> Active las notificaciones para recibir alertas de fugas';
                    permissionStatus.className = 'permission-default';
                    requestPermissionBtn.disabled = false;
                }
            }

            // Solicitar permisos
            requestPermissionBtn.addEventListener('click', function() {
                Notification.requestPermission().then(function(permission) {
                    addLog('Solicitud de permisos: ' + permission);
                    checkNotificationPermission();
                    // Intento de desbloquear audio por la interacción del usuario
                    tryUnlockAudio();
                });
            });

            // Botón para probar el audio
            testAudioBtn.addEventListener('click', function() {
                playAudioForNotification('Prueba de sonido');
                audioStatus.innerHTML = '<span class="text-success">Reproduciendo sonido de prueba...</span>';
                setTimeout(() => {
                    audioStatus.innerHTML = '<span class="text-success">Sonido probado correctamente</span>';
                }, 1000);
            });

            // Intenta reproducir en silencio para "desbloquear" reproducción futura
            function tryUnlockAudio() {
                if (!audioEl || audioUnlocked) return;
                
                try {
                    // Reproducir silenciado y pausar para que el navegador considere que hubo interacción del usuario
                    audioEl.volume = 0.1; // Volumen muy bajo en lugar de muteado
                    
                    const playPromise = audioEl.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            // Pausar inmediatamente después de comenzar la reproducción
                            audioEl.pause();
                            audioEl.currentTime = 0;
                            audioEl.volume = 1.0;
                            audioUnlocked = true;
                            addLog('Audio desbloqueado por interacción del usuario.');
                        }).catch(err => {
                            audioEl.volume = 1.0;
                            addLog('No se pudo desbloquear audio automáticamente: ' + err.message);
                        });
                    }
                } catch (e) {
                    audioEl.volume = 1.0;
                    addLog('Error al intentar desbloquear audio: ' + e.message);
                }
            }

            // Reproducir audio para la notificación
            function playAudioForNotification(context) {
                if (!audioEl) {
                    addLog('Elemento de audio no encontrado');
                    return;
                }
                
                try {
                    // Detener cualquier reproducción previa y reiniciar
                    audioEl.pause();
                    audioEl.currentTime = 0;
                    
                    // Intentar reproducir
                    const playPromise = audioEl.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            addLog('Sonido de alerta reproducido correctamente: ' + context);
                        }).catch(err => {
                            addLog('No se pudo reproducir el sonido de alerta (' + context + '): ' + err.message);
                            // Si falla, intentar desbloquear el audio
                            if (!audioUnlocked) {
                                tryUnlockAudio();
                            }
                        });
                    }
                } catch (e) {
                    addLog('Error al reproducir audio (' + context + '): ' + e.message);
                }
            }

            // Añadir entrada al registro
            function addLog(message) {
                const now = new Date();
                const timeString = now.toLocaleTimeString();

                const logEntry = document.createElement('div');
                logEntry.className = 'log-entry';
                logEntry.innerHTML = '<span class="log-time">[' + timeString + ']</span> <span class="log-message">' + message + '</span>';

                systemLog.appendChild(logEntry);
                systemLog.scrollTop = systemLog.scrollHeight;
            }

            // Iniciar cuenta regresiva
            function startCountdown() {
                countdownTimer = setInterval(function() {
                    secondsLeft--;

                    const minutes = Math.floor(secondsLeft / 60);
                    const seconds = secondsLeft % 60;

                    countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    if (secondsLeft <= 0) {
                        clearInterval(countdownTimer);
                        startSimulation();
                    }
                }, 1000);
            }

            // Iniciar simulación de fuga de gas
            function startSimulation() {
                simulationActive = true;
                addLog('Iniciando simulación de fuga de gas...');

                let gasLevel = 0;
                let simulationStage = 0;

                // Ocultar countdown
                document.querySelector('.countdown').classList.add('d-none');

                // Función para actualizar el nivel de gas
                function updateGasLevel() {
                    if (!simulationActive) return;

                    // Aumentar el nivel de gas según la etapa de simulación
                    if (simulationStage === 0) {
                        gasLevel += 5; // Aumento lento al inicio
                        if (gasLevel >= 100) simulationStage = 1;
                    } else if (simulationStage === 1) {
                        gasLevel += 15; // Aumento moderado
                        if (gasLevel >= 300) simulationStage = 2;
                    } else {
                        gasLevel += 25; // Aumento rápido (fuga crítica)
                        if (gasLevel >= 1000) {
                            clearInterval(simulationTimer);
                            addLog('Niveles de gas críticos. Simulando cierre de válvula principal...');
                            return;
                        }
                    }

                    // Actualizar UI
                    gasValueElement.textContent = gasLevel + ' ppm';
                    gasProgressElement.style.width = Math.min(gasLevel / 10, 100) + '%';

                    // Cambiar colores según el nivel
                    if (gasLevel < 100) {
                        gasProgressElement.className = 'progress-bar bg-success';
                        sensorStatusElement.textContent = 'Normal';
                        sensorStatusElement.className = 'text-success';
                    } else if (gasLevel < 300) {
                        gasProgressElement.className = 'progress-bar bg-warning';
                        sensorStatusElement.textContent = 'Fuga Leve';
                        sensorStatusElement.className = 'text-warning';

                        // Enviar notificación de fuga leve
                        if (gasLevel === 100) {
                            showNotification('⚠️ Fuga de Gas Leve', 'Se detectaron niveles bajos de gas (100 ppm). Verifique ventilación.');
                        }
                    } else if (gasLevel < 600) {
                        gasProgressElement.className = 'progress-bar bg-warning';
                        sensorStatusElement.textContent = 'Fuga Moderada';
                        sensorStatusElement.className = 'text-warning';

                        // Enviar notificación de fuga moderada
                        if (gasLevel === 300) {
                            showNotification('🚨 Fuga de Gas Moderada', 'Niveles moderados de gas detectados (300 ppm). Ventile el área y evite llamas.');
                        }
                    } else {
                        gasProgressElement.className = 'progress-bar bg-danger';
                        sensorStatusElement.textContent = 'Fuga Crítica';
                        sensorStatusElement.className = 'text-danger';

                        // Enviar notificación de fuga crítica
                        if (gasLevel === 600) {
                            showNotification('🔥 ¡PELIGRO! Fuga de Gas Crítica', '¡EVACUACIÓN INMEDIATA! Niveles peligrosos de gas detectados (600 ppm).');
                        }
                    }
                }

                // Actualizar cada 2 segundos
                simulationTimer = setInterval(updateGasLevel, 2000);
            }

            // Mostrar notificación
            function showNotification(title, message) {
                addLog('Enviando notificación: ' + title);

                // Mostrar badge de notificación
                notificationBadge.classList.remove('d-none');

                // Reproducir sonido de alerta
                playAudioForNotification(title);

                if (Notification.permission === 'granted') {
                    try {
                        const notification = new Notification(title, {
                            body: message,
                            icon: 'https://cdn3d.iconscout.com/3d/premium/thumb/fuga-de-gas-8440307-6706766.png?f=webp',
                            requireInteraction: title.includes('PELIGRO'),
                            tag: 'asg-gas-alert'
                        });

                        notification.onclick = function() {
                            window.focus();
                            notification.close();
                            addLog('Notificación "' + title + '" fue clickeada por el usuario');
                            notificationBadge.classList.add('d-none');
                        };

                        // Cerrar automáticamente después de 10 segundos (excepto críticas)
                        if (!title.includes('PELIGRO')) {
                            setTimeout(function() {
                                try {
                                    notification.close();
                                } catch (e) {
                                    // no-op
                                }
                                notificationBadge.classList.add('d-none');
                            }, 10000);
                        }
                    } catch (error) {
                        addLog('Error al mostrar notificación: ' + error.message);
                    }
                } else {
                    addLog('No se pudo enviar notificación: Permisos no concedidos');
                }
            }

            // Configurar el badge de notificación
            notificationBadge.addEventListener('click', function() {
                this.classList.add('d-none');
            });

            // Inicializar comprobación de permisos
            checkNotificationPermission();
            addLog('Sistema de monitoreo de gas inicializado');
            
            // Intentar desbloquear audio al hacer clic en cualquier parte de la página
            document.body.addEventListener('click', function() {
                if (!audioUnlocked) {
                    tryUnlockAudio();
                }
            });

            // Informar sobre las restricciones de notificaciones en móviles
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                addLog('Dispositivo móvil detectado: Las notificaciones push pueden tener restricciones adicionales');
            }
        });
    </script>
</body>
</html>
