<?php
// Esta vista espera la variable $dispositivo del controlador,
// que contiene los detalles del dispositivo a editar (MAC, nombre, ubicacion).
$dispositivo = $dispositivo ?? ['MAC' => '', 'nombre' => '', 'ubicacion' => ''];

// CodeIgniter pasa los errores de validación en la variable $errors si la validación falla con with('errors', ...)'
// Si la validación es exitosa, $errors será null o un array vacío.
$errors = session('errors') ?? []; // Obtener errores de validación de la sesión flashdata
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dispositivo - ASG</title>
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
            background-color: #1a202c; /* Fondo oscuro principal */
            color: #cbd5e0; /* Texto claro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- ESTILOS PARA AGRANDAR EL NAVBAR --- */
        .navbar {
            background-color: #2d3748 !important; /* Color de fondo oscuro */
            padding-top: 1rem; /* Más espacio arriba */
            padding-bottom: 1rem; /* Más espacio abajo */
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
            max-width: 800px; /* Ancho máximo para el formulario */
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

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: #a0aec0; /* Color de etiqueta */
            font-weight: bold;
            display: block; /* Asegura que la etiqueta esté en su propia línea */
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: #4a5568;
            color: #fff;
            border: 1px solid #6b7280;
            border-radius: 0.25rem;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background-color: #4a5568;
            color: #fff;
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
        }

        .invalid-feedback {
            color: #fc8181; /* Color rojo para errores de validación */
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

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
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
                <h5 class="card-title"><i class="fas fa-microchip me-2"></i> Editar Dispositivo</h5>
            </div>
            <div class="card-body">
                <?php if (session('success')): ?>
                    <div class="alert alert-success mt-3"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
                <?php endif; ?>
                <?php if (session('error')): ?>
                    <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('/dispositivos/actualizar/' . esc($dispositivo['MAC'])) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="MAC"><i class="fas fa-barcode me-2"></i> MAC del Dispositivo:</label>
                        <input type="text" class="form-control" id="MAC" name="MAC"
                            value="<?= esc($dispositivo['MAC'] ?? '') ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nombre"><i class="fas fa-tag me-2"></i> Nombre del Dispositivo:</label>
                        <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre"
                            value="<?= esc(set_value('nombre', $dispositivo['nombre'] ?? '')) ?>" required>
                        <?php if (isset($errors['nombre'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['nombre']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion"><i class="fas fa-map-marker-alt me-2"></i> Ubicación:</label>
                         <input type="text" class="form-control <?= isset($errors['ubicacion']) ? 'is-invalid' : '' ?>" id="ubicacion" name="ubicacion"
                             value="<?= esc(set_value('ubicacion', $dispositivo['ubicacion'] ?? '')) ?>">
                         <?php if (isset($errors['ubicacion'])): ?>
                             <div class="invalid-feedback">
                                 <?= esc($errors['ubicacion']) ?>
                             </div>
                         <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2 me-2"><i class="fas fa-save me-2"></i> Guardar Cambios</button>
                    <a href="<?= base_url('/perfil') ?>" class="btn btn-secondary mt-2"><i class="fas fa-times-circle me-2"></i> Cancelar</a>
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
