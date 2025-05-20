<?php
// Esta vista espera la variable $userEmail del controlador
$userEmail = $userEmail ?? 'No disponible'; // Asegurarse de que la variable exista
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Email - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos personalizados */
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
        }

        /* --- ESTILOS PARA AGRANDAR EL NAVBAR --- */
        .navbar {
            background-color: #2d3748 !important; /* Color de fondo oscuro */
        }

        .navbar-brand {
            color: #fff !important; /* Color blanco para la marca */
            font-size: 1.5rem; /* Tamaño más grande */
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #ccc !important; /* Ligeramente más claro al pasar el ratón */
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        .nav-link {
            color: #cbd5e0 !important; /* Color claro para los enlaces */
            font-size: 1.1rem;
            padding-left: 1rem !important; /* Espacio entre enlaces en pantallas grandes */
            padding-right: 1rem !important;
        }

        .nav-link:hover {
            color: #fff !important; /* Color blanco al pasar el ratón */
        }
        /* --- FIN ESTILOS NAVBAR --- */

        .container {
            flex: 1; /* Permite que el contenedor crezca y ocupe el espacio disponible */
            padding: 2rem;
            max-width: 700px;
            margin-top: 20px; /* Espacio superior para compensar navbar */
        }

        .card {
            background-color: #2d3748; /* Fondo oscuro de la tarjeta */
            color: #fff; /* Texto blanco en la tarjeta */
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #4a5568; /* Un poco más claro para el encabezado */
            border-bottom: none;
            color: #fff;
            font-weight: bold;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn-primary {
            background-color: #4CAF50; /* Green */
            border-color: #4CAF50;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .alert {
            margin-top: 1rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #1a202c;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background-color: #fed7d7;
            color: #1a202c;
            border-color: #fbcbcb;
        }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/perfil'); ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil'); ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion'); ?>">Configuración</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/logout'); ?>">Cerrar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-shield-alt me-2"></i> Verificación de Email</h5>
            </div>
            <div class="card-body">

                 <?php if (session('success')): ?>
                     <div class="alert alert-success mt-3"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
                 <?php endif; ?>
                 <?php if (session('error')): ?>
                     <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
                 <?php endif; ?>

                <p class="card-text mb-4">
                    Para acceder a la configuración de tu perfil, necesitamos verificar que tienes acceso a tu email actual.
                </p>
                <p class="card-text mb-4">
                    Enviaremos un código de verificación a: <br><strong><?= esc($userEmail) ?></strong>
                </p>

                <form method="post" action="<?= base_url('/perfil/enviar-verificacion') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i> Enviar correo de verificación</button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
