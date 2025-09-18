<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Descargar ASG App - Seguridad en tu Hogar</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0A192F, #0D203B);
            font-family: 'Poppins', sans-serif;
            color: #AFB3B7;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .download-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            padding-top: 80px;
        }

        .download-card {
            background: rgba(26, 62, 92, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .app-icon {
            width: 120px;
            height: 120px;
            border-radius: 25px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #36678C, #2A5173);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .app-icon i {
            font-size: 60px;
            color: white;
        }

        h1 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .description {
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .features-list {
            text-align: left;
            margin-bottom: 2.5rem;
            display: inline-block;
        }

        .features-list li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }

        .features-list i {
            color: #36678C;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .btn-download {
            background: linear-gradient(135deg, #36678C, #2A5173);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(54, 103, 140, 0.4);
            margin: 0.5rem;
        }

        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(54, 103, 140, 0.6);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #AFB3B7;
            font-weight: 600;
            border-radius: 30px;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .instructions {
            margin-top: 2.5rem;
            padding: 1.5rem;
            background: rgba(10, 25, 47, 0.5);
            border-radius: 15px;
            text-align: left;
        }

        .instructions h3 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .step {
            display: flex;
            margin-bottom: 1rem;
            align-items: flex-start;
        }

        .step-number {
            background: #36678C;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #AFB3B7;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .back-button:hover {
            color: white;
        }

        footer {
            background-color: #0A192F;
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
        }

        /* Animaciones */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @media (max-width: 768px) {
            .download-card {
                padding: 2rem 1.5rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .btn-download, .btn-secondary {
                padding: 0.8rem 1.8rem;
                font-size: 1rem;
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>

    <a href="<?= base_url('/') ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="download-container">
        <div class="download-card">
            <div class="app-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            
            <h1>Descarga la App ASG</h1>
            <p class="description">Protege tu hogar con nuestra aplicación. Recibe alertas instantáneas, controla tus dispositivos y mantén tu familia segura desde cualquier lugar.</p>
            
            <ul class="features-list">
                <li><i class="fas fa-check-circle"></i> Alertas en tiempo real de fugas de gas</li>
                <li><i class="fas fa-check-circle"></i> Control remoto de tus dispositivos</li>
                <li><i class="fas fa-check-circle"></i> Historial de eventos y notificaciones</li>
                <li><i class="fas fa-check-circle"></i> Interfaz intuitiva y fácil de usar</li>
                <li><i class="fas fa-check-circle"></i> Funciona sin conexión a internet</li>
            </ul>
            
            <div>
                <button id="installPWAButton" class="btn-download pulse">
                    <i class="fas fa-download me-2"></i> Instalar App
                </button>
                <a href="<?= base_url('/') ?>" class="btn-secondary">
                    <i class="fas fa-home me-2"></i> Volver al Inicio
                </a>
            </div>
            
            <div class="instructions">
                <h3>¿Cómo instalar?</h3>
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">Haz clic en el botón "Instalar App"</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">Sigue las instrucciones en tu navegador para completar la instalación</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">Encuentra el icono de ASG en tu pantalla de inicio y ¡listo!</div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 AgainSafeGas Solutions | Todos los derechos reservados.</p>
    </footer>

    <script>
        let deferredPrompt;
        const installButton = document.getElementById('installPWAButton');

        // Escuchar el evento 'beforeinstallprompt'
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installButton.style.display = 'inline-block';
        });

        // Manejar el clic en el botón de instalación
        installButton.addEventListener('click', async () => {
            if (deferredPrompt) {
                installButton.classList.remove('pulse');
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    installButton.innerHTML = '<i class="fas fa-check me-2"></i> ¡App Instalada!';
                    installButton.style.background = 'linear-gradient(135deg, #4CAF50, #2E7D32)';
                } else {
                    installButton.innerHTML = '<i class="fas fa-download me-2"></i> Instalar App';
                    installButton.classList.add('pulse');
                }
                
                deferredPrompt = null;
            }
        });

        // Si el evento 'beforeinstallprompt' no se dispara
        window.addEventListener('load', () => {
            if (!deferredPrompt) {
                installButton.innerHTML = '<i class="fas fa-external-link-alt me-2"></i> Abrir en Navegador';
                installButton.onclick = function() {
                    window.open('https://pwa-1s1m.onrender.com/', '_blank');
                };
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>