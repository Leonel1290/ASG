<?php
$idioma = session('lang') ?? 'es';
$perfilLang = require APPPATH . "Language/{$idioma}/Perfil.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Compras - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Paleta de la vista de referencia (Tailwind CSS Dark-Gray/Blue) */
        :root {
            --color-bg-primary: #1a202c; /* Fondo principal (body) */
            --color-bg-secondary: #2d3748; /* Fondo de tarjetas/Contenedores */
            --color-bg-tertiary: #4a5568; /* Header de tarjetas/Acentos */
            --color-text-primary: #cbd5e0; /* Texto claro principal */
            --color-text-secondary: #a0aec0; /* Texto de acento/Secundario */
            --color-accent: #4299e1; /* Color de acento (Azul) */
            --color-accent-dark: #2b6cb0; /* Azul oscuro para hover */
            --color-border: #4a5568; /* Borde sutil */
            --color-success: #48bb78; /* Verde para éxito */
            --color-danger: #e53e3e; /* Rojo para peligro */
            --color-warning: #ecc94b; /* Amarillo para advertencia */
            --color-text-dark: #1a202c; /* Color de texto oscuro para advertencias */
        }

        body {
            background-color: var(--color-bg-primary); /* Fondo principal simple */
            color: var(--color-text-primary);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Navbar - Simplificado */
        .navbar {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1030;
            background-color: var(--color-bg-secondary) !important; /* Usar el fondo de tarjetas */
            /* Eliminar el blur y el borde inferior para igualar la referencia */
            backdrop-filter: none;
            border-bottom: none; 
            transition: none;
        }

        .navbar-brand {
            color: #fff !important; /* Blanco para el brand */
            font-size: 1.4rem;
        }
        .nav-link {
            color: var(--color-text-primary) !important;
            font-size: 1.1rem;
            padding-top: .75rem;
            padding-bottom: .75rem;
        }

        .nav-link.active {
            color: var(--color-accent) !important;
            border-bottom: none; /* Eliminar el borde azul inferior */
            font-weight: bold;
        }
        
        .nav-link:hover {
            color: #fff !important; /* Blanco en hover */
        }

        /* Botón Outline */
        .btn-outline-secondary {
            color: var(--color-text-primary);
            border-color: var(--color-text-primary);
            transition: all 0.2s;
        }

        .btn-outline-secondary:hover {
            color: var(--color-bg-primary);
            background-color: var(--color-text-primary);
            border-color: var(--color-text-primary);
        }

        /* Cards */
        .card {
            background-color: var(--color-bg-secondary);
            color: var(--color-text-primary);
            border: none; /* Sin borde explícito */
            border-radius: 0.5rem; /* Ajustar radio al de referencia */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1); /* Sombra más sutil de referencia */
        }
        
        .card-header {
            background-color: var(--color-bg-tertiary);
            color: #fff; /* Título en blanco para mejor contraste */
            border-bottom: 1px solid var(--color-bg-secondary); /* Borde más suave */
            border-radius: 0.5rem 0.5rem 0 0; /* Ajustar radio */
            padding: 1rem 1.5rem; /* Ajustar padding */
        }
        
        /* Formularios */
        .form-label {
            color: var(--color-text-primary);
            font-weight: bold; /* Hacer la etiqueta más destacada como en la referencia */
            margin-bottom: 0.5rem; /* Aumentar espacio */
        }

        .form-control, .form-control:focus {
            background-color: var(--color-bg-secondary); /* Usar el color de tarjeta para los inputs */
            border: 1px solid var(--color-border);
            color: var(--color-text-primary);
            border-radius: 0.375rem; /* Ligeramente más pequeño que antes */
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25); /* Sombra de foco azul */
        }
        
        /* Botón Primario (Save/Guardar) */
        .btn-primary {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            color: #fff;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--color-accent-dark);
            border-color: var(--color-accent-dark);
        }

        /* Alertas - Adaptadas a la paleta de la referencia */
        .alert-success {
            background-color: #c6f6d5; 
            color: var(--color-text-dark); /* Color de texto oscuro para fondo claro */
            border-color: #a7f3d0;
            border-radius: 0.375rem;
        }
        .alert-danger {
            background-color: #fed7d7; 
            color: var(--color-text-dark);
            border-color: #fbcbcb;
            border-radius: 0.375rem;
        }
        .alert-warning {
            background-color: #feebc8; /* Nuevo estilo para warning */
            color: var(--color-text-dark);
            border-color: #faf089;
            border-radius: 0.375rem;
        }

        /* Listado de Órdenes */
        .order-item {
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 0;
            transition: none; /* Eliminar transición y hover de fondo para simplificar */
            margin-left: 0;
            margin-right: 0;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }

        .order-item:hover {
            background-color: transparent; /* Eliminar efecto de hover */
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
        }

        .text-info-custom {
            color: var(--color-accent) !important; /* Usar azul de acento */
            font-weight: 700;
        }

        .badge-status {
            font-size: 0.75em; /* Ligeramente más pequeño */
            padding: 0.3em 0.7em;
            border-radius: 0.5rem; /* Ajustar radio */
            font-weight: 700;
            color: #fff; /* Asegurar texto blanco por defecto en badges */
        }
        
        /* Override de Bootstrap y ajuste de colores de badge */
        .badge.bg-success { background-color: var(--color-success) !important; color: var(--color-text-dark) !important; }
        .badge.bg-warning { background-color: var(--color-warning) !important; color: var(--color-text-dark) !important; } 
        .badge.bg-danger { background-color: var(--color-danger) !important; color: #fff !important; }
        .badge.bg-secondary { background-color: var(--color-bg-tertiary) !important; color: #fff !important; }

        .address-box {
            background-color: var(--color-bg-tertiary); /* Usar el color de header de card como fondo de dirección */
            border: 1px solid var(--color-border);
            padding: 0.75rem;
            border-radius: 0.5rem;
            color: #fff; /* Texto blanco en el address box para buen contraste */
            font-size: 0.85rem;
            margin-top: 0.75rem;
        }
        
        .text-success { color: var(--color-success) !important; }
        .text-secondary { color: var(--color-text-secondary) !important; }

        .placeholder-empty {
            color: var(--color-text-secondary) !important;
            opacity: 1; /* Quitar opacidad para mantener el color */
        }

        /* Regla para que los divisores horizontales se vean bien */
        hr {
            border-color: var(--color-border) !important; 
            opacity: 1 !important; /* Quitar opacidad para que el color sea claro */
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid container-xl">
                <a class="navbar-brand fw-bold" href="<?= base_url('/perfil') ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil') ?>"><i class="fas fa-home me-1"></i> Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion') ?>"><i class="fas fa-user-circle me-1"></i> Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?= base_url('/mis_compras') ?>"><i class="fas fa-receipt me-1"></i> Mis Compras</a>
                        </li>
                    </ul>
                    <form action="<?= base_url('/logout') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fas fa-sign-out-alt me-2"></i> <?= $perfilLang['cerrar_sesion'] ?>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <div class="container container-xl my-5">
        <h2 class="mb-4 text-center text-white">Gestión de Compras y Envíos <i class="fas fa-shipping-fast text-secondary"></i></h2>

        <?php if (session('success')): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div><?= session('success') ?></div>
            </div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                <div><?= session('error') ?></div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i> Agregar Dirección de Envío</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?= base_url('/perfil/guardar-direccion-envio') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-4">
                                <label for="payment_id" class="form-label"><i class="fas fa-barcode me-1"></i> Payment ID (ID de Compra) *</label>
                                <input type="text" class="form-control" id="payment_id" name="payment_id" required 
                                        placeholder="Ingrese el ID de pago de su compra">
                                <div class="form-text text-secondary small mt-2">Este ID vincula la dirección a tu compra.</div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3 text-white"><i class="fas fa-user me-1"></i> Datos del Receptor</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                                value="<?= session()->get('nombre') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido *</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="telefono" class="form-label"><i class="fas fa-phone me-1"></i> Teléfono *</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3 text-white"><i class="fas fa-location-arrow me-1"></i> Detalles del Domicilio</h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="pais" class="form-label">País *</label>
                                    <input type="text" class="form-control" id="pais" name="pais" required value="Argentina" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="provincia" class="form-label">Provincia *</label>
                                    <input type="text" class="form-control" id="provincia" name="provincia" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="ciudad" class="form-label">Ciudad *</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="calle" class="form-label">Calle *</label>
                                    <input type="text" class="form-control" id="calle" name="calle" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="numero" class="form-label">Número *</label>
                                    <input type="text" class="form-control" id="numero" name="numero" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="piso" class="form-label">Piso</label>
                                    <input type="text" class="form-control" id="piso" name="piso">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="depto" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="depto" name="depto">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="codigo_postal" class="form-label">Código Postal *</label>
                                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="referencias" class="form-label"><i class="fas fa-info-circle me-1"></i> Referencias</label>
                                <textarea class="form-control" id="referencias" name="referencias" rows="3" 
                                                placeholder="Referencias adicionales para la entrega (ej: 'Dejar con portería', 'Casa portón verde')"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-save me-2"></i> Guardar Dirección de Envío
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i> Mis Órdenes de Compra</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($compras)): ?>
                            <div class="text-center p-5 placeholder-empty">
                                <i class="fas fa-box-open fa-3x mb-3"></i><br>
                                <p class="lead">No tienes órdenes de compra registradas.</p>
                                <p class="small">Ingresa un Payment ID válido a la izquierda para ver tu orden aquí.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($compras as $compra): ?>
                                    <div class="order-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>Orden: <span class="text-info-custom">#<?= esc($compra['order_id']) ?></span></strong>
                                                <div class="small text-secondary">ID de Pago: <?= esc($compra['payment_id']) ?></div>
                                            </div>
                                            <?php 
                                                // Mapeo de estado para colores adaptados
                                                $status = strtolower(esc($compra['status']));
                                                $statusClass = 'bg-secondary';
                                                if ($status === 'aprobada' || $status === 'entregado' || $status === 'enviado') {
                                                    $statusClass = 'bg-success';
                                                } else if ($status === 'pendiente' || $status === 'en proceso') {
                                                    $statusClass = 'bg-warning';
                                                } else if ($status === 'rechazado' || $status === 'cancelado') {
                                                    $statusClass = 'bg-danger';
                                                }
                                            ?>
                                            <span class="badge badge-status <?= $statusClass ?>"><?= esc($compra['status']) ?></span>
                                        </div>
                                        
                                        <div class="row g-0 small">
                                            <div class="col-6 mb-1">
                                                <strong><i class="fas fa-money-bill-wave me-1"></i> Monto:</strong> 
                                                <span class="text-success">$<?= number_format($compra['monto'], 2) ?></span>
                                            </div>
                                            <div class="col-6 mb-1 text-end">
                                                <strong><i class="fas fa-calendar-alt me-1"></i> Fecha:</strong> 
                                                <?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?>
                                            </div>
                                            <div class="col-12 text-secondary">
                                                <i class="fas fa-envelope me-1"></i> Email: <?= esc($compra['email']) ?>
                                            </div>
                                        </div>
                                        
                                        <?php if (isset($direccionesIndexadas[$compra['id']])): 
                                                $direccionEnvio = $direccionesIndexadas[$compra['id']];
                                        ?>
                                            <div class="address-box">
                                                <strong><i class="fas fa-truck me-1"></i> Dirección de Envío:</strong><br>
                                                <small>
                                                    <?= esc($direccionEnvio['nombre']) ?> <?= esc($direccionEnvio['apellido']) ?> (Tel: <?= esc($direccionEnvio['telefono']) ?>)<br>
                                                    <?= esc($direccionEnvio['calle']) ?> <?= esc($direccionEnvio['numero']) ?>
                                                    <?= $direccionEnvio['piso'] ? ', Piso ' . esc($direccionEnvio['piso']) : '' ?>
                                                    <?= $direccionEnvio['depto'] ? ', Depto ' . esc($direccionEnvio['depto']) : '' ?><br>
                                                    <?= esc($direccionEnvio['ciudad']) ?>, <?= esc($direccionEnvio['provincia']) ?> (C.P. <?= esc($direccionEnvio['codigo_postal']) ?>)<br>
                                                    <?php if ($direccionEnvio['referencias']): ?>
                                                        <span class="text-secondary d-block mt-1">Ref.: *<?= esc($direccionEnvio['referencias']) ?>*</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-3 alert alert-warning p-2 small m-0 border-0">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                **Atención:** Falta registrar la dirección de envío para esta orden.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // Asegúrate de que base_url('service-worker.js') apunte a la ruta correcta en Render
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