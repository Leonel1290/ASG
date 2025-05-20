<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio Exitoso - ASG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos personalizados (puedes copiarlos de tus otras vistas) */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c; /* Fondo oscuro */
            color: #cbd5e0; /* Texto claro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center; /* Centrar contenido verticalmente */
            justify-content: center; /* Centrar contenido horizontalmente */
        }

        .navbar {
            background-color: #2d3748 !important;
            position: fixed; /* Fija la navbar en la parte superior */
            width: 100%;
            top: 0;
            z-index: 1000; /* Asegura que esté por encima de otros elementos */
        }

        .navbar-brand {
            color: #fff !important; /* Color blanco para la marca */
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #ccc !important; /* Ligeramente más claro al pasar el ratón */
        }

        .nav-link {
            color: #cbd5e0 !important; /* Color claro para los enlaces */
        }

        .nav-link:hover {
            color: #fff !important; /* Color blanco al pasar el ratón */
        }

        .container {
            flex: 1; /* Permite que el contenedor crezca y ocupe el espacio disponible */
            padding: 2rem;
            max-width: 600px;
            margin-top: 80px; /* Espacio para la navbar fija */
            text-align: center; /* Centrar contenido */
        }

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .success-icon {
            color: #48bb78; /* Color verde */
            font-size: 5rem; /* Tamaño grande del icono */
            margin-bottom: 1rem;
        }

        .success-message {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .btn-secondary {
            background-color: #6b7280;
            border-color: #6b7280;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
            border-color: #4b5563;
        }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>

    <div class="container">
        <div class="card">
            <i class="fas fa-check-circle success-icon"></i>
            <p class="success-message">¡Cambio exitoso!</p>
            <p>Tu información ha sido actualizada correctamente.</p>
            <a href="<?= base_url('/perfil/configuracion') ?>" class="btn btn-secondary mt-3">Volver a Configuración</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Fallo el registro de ServiceWorker:', error);
                    });
            });
        }
    </script>

</body>
</html>
