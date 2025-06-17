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
            background-color: #2d3748 !important;
            /* Dark background */
        }

        .navbar-brand {
            color: #4299e1 !important;
            /* Blue for brand */
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #a0aec0 !important;
            /* Light gray for links */
            transition: color 0.3s ease;
            /* Smooth transition for hover */
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            /* White on hover */
        }

        .dropdown-menu {
            background-color: #2d3748;
            /* Dark background for dropdown */
            border: none;
        }

        .dropdown-item {
            color: #a0aec0;
            /* Light gray for dropdown items */
        }

        .dropdown-item:hover {
            background-color: #4a5568;
            /* Slightly lighter dark on hover */
            color: #fff;
        }

        /* --- END NAVBAR IMPROVEMENTS --- */

        .container {
            flex: 1;
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .card {
            background-color: #2d3748;
            /* Card background */
            color: #cbd5e0;
            /* Card text */
            border: 1px solid #4a5568;
            /* Card border */
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #4a5568;
            /* Card header background */
            color: #fff;
            /* Card header text */
            font-weight: bold;
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

        .btn-info {
            background-color: #4fd1c5;
            border-color: #4fd1c5;
        }

        .btn-info:hover {
            background-color: #38b2ac;
            border-color: #38b2ac;
        }

        .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
        }

        .btn-danger:hover {
            background-color: #c53030;
            border-color: #c53030;
        }

        .btn-secondary {
            background-color: #a0aec0;
            border-color: #a0aec0;
        }

        .btn-secondary:hover {
            background-color: #718096;
            border-color: #718096;
        }

        .form-control {
            background-color: #2d3748;
            color: #cbd5e0;
            border-color: #4a5568;
        }

        .form-control:focus {
            background-color: #2d3748;
            color: #cbd5e0;
            border-color: #4299e1;
            box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25);
        }

        .table {
            color: #cbd5e0;
        }

        .table th,
        .table td {
            border-color: #4a5568;
        }

        .alert {
            margin-top: 20px;
        }

        footer {
            background-color: #2d3748;
            color: #cbd5e0;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }

            .navbar-toggler {
                margin-left: auto;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo ASG" width="30" height="30" class="d-inline-block align-text-top me-2">
                ASG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= base_url('/perfil') ?>">Perfil</a>
                    </li>
                    </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= esc(session()->get('nombre')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('/perfil/configuracion') ?>">Configuración</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= base_url('/logout') ?>">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/login') ?>">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/register') ?>">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4 text-center">Mi Perfil</h1>

        <?= view('CodeIgniter\Shield\Views\messages\message_info') ?>
        <?= view('CodeIgniter\Shield\Views\messages\message_success') ?>
        <?= view('CodeIgniter\Shield\Views\messages\message_errors') ?>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                Información del Usuario
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?= esc(session()->get('nombre')) ?></p>
                <p><strong>Email:</strong> <?= esc(session()->get('email')) ?></p>
                <a href="<?= base_url('/perfil/configuracion') ?>" class="btn btn-info"><i class="fas fa-cog me-2"></i> Configuración del Perfil</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Mis Dispositivos Enlazados
                <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;">
                    <i class="fas fa-trash me-2"></i> Eliminar Seleccionados
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($dispositivosEnlazados)): ?>
                    <p class="text-center">No tienes dispositivos enlazados todavía.</p>
                <?php else: ?>
                    <form id="delete-devices-form" action="<?= base_url('/perfil/eliminar-dispositivos') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllCheckbox"></th>
                                        <th>MAC</th>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dispositivosEnlazados as $dispositivo): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="macs[]" value="<?= esc($dispositivo['MAC']) ?>" class="delete-checkbox">
                                            </td>
                                            <td><?= esc($dispositivo['MAC']) ?></td>
                                            <td><?= esc($dispositivo['nombre']) ?></td>
                                            <td><?= esc($dispositivo['ubicacion']) ?></td>
                                            <td>
                                                <?php
                                                $estado_clase = '';
                                                switch ($dispositivo['estado_dispositivo']) {
                                                    case 'disponible':
                                                        $estado_clase = 'badge bg-success';
                                                        break;
                                                    case 'en_uso':
                                                        $estado_clase = 'badge bg-primary';
                                                        break;
                                                    case 'mantenimiento':
                                                        $estado_clase = 'badge bg-warning text-dark';
                                                        break;
                                                    default:
                                                        $estado_clase = 'badge bg-secondary';
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= $estado_clase ?>"><?= esc(ucfirst(str_replace('_', ' ', $dispositivo['estado_dispositivo']))) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('perfil/dispositivo/editar/' . esc($dispositivo['MAC'])) ?>" class="btn btn-sm btn-warning me-2" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('lecturas/dispositivo/' . esc($dispositivo['MAC'])) ?>" class="btn btn-sm btn-secondary" title="Ver Lecturas">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que quieres eliminar los dispositivos seleccionados de tu perfil? Esta acción los desvinculará de tu cuenta y los marcará como disponibles.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> ASG. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const deleteCheckboxes = document.querySelectorAll('.delete-checkbox');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
            const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const deleteDevicesForm = document.getElementById('delete-devices-form');

            // Función para actualizar la visibilidad del botón de eliminar
            function updateDeleteButtonVisibility() {
                const checkedCount = document.querySelectorAll('.delete-checkbox:checked').length;
                if (checkedCount > 0) {
                    deleteSelectedBtn.style.display = 'block';
                } else {
                    deleteSelectedBtn.style.display = 'none';
                }
            }

            // Manejar el checkbox "Seleccionar todos"
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    deleteCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                    updateDeleteButtonVisibility();
                });
            }

            // Manejar cambios en los checkboxes individuales
            deleteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (!this.checked) {
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                        }
                    } else {
                        // Si todos los individuales están marcados, marcar "Seleccionar todos"
                        const allChecked = Array.from(deleteCheckboxes).every(cb => cb.checked);
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = allChecked;
                        }
                    }
                    updateDeleteButtonVisibility();
                });
            });

            // Mostrar el modal de confirmación al hacer clic en el botón de eliminar
            if (deleteSelectedBtn) {
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
