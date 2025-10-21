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
        /* Variables de color adaptadas de inicio.php */
        :root {
            --color-bg-primary: #0D1F23; /* Fondo principal */
            --color-bg-secondary: #132E35; /* Fondo de tarjetas */
            --color-bg-tertiary: #2D4A53; /* Header de tarjetas / Elementos de acento */
            --color-text-primary: #AFB3B7; /* Texto claro principal */
            --color-text-secondary: #698180; /* Texto de acento / Botón hover */
            --color-accent: #698180; /* Color de acento (verde azulado) */
            --color-accent-dark: #2D4A53;
            --color-border: #334e56; /* Borde sutil */
            --color-success: #38a169; /* Verde más oscuro y corporativo */
            --color-danger: #e53e3e;
            --color-warning: #ecc94b;
        }

        body {
            background: linear-gradient(135deg, var(--color-bg-primary), var(--color-bg-secondary));
            color: var(--color-text-primary);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            /* Añadir padding para compensar el navbar fijo */
            padding-top: 70px;
        }
        
        /* Navbar - Adaptado a Fixed + Blur Effect de inicio.php */
        .navbar {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1030;
            /* #0D1F23 es rgb(13, 31, 35) */
            background-color: rgba(13, 31, 35, 0.9) !important; 
            backdrop-filter: blur(8px); /* Efecto de vidrio esmerilado */
            border-bottom: 1px solid var(--color-border);
            transition: background-color 0.3s ease; /* Transición sutil */
        }

        .navbar-brand, .nav-link {
            color: var(--color-text-primary) !important;
        }

        .nav-link.active {
            color: var(--color-accent) !important;
            border-bottom: 2px solid var(--color-accent);
            padding-bottom: 0.25rem;
        }

        .btn-outline-secondary {
            color: var(--color-text-secondary);
            border-color: var(--color-border);
            transition: all 0.2s;
        }

        .btn-outline-secondary:hover {
            color: #fff;
            background-color: var(--color-accent-dark);
            border-color: var(--color-accent-dark);
        }

        /* Cards y Formularios */
        .card {
            background-color: var(--color-bg-secondary);
            color: var(--color-text-primary);
            border: 1px solid var(--color-border);
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        
        .card-header {
            background-color: var(--color-bg-tertiary);
            color: #fff; /* Título en blanco para mejor contraste */
            border-bottom: 1px solid var(--color-border);
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 1.25rem 1.5rem;
        }
        
        .form-label {
            color: var(--color-text-primary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .form-control, .form-control:focus {
            background-color: var(--color-bg-primary); 
            border: 1px solid var(--color-border);
            color: var(--color-text-primary);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 0.2rem rgba(105, 129, 128, 0.4); /* Sombra de foco con el color de acento */
        }
        
        /* Botón Primario (Save/Guardar) */
        .btn-primary {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--color-accent-dark);
            border-color: var(--color-accent-dark);
        }

        /* Alertas */
        .alert-success {
            background-color: rgba(56, 161, 105, 0.2); 
            color: var(--color-success);
            border-color: var(--color-success);
            border-radius: 0.5rem;
        }
        .alert-danger {
            background-color: rgba(229, 62, 62, 0.2); 
            color: var(--color-danger);
            border-color: var(--color-danger);
            border-radius: 0.5rem;
        }

        /* Listado de Órdenes */
        .order-item {
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 0;
            transition: background-color 0.3s;
        }
        
        .order-item:last-child {
             border-bottom: none; /* Eliminar borde inferior del último elemento */
        }

        .order-item:hover {
            background-color: rgba(45, 74, 83, 0.3); /* Color de acento oscuro con transparencia */
            border-radius: 0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }

        .text-info-custom {
            color: #4dc0b5 !important; /* Un turquesa sutil para Payment/Order ID */
            font-weight: 600;
        }

        .badge-status {
            font-size: 0.8em;
            padding: 0.4em 0.8em;
            border-radius: 1rem;
            font-weight: 600;
        }
        
        .badge.bg-success { background-color: var(--color-success) !important; }
        .badge.bg-warning { background-color: var(--color-warning) !important; color: #1a202c !important; } /* Texto oscuro para el fondo amarillo */
        .badge.bg-secondary { background-color: var(--color-border) !important; }

        .address-box {
            background-color: rgba(45, 74, 83, 0.6); /* Un poco más de transparencia en el fondo terciario */
            border: 1px solid var(--color-border);
            padding: 0.75rem;
            border-radius: 0.5rem;
            color: var(--color-text-primary);
            font-size: 0.85rem;
            margin-top: 0.75rem;
        }

        .placeholder-empty {
            color: var(--color-text-secondary) !important;
            opacity: 0.8;
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
                                <div class="form-text text-muted small mt-2">Este ID vincula la dirección a tu compra.</div>
                            </div>

                            <hr style="border-color: var(--color-border); opacity: 0.5;">
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

                            <hr style="border-color: var(--color-border); opacity: 0.5;">
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
                                                <div class="small text-muted">ID de Pago: <?= esc($compra['payment_id']) ?></div>
                                            </div>
                                            <?php 
                                                // Mapeo de estado para colores adaptados
                                                $status = strtolower(esc($compra['status']));
                                                $statusClass = 'bg-secondary';
                                                if ($status === 'aprobada' || $status === 'entregado' || $status === 'enviado') {
                                                    $statusClass = 'bg-success';
                                                } else if ($status === 'pendiente' || $status === 'en proceso') {
                                                    $statusClass = 'bg-warning text-dark';
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
                                            <div class="col-12 text-muted">
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