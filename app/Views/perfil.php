<?php
$idioma = session('lang') ?? 'es'; // Por defecto español
$perfilLang = require APPPATH . "Language/{$idioma}/Perfil.php";

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
            /* Navbar is block level and takes full width by default */
        }

        .navbar-brand {
            color: #fff !important; /* White color for brand text */
            font-weight: bold;
            /* Increase font size for brand */
            font-size: 1.4rem; /* Slightly larger for brand */
        }

        .navbar-nav .nav-link {
            color: #cbd5e0 !important; /* Color for links */
            /* Increase font size for links */
            font-size: 1.1rem; /* Larger font size */
            /* Add vertical padding to make links taller */
            padding-top: .75rem;
            padding-bottom: .75rem;
        }

        .navbar-nav .nav-link.active {
            color: #4299e1 !important; /* Blue for active link */
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
             color: #fff !important; /* White on hover */
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
             background-color: #4299e1; /* Maintain desired blue */
             border-color: #4299e1;
             color: white; /* Ensure text is white */
        }
        .btn-primary:hover {
             background-color: #2b6cb0; /* Darker blue on hover */
             border-color: #2b6cb0;
             color: white;
        }

        .btn-success {
            background-color: #48bb78; /* Maintain desired green */
            border-color: #48bb78;
            color: white; /* Ensure text is white */
        }
        .btn-success:hover {
            background-color: #38a169; /* Darker green on hover */
            border-color: #38a169;
            color: white;
        }

        .btn-danger {
            background-color: #e53e3e; /* Maintain desired red */
            border-color: #e53e3e;
            color: white; /* Ensure text is white */
        }
        .btn-danger:hover {
            background-color: #c53030; /* Darker red on hover */
            border-color: #c53030;
            color: white;
        }

        .btn-info {
            /* Using a color similar to primary for info/view details in this theme */
            background-color: #4299e1;
            border-color: #4299e1;
            color: white; /* Ensure text is white */
        }
         .btn-info:hover {
            background-color: #2b6cb0; /* Darker blue on hover */
            border-color: #2b6cb0;
            color: white;
        }

        /* Specific style for the add MAC form button - using btn-success */
        #add-mac-form .btn-success {
             margin-top: 0.5rem; /* Add space above the button in the form */
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
             background-color: #2d3748; /* Dark background for input */
             border: 1px solid #718096; /* Border color */
             border-radius: 0.375rem;
             color: #edf2f7; /* Text color */
             box-sizing: border-box;
             margin-bottom: 1rem;
           }
           /* Style for Bootstrap's default focus ring in dark mode */
           #add-mac-form .form-control:focus {
             background-color: #2d3748;
             color: #edf2f7;
             border-color: #63b3ed; /* Blue focus border */
             box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25); /* Blue focus shadow */
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
              box-shadow: 0 0 10px rgba(66, 153, 225, 0.4); /* Use a blue glow on hover */
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
             /* Space out the checkbox and the buttons */
             gap: 0.5rem; /* Gap between flex items */
        }
         /* Removed old margin-left rules as gap handles spacing */


        /* Custom Checkbox styles for dark theme */
        .delete-checkbox {
            width: 1.2em;
            height: 1.2em;
            vertical-align: middle;
            cursor: pointer;
            background-color: #4a5568; /* Dark background */
            border: 1px solid #718096; /* Border color */
            border-radius: 0.25em; /* Slight border radius */
            appearance: none; /* Hide default checkbox */
            -webkit-appearance: none;
            position: relative;
            flex-shrink: 0; /* Prevent shrinking in flex container */
            transition: background-color 0.2s ease, border-color 0.2s ease;
           }

           .delete-checkbox:checked {
             background-color: #48bb78; /* Green when checked */
             border-color: #48bb78;
           }

           .delete-checkbox:focus {
             outline: none; /* Remove default focus outline */
             box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25); /* Add custom focus ring */
           }

           /* Add custom checkmark using Font Awesome */
           .delete-checkbox:checked::after {
             content: '\f00c'; /* Font Awesome check icon */
             font-family: 'Font Awesome 6 Free';
             font-weight: 900; /* Solid icon weight */
             color: white; /* White checkmark */
             font-size: 0.8em;
             position: absolute;
             top: 50%;
             left: 50%;
             transform: translate(-50%, -50%);
           }


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

         /* Modal button styles - kept as is */
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
            filter: invert(1) grayscale(100%) brightness(200%); /* Makes the close button white */
        }


    </style>
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

                    <form method="post" action="<?= base_url('/cambiar-idioma') ?>"class="d-flex me-3">
                        <?= csrf_field() ?>
                        <select name="idioma" onchange="this.form.submit()" class="form-select form-select-sm">
                            <option value="es" <?= $idioma == 'es' ? 'selected' : '' ?>>Español</option>
                            <option value="en" <?= $idioma == 'en' ? 'selected' : '' ?>>English</option>
                        </select>
                    </form>

                    <form action="<?= base_url('/logout') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                             <i class="fas fa-sign-out-alt me-2"></i> <?= $perfilLang['cerrar_sesion'] ?>
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
                 <h5 class="card-title"><i class="fas fa-user-circle me-2"></i> <?= $perfilLang['mi_perfil'] ?></h5>
             </div>
             <div class="card-body">
                 <p><strong><?= $perfilLang['nombre'] ?>:</strong> <?= esc(session()->get('nombre')) ?></p>
                 <p><strong><?= $perfilLang['email'] ?>:</strong> <?= esc(session()->get('email')) ?></p>
             </div>
        </div>

        <div class="devices-section-title">
             <h2 style="margin: 0;"><i class="fas fa-microchip me-2"></i> <?= $perfilLang['mis_dispositivos'] ?></h2>
             <button id="show-add-mac-form" class="btn btn-primary btn-sm">
                 <i class="fas fa-plus-circle me-2"></i> <?= $perfilLang['anadir_dispositivo'] ?>
             </button>
        </div>


        <div id="add-mac-form" style="display: none;">
             <form action="<?= base_url('/enlace/store') ?>" method="post">
                 <?= csrf_field() ?>
                 <div class="mb-3">
                     <label for="mac"><?= $perfilLang['mac'] ?>:</label>
                     <input type="text" class="form-control" id="mac" name="mac" placeholder="Ej: XX:XX:XX:XX:XX:XX" required>
                 </div>
                 <button type="submit" class="btn btn-success"><i class="fas fa-link me-2"></i> <?= $perfilLang['enlazar_dispositivo'] ?></button>
             </form>
              <hr class="my-4" style="border-color: #4a5568;">
        </div>


        <?php if (empty($dispositivosEnlazados)): ?>
             <p><?= $perfilLang['no_dispositivos'] ?></p>
           <?php else: ?>
             <form id="delete-devices-form" action="<?= base_url('/perfil/eliminar-dispositivos') ?>" method="post">
                 <?= csrf_field() ?>
                 <button type="button" id="delete-selected-btn" class="btn btn-danger mb-3">
                     <i class="fas fa-trash-alt me-2"></i> <?= $perfilLang['eliminar_seleccionados'] ?>
                 </button>

                 <ul class="device-list">
                     <?php foreach ($dispositivosEnlazados as $dispositivo): ?>
                         <li class="device-item">
                             <div class="device-info">
                                 <div class="device-name"><?= esc($dispositivo['nombre'] ?: $perfilLang['dispositivo_sin_nombre']) ?></div>
                                 <div class="device-details">
                                      <?= $perfilLang['mac'] ?>: <?= esc($dispositivo['MAC'] ?? $perfilLang['desconocida']) ?> |
                                      <?= $perfilLang['ubicacion'] ?>: <?= esc($dispositivo['ubicacion'] ?: $perfilLang['desconocida']) ?>
                                 </div>
                             </div>
                             <div class="device-actions">
                                 <input type="checkbox" name="macs[]" value="<?= esc($dispositivo['MAC']) ?>" class="delete-checkbox">
                                 <a href="<?= base_url('/perfil/dispositivo/editar/' . esc($dispositivo['MAC'])) ?>" class="btn btn-primary btn-sm" title="<?= $perfilLang['editar'] ?>">
                                      <i class="fas fa-edit"></i> <?= $perfilLang['editar'] ?>
                                 </a>
                                <a href="<?= base_url('/detalles/' . esc($dispositivo['MAC'])) ?>" class="btn btn-info btn-sm" title="<?= $perfilLang['ver_detalles'] ?>">
                                    <i class="fas fa-chart-bar"></i> <?= $perfilLang['ver_detalles'] ?>
                                </a>
                             </div>
                         </li>
                     <?php endforeach; ?>
                 </ul>

             </form>
           <?php endif; ?>

        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
             <div class="modal-dialog">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="confirmDeleteModalLabel"><?= $perfilLang['confirmar_eliminacion'] ?></h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <div class="modal-body">
                          <?= $perfilLang['seguro_desea_eliminar'] ?>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $perfilLang['cancelar'] ?></button>
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
                     // Use class 'd-none' for better Bootstrap compatibility, but style="display: none" is also fine
                     if (addMacForm.style.display === "none" || addMacForm.style.display === "") {
                         addMacForm.style.display = "block"; // Show
                         this.innerHTML = '<i class="fas fa-minus-circle me-2"></i> <?= $perfilLang['ocultar_formulario'] ?>'; // Change text
                     } else {
                         addMacForm.style.display = "none"; // Hide
                         this.innerHTML = '<i class="fas fa-plus-circle me-2"></i> <?= $perfilLang['anadir_dispositivo'] ?>'; // Change text
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

</body>

</html>
