<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/forgot.css') ?>">
    <title>Recuperar Contraseña</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

    <section class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h1 class="mb-4 text-center">Recuperar Contraseña</h1>

        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->get('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->get('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= session()->get('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/forgotpassword1') ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo electrónico" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= base_url('login') ?>">Acceder</a>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
