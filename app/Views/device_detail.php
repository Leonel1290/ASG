<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Dispositivo - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos generales para el cuerpo */
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Alinea al inicio, no al centro */
            align-items: center;
            padding-top: 2rem; /* Espacio desde la parte superior */
            padding-bottom: 2rem;
        }

        /* Contenedor principal */
        .container {
            background-color: #2d3748;
            border-radius: 0.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
            max-width: 800px; /* Ancho máximo para el contenido */
            width: 100%;
            margin-top: 2rem; /* Margen superior para separarse de la cabecera si la tuvieras */
        }

        /* Títulos */
        .page-title {
            color: #f7fafc;
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-title i {
            margin-right: 0.75rem;
            color: #4299e1; /* Color de ícono azul */
        }

        /* Estilo para las tarjetas de información */
        .info-card {
            background-color: #4a5568;
            color: #edf2f7;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .info-card h3 {
            color: #f7fafc;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #667eea;
            padding-bottom: 0.5rem;
        }
        .info-card p {
            margin-bottom: 0.5rem;
        }
        .info-card strong {
            color: #bee3f8; /* Un azul más claro para las etiquetas */
        }

        /* Estilos para la tabla de lecturas */
        .table {
            background-color: #4a5568;
            color: #edf2f7;
            border-radius: 0.5rem;
            overflow: hidden; /* Para que los bordes de la tabla se ajusten al radio */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .table th, .table td {
            border-color: #2d3748; /* Bordes de celdas más oscuros */
            vertical-align: middle;
        }
        .table thead th {
            background-color: #2d3748;
            color: #f7fafc;
            font-weight: bold;
            text-align: center;
        }
        .table tbody tr:nth-child(even) {
            background-color: #5b677a; /* Color para filas pares */
        }
        .table tbody tr:hover {
            background-color: #667eea; /* Color al pasar el mouse */
            color: #fff;
        }
        .table-responsive {
            margin-bottom: 1.5rem;
        }

        /* Mensajes de "No hay lecturas" */
        .no-data-message {
            text-align: center;
            color: #a0aec0;
            font-style: italic;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #4a5568;
            border-radius: 0.5rem;
        }

        /* Botón de volver */
        .btn-back {
            background-color: #6b7280;
            border-color: #6b7280;
            color: white;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            margin-top: 1rem; /* Espacio por encima */
            display: inline-flex; /* Para alinear el ícono */
            align-items: center;
        }
        .btn-back:hover {
            background-color: #4a5568;
            border-color: #4a5568;
            color: white;
        }
        .btn-back i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= base_url('/perfil') ?>" class="btn btn-back mb-4">
            <i class="fas fa-arrow-left"></i> Volver al Perfil
        </a>

        <h1 class="page-title"><i class="fas fa-microchip"></i> Detalles del Dispositivo</h1>

        <?php if (!empty($dispositivo)): ?>
            <div class="info-card">
                <h3>Información del Dispositivo</h3>
                <p><strong>Nombre:</strong> <?= esc($dispositivo['nombre'] ?? 'N/A') ?></p>
                <p><strong>MAC:</strong> <?= esc($dispositivo['MAC'] ?? 'N/A') ?></p>
                <p><strong>Ubicación:</strong> <?= esc($dispositivo['ubicacion'] ?? 'N/A') ?></p>
                <p><strong>Descripción:</strong> <?= esc($dispositivo['descripcion'] ?? 'Sin descripción') ?></p>
                <p><strong>Fecha de Registro:</strong> <?= esc($dispositivo['fecha_registro'] ?? 'N/A') ?></p>
            </div>

            <div class="info-card">
                <h3>Últimas Lecturas de Gas</h3>
                <?php if (!empty($lecturas)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Valor (PPM)</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lecturas as $lectura): ?>
                                    <tr>
                                        <td class="text-center"><?= esc($lectura['valor_gas_ppm'] ?? 'N/A') ?></td>
                                        <td class="text-center"><?= esc($lectura['fecha_hora'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data-message">No hay lecturas de gas disponibles para este dispositivo.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle me-2"></i> No se encontró el dispositivo o no tienes acceso.
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>