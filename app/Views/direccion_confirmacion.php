<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dirección registrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container py-4">
    <h2 class="mb-3">¡Dirección registrada!</h2>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
    <?php endif; ?>

    <div class="card bg-secondary">
        <div class="card-body">
            <h5 class="card-title">Compra #<?= esc($compra['id']); ?> (Orden <?= esc($compra['order_id']); ?>)</h5>
            <p class="card-text mb-1">Enviaremos tu dispositivo a:</p>
            <ul class="mb-0">
                <li><?= esc($direccion['calle']); ?> <?= esc($direccion['numero']); ?>, Piso <?= esc($direccion['piso']); ?>, Dpto <?= esc($direccion['depto']); ?></li>
                <li><?= esc($direccion['ciudad']); ?>, <?= esc($direccion['provincia']); ?>, CP <?= esc($direccion['codigo_postal']); ?>, <?= esc($direccion['pais']); ?></li>
                <?php if (!empty($direccion['telefono'])): ?>
                    <li>Tel: <?= esc($direccion['telefono']); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="mt-3">
        <a href="/" class="btn btn-primary">Volver al inicio</a>
    </div>
</div>
</body>
</html>