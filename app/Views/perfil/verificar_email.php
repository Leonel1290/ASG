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
            color: #fff !important; /* Color del texto de la marca */
            font-weight: bold;
            font-size: 1.4rem; /* Agrandar letra de la marca */
        }

        .navbar-nav .nav-link {
            color: #cbd5e0 !important; /* Color de los enlaces de navegación */
            font-size: 1.1rem; /* Agrandar letra de los enlaces */
            padding-top: .75rem; /* Aumentar relleno vertical */
            padding-bottom: .75rem; /* Aumentar relleno vertical */
        }

        .navbar-nav .nav-link.active {
            color: #4299e1 !important; /* Color del enlace activo (azul) */
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
             color: #fff !important; /* Color al pasar el ratón */
        }
        /* --- FIN ESTILOS NAVBAR --- */


        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
        }
        .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }

        .container {
            flex: 1;
            padding: 2rem;
            max-width: 600px; /* Mantenemos el ancho máximo para el contenido principal */
            margin-top: 2rem;
        }

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            text-align: center; /* Centrar contenido de la tarjeta */
        }

        .card-header {
            background-color: #4a5568;
            color: #edf2f7;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2d3748;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
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

         .btn-primary {
            background-color: #4299e1; /* Botón principal */
            border-color: #4299e1;
            color: white; /* Asegurar texto blanco */
            transition: background-color 0.3s ease;
         }

         .btn-primary:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
         }

        .mt-3 { margin-top: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .me-2 { margin-right: 0.5rem; }

    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/perfil') ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil') ?>">Inicio</a>
                        </li>
                        <li class="nav-item">
                             <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil/configuracion') ?>">Mi Perfil</a>
                        </li>
                    </ul>
                    <form action="<?= base_url('/logout') ?>" method="post" class="d-flex">
                         <?= csrf_field() ?>
                         <button type="submit" class="btn btn-outline-secondary btn-sm">
                             <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                         </button>
                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>