<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Última Lectura de Gas</title>
</head>
<body>
    <h1>Última Lectura de Gas</h1>
    <?php if (isset($lectura)) : ?>
        <p><strong>Lectura:</strong> <?= esc($lectura['lectura']); ?></p>
        <p><strong>Fecha y Hora:</strong> <?= esc($lectura['created_at']); ?></p>
    <?php else : ?>
        <p>No hay lecturas disponibles.</p>
    <?php endif; ?>
</body>
</html>
