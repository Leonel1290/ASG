<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalar ASG App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <style>
        /* Estilos personalizados para la vista */
        body {
            @apply bg-gray-900 text-gray-100 font-sans flex flex-col items-center justify-center min-h-screen p-4;
        }
        .card {
            @apply bg-gray-800 p-8 rounded-lg shadow-xl max-w-md w-full text-center;
        }
        .btn-install {
            @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full transition duration-300 ease-in-out mt-6;
        }
        .icon-container {
            @apply flex justify-center items-center mb-6;
        }
        .icon {
            width: 80px; /* Ajusta el tamaño del icono */
            height: 80px;
            border-radius: 15px; /* Bordes ligeramente redondeados para simular icono de app */
            @apply bg-blue-500 flex items-center justify-center text-white text-5xl;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon-container">
            <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="ASG Logo" class="w-20 h-20 rounded-lg">
        </div>
        <h1 class="text-3xl font-bold mb-4">Instala la Aplicación ASG</h1>
        <p class="text-gray-300 mb-6">
            Lleva la seguridad de tu hogar directamente a tu dispositivo. Instala nuestra aplicación para un acceso rápido y notificaciones instantáneas, ¡como una app nativa!
        </p>
        
        <button id="installButton" class="btn-install hidden">
            Descargar e Instalar App
        </button>

        <p id="notSupportedMessage" class="text-gray-400 mt-4 hidden">
            Tu navegador no soporta la instalación de aplicaciones web (PWA) de esta manera, o ya la tienes instalada.
            Puedes intentar añadir la página a tu pantalla de inicio manualmente si tu navegador lo permite (busca la opción "Añadir a pantalla de inicio" o similar en el menú del navegador).
        </p>
    </div>

    <script>
        let deferredPrompt; // Variable para almacenar el evento de instalación
        const installButton = document.getElementById('installButton');
        const notSupportedMessage = document.getElementById('notSupportedMessage');

        // 1. Escuchar el evento 'beforeinstallprompt'
        // Este evento se dispara cuando el navegador está listo para mostrar el prompt de instalación.
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevenir que el prompt se muestre automáticamente
            e.preventDefault();
            // Almacenar el evento para poder dispararlo más tarde
            deferredPrompt = e;
            // Mostrar el botón de instalación al usuario
            installButton.classList.remove('hidden');
            console.log('Evento beforeinstallprompt disparado. Botón de instalación visible.');
        });

        // 2. Manejar el clic en el botón de instalación
        installButton.addEventListener('click', async () => {
            // Ocultar el botón después de que el usuario haga clic
            installButton.classList.add('hidden');

            // Mostrar el prompt de instalación guardado
            if (deferredPrompt) {
                console.log('Llamando a deferredPrompt.prompt()...');
                deferredPrompt.prompt();
                // Esperar la respuesta del usuario al prompt
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`El usuario eligió: ${outcome}`);
                // Resetear deferredPrompt, ya que solo se puede usar una vez
                deferredPrompt = null;

                // Si el usuario aceptó la instalación, puedes redirigirlo o mostrar un mensaje
                if (outcome === 'accepted') {
                    console.log('El usuario aceptó la instalación de la PWA.');
                    // Opcional: Puedes redirigir al usuario a la página de inicio o a su perfil
                    // después de la instalación. Esto es para la *navegación actual* en el navegador.
                    // La PWA instalada se abrirá según el start_url del manifest.
                    // window.location.href = '<?= base_url('/') ?>'; // Redirigir a la página de inicio
                } else {
                    console.log('El usuario rechazó la instalación de la PWA.');
                    // Opcional: Mostrar un mensaje al usuario de que puede instalarla más tarde
                    // notSupportedMessage.classList.remove('hidden'); // Podrías reutilizar este mensaje o crear uno nuevo
                }
            } else {
                console.log('deferredPrompt no está disponible. Posiblemente ya se instaló o no es compatible.');
                // Si el botón se hizo visible pero deferredPrompt es nulo (ej. ya se usó),
                // o si el navegador no lo soporta, mostrar el mensaje de no soportado.
                notSupportedMessage.classList.remove('hidden');
            }
        });

        // 3. Manejar el evento 'appinstalled'
        // Este evento se dispara si la PWA se instala correctamente (ya sea por el prompt o manualmente)
        window.addEventListener('appinstalled', () => {
            console.log('ASG PWA instalada exitosamente!');
            // Opcional: Ocultar el botón de instalación si la app ya está instalada
            installButton.classList.add('hidden');
            notSupportedMessage.classList.add('hidden'); // Ocultar también el mensaje de no soportado
            // Puedes mostrar un mensaje de éxito o redirigir aquí si lo deseas
            // alert('¡ASG se ha instalado en tu pantalla de inicio!');
        });

        // 4. Fallback si 'beforeinstallprompt' nunca se dispara (navegador no compatible o ya instalada)
        // Un pequeño retraso para asegurar que 'beforeinstallprompt' tenga tiempo de dispararse.
        setTimeout(() => {
            // Si deferredPrompt nunca se asignó Y el botón de instalación sigue oculto
            // (lo que significa que beforeinstallprompt no se disparó),
            // entonces el navegador no lo soporta o la PWA ya está instalada.
            if (!deferredPrompt && installButton.classList.contains('hidden')) {
                notSupportedMessage.classList.remove('hidden');
                console.log('No se detectó soporte para beforeinstallprompt o la PWA ya está instalada.');
            }
        }, 1000); // Espera 1 segundo

        // --- NUEVO: Registro del Service Worker para la PWA ---
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
        // --- FIN NUEVO ---
    </script>

</body>
</html>
