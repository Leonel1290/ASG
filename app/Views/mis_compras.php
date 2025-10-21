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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Paleta de colores mejorada para el tema oscuro */
        :root {
            --color-bg-primary: #121212; /* Fondo muy oscuro */
            --color-bg-secondary: #1e1e1e; /* Fondo de tarjetas */
            --color-bg-tertiary: #2a2a2a; /* Encabezado de tarjetas/elementos de lista */
            --color-text-primary: #e0e0e0; /* Texto claro principal */
            --color-text-secondary: #a0a0a0; /* Texto secundario/muted */
            --color-accent-blue: #007bff; /* Azul primario (mejor contraste) */
            --color-accent-blue-hover: #0056b3;
            --color-border: #333333;
            --color-success: #28a745;
            --color-warning: #ffc107;
            --color-danger: #dc3545;
        }

        body {
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
            font-family: 'Inter', sans-serif; /* Fuente más moderna */
            min-height: 100vh;
        }
        
        /* Navbar */
        .navbar {
            background-color: var(--color-bg-secondary) !important;
            border-bottom: 1px solid var(--color-border);
        }

        .navbar-brand, .nav-link {
            color: var(--color-text-primary) !important;
        }

        .nav-link.active {
            color: var(--color-accent-blue) !important;
            border-bottom: 2px solid var(--color-accent-blue);
            padding-bottom: 0.25rem;
        }

        /* Cards y Formularios */
        .card {
            background-color: var(--color-bg-secondary);
            color: var(--color-text-primary);
            border: 1px solid var(--color-border);
            border-radius: 0.75rem; /* Bordes más redondeados */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sutil sombra */
        }
        
        .card-header {
            background-color: var(--color-bg-tertiary);
            color: var(--color-text-primary);
            border-bottom: 1px solid var(--color-border);
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 1rem 1.5rem;
        }

        .form-label {
            color: var(--color-text-primary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .form-control, .form-control:focus {
            background-color: var(--color-bg-primary); /* Fondo más oscuro para los inputs */
            border: 1px solid var(--color-border);
            color: var(--color-text-primary);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--color-accent-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        /* Botones */
        .btn-primary {
            background-color: var(--color-accent-blue);
            border-color: var(--color-accent-blue);
            transition: background-color 0.2s, border-color 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--color-accent-blue-hover);
            border-color: var(--color-accent-blue-hover);
        }

        .btn-outline-secondary {
            color: var(--color-text-secondary);
            border-color: var(--color-text-secondary);
        }

        .btn-outline-secondary:hover {
            color: var(--color-bg-secondary);
            background-color: var(--color-text-secondary);
        }

        /* Alertas y Feedback */
        .alert-success {
            background-color: rgba(40, 167, 69, 0.15); /* Fondo sutil de éxito */
            color: var(--color-success);
            border-color: var(--color-success);
            border-radius: 0.5rem;
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.15); /* Fondo sutil de error */
            color: var(--color-danger);
            border-color: var(--color-danger);
            border-radius: 0.5rem;
        }

        /* Estilo de la lista de órdenes */
        .order-item {
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 0;
            transition: background-color 0.3s;
        }
        
        .order-item:hover {
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }

        .order-details strong {
            color: var(--color-text-primary);
        }

        .small-address {
            background-color: var(--color-bg-tertiary);
            border: 1px solid var(--color-border);
            padding: 0.75rem;
            border-radius: 0.5rem;
            color: var(--color-text-secondary);
            font-size: 0.85rem;
            margin-top: 0.75rem;
        }
        
        .text-muted {
            color: var(--color-text-secondary) !important;
        }

        /* Placeholder personalizado */
        .form-control::placeholder {
            color: var(--color-text-secondary);
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid container-xl"> <a class="navbar-brand fw-bold" href="<?= base_url('/perfil') ?>">ASG <span class="badge bg-secondary">BETA</span></a>
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
        <h2 class="mb-4 text-center">Gestión de Compras y Envíos 📦</h2>

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
        <div class="row g-4"> <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i> Dirección de Envío</h5>
                        <p class="mb-0 text-muted small">Registra la dirección para recibir tu compra.</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?= base_url('/perfil/guardar-direccion-envio') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="payment_id" class="form-label">Payment ID (ID de Transacción) *</label>
                                <input type="text" class="form-control" id="payment_id" name="payment_id" required 
                                        placeholder="Ingrese el ID de pago de su compra">
                                <div class="form-text text-muted">Asegúrate de que este ID sea el correcto para vincular la dirección.</div>
                            </div>

                            <hr class="my-4" style="border-color: var(--color-border);">
                            <h6 class="mb-3 text-white"><i class="fas fa-user me-1"></i> Datos Personales</h6>

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

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono *</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required 
                                        placeholder="Ej: 3584123456">
                            </div>

                            <hr class="my-4" style="border-color: var(--color-border);">
                            <h6 class="mb-3 text-white"><i class="fas fa-map-marked-alt me-1"></i> Ubicación</h6>

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
                                    <input type="text" class="form-control" id="piso" name="piso" placeholder="Ej: 5">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="depto" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="depto" name="depto" placeholder="Ej: A / B">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="codigo_postal" class="form-label">Código Postal *</label>
                                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required placeholder="C.P.">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="referencias" class="form-label">Referencias Adicionales</label>
                                <textarea class="form-control" id="referencias" name="referencias" rows="3" 
                                            placeholder="Detalles sobre el domicilio, ej: 'Casa con rejas azules', 'Timbrar en el 3er piso'"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-shipping-fast me-2"></i> Guardar Dirección
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-list-alt me-2"></i> Historial de Órdenes</h5>
                        <p class="mb-0 text-muted small">Listado de las compras vinculadas a tu cuenta.</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($compras)): ?>
                            <div class="text-center p-5">
                                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i><br>
                                <p class="lead text-muted">¡Aún no tienes órdenes de compra!</p>
                                <p class="text-muted small">Usa el formulario de la izquierda para ingresar un **Payment ID** y ver tu orden aquí.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($compras as $compra): ?>
                                    <div class="order-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0 text-white">Orden ID: <span class="fw-bold text-info">#<?= esc($compra['order_id']) ?></span></h6>
                                                <div class="small text-muted">
                                                    Transacción: **<?= esc($compra['payment_id']) ?>**
                                                </div>
                                            </div>
                                            <?php 
                                                $statusClass = 'bg-secondary';
                                                if (strtolower(esc($compra['status'])) === 'aprobada') {
                                                    $statusClass = 'bg-success';
                                                } else if (strtolower(esc($compra['status'])) === 'pendiente') {
                                                    $statusClass = 'bg-warning text-dark';
                                                }
                                            ?>
                                            <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2"><?= esc($compra['status']) ?></span>
                                        </div>
                                        
                                        <div class="order-details mt-3 row g-0 small">
                                            <div class="col-6 mb-1">
                                                <strong><i class="fas fa-dollar-sign me-1"></i> Monto:</strong> 
                                                <span class="text-success">$<?= number_format($compra['monto'], 2) ?></span>
                                            </div>
                                            <div class="col-6 mb-1">
                                                <strong><i class="fas fa-calendar-alt me-1"></i> Fecha:</strong> 
                                                <?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?>
                                            </div>
                                            <div class="col-12">
                                                <strong><i class="fas fa-at me-1"></i> Email de compra:</strong> 
                                                <span class="text-secondary"><?= esc($compra['email']) ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php if (isset($direccionesIndexadas[$compra['id']])): 
                                            $direccionEnvio = $direccionesIndexadas[$compra['id']];
                                        ?>
                                            <div class="small-address">
                                                <strong><i class="fas fa-map-pin me-1"></i> Dirección de Envío Registrada:</strong><br>
                                                <div class="mt-1">
                                                    **<?= esc($direccionEnvio['nombre']) ?> <?= esc($direccionEnvio['apellido']) ?>** (<?= esc($direccionEnvio['telefono']) ?>)<br>
                                                    <?= esc($direccionEnvio['calle']) ?> <?= esc($direccionEnvio['numero']) ?>
                                                    <?= $direccionEnvio['piso'] ? ' / Piso ' . esc($direccionEnvio['piso']) : '' ?>
                                                    <?= $direccionEnvio['depto'] ? ' / Depto ' . esc($direccionEnvio['depto']) : '' ?><br>
                                                    <?= esc($direccionEnvio['ciudad']) ?> (<?= esc($direccionEnvio['provincia']) ?>) - C.P. <?= esc($direccionEnvio['codigo_postal']) ?><br>
                                                    <?php if ($direccionEnvio['referencias']): ?>
                                                        <span class="text-info mt-1 d-block">Referencias: *<?= esc($direccionEnvio['referencias']) ?>*</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-3 alert alert-warning p-2 small m-0">
                                                <i class="fas fa-exclamation-circle me-1"></i> 
                                                **Falta Dirección de Envío:** Registra los datos en el formulario para esta orden.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>