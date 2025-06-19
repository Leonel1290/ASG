<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Gas - <?= esc($nombreDispositivo) ?></title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
            padding: 2rem;
        }
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }
        .page-title {
            color: #f7fafc;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .device-details-line {
            text-align: center;
            color: #a0aec0;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .section-title {
            color: #edf2f7;
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 0.5rem;
        }
        .table-responsive {
            margin-bottom: 1.5rem;
        }
        .table {
            width: 100%;
            color: #e2e8f0;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #2d3748;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #4a5568;
        }
        .table th {
            background-color: #4a5568;
            color: #f7fafc;
            font-weight: bold;
        }
        .table tbody tr:nth-child(even) {
            background-color: #374151;
        }
        .table tbody tr:hover {
            background-color: #4a5568;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }
        .bg-success { background-color: #48bb78 !important; color: #2d3748; }
        .bg-warning { background-color: #f6e05e !important; color: #2d3748; }
        .bg-danger { background-color: #e53e3e !important; color: #edf2f7; }

        @media (max-width: 768px) {
            .page-title { font-size: 1.75rem; }
            .section-title { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <a href="<?= base_url('/dispositivos') ?>" class="btn btn-outline-secondary mb-4">
            <i class="fas fa-arrow-left me-2"></i> Volver a Dispositivos
        </a>

        <h1 class="page-title"><i class="fas fa-clipboard-list me-2"></i> Registros de Gas para: <span class="text-primary"><?= esc($nombreDispositivo) ?></span></h1>
        <p class="device-details-line">
            MAC: <?= esc($mac) ?> | Ubicación: <?= esc($ubicacionDispositivo) ?>
        </p>

        <h2 class="section-title"><i class="fas fa-list-alt me-2"></i> Historial de Lecturas</h2>
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-alt me-2"></i> Fecha</th>
                        <th><i class="fas fa-thermometer-half me-2"></i> Nivel de Gas (PPM)</th>
                        <th class="text-center"><i class="fas fa-exclamation-triangle me-2"></i> Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lecturas)): ?>
                        <tr><td colspan="3" class="text-center">No hay lecturas registradas para este dispositivo.</td></tr>
                    <?php else: ?>
                        <?php foreach ($lecturas as $lectura): ?>
                            <tr>
                                <td><?= esc($lectura['fecha'] ?? 'Fecha desconocida') ?></td>
                                <td><?= esc($lectura['nivel_gas'] ?? 'N/D') ?></td>
                                <td class="text-center">
                                    <?php
                                        $nivel = isset($lectura['nivel_gas']) ? (float) $lectura['nivel_gas'] : -1;
                                        $estado = 'Desconocido';
                                        $class = '';

                                        if ($nivel >= 500) {
                                            $estado = 'Peligro';
                                            $class = 'bg-danger';
                                        } elseif ($nivel >= 200) {
                                            $estado = 'Precaución';
                                            $class = 'bg-warning';
                                        } elseif ($nivel >= 0) {
                                            $estado = 'Seguro';
                                            $class = 'bg-success';
                                        }
                                    ?>
                                    <span class="badge <?= $class ?>"><?= $estado ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>