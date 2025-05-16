<?php
// Esta vista espera que el controlador le pase una variable $userData
// con las claves 'nombre' y 'email' del usuario logueado.
// Usamos 'array' como returnType en el modelo, así que accedemos con ['clave'].
$userData = $userData ?? ['nombre' => '', 'email' => ''];

// CodeIgniter pasa los errores de validación en la variable $errors si la validación falla con with('errors', ...)
// Si la validación es exitosa, $errors será null o un array vacío.
$errors = session('errors') ?? []; // Obtener errores de validación de la sesión flashdata
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Configuración - ASG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos personalizados (copiados de las vistas anteriores) */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c; /* Fondo oscuro principal */
            color: #cbd5e0; /* Texto claro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ocupar al menos el 100% de la altura de la ventana */
        }

        /* Estilos para la barra de navegación */
        .navbar {
            background-color: #2d3748 !important; /* Color de fondo oscuro similar al de las tarjetas */
        }

        .navbar-brand {
            color: #fff !important; /* Color del texto de la marca */
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #cbd5e0 !important; /* Color de los enlaces de navegación */
        }

        .navbar-nav .nav-link.active {
            color: #4299e1 !important; /* Color del enlace activo (azul) */
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
             color: #fff !important; /* Color al pasar el ratón */
        }

        /* Estilos para el botón de Cerrar Sesión */
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
        }
         .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }

        /* Contenedor principal del contenido */
        .container {
            flex: 1; /* Permite que el contenedor ocupe el espacio restante */
            padding: 2rem;
            max-width: 600px; /* Limitar el ancho del formulario para mejor legibilidad */
            margin-top: 2rem; /* Espacio superior */
            margin-bottom: 2rem; /* Espacio inferior */
        }

        /* Estilos para las tarjetas */
        .card {
            background-color: #2d3748; /* Fondo de tarjeta oscuro */
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #4a5568; /* Color de encabezado de tarjeta */
            color: #edf2f7;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2d3748;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            align-items: center;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .card-title i {
            margin-right: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Estilos para el botón principal (Guardar Cambios) */
        .btn-primary {
            background-color: #4299e1; /* Botón principal (azul) */
            border-color: #4299e1;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }

        /* Estilos para mensajes de alerta */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }

        .alert-success {
            background-color: #c6f6d5; /* Alerta verde */
            color: #1a202c;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background-color: #fed7d7; /* Alerta roja */
            color: #1a202c;
            border-color: #fbcbcb;
        }

        /* Estilos para grupos de formulario (label + input) */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e2e8f0; /* Color de label */
            font-weight: bold;
        }

        /* Estilos para campos de input */
        .form-control {
            width: 100%;
            padding: 0.75rem;
            background-color: #4a5568; /* Fondo de input */
            border: 1px solid #718096;
            border-radius: 0.375rem;
            color: #edf2f7; /* Color de texto de input */
            box-sizing: border-box; /* Incluir padding y borde en el ancho total */
        }

        .form-control::placeholder {
            color: #a0aec0; /* Color de placeholder */
        }

        /* Estilos para mensajes de error de validación bajo los inputs */
        .invalid-feedback {
            display: block; /* Mostrar el mensaje de error */
            color: #e53e3e; /* Color rojo */
            font-size: 0.875em; /* Tamaño de fuente más pequeño */
            margin-top: 0.25rem;
        }


        /* Utilidades de espaciado (ya definidas por Bootstrap, pero se incluyen por consistencia) */
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }

        /* Estilos para iconos dentro de labels */
        label i {
            margin-right: 0.5rem;
        }

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
                            <a class="nav-link" href="<?= base_url('/perfil') ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil/configuracion') ?>">Configuración</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Gráficos</a>
                        </li>
                    </ul>

                    <form action="<?= base_url('/logout') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?> <button type="submit" class="btn btn-outline-secondary btn-sm">
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
                <h5 class="card-title"><i class="fas fa-user-edit me-2"></i> Editar Perfil</h5>
            </div>
            <div class="card-body">

                <?php if (session('success')): ?>
                    <div class="alert alert-success mt-3"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
                <?php endif; ?>
                <?php if (session('error')): ?>
                    <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> Por favor, corrige los siguientes errores:
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>


                <form method="post" action="<?= base_url('/perfil/actualizar') ?>">
                    <?= csrf_field() ?> <div class="form-group">
                        <label for="nombre"><i class="fas fa-user me-2"></i> Nombre:</label>
                        <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre"
                            value="<?= esc(set_value('nombre', $userData['nombre'] ?? '')) ?>" required>
                        <?php if (isset($errors['nombre'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['nombre']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope me-2"></i> Email:</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email"
                            value="<?= esc(set_value('email', $userData['email'] ?? '')) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-save me-2"></i> Guardar Cambios</button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
