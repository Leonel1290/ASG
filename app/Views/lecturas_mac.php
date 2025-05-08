<!DOCTYPE html>
<html>
<head>
    <title>Lectura de Gas</title>
</head>
<body>
    <h2>Lectura de Gas para el dispositivo <?= esc($mac) ?></h2>
    <p><?= esc($lectura) ?: 'No hay lectura registrada.' ?></p>

    <a href="<?= base_url('/perfil') ?>">Volver al perfil</a>
</body>
</html>
