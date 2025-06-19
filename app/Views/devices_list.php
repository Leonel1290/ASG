<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Dispositivos</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .container {
            background-color: #2d3748;
            border-radius: 0.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            max-width: 600px;
            width: 100%;
        }
        .page-title {
            color: #f7fafc;
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .list-group-item {
            background-color: #4a5568;
            color: #edf2f7;
            border: 1px solid #667eea;
            margin-bottom: 0.5rem;
            border-radius: 0.3rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-group-item:hover {
            background-color: #667eea;
            color: #fff;
            transform: translateY(-2px);
        }
        .list-group-item a {
            color: inherit;
            text-decoration: none;
            flex-grow: 1;
            padding: 0.75rem 1.25rem;
        }
        .list-group-item .badge {
            margin-left: 1rem;
            font-size: 0.8em;
            padding: 0.4em 0.7em;
            border-radius: 0.25rem;
            background-color: #2d3748; /* Darker badge */
            color: #fff;
        }
        .no-devices-message {
            text-align: center;
            color: #a0aec0;
            font-style: italic;
            margin-top: 1.5rem;
        }
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            display: block; /* Ocupa todo el ancho */
            width: fit-content; /* Se ajusta al contenido */
            margin: 0 auto 1.5rem auto; /* Centrar y añadir margen inferior */
        }
        .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
            <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
        </a>
        <h1 class="page-title"><i class="fas fa-network-wired me-2"></i> Dispositivos Registrados</h1>
        <ul class="list-group">
            <?php if (empty($uniqueMacs)): ?>
                <p class="no-devices-message">No hay dispositivos registrados.</p>
            <?php else: ?>
                <?php foreach ($uniqueMacs as $macData): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('/dispositivo/' . esc($macData['mac'])) ?>">
                            <i class="fas fa-hdd me-2"></i>
                            <strong><?= esc($macData['nombre']) ?></strong>
                            <span class="text-muted ms-3">(<?= esc($macData['mac']) ?>)</span>
                        </a>
                        <span class="badge">Ubicación: <?= esc($macData['ubicacion']) ?></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>