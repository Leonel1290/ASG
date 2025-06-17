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

        .navbar {
            background-color: #2d3748 !important; /* Fondo oscuro navbar */
        }

        .navbar-brand {
            color: #4299e1 !important; /* Azul para el branding */
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #a0aec0 !important; /* Gris claro para los enlaces */
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important; /* Blanco al pasar el ratón */
        }

        .dropdown-menu {
            background-color: #2d3748;
            border: none;
        }

        .dropdown-item {
            color: #a0aec0;
        }

        .dropdown-item:hover {
            background-color: #4a5568;
            color: #fff;
        }

        .container {
            flex: 1;
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .card {
            background-color: #2d3748; /* Fondo de tarjeta */
            color: #cbd5e0; /* Texto de tarjeta */
            border: 1px solid #4a5568; /* Borde de tarjeta */
        }

        .card-header {
            background-color: #4a5568; /* Fondo del encabezado de la tarjeta */
            color: #fff; /* Texto del encabezado de la tarjeta */
            font-weight: bold;
        }

        .form-label {
            color: #cbd5e0;
        }

        .form-control {
            background-color: #2d3748;
            color: #cbd5e0;
            border-color: #4a5568;
        }

        .form-control:focus {
            background-color: #2d3748;
            color: #cbd5e0;
            border-color: #4299e1;
            box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25);
        }

        .invalid-feedback {
            color: #ff5252; /* Color para mensajes de error de validación */
        }

        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
        }

        .btn-primary:hover {
            background-color: #3182ce;
            border-color: #3182ce;
        }

        .btn-secondary {
            background-color: #a0aec0;
            border-color: #a0aec0;
        }

        .btn-secondary:hover {
            background-color: #718096;
            border-color: #718096;
        }

        footer {
            background-color: #2d3748;
            color: #cbd5e0;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo ASG" width="30" height="30" class="d-inline-block align-text-top me-2">
                ASG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil') ?>">Perfil</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= esc(session()->get('nombre')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('/perfil/configuracion') ?>">Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('/logout') ?>">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/login') ?>">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/register') ?>">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                Editar Dispositivo: <?= esc($dispositivo['nombre'] ?? 'Desconocido') ?>
            </div>
            <div class="card-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/perfil/dispositivo/actualizar') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="MAC" value="<?= esc($dispositivo['MAC'] ?? '') ?>">

                    <div class="mb-3">
                        <label for="mac" class="form-label">Dirección MAC</label>
                        <input type="text" class="form-control" id="mac" value="<?= esc($dispositivo['MAC'] ?? '') ?>" readonly>
                        <div class="form-text text-muted">La dirección MAC no se puede cambiar.</div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Dispositivo</label>
                        <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre"
                               value="<?= esc(set_value('nombre', $dispositivo['nombre'] ?? '')) ?>">
                        <?php if (isset($errors['nombre'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['nombre']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
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
