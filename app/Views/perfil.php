<?php
// Ensure these variables are set, even if empty arrays, to avoid undefined variable errors
$dispositivosEnlazados = $dispositivosEnlazados ?? [];
$lecturasPorMac = $lecturasPorMac ?? []; // This variable doesn't seem used in the provided code, but keep the null coalesce operator just in case.
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General body styles */
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

        /* --- NAVBAR IMPROVEMENTS --- */
        .navbar {
            background-color: #2d3748 !important; /* Dark background */
            padding-top: 1rem; /* More vertical padding */
            padding-bottom: 1rem;
        }

        .navbar-brand {
            color: #fff !important; /* White color for brand */
            font-size: 1.5rem; /* Larger font size */
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #ccc !important; /* Slightly lighter on hover */
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        .nav-link {
            color: #cbd5e0 !important; /* Light text for links */
            font-size: 1.1rem;
            padding-left: 1rem !important; /* Space between links on larger screens */
            padding-right: 1rem !important;
        }

        .nav-link:hover {
            color: #fff !important; /* White on hover */
        }
        /* --- END NAVBAR IMPROVEMENTS --- */

        .profile-container {
            flex: 1; /* Allows the container to grow and fill available space */
            padding: 2rem;
            max-width: 900px;
            margin-top: 20px; /* Space for the fixed navbar */
            margin-left: auto;
            margin-right: auto;
        }

        .profile-header {
            background-color: #2d3748;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header h1 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            color: #a0aec0;
            font-size: 1.1rem;
        }

        .section-card {
            background-color: #2d3748;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .section-card-header {
            background-color: #4a5568;
            border-bottom: none;
            color: #fff;
            font-weight: bold;
            padding: 1rem 1.5rem;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .section-card-body {
            padding: 1.5rem;
        }

        .device-item {
            background-color: #4a5568;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; /* Allow items to wrap on small screens */
        }

        .device-item:last-child {
            margin-bottom: 0;
        }

        .device-details {
            flex-grow: 1;
            margin-right: 1rem;
        }

        .device-details h5 {
            color: #fff;
            margin-bottom: 0.25rem;
        }

        .device-details p {
            color: #a0aec0;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .device-actions .btn {
            margin-left: 0.5rem;
            margin-top: 0.5rem; /* Space between buttons on wrap */
        }

        .btn-primary {
            background-color: #4CAF50; /* Green */
            border-color: #4CAF50;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .btn-info {
            background-color: #3182ce;
            border-color: #3182ce;
            transition: background-color 0.3s ease;
        }

        .btn-info:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }

        .btn-warning {
            background-color: #ecc94b;
            border-color: #ecc94b;
            color: #333; /* Darker text for contrast */
            transition: background-color 0.3s ease;
        }

        .btn-warning:hover {
            background-color: #d69e2e;
            border-color: #d69e2e;
        }

        .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
            transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #c53030;
            border-color: #c53030;
        }

        .btn-outline-secondary {
            border-color: #a0aec0;
            color: #a0aec0;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background-color: #a0aec0;
            color: #2d3748;
        }

        /* Form para añadir dispositivo (inicialmente oculto) */
        #addMacForm {
            display: none;
            padding: 1.5rem;
            background-color: #374151; /* Ligeramente más claro para el form */
            border-radius: 0.5rem;
            margin-top: 1.5rem;
        }

        #addMacForm label {
            color: #cbd5e0;
        }

        #addMacForm .form-control {
            background-color: #4a5568;
            border-color: #6b7280;
            color: #fff;
        }

        #addMacForm .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
        }

        .invalid-feedback {
            color: #fc8181;
        }

        .form-check-input:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        /* Modal specific styles */
        .modal-content {
            background-color: #2d3748;
            color: #fff;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #4a5568;
            color: #fff;
        }

        .modal-footer {
            border-top: 1px solid #4a5568;
        }

        .btn-close {
            filter: invert(1); /* Makes the close button white */
        }
        .text-truncate-custom {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px; /* Adjust as needed */
            display: inline-block;
            vertical-align: middle;
        }
        .status-circle {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
        .status-ok { background-color: #4CAF50; }
        .status-danger { background-color: #f56565; }
        .status-unknown { background-color: #ecc94b; }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/perfil'); ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil'); ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion'); ?>">Configuración</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/logout'); ?>">Cerrar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="profile-container">
        <div class="profile-header">
            <h1>Bienvenido, <?= esc(session()->get('user_name')) ?>!</h1>
            <p>Este es tu panel de control. Gestiona tus dispositivos y configuración.</p>
        </div>

        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="section-card">
            <div class="section-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-microchip me-2"></i> Mis Dispositivos Enlazados</span>
                <button class="btn btn-sm btn-primary" id="toggleAddMacForm"><i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo</button>
            </div>
            <div class="section-card-body">
                <div id="addMacForm">
                    <form action="<?= base_url('/dispositivos/enlazar') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="mac_address" class="form-label">Dirección MAC del Dispositivo:</label>
                            <input type="text" class="form-control <?= isset($errors['mac_address']) ? 'is-invalid' : '' ?>" id="mac_address" name="mac_address" placeholder="AA:BB:CC:DD:EE:FF" value="<?= old('mac_address') ?>" required pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" title="Formato: AA:BB:CC:DD:EE:FF o AA-BB-CC-DD-EE-FF">
                            <?php if (isset($errors['mac_address'])): ?>
                                <div class="invalid-feedback">
                                    <?= esc($errors['mac_address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_dispositivo" class="form-label">Nombre del Dispositivo:</label>
                            <input type="text" class="form-control <?= isset($errors['nombre_dispositivo']) ? 'is-invalid' : '' ?>" id="nombre_dispositivo" name="nombre_dispositivo" placeholder="Ej. Detector Cocina" value="<?= old('nombre_dispositivo') ?>" required>
                             <?php if (isset($errors['nombre_dispositivo'])): ?>
                                <div class="invalid-feedback">
                                    <?= esc($errors['nombre_dispositivo']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion_dispositivo" class="form-label">Ubicación (opcional):</label>
                            <input type="text" class="form-control <?= isset($errors['ubicacion_dispositivo']) ? 'is-invalid' : '' ?>" id="ubicacion_dispositivo" name="ubicacion_dispositivo" placeholder="Ej. Cocina, Sala, etc." value="<?= old('ubicacion_dispositivo') ?>">
                             <?php if (isset($errors['ubicacion_dispositivo'])): ?>
                                <div class="invalid-feedback">
                                    <?= esc($errors['ubicacion_dispositivo']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-link me-2"></i> Enlazar Dispositivo</button>
                    </form>
                </div>

                <?php if (empty($dispositivosEnlazados)): ?>
                    <p class="text-center mt-3">Aún no tienes dispositivos enlazados. ¡Añade el primero!</p>
                <?php else: ?>
                    <form id="delete-devices-form" action="<?= base_url('/dispositivos/desenlazar-multiple') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php foreach ($dispositivosEnlazados as $dispositivo): ?>
                            <div class="device-item">
                                <div class="form-check me-3">
                                    <input class="form-check-input delete-checkbox" type="checkbox" name="macs_to_delete[]" value="<?= esc($dispositivo['MAC']) ?>" id="check-<?= esc($dispositivo['MAC']) ?>">
                                    <label class="form-check-label" for="check-<?= esc($dispositivo['MAC']) ?>"></label>
                                </div>
                                <div class="device-details">
                                    <h5>
                                        <?php
                                            $lastReading = null;
                                            // Asumiendo que $dispositivo['ultima_lectura'] ya contiene el nivel de gas
                                            $nivelGas = $dispositivo['ultima_lectura'] ?? null;
                                            $statusClass = 'status-unknown';

                                            if ($nivelGas !== null) {
                                                $nivelGas = (float)$nivelGas;
                                                if ($nivelGas <= 500) { // Umbral de seguridad (ajusta según tus necesidades)
                                                    $statusClass = 'status-ok';
                                                } else {
                                                    $statusClass = 'status-danger';
                                                }
                                            }
                                        ?>
                                        <span class="status-circle <?= $statusClass ?>"></span>
                                        <?= esc($dispositivo['nombre_dispositivo']) ?>
                                    </h5>
                                    <p>MAC: <span class="text-truncate-custom"><?= esc($dispositivo['MAC']) ?></span></p>
                                    <p>Ubicación: <?= esc($dispositivo['ubicacion'] ?: 'No especificada') ?></p>
                                    <p>Nivel Actual: <?= ($nivelGas !== null) ? esc($nivelGas) . ' PPM' : 'Sin datos' ?></p>
                                </div>
                                <div class="device-actions d-flex flex-wrap justify-content-end">
                                    <a href="<?= base_url('/dispositivo/' . esc($dispositivo['MAC'])) ?>" class="btn btn-info btn-sm mb-2 mb-md-0 me-2"><i class="fas fa-chart-line me-1"></i> Ver Detalles</a>
                                    <a href="<?= base_url('/dispositivos/editar/' . esc($dispositivo['MAC'])) ?>" class="btn btn-warning btn-sm mb-2 mb-md-0 me-2"><i class="fas fa-edit me-1"></i> Editar</a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteSingleModal" data-mac="<?= esc($dispositivo['MAC']) ?>"><i class="fas fa-unlink me-1"></i> Desenlazar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-danger" id="deleteSelectedBtn"><i class="fas fa-trash-alt me-2"></i> Eliminar Seleccionados</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteSingleModal" tabindex="-1" aria-labelledby="confirmDeleteSingleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteSingleModalLabel">Confirmar Desenlace</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres desenlazar este dispositivo? Esta acción es irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="confirmSingleDeleteBtn" class="btn btn-danger">Desenlazar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación Masiva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar los dispositivos seleccionados? Esta acción es irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleAddMacFormBtn = document.getElementById('toggleAddMacForm');
            const addMacForm = document.getElementById('addMacForm');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
            const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const deleteDevicesForm = document.getElementById('delete-devices-form');

            // Para el modal de eliminación de un solo dispositivo
            const confirmDeleteSingleModalElement = document.getElementById('confirmDeleteSingleModal');
            const confirmSingleDeleteBtn = document.getElementById('confirmSingleDeleteBtn');

            if (confirmDeleteSingleModalElement) {
                confirmDeleteSingleModalElement.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget; // Button that triggered the modal
                    const mac = button.getAttribute('data-mac'); // Extract info from data-mac attribute
                    const actionUrl = '<?= base_url('/dispositivos/desenlazar/') ?>' + mac;
                    confirmSingleDeleteBtn.setAttribute('href', actionUrl);
                });
            }

            // Toggle add device form
            if (toggleAddMacFormBtn && addMacForm) {
                toggleAddMacFormBtn.addEventListener('click', function () {
                    if (addMacForm.style.display === "none" || addMacForm.style.display === "") {
                        addMacForm.style.display = "block"; // Show
                        this.innerHTML = '<i class="fas fa-minus-circle me-2"></i> Ocultar Formulario'; // Change text
                    } else {
                        addMacForm.style.display = "none"; // Hide
                        this.innerHTML = '<i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo'; // Change text
                    }
                });
            }


            // Mostrar modal de confirmación al hacer clic en "Eliminar Seleccionados"
            if (deleteSelectedBtn && confirmDeleteModalElement) { // Check if button and modal element exist
                 deleteSelectedBtn.addEventListener('click', function () {
                     const checkedDevices = document.querySelectorAll('#delete-devices-form .delete-checkbox:checked');
                     if (checkedDevices.length > 0) {
                         confirmDeleteModal.show(); // Use Bootstrap's show method
                     } else {
                         alert('Por favor, selecciona al menos un dispositivo para eliminar.');
                     }
                 });
             }


            // Enviar el formulario de eliminación cuando se confirma en el modal
            if (confirmDeleteBtn && deleteDevicesForm && confirmDeleteModalElement) { // Check if all elements exist
                 confirmDeleteBtn.addEventListener('click', function () {
                     confirmDeleteModal.hide(); // Use Bootstrap's hide method
                     deleteDevicesForm.submit();
                 });
             }

        });
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Fallo el registro de ServiceWorker:', error);
                    });
            });
        }
    </script>

</body>

</html>
