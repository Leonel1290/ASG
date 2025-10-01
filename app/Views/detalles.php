<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles - Control de Válvula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php $estado_valvula = $estado_valvula ?? 0; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Control de Válvula</h2>

    <div class="card shadow p-4">
        <h5 class="mb-3">Estado actual: 
            <span class="badge bg-<?php echo ($estado_valvula == 1) ? 'danger' : 'success'; ?>">
                <?php echo ($estado_valvula == 1) ? 'Cerrada' : 'Abierta'; ?>
            </span>
        </h5>

        <form method="post" action="<?= base_url('/servo/abrir') ?>">
            <button type="submit" class="btn btn-success w-100 mb-3">Abrir Válvula</button>
        </form>

        <form method="post" action="<?= base_url('/servo/cerrar') ?>">
            <button type="submit" class="btn btn-danger w-100">Cerrar Válvula</button>
        </form>
    </div>
</div>

</body>
</html>
