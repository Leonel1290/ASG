<?php
// Asegurar que las variables estén definidas para evitar errores de CodeIgniter/PHP
$dispositivosEnlazados = $dispositivosEnlazados ?? [];
$lecturasPorMac = $lecturasPorMac ?? [];
$modelos_catalogo = $modelos_catalogo ?? []; // <-- Variable del catálogo
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

        /* --- NAVBAR STYLES --- */
        .navbar {
            background-color: #2d3748;
            border-bottom: 1px solid #4a5568;
        }

        .navbar .nav-link,
        .navbar .navbar-brand {
            color: #cbd5e0 !important;
        }

        .navbar .nav-link:hover {
            color: #48bb78 !important;
        }

        /* --- CONTAINER AND CARD STYLES --- */
        .container-fluid {
            padding-top: 20px;
        }

        .card {
            background-color: #2d3748;
            border: 1px solid #4a5568;
            border-radius: 0.5rem;
            color: #cbd5e0;
        }

        .card-header {
            background-color: #4a5568;
            border-bottom: 1px solid #4a5568;
        }

        .device-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .device-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
        }

        /* --- STATUS AND BUTTON STYLES --- */
        .status-badge {
            font-size: 0.75rem;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
        }

        .status-on {
            background-color: #48bb78;
            color: #1a202c;
        }

        .status-off {
            background-color: #f56565;
            color: #1a202c;
        }

        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
        }

        .btn-primary:hover {
            background-color: #3182ce;
            border-color: #3182ce;
        }

        .btn-success {
            background-color: #48bb78;
            border-color: #48bb78;
        }

        .btn-success:hover {
            background-color: #38a169;
            border-color: #38a169;
        }

        .btn-danger {
            background-color: #f56565;
            border-color: #f56565;
        }

        .btn-danger:hover {
            background-color: #e53e3e;
            border-color: #e53e3e;
        }

        .btn-info {
            background-color: #4a5568;
            border-color: #4a5568;
            color: #cbd5e0;
        }

        .btn-info:hover {
            background-color: #2d3748;
            border-color: #2d3748;
        }

        /* Custom Checkbox Style for dark background */
        .form-check-input:checked {
            background-color: #48bb78;
            border-color: #48bb78;
        }
        
        /* Modal dark theme */
        .modal-content {
            background-color: #2d3748;
            color: #cbd5e0;
        }

        .modal-header,
        .modal-footer {
            border-color: #4a5568;
        }

        .btn-close-white {
            filter: invert(1);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/'); ?>">
                <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo ASG" width="30" height="30" class="d-inline-block align-text-top">
                ASG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/dashboard'); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil'); ?>">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/comprar'); ?>">Comprar</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/logout'); ?>">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid flex-grow-1 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <h1 class="text-center mb-5 text-success">Bienvenido a tu Perfil</h1>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card mb-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-user-cog me-2"></i>Configuración de Cuenta</h4>
                        <a href="<?= base_url('/perfil/configuracion'); ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Aquí puedes gestionar tu nombre, email y contraseña.</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white">Mis Dispositivos Enlazados</h2>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                        <i class="fas fa-link"></i> Enlazar Nuevo Dispositivo
                    </button>
                </div>

                <form id="deleteDevicesForm" action="<?= base_url('perfil/dispositivo/eliminar') ?>" method="post">
                    <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                        <?php if (empty($dispositivosEnlazados)) : ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-dark" role="alert">
                                    Aún no tienes dispositivos enlazados. ¡Enlaza uno para empezar a monitorear!
                                </div>
                            </div>
                        <?php else : ?>
                            <?php foreach ($dispositivosEnlazados as $dispositivo) : ?>
                                <div class="col">
                                    <div class="card device-card">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title text-success"><?= esc($dispositivo->nombre) ?></h5>
                                                <span class="status-badge <?= ($dispositivo->estado_valvula ?? 0) ? 'status-on' : 'status-off' ?>">
                                                    <?= ($dispositivo->estado_valvula ?? 0) ? 'Válvula Abierta' : 'Válvula Cerrada' ?>
                                                </span>
                                            </div>
                                            <p class="card-text mb-1">
                                                <strong>MAC:</strong> <?= esc($dispositivo->MAC) ?>
                                            </p>
                                            <p class="card-text mb-3">
                                                <strong>Ubicación:</strong> <?= esc($dispositivo->ubicacion ?? 'No especificada') ?>
                                            </p>
                                            <p class="card-text mb-3">
                                                <strong>Modelo:</strong> <?= esc($dispositivo->modelo ?? 'ASG Sentinel') ?>
                                            </p>
                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input delete-checkbox" type="checkbox" name="macs[]" value="<?= esc($dispositivo->MAC) ?>" id="check-<?= esc($dispositivo->MAC) ?>">
                                                    <label class="form-check-label" for="check-<?= esc($dispositivo->MAC) ?>">
                                                        Desenlazar
                                                    </label>
                                                </div>
                                                <div>
                                                    <a href="<?= base_url('detalles/' . esc($dispositivo->MAC)); ?>" class="btn btn-primary btn-sm me-2">
                                                        <i class="fas fa-chart-line"></i> Ver Detalles
                                                    </a>
                                                    <a href="<?= base_url('perfil/dispositivo/editar/' . esc($dispositivo->MAC)); ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-pencil-alt"></i> Editar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($dispositivosEnlazados)) : ?>
                        <div class="text-end mb-5">
                            <button type="button" id="deleteSelectedBtn" class="btn btn-danger" disabled>
                                <i class="fas fa-trash-alt"></i> Desenlazar Seleccionados
                            </button>
                        </div>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    </div>

    <footer class="bg-dark text-center text-white py-3 mt-auto border-top border-secondary">
        <p class="mb-0">&copy; <?= date('Y'); ?> ASG (Again Safe Gas). Todos los derechos reservados.</p>
    </footer>


    <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="addDeviceModalLabel"><i class="fas fa-link me-2"></i>Enlazar Nuevo Dispositivo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="<?= base_url('enlace/store') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="deviceModel" class="form-label">Modelo de Dispositivo:</label>
                            <select class="form-select bg-secondary text-light border-dark" id="deviceModel" name="modelo" required>
                                <option value="" disabled selected>Seleccione un modelo</option>
                                <?php foreach ($modelos_catalogo as $modelo) : ?>
                                    <option value="<?= esc($modelo->nombre_modelo) ?>"><?= esc($modelo->nombre_modelo) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Seleccione el modelo correcto para el dispositivo que desea enlazar.</small>
                        </div>

                        <div class="mb-3">
                            <label for="mac_address" class="form-label">Dirección MAC:</label>
                            <input type="text" class="form-control bg-secondary text-light border-dark" id="mac_address" name="mac" placeholder="Ej: AA:BB:CC:DD:EE:FF" required maxlength="17" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" title="Formato MAC: XX:XX:XX:XX:XX:XX">
                            <small class="form-text text-muted">Ingrese la dirección MAC (con dos puntos o guiones) que figura en su dispositivo.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                             <i class="fas fa-link"></i> Enlazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Desenlace</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessageText">¿Está seguro de que desea desenlazar los dispositivos seleccionados? Perderá el acceso a su monitoreo.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i class="fas fa-trash-alt"></i> Desenlazar</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.delete-checkbox');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            const deleteDevicesForm = document.getElementById('deleteDevicesForm');
            const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const modalMessageText = document.getElementById('modalMessageText');

            // Inicializar modal de Bootstrap
            let confirmDeleteModal;
            if (confirmDeleteModalElement) {
                confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
            }

            // Función para actualizar el estado del botón de desenlazar
            function updateDeleteButtonState() {
                const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
                if (deleteSelectedBtn) {
                    deleteSelectedBtn.disabled = checkedCount === 0;
                }
            }

            // Añadir listener a todos los checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateDeleteButtonState);
            });

            // Inicializar el estado del botón al cargar
            updateDeleteButtonState();

            // Mostrar el modal de confirmación al hacer clic en el botón de desenlazar
            if (deleteSelectedBtn) {
                deleteSelectedBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;

                    if (checkedCount > 0) {
                        if (confirmDeleteModalElement && modalMessageText && confirmDeleteBtn) {
                            // Actualizar el texto del modal
                            modalMessageText.innerHTML = '¿Está seguro de que desea desenlazar **' + checkedCount + '** dispositivo(s) seleccionado(s)? Perderá el acceso a su monitoreo.';
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