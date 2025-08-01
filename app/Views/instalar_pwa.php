<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalar ASG App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos personalizados para la vista, ambientados con el resto de la app */
        body {
            background: linear-gradient(135deg, #0D1F23, #132E35); /* Fondo degradado de inicio.php */
            font-family: 'Poppins', sans-serif; /* Fuente de inicio.php */
            color: #AFB3B7; /* Color de texto general de inicio.php */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem; /* Padding para evitar que el contenido toque los bordes en pantallas pequeñas */
        }
        .card {
            background-color: #2D4A53; /* Color de fondo de .company-info en inicio.php */
            padding: 2.5rem; /* Un poco más de padding */
            border-radius: 0.75rem; /* Bordes más redondeados */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Sombra sutil */
            max-width: 28rem; /* Ancho máximo para el card */
            width: 100%;
            text-align: center;
            color: #fff; /* Texto del card en blanco para contraste */
        }
        .btn-install, .btn-open {
            background-color: #698180; /* Color de btn-custom de inicio.php */
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 30px; /* Bordes redondeados como los botones de inicio.php */
            padding: 0.8rem 2rem; /* Más padding para que se vea como botón */
            transition: background-color 0.3s ease-in-out;
            margin-top: 1.5rem; /* Espacio superior */
            cursor: pointer;
            display: inline-block; /* Para que el padding y el margen funcionen bien */
            text-decoration: none; /* Eliminar subrayado si fuera un enlace */
        }
        .btn-install:hover, .btn-open:hover {
            background-color: #2D4A53; /* Hover de btn-custom de inicio.php */
        }
        .icon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem; /* Espacio inferior */
        }
        .icon-container img {
            width: 96px; /* Ajusta el tamaño del icono */
            height: 96px;
            border-radius: 1.5rem; /* Bordes más redondeados para el icono */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Sombra para el icono */
        }
        h1 {
            font-size: 2.25rem; /* Tamaño de fuente más grande para el título */
            font-weight: 700; /* Negrita */
            color: #fff; /* Título en blanco */
            margin-bottom: 0.75rem; /* Espacio debajo del título */
        }
        p {
            font-size: 1rem;
            line-height: 1.5;
            color: #cbd5e0; /* Un gris claro para el texto de descripción */
            margin-bottom: 1rem;
        }
        
        /* Estilos para el modal de incompatibilidad */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #2D4A53;
            padding: 2rem;
            border-radius: 0.75rem;
            max-width: 28rem;
            width: 90%;
            text-align: center;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }
        .modal-message {
            margin-bottom: 1.5rem;
            color: #cbd5e0;
        }
        .modal-close {
            background-color: #698180;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
        }
        .modal-close:hover {
            background-color: #2D4A53;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon-container">
            <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="ASG Logo">
        </div>
        <h1>Instala la Aplicación ASG</h1>
        <p>
            Lleva la seguridad de tu hogar directamente a tu dispositivo. Instala nuestra aplicación para un acceso rápido y notificaciones instantáneas, ¡como una app nativa!
        </p>
        
        <button id="installButton" class="btn-install hidden">
            Descargar e Instalar App
        </button>
        
        <button id="openButton" class="btn-open hidden">
            Abrir Aplicación
        </button>
    </div>

    <!-- Modal de incompatibilidad -->
    <div id="incompatibilityModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Compatibilidad no soportada</h3>
            <p class="modal-message">
                Tu navegador no soporta la instalación de aplicaciones web (PWA) o ya tienes la aplicación instalada.
                <br><br>
                Puedes intentar añadir la página a tu pantalla de inicio manualmente si tu navegador lo permite (busca la opción "Añadir a pantalla de inicio" o similar en el menú del navegador).
            </p>
            <button id="modalClose" class="modal-close">Entendido</button>
        </div>
    </div>

    <script>
        let deferredPrompt;
        const installButton = document.getElementById('installButton');
        const openButton = document.getElementById('openButton');
        const incompatibilityModal = document.getElementById('incompatibilityModal');
        const modalClose = document.getElementById('modalClose');

        // Verificar si la app ya está instalada
        function checkIfAppInstalled() {
            if (window.matchMedia('(display-mode: standalone)').matches) {
                console.log('La aplicación ya está instalada y se está ejecutando en modo standalone.');
                openButton.classList.remove('hidden');
                openButton.addEventListener('click', () => {
                    // Intenta abrir la app (esto depende de cómo esté configurado tu manifest)
                    window.location.href = '/';
                });
                return true;
            }
            return false;
        }

        // Mostrar modal de incompatibilidad
        function showIncompatibilityModal() {
            incompatibilityModal.style.display = 'flex';
        }

        // Cerrar modal
        modalClose.addEventListener('click', () => {
            incompatibilityModal.style.display = 'none';
        });

        // Verificar compatibilidad con PWA
        function checkPwaSupport() {
            if (!('BeforeInstallPromptEvent' in window) && !checkIfAppInstalled()) {
                console.log('Navegador no soporta instalación de PWA');
                showIncompatibilityModal();
                return false;
            }
            return true;
        }

        // Log de las URLs generadas por base_url()
        console.log('URL del Manifest:', '<?= base_url('manifest.json'); ?>');
        console.log('URL del Service Worker:', '<?= base_url('service-worker.js'); ?>');

        // Verificaciones iniciales
        if (!checkIfAppInstalled()) {
            checkPwaSupport();
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installButton.classList.remove('hidden');
            console.log('Evento beforeinstallprompt disparado. Botón de instalación visible.');
        });

        installButton.addEventListener('click', async () => {
            installButton.classList.add('hidden');
            if (deferredPrompt) {
                console.log('Llamando a deferredPrompt.prompt()...');
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`El usuario eligió: ${outcome}`);
                deferredPrompt = null;
                if (outcome === 'accepted') {
                    console.log('El usuario aceptó la instalación de la PWA.');
                } else {
                    console.log('El usuario rechazó la instalación de la PWA.');
                    showIncompatibilityModal();
                }
            } else {
                console.log('deferredPrompt no está disponible. Posiblemente ya se instaló o no es compatible.');
                showIncompatibilityModal();
            }
        });

        window.addEventListener('appinstalled', () => {
            console.log('ASG PWA instalada exitosamente!');
            installButton.classList.add('hidden');
            openButton.classList.remove('hidden');
        });

        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito en la PWA:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Fallo el registro de ServiceWorker en la PWA:', error);
                    });
            });
        }

        // Verificación final después de un tiempo
        setTimeout(() => {
            if (!deferredPrompt && !checkIfAppInstalled()) {
                console.log('No se detectó soporte para beforeinstallprompt o la PWA ya está instalada.');
                showIncompatibilityModal();
            }
        }, 1000);
    </script>

</body>
</html>