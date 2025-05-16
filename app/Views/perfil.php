<?php
// Esta vista espera las variables $dispositivosEnlazados y $lecturasPorMac del controlador.
// $dispositivosEnlazados será un array de arrays, donde cada array representa un dispositivo
// enlazado con sus detalles (MAC, nombre, ubicacion).
// $lecturasPorMac será un array asociativo donde la clave es la MAC y el valor es un array de lecturas.

// Asegurarse de que las variables existan
$dispositivosEnlazados = $dispositivosEnlazados ?? [];
$lecturasPorMac = $lecturasPorMac ?? [];

// CodeIgniter pasa los mensajes flashdata (success, error, info) a la sesión automáticamente.
// Se pueden acceder directamente con session('clave').
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - ASG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Añadimos un reset general para asegurar que no haya márgenes/paddings por defecto */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; /* Aseguramos que el margen sea 0 */
            padding: 0; /* Aseguramos que el padding sea 0 */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Eliminamos padding-top ya que la navbar no es fija por defecto */
            /* padding-top: 56px; */
        }

        /* Estilos para la barra de navegación */
        .navbar {
            background-color: #2d3748 !important; /* Color de fondo oscuro similar al de las tarjetas */
        }

        .navbar-brand {
            color: #fff !important; /* Color del texto de la marca */
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #cbd5e0 !important; /* Color de los enlaces de navegación */
        }

        .navbar-nav .nav-link.active {
            color: #4299e1 !important; /* Color del enlace activo (azul) */
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
             color: #fff !important; /* Color al pasar el ratón */
        }

        /* Estilos para el botón de Cerrar Sesión */
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
        }
         .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }

        /* Contenedor principal del contenido */
        .container {
            flex: 1; /* Permite que el contenedor ocupe el espacio restante */
            padding: 2rem;
            margin-top: 2rem; /* Espacio superior */
            margin-bottom: 2rem; /* Espacio inferior */
        }

        /* Estilos para las tarjetas generales (Perfil) */
        .card {
            background-color: #2d3748; /* Fondo de tarjeta oscuro */
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem; /* Espacio entre tarjetas */
        }

        .card-header {
            background-color: #4a5568; /* Color de encabezado de tarjeta */
            color: #edf2f7;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2d3748;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Espacio entre título y botones/acciones */
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

        /* Estilos para mensajes de alerta */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }

        .alert-success {
            background-color: #c6f6d5; /* Alerta verde */
            color: #1a202c;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background-color: #fed7d7; /* Alerta roja */
            color: #1a202c;
            border-color: #fbcbcb;
        }

         .alert-info {
            background-color: #bee3f8; /* Alerta azul claro */
            color: #1a202c;
            border-color: #90cdf4;
        }

        /* Estilos para el título de la sección de dispositivos (fuera de la tarjeta principal) */
        .devices-section-title {
            color: #edf2f7;
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Para alinear título y botón añadir */
        }

         .devices-section-title i {
             margin-right: 0.5rem;
         }

        /* Estilos para el formulario de añadir MAC (fuera de la tarjeta principal) */
        #add-mac-form {
            background-color: #4a5568;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            /* display: none; /* Inicialmente oculto */
        }

         #add-mac-form label {
            color: #edf2f7;
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }

         #add-mac-form input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #2d3748;
            border: 1px solid #718096;
            border-radius: 0.375rem;
            color: #edf2f7;
            box-sizing: border-box;
            margin-bottom: 1rem;
        }

        #add-mac-form button {
            background-color: #48bb78; /* Botón verde */
            border-color: #48bb78;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

         #add-mac-form button:hover {
            background-color: #38a169;
            border-color: #38a169;
        }


        /* Estilos para el formulario de eliminar (envuelve la lista) */
        #delete-devices-form {
            margin-top: 1.5rem; /* Espacio entre el formulario añadir y la lista */
        }

        /* Estilos para la lista de dispositivos (sin estilos de lista por defecto) */
        .device-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* --- CAMBIO AQUÍ: Estilo para cada item de dispositivo como una tarjeta individual --- */
        .device-item {
            background-color: #2d3748; /* Fondo de tarjeta oscuro (igual que la tarjeta principal anterior) */
            border: 1px solid #4a5568; /* Borde similar al header de tarjeta */
            border-radius: 0.5rem; /* Radio igual que la tarjeta principal */
            padding: 1.5rem; /* Padding igual que el body de tarjeta */
            margin-bottom: 1.5rem; /* Espacio entre tarjetas de dispositivo */
            display: flex;
            justify-content: space-between; /* Espacio entre info y acciones */
            align-items: center; /* Alinear verticalmente */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1); /* Sombra sutil */
            transition: box-shadow 0.3s ease; /* Animación para la sombra */
        }

        .device-item:hover {
             box-shadow: 0 0 10px rgba(102, 126, 234, 0.4); /* Sombra al pasar el ratón */
        }
        /* --- FIN CAMBIO --- */


        .device-info {
            flex-grow: 1; /* Permite que la información ocupe el espacio restante */
            margin-right: 1rem;
        }

        .device-name {
            font-size: 1.1rem;
            font-weight: bold;
            color: #edf2f7;
            margin-bottom: 0.25rem; /* Espacio debajo del nombre */
        }

        .device-details {
            font-size: 0.9rem;
            color: #a0aec0;
        }

        .device-actions {
             /* Asegura que los elementos de acción estén alineados */
             display: flex;
             align-items: center;
        }

        .device-actions a,
        .device-actions input[type="checkbox"] {
            margin-left: 0.5rem; /* Espacio entre elementos de acción */
        }


        /* Estilos para checkboxes */
        .delete-checkbox {
            margin-right: 0.5rem;
            /* Ajustar tamaño si es necesario */
            width: 1.2em;
            height: 1.2em;
            vertical-align: middle; /* Alinear con el texto/iconos */
            cursor: pointer;
        }

        /* Estilos para el botón de eliminar seleccionados */
        .delete-selected-btn {
            background-color: #e53e3e; /* Botón rojo */
            border-color: #e53e3e;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

         .delete-selected-btn:hover {
            background-color: #c53030;
            border-color: #c53030;
        }

        /* Estilos para el botón "Mostrar formulario añadir MAC" */
        #show-add-mac-form {
            background-color: #4299e1; /* Botón azul */
            border-color: #4299e1;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

         #show-add-mac-form:hover {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }

         /* Estilos para enlaces de acción (Editar, Ver Detalles) */
        .action-link {
            color: #4299e1; /* Color azul */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .action-link:hover {
            color: #63b3ed;
            text-decoration: underline;
        }

        /* Estilos para el modal de confirmación de eliminación */
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">Gráficos</a>
                        </li>
                    </ul>

                    <form action="<?= base_url('/logout') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?> <button type="submit" class="btn btn-outline-secondary btn-sm">
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
             <button id="show-add-mac-form" class="btn btn-sm" style="color: white;"><i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo</button>
        </div>


        <div id="add-mac-form" style="display: none;">
             <form action="<?= base_url('/enlace/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="mac">Dirección MAC:</label>
                    <input type="text" class="form-control" id="mac" name="mac" placeholder="Ej: XX:XX:XX:XX:XX:XX" required>
                </div>
                <button type="submit" class="btn"><i class="fas fa-link me-2"></i> Enlazar Dispositivo</button>
            </form>
             <hr class="my-4" style="border-color: #4a5568;"> </div>


        <?php if (empty($dispositivosEnlazados)): ?>
            <p>No tienes dispositivos enlazados aún.</p>
        <?php else: ?>
            <form id="delete-devices-form" action="<?= base_url('/perfil/eliminar-dispositivos') ?>" method="post">
                <?= csrf_field() ?>
                 <button type="button" id="delete-selected-btn" class="delete-selected-btn mb-3"><i class="fas fa-trash-alt me-2"></i> Eliminar Seleccionados</button>

                <ul class="device-list">
                    <?php foreach ($dispositivosEnlazados as $dispositivo): ?>
                        <li class="device-item">
                            <div class="device-info">
                                <div class="device-name"><?= esc($dispositivo['nombre'] ?: 'Dispositivo sin nombre') ?></div>
                                <div class="device-details">
                                     MAC: <?= esc($dispositivo['MAC'] ?? 'Desconocida') ?> |
                                     Ubicación: <?= esc($dispositivo['ubicacion'] ?: 'Desconocida') ?>
                                </div>
                            </div>
                            <div class="device-actions">
                                <input type="checkbox" name="macs[]" value="<?= esc($dispositivo['MAC']) ?>" class="delete-checkbox">
                                <a href="<?= base_url('/perfil/dispositivo/editar/' . esc($dispositivo['MAC'])) ?>" class="action-link btn btn-sm btn-primary me-2" title="Editar Dispositivo"><i class="fas fa-edit"></i> Editar</a>
                                <a href="<?= base_url('/detalles/' . esc($dispositivo['MAC'])) ?>" class="action-link btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-chart-bar"></i> Ver Detalles</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Script para manejar la visibilidad del formulario de añadir MAC y el modal de eliminación
        document.addEventListener('DOMContentLoaded', function () {
            const deleteSelectedBtn = document.getElementById('delete-selected-btn');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            const deleteDevicesForm = document.getElementById('delete-devices-form');
            const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            const addMacForm = document.getElementById('add-mac-form');
            const showAddMacFormButton = document.getElementById('show-add-mac-form');
            // No necesitamos seleccionar checkboxes aquí, solo en el evento del botón

            // Mostrar/ocultar formulario de añadir MAC
            if (showAddMacFormButton) { // Verificar si el botón existe
                 showAddMacFormButton.addEventListener('click', function () {
                    if (addMacForm.style.display === "none" || addMacForm.style.display === "") {
                         addMacForm.style.display = "block";
                         this.innerHTML = '<i class="fas fa-minus-circle me-2"></i> Ocultar Formulario'; // Cambiar texto del botón
                    } else {
                         addMacForm.style.display = "none";
                         this.innerHTML = '<i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo'; // Cambiar texto del botón
                    }
                });
            }


            // Mostrar modal de confirmación al hacer clic en "Eliminar Seleccionados"
            if (deleteSelectedBtn) { // Verificar si el botón existe
                deleteSelectedBtn.addEventListener('click', function () {
                    const checkedDevices = document.querySelectorAll('#delete-devices-form .delete-checkbox:checked');
                    if (checkedDevices.length > 0) {
                        confirmDeleteModal.show();
                    } else {
                        alert('Por favor, selecciona al menos un dispositivo para eliminar.');
                    }
                });
            }


            // Enviar el formulario de eliminación cuando se confirma en el modal
            if (confirmDeleteBtn && deleteDevicesForm) { // Verificar si ambos elementos existen
                 confirmDeleteBtn.addEventListener('click', function () {
                    confirmDeleteModal.hide();
                    deleteDevicesForm.submit();
                });
            }


        });
    </script>

</body>

</html>
