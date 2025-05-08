<?php  ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispositivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
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

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-0.25rem);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #4a5568;
            color: #edf2f7;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2d3748;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }

        .btn-success {
            background-color: #48bb78;
            border-color: #48bb78;
            transition: background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #38a169;
            border-color: #38a169;
        }

        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #1a202c;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background-color: #fed7d7;
            color: #1a202c;
            border-color: #fbcbcb;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e2e8f0;
            font-weight: bold;
        }

        .mac-input {
            width: 100%;
            padding: 0.75rem;
            background-color: #4a5568;
            border: 1px solid #718096;
            border-radius: 0.375rem;
            color: #edf2f7;
            box-sizing: border-box;
        }

        .mac-input::placeholder {
            color: #a0aec0;
        }

        .text-center {
            text-align: center;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .device-info {
            font-size: 1rem;
        }

        .device-info strong {
            font-weight: bold;
            color: #f7fafc;
        }

        .btn-details {
            display: block;
            width: 100%;
            text-align: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #fff;
            background-color: #667eea;
            transition: background-color 0.3s ease;
        }

        .btn-details:hover {
            background-color: #5a67d8;
        }

        .row.justify-content-center {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .col-md-4 {
            flex-basis: calc(50% - 1.5rem);
            max-width: calc(50% - 1.5rem);
        }

        @media (max-width: 768px) {
            .col-md-4 {
                flex-basis: 100%;
                max-width: 100%;
            }
        }

        /* Estilos para la funcionalidad de eliminar */
        .delete-checkbox {
            margin-right: 0.5rem;
        }

        .delete-btn {
            background-color: #e53e3e;
            border-color: #e53e3e;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c53030;
            border-color: #c53030;
        }

        #confirm-delete-btn {
            background-color: #4299e1;
            border-color: #4299e1;
            transition: background-color 0.3s ease;
        }

        #confirm-delete-btn:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }

        .delete-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        /* Nuevos estilos para los checkboxes */
        .form-check-input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #6b7280;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            background-color: #2d3748;
            position: relative;
        }

        .form-check-input[type="checkbox"]:checked {
            background-color: #4299e1;
            border-color: #4299e1;
        }

        .form-check-input[type="checkbox"]:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 14px;
            color: #fff;
            position: absolute;
            top: 1px;
            left: 3px;
        }

        .form-check-label {
            color: #e2e8f0;
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <div class="text-center mb-4">
            <h2 class="text-white mb-3"><i class="fas fa-link me-2"></i> Enlazar nueva MAC</h2>
            <form method="post" action="<?= base_url('/enlazar-mac') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="mac"><i class="fas fa-network-wired me-2"></i> Dirección MAC:</label>
                    <input type="text" class="form-control mac-input" id="mac" name="mac" placeholder="AA:BB:CC:DD:EE:FF"
                        required>
                </div>
                <button type="submit" class="btn btn-success mt-2"><i class="fas fa-plus-circle me-2"></i> Enlazar</button>
            </form>
        </div>

        <?php if (session('success')): ?>
            <div class="alert alert-success mt-3"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?>
            </div>
        <?php endif; ?>

        <h2 class="mb-4 text-white text-center"><i class="fas fa-server me-2"></i> Dispositivos</h2>

        <div class="delete-section">
            <button type="button" class="btn btn-danger" id="delete-selected-btn"><i class="fas fa-trash-alt me-2"></i>
                Eliminar</button>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label text-white" for="select-all">Seleccionar Todos</label>
            </div>
        </div>


        <form id="delete-devices-form" method="post" action="<?= base_url('/perfil/eliminar-dispositivos') ?>">
            <?= csrf_field() ?>
            <div class="row justify-content-center">
                <?php if (empty($macs)): ?>
                    <div class="col-12 text-center text-white">
                        <p><i class="fas fa-info-circle me-2"></i> No hay dispositivos enlazados aún.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($macs as $index => $macObj):
                        $mac = $macObj['MAC'];
                        $lects = $lecturasPorMac[$mac] ?? [];
                        $nombre = "Dispositivo " . ($index + 1);
                        $ubicacion = "Ubicación " . ($index + 1);
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <input type="checkbox" class="form-check-input delete-checkbox" name="macs[]"
                                            value="<?= esc($mac) ?>">
                                        <i class="fas fa-microchip me-2"></i> <?= esc($mac) ?>
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <p class="card-text"><i class="fas fa-map-marker-alt me-2"></i> Ubicación: <span
                                            class="fw-bold">Desconocida</span></p>
                                    <a href="<?= base_url('/detalles/' . urlencode($mac)) ?>"
                                        class="btn btn-primary btn-details"><i class="fas fa-chart-line me-2"></i> Ver
                                        detalles</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </form>
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar los dispositivos seleccionados?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirm-delete-btn">Confirmar Eliminación</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteSelectedBtn = document.getElementById('delete-selected-btn');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            const deleteDevicesForm = document.getElementById('delete-devices-form');
            const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            const selectAllCheckbox = document.getElementById('select-all');
            const deleteCheckboxes = document.querySelectorAll('.delete-checkbox');

            deleteSelectedBtn.addEventListener('click', function () {
                const checkedDevices = document.querySelectorAll('.delete-checkbox:checked');
                if (checkedDevices.length > 0) {
                    confirmDeleteModal.show();
                } else {
                    alert('Por favor, selecciona al menos un dispositivo para eliminar.');
                }
            });

            confirmDeleteBtn.addEventListener('click', function () {
                confirmDeleteModal.hide();
                deleteDevicesForm.submit();
            });

            // Seleccionar/Deseleccionar todos los checkboxes
            selectAllCheckbox.addEventListener('change', function () {
                deleteCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Evitar que "Seleccionar Todos" esté marcado si no están todos los dispositivos seleccionados
            deleteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    let allChecked = true;
                    deleteCheckboxes.forEach(check => {
                        if (!check.checked) {
                            allChecked = false;
                        }
                    });
                    selectAllCheckbox.checked = allChecked;
                });
            });
        });
    </script>
</body>

</html>
