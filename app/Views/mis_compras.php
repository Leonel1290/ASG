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
        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: #2d3748 !important;
        }
        
        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #4a5568;
            color: #edf2f7;
            border-bottom: 1px solid #2d3748;
        }
        
        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
        }
        
        .form-control {
            background-color: #2d3748;
            border: 1px solid #718096;
            color: #edf2f7;
        }
        
        .form-control:focus {
            background-color: #2d3748;
            color: #edf2f7;
            border-color: #63b3ed;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/perfil') ?>">ASG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil') ?>">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil/configuracion') ?>">Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('/perfil/mis-compras') ?>">Mis Compras</a>
                        </li>
                    </ul>
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

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-truck me-2"></i> Agregar Dirección de Envío</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('/perfil/guardar-direccion-envio') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="payment_id" class="form-label">Payment ID *</label>
                                <input type="text" class="form-control" id="payment_id" name="payment_id" required 
                                       placeholder="Ingrese el ID de pago de su compra">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               value="<?= session()->get('nombre') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido *</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono *</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pais" class="form-label">País *</label>
                                        <input type="text" class="form-control" id="pais" name="pais" required value="Argentina">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="provincia" class="form-label">Provincia *</label>
                                        <input type="text" class="form-control" id="provincia" name="provincia" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad *</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="calle" class="form-label">Calle *</label>
                                        <input type="text" class="form-control" id="calle" name="calle" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="numero" class="form-label">Número *</label>
                                        <input type="text" class="form-control" id="numero" name="numero" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="piso" class="form-label">Piso</label>
                                        <input type="text" class="form-control" id="piso" name="piso">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="depto" class="form-label">Departamento</label>
                                        <input type="text" class="form-control" id="depto" name="depto">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="codigo_postal" class="form-label">Código Postal *</label>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
                            </div>

                            <div class="mb-3">
                                <label for="referencias" class="form-label">Referencias</label>
                                <textarea class="form-control" id="referencias" name="referencias" rows="3" 
                                          placeholder="Referencias adicionales para la entrega"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> Guardar Dirección de Envío
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
    <div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i> Mis Órdenes de Compra</h5>
        </div>
        <div class="card-body">
            <?php if (empty($compras)): ?>
                <p class="text-center text-muted">
                    <i class="fas fa-shopping-cart fa-2x mb-3"></i><br>
                    No tienes órdenes de compra registradas.<br>
                    <small>Agrega un Payment ID válido para ver tu orden aquí.</small>
                </p>
            <?php else: ?>
                <?php foreach ($compras as $compra): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>Orden: <?= esc($compra['order_id']) ?></strong>
                                <div class="small text-muted">Payment ID: <?= esc($compra['payment_id']) ?></div>
                            </div>
                            <span class="badge bg-success"><?= esc($compra['status']) ?></span>
                        </div>
                        
                        <div class="mt-2">
                            <div><strong>Monto:</strong> $<?= number_format($compra['monto'], 2) ?></div>
                            <div><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?></div>
                            <div><strong>Email de compra:</strong> <?= esc($compra['email']) ?></div>
                        </div>
                        
                        <?php if (isset($direccionesIndexadas[$compra['id']])): 
                            $direccionEnvio = $direccionesIndexadas[$compra['id']];
                        ?>
                            <div class="mt-2 p-2 bg-dark rounded">
                                <small>
                                    <strong><i class="fas fa-truck me-1"></i> Dirección de envío:</strong><br>
                                    <?= esc($direccionEnvio['nombre']) ?> <?= esc($direccionEnvio['apellido']) ?><br>
                                    <?= esc($direccionEnvio['calle']) ?> <?= esc($direccionEnvio['numero']) ?>
                                    <?= $direccionEnvio['piso'] ? ', Piso ' . esc($direccionEnvio['piso']) : '' ?>
                                    <?= $direccionEnvio['depto'] ? ', Depto ' . esc($direccionEnvio['depto']) : '' ?><br>
                                    <?= esc($direccionEnvio['ciudad']) ?>, <?= esc($direccionEnvio['provincia']) ?><br>
                                    <?= esc($direccionEnvio['pais']) ?> - C.P. <?= esc($direccionEnvio['codigo_postal']) ?><br>
                                    <strong>Teléfono:</strong> <?= esc($direccionEnvio['telefono']) ?>
                                    <?php if ($direccionEnvio['referencias']): ?>
                                        <br><strong>Referencias:</strong> <?= esc($direccionEnvio['referencias']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Sin dirección de envío registrada
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>