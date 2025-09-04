<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ASG - Prueba de Notificaciones</title>
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

        .btn-warning {
            background-color: var(--warning-alert);
            color: #333;
            border: none;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            background-color: #eeaa22;
            transform: translateY(-2px);
        }

        .notification-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .notification-type {
            flex: 1;
            min-width: 300px;
            background: var(--dark-background);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .notification-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
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

        @media (max-width: 768px) {
            .notification-panel {
                flex-direction: column;
            }
            
            .notification-type {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gas-pump"></i> ASG - Prueba de Notificaciones
            </a>
        </div>
    </nav>

    <div class="container-main">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-bell"></i> Panel de Control de Notificaciones</h2>
                <p class="mb-0">Simula y prueba las notificaciones push para fugas de gas</p>
            </div>
            <div class="card-body">
                <div class="permission-status" id="permissionStatus">
                    Comprobando estado de permisos...
                </div>

                <button id="requestPermission" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Solicitar Permiso para Notificaciones
                </button>

                <div class="notification-panel">
                    <div class="notification-type">
                        <div class="notification-icon text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4>Fuga Leve</h4>
                        <p>Niveles bajos de gas detectados</p>
                        <button class="btn btn-warning test-notification" data-level="leve">
                            <i class="fas fa-bell"></i> Probar Notificación
                        </button>
                    </div>

                    <div class="notification-type">
                        <div class="notification-icon text-danger">
                            <i class="fas fa-radiation"></i>
                        </div>
                        <h4>Fuga Moderada</h4>
                        <p>Niveles moderados de gas detectados</p>
                        <button class="btn btn-danger test-notification" data-level="moderada">
                            <i class="fas fa-bell"></i> Probar Notificación
                        </button>
                    </div>

                    <div class="notification-type">
                        <div class="notification-icon text-danger">
                            <i class="fas fa-skull-crossbones"></i>
                        </div>
                        <h4>Fuga Crítica</h4>
                        <p>Niveles peligrosos de gas detectados - Evacuación inmediata</p>
                        <button class="btn btn-danger test-notification" data-level="critica">
                            <i class="fas fa-bell"></i> Probar Notificación
                        </button>
                    </div>
                </div>

                <div class="test-results">
                    <h4><i class="fas fa-clipboard-list"></i> Registro de Pruebas</h4>
                    <div class="log-container" id="testLog" style="max-height: 300px; overflow-y: auto; margin-top: 1rem;">
                        <div class="log-entry">
                            <span class="log-time" id="current-time"></span>
                            <span class="log-message">Sistema de prueba inicializado. Haz clic en los botones para probar las notificaciones.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Información de Prueba</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>¿Cómo probar las notificaciones?</h4>
                        <ol>
                            <li>Haz clic en "Solicitar Permiso para Notificaciones"</li>
                            <li>Acepta los permisos cuando tu navegador te lo solicite</li>
                            <li>Haz clic en cualquiera de los botones de prueba</li>
                            <li>Verifica que recibes la notificación</li>
                            <li>Revisa los resultados en el registro de pruebas</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h4>Solución de problemas</h4>
                        <ul>
                            <li>Asegúrate de que tu navegador permita notificaciones</li>
                            <li>Verifica que no tengas el "modo silencioso" activado</li>
                            <li>Si usas Chrome, revisa la configuración de notificaciones en:
                                <br><code>chrome://settings/content/notifications</code></li>
                            <li>Actualiza tu navegador a la versión más reciente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const permissionStatus = document.getElementById('permissionStatus');
            const requestPermissionBtn = document.getElementById('requestPermission');
            const testLog = document.getElementById('testLog');
            const testButtons = document.querySelectorAll('.test-notification');
            
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
                    permissionStatus.innerHTML = '<span class="status-indicator status-active"></span> Permisos concedidos: Puedes probar las notificaciones';
                    permissionStatus.className = 'permission-granted';
                    requestPermissionBtn.disabled = true;
                } else if (Notification.permission === 'denied') {
                    permissionStatus.innerHTML = '<span class="status-indicator status-inactive"></span> Permisos denegados: Debes habilitar manualmente las notificaciones';
                    permissionStatus.className = 'permission-denied';
                    requestPermissionBtn.disabled = true;
                } else {
                    permissionStatus.innerHTML = '<span class="status-indicator status-inactive"></span> Permisos no concedidos: Haz clic en el botón para solicitar permisos';
                    permissionStatus.className = 'permission-default';
                    requestPermissionBtn.disabled = false;
                }
            }
            
            // Solicitar permisos
            requestPermissionBtn.addEventListener('click', function() {
                Notification.requestPermission().then(function(permission) {
                    addLog('Solicitud de permisos: ' + permission);
                    checkNotificationPermission();
                });
            });
            
            // Añadir entrada al registro
            function addLog(message) {
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                
                const logEntry = document.createElement('div');
                logEntry.className = 'log-entry';
                logEntry.innerHTML = '<span class="log-time">[' + timeString + ']</span> <span class="log-message">' + message + '</span>';
                
                testLog.appendChild(logEntry);
                testLog.scrollTop = testLog.scrollHeight;
            }
            
            // Probar notificación
            function testNotification(level) {
                if (Notification.permission !== 'granted') {
                    addLog('Error: Primero debes conceder los permisos de notificación');
                    return;
                }
                
                let title, message, icon;
                
                switch(level) {
                    case 'leve':
                        title = '⚠️ Fuga de Gas Leve';
                        message = 'Se detectaron niveles bajos de gas. Verifique ventilación.';
                        icon = 'https://cdn-icons-png.flaticon.com/512/3616/3616932.png';
                        break;
                    case 'moderada':
                        title = '🚨 Fuga de Gas Moderada';
                        message = 'Niveles moderados de gas detectados. Ventile el área y evite llamas.';
                        icon = 'https://cdn-icons-png.flaticon.com/512/3616/3616945.png';
                        break;
                    case 'critica':
                        title = '🔥¡PELIGRO! Fuga de Gas Crítica';
                        message = '¡EVACUACIÓN INMEDIATA! Niveles peligrosos de gas detectados.';
                        icon = 'https://cdn-icons-png.flaticon.com/512/3616/3616961.png';
                        break;
                    default:
                        title = 'Notificación de Prueba';
                        message = 'Esta es una notificación de prueba del sistema ASG.';
                        icon = 'https://cdn-icons-png.flaticon.com/512/3616/3616932.png';
                }
                
                try {
                    const notification = new Notification(title, {
                        body: message,
                        icon: icon,
                        requireInteraction: level === 'critica',
                        tag: 'asg-gas-alert',
                        vibrate: [200, 100, 200, 100, 200]
                    });
                    
                    notification.onclick = function() {
                        window.focus();
                        notification.close();
                        addLog('Notificación "' + title + '" fue clickeada por el usuario');
                    };
                    
                    addLog('Notificación enviada: ' + title);
                    
                    // Cerrar automáticamente después de 10 segundos (excepto críticas)
                    if (level !== 'critica') {
                        setTimeout(function() {
                            notification.close();
                        }, 10000);
                    }
                } catch (error) {
                    addLog('Error al mostrar notificación: ' + error.message);
                }
            }
            
            // Configurar botones de prueba
            testButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const level = this.getAttribute('data-level');
                    testNotification(level);
                });
            });
            
            // Inicializar comprobación de permisos
            checkNotificationPermission();
            addLog('Sistema de prueba de notificaciones inicializado');
        });
    </script>
</body>
</html>