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
    <link rel="stylesheet" href="https://cdnjs->cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            background-color: #2d3748 !important;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
            font-size: 1.4rem;
        }

        .navbar-nav .nav-link {
            color: #cbd5e0 !important;
            font-size: 1.1rem;
            padding-top: .75rem;
            padding-bottom: .75rem;
        }

        .navbar-nav .nav-link.active {
            color: #4299e1 !important;
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
        }
        /* --- END NAVBAR IMPROVEMENTS --- */


        /* --- BUTTON IMPROVEMENTS (using Bootstrap classes and tailoring where needed) --- */

        /* Logout Button (already decent, kept custom styles for outline variant) */
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
        }
        .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }

        /* Override Bootstrap default colors to match dark theme palette */
        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
            color: white;
        }

        .btn-success {
            background-color: #48bb78;
            border-color: #48bb78;
            color: white;
        }
        .btn-success:hover {
            background-color: #38a169;
            border-color: #38a169;
            color: white;
        }

        .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c53030;
            border-color: #c53030;
            color: white;
        }

        .btn-info {
            background-color: #4299e1;
            border-color: #4299e1;
            color: white;
        }
        .btn-info:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
            color: white;
        }

        /* Specific style for the add MAC form button - using btn-success */
        #add-mac-form .btn-success {
            margin-top: 0.5rem;
        }

        /* --- END BUTTON IMPROVEMENTS --- */


        /* Contenedor principal del contenido */
        .container {
            flex: 1;
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        /* Card styles (kept as is, they fit the dark theme) */
        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #4a5568;
            color: #edf2f7;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2d3748;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .card-title i {
            margin-right: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Alert styles (kept as is) */
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

        .alert-info {
            background-color: #bee3f8;
            color: #1a202c;
            border-color: #90cdf4;
        }

        /* Devices Section Title (kept as is) */
        .devices-section-title {
            color: #edf2f7;
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .devices-section-title i {
            margin-right: 0.5rem;
        }

        /* Add MAC Form (kept most styles, added Bootstrap form control style) */
        #add-mac-form {
            background-color: #4a5568;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        #add-mac-form label {
            color: #edf2f7;
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Ensure Bootstrap form-control styles apply */
        #add-mac-form .form-control {
            width: 100%;
            padding: 0.75rem;
            background-color: #2d3748;
            border: 1px solid #718096;
            border-radius: 0.375rem;
            color: #edf2f7;
            box-sizing: border-box;
            margin-bottom: 1rem;
        }
        /* Style for Bootstrap's default focus ring in dark mode */
        #add-mac-form .form-control:focus {
            background-color: #2d3748;
            color: #edf2f7;
            border-color: #63b3ed;
            box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25);
        }


        /* Delete Devices Form wrapper */
        #delete-devices-form {
            margin-top: 1.5rem;
        }

        /* Device List and Item styles (kept as is, they look good) */
        .device-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .device-item {
            background-color: #2d3748;
            border: 1px solid #4a5568;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .device-item:hover {
            box-shadow: 0 0 10px rgba(66, 153, 225, 0.4);
        }

        .device-info {
            flex-grow: 1;
            margin-right: 1rem;
        }

        .device-name {
            font-size: 1.1rem;
            font-weight: bold;
            color: #edf2f7;
            margin-bottom: 0.25rem;
        }

        .device-details {
            font-size: 0.9rem;
            color: #a0aec0;
        }

        .device-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Custom Checkbox styles for dark theme */
        .delete-checkbox {
            width: 1.2em;
            height: 1.2em;
            vertical-align: middle;
            cursor: pointer;
            background-color: #4a5568;
            border: 1px solid #718096;
            border-radius: 0.25em;
            appearance: none;
            -webkit-appearance: none;
            position: relative;
            flex-shrink: 0;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .delete-checkbox:checked {
            background-color: #48bb78;
            border-color: #48bb78;
        }

        .delete-checkbox:focus {
            outline: none;
            box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25);
        }

        /* Add custom checkmark using Font Awesome */
        .delete-checkbox:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: white;
            font-size: 0.8em;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* *** INICIO: ESTILOS DE TARJETA DE PRODUCTO Y RIBBON *** */
        .product-card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            text-align: center;
            overflow: hidden;
            position: relative; /* Necesario para el ribbon absoluto */
            margin-bottom: 2rem;
            height: 100%; /* Para que las columnas tengan la misma altura */
        }

        .discount-ribbon {
            position: absolute;
            top: 0;
            left: 0;
            background-color: #4299e1; /* Color para destacar */
            color: white;
            padding: 0.25rem 0.75rem;
            border-bottom-right-radius: 0.5rem;
            font-weight: bold;
            font-size: 0.9rem;
            z-index: 10;
            cursor: pointer; /* <-- IMPORTANTE: Hace que sea clickeable */
            transition: background-color 0.2s;
        }

        .discount-ribbon:hover {
            background-color: #2b6cb0;
        }

        .product-image-container {
            padding: 1rem;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        /* *** FIN: ESTILOS DE TARJETA DE PRODUCTO Y RIBBON *** */


        /* Modal styles (kept as is, fit dark theme) */
        .modal-content {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
        }

        .modal-header {
            background-color: #4a5568;
            border-bottom: 1px solid #2d3748;
            color: #edf2f7;
        }

        .modal-footer {
            border-top: 1px solid #2d3748;
        }

        .modal-footer .btn-secondary {
            background-color: #6b7280;
            border-color: #6b7280;
            color: white;
        }

        .modal-footer .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
            color: white;
        }

        /* Ensure close button color is visible in dark modal header */
        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }


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
                <a class="navbar-brand" href="<?= base_url('/perfil') ?>">ASG</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil') ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion') ?>">Configuración</a>
                        </li>
                    </ul>

                    <form action="<?= base_url('/logout') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <div class="container my-5">

        <?php if (session('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
        <?php endif; ?>
        <?php if (session('info')): ?>
            <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i> <?= session('info') ?></div>
        <?php endif; ?>


        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-user-circle me-2"></i> Mi Perfil</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?= esc(session()->get('nombre')) ?></p>
                <p><strong>Email:</strong> <?= esc(session()->get('email')) ?></p>
            </div>
        </div>

        <div class="devices-section-title">
            <h2 style="margin: 0;"><i class="fas fa-microchip me-2"></i> Mis Dispositivos Enlazados</h2>
            <button id="show-add-mac-form" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 col-lg-3">
                <div class="product-card">
                    <div class="discount-ribbon" data-bs-toggle="modal" data-bs-target="#otherDevicesModal">
                        ASG Sentinel
                    </div>

                    <div class="product-image-container">
                        <img src="<?= base_url('/imagenes/ASG_SENTINEL.jpg') ?>" class="product-image" alt="ASG Sentinel">
                    </div>

                    <div class="card-body p-3">
                        <h5 class="card-title text-center" style="color: #4299e1;">ASG Sentinel</h5>
                        <p class="card-text text-muted" style="font-size: 0.9rem;">
                            Dispositivo de monitoreo avanzado para seguridad perimetral. (Ejemplo Estático)
                        </p>
                        <a href="#" class="btn btn-info btn-sm w-100 mt-2">Ver Información</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="add-mac-form" style="display: none;">
            <form action="<?= base_url('/enlace/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="mac">Dirección MAC:</label>
                    <input type="text" class="form-control" id="mac" name="mac" placeholder="Ej: XX:XX:XX:XX:XX:XX" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-link me-2"></i> Enlazar Dispositivo</button>
            </form>
            <hr class="my-4" style="border-color: #4a5568;">
        </div>


        <?php if (empty($dispositivosEnlazados)): ?>
            <p>No tienes dispositivos enlazados aún.</p>
        <?php else: ?>
            <form id="delete-devices-form" action="<?= base_url('/perfil/eliminar-dispositivos') ?>" method="post">
                <?= csrf_field() ?>
                <button type="button" id="delete-selected-btn" class="btn btn-danger mb-3">
                    <i class="fas fa-trash-alt me-2"></i> Eliminar Seleccionados
                </button>

                <ul class="device-list">
                    <?php foreach ($dispositivosEnlazados as $dispositivo): ?>
                        <li class="device-item">
                            <div class="device-info">
                                <div class="device-name"><?= esc($dispositivo->nombre ?: 'Dispositivo sin nombre') ?></div>
                                <div class="device-details">
                                    MAC: <?= esc($dispositivo->MAC ?? 'Desconocida') ?> |
                                    Ubicación: <?= esc($dispositivo->ubicacion ?: 'Desconocida') ?>
                                </div>
                            </div>
                            <div class="device-actions">
                                <input type="checkbox" name="macs[]" value="<?= esc($dispositivo->MAC) ?>" class="delete-checkbox">
                                <a href="<?= base_url('/perfil/dispositivo/editar/' . esc($dispositivo->MAC)) ?>" class="btn btn-primary btn-sm" title="Editar Dispositivo">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="<?= base_url('/detalles/' . esc($dispositivo->MAC)) ?>" class="btn btn-info btn-sm" title="Ver Detalles">
                                    <i class="fas fa-chart-bar"></i> Ver Detalles
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </form>
        <?php endif; ?>

        <div class="modal fade" id="otherDevicesModal" tabindex="-1" aria-labelledby="otherDevicesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otherDevicesModalLabel"><i class="fas fa-grip-horizontal me-2"></i> Otros Dispositivos ASG</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Descubre otros dispositivos de la línea ASG disponibles:</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item" style="background-color: #2d3748; color: #fff; border-color: #4a5568;">ASG Medial</li>
                            <li class="list-group-item" style="background-color: #2d3748; color: #fff; border-color: #4a5568;">ASG Uranus</li>
                            <li class="list-group-item" style="background-color: #2d3748; color: #fff; border-color: #4a5568;">ASG Blaze</li>
                            <li class="list-group-item" style="background-color: #2d3748; color: #fff; border-color: #4a5568;">ASG Aero</li>
                            <li class="list-group-item" style="background-color: #2d3748; color: #fff; border-color: #4a5568;">ASG Cosma</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas desenlazar los dispositivos seleccionados?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirm-delete-btn">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Script para manejar la visibilidad del formulario de añadir MAC y el modal de eliminación
        document.addEventListener('DOMContentLoaded', function () {
            const deleteSelectedBtn = document.getElementById('delete-selected-btn');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            const deleteDevicesForm = document.getElementById('delete-devices-form');
            const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
            const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement); // Get Bootstrap modal instance
            const addMacForm = document.getElementById('add-mac-form');
            const showAddMacFormButton = document.getElementById('show-add-mac-form');

            // Mostrar/ocultar formulario de añadir MAC
            if (showAddMacFormButton && addMacForm) { // Check if both elements exist
                showAddMacFormButton.addEventListener('click', function () {
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
                        // Reemplazado alert() con un modal de mensaje simple
                        const messageModalContent = document.querySelector('#confirmDeleteModal .modal-body');
                        const messageModalTitle = document.querySelector('#confirmDeleteModal .modal-title');
                        const messageModalFooter = document.querySelector('#confirmDeleteModal .modal-footer');

                        if (messageModalContent && messageModalTitle && messageModalFooter) {
                            messageModalTitle.textContent = "Atención";
                            messageModalContent.textContent = 'Por favor, selecciona al menos un dispositivo para eliminar.';
                            // Ocultar botones de acción y mostrar solo el de cerrar si no hay selección
                            messageModalFooter.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
                            confirmDeleteModal.show();
                        } else {
                             // Fallback si los elementos del modal no se encuentran
                            console.error("No se pudieron encontrar elementos del modal de mensaje.");
                             // Evita alert() en producción
                        }
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