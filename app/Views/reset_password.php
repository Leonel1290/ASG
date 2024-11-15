<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/reset.css') ?>">
    <title>Restablecer Contraseña</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

    <section class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h1 class="mb-4 text-center">Restablecer Contraseña</h1>

        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->get('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/reset-password') ?>" method="post">
            <input type="hidden" name="token" value="<?= $token ?>">
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Introduce tu nueva contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Restablecer Contraseña</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= base_url('login') ?>">Acceder</a>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
