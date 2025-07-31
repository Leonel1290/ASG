<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Compras | AgainSafeGas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d1117;
            color: #e6edf3;
        }
        .card {
            background-color: #161b22;
            border: 1px solid #30363d;
        }
        .table {
            color: #e6edf3;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Mis Compras</h1>
        
        <?php if (empty($compras)): ?>
            <div class="alert alert-info">
                No has realizado ninguna compra aún.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td><?= $compra['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?></td>
                            <td><?= $compra['moneda'] ?> <?= number_format($compra['monto'], 2) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $compra['estado_pago'] === 'completado' ? 'bg-success' : 
                                       ($compra['estado_pago'] === 'pendiente' ? 'bg-warning' : 'bg-danger') ?>">
                                    <?= ucfirst($compra['estado_pago']) ?>
                                </span>
                            </td>
                            <td><?= nl2br(htmlspecialchars($compra['direccion_envio'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary mt-3">Volver al Dashboard</a>
    </div>
</body>
</html>