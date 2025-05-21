<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/forgot.css') ?>">
    <title>Recuperar Contraseña</title>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

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
            <a href="<?= base_url('/loginobtener') ?>">Acceder</a>
        </div>
    </section>

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
