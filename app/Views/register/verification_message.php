<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu Email - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
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

        .container {
            flex: 1;
            padding: 2rem;
            max-width: 600px;
            text-align: center;
        }

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .email-icon {
            color: #4299e1; /* Color azul */
            font-size: 5rem; /* Tamaño grande del icono */
            margin-bottom: 1rem;
        }

        .message-text {
            font-size: 1.25rem;
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
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <i class="fas fa-envelope-open-text email-icon"></i>
            <p class="message-text">
                ¡Gracias por registrarte!
            </p>
             <p class="message-text">
                Hemos enviado un correo electrónico de verificación a tu dirección.
                Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace para activar tu cuenta.
            </p>

             <?php if (session('success')): ?>
                <div class="alert alert-success mt-3"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
            <?php endif; ?>
            <?php if (session('error')): ?>
                <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
            <?php endif; ?>

            <a href="<?= base_url('/loginobtener') ?>" class="btn btn-secondary mt-3"><i class="fas fa-sign-in-alt me-2"></i> Ir a Iniciar Sesión</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
