<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión para Comprar - ASG</title>
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
            align-items: center;
            justify-content: center;
        }

        .container {
            flex: 1;
            padding: 2rem;
            max-width: 500px;
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

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .form-control {
            background-color: #4a5568;
            border: 1px solid #2d3748;
            color: #fff;
        }
        .form-control:focus {
            background-color: #4a5568;
            color: #fff;
            border-color: #63b3ed;
            box-shadow: 0 0 0 0.25rem rgba(99, 179, 237, 0.25);
        }

        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #3182ce;
            border-color: #3182ce;
        }

        .alert {
            margin-top: 1rem;
            text-align: left;
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
        .alert-info {
            background-color: #bee3f8;
            color: #1a202c;
            border-color: #a0aec0;
        }
        a {
            color: #4299e1;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <h2 class="mb-4"><i class="fas fa-lock me-2"></i> Iniciar Sesión para Comprar</h2>

            <?php if (session('info')): ?>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <?= session('info') ?>
                </div>
            <?php endif; ?>
            <?php if (session('success')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errors) && is_array($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <p class="mt-3 mb-4">Para continuar con tu compra, por favor, inicia sesión con tu cuenta.</p>

            <form action="<?= base_url('/login/paypal') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope me-2"></i> Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= esc(set_value('email')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-key me-2"></i> Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3"><i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión</button>
            </form>

            <p class="mt-3">¿Olvidaste tu contraseña? <a href="<?= base_url('/forgotpassword') ?>">Restablecer contraseña</a></p>
            <p>¿No tienes una cuenta? <a href="<?= base_url('/register/paypal') ?>">Regístrate aquí</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
