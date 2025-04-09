<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enlazar MAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Enlazar Dispositivo</h2>

        <?php if (session('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>
        <?php if (session('success')): ?>
            <div class="alert alert-success"><?= session('success') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('/enlace/store') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="mac" class="form-label">Dirección MAC</label>
                <input type="text" class="form-control" id="mac" name="mac" placeholder="AA:BB:CC:DD:EE:FF" required>
            </div>
            <button type="submit" class="btn btn-primary">Enlazar</button>
        </form>
    </div>
</body>
</html>
