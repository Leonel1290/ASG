<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dirección de Envío</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container py-4">
    <h2 class="mb-3">Dirección de envío</h2>
    <p class="text-secondary">Completa tus datos de envío para la compra <strong>#<?= esc($compra['id']); ?></strong>.</p>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
    <?php endif; ?>

    <form action="<?= site_url('direccion/guardar'); ?>" method="post" class="row g-3">
        <?= csrf_field(); ?>
        <input type="hidden" name="compra_id" value="<?= esc($compra['id']); ?>" />

        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= set_value('nombre', $compra['nombre'] ?? ''); ?>">
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('nombre') : '' ?></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" value="<?= set_value('apellido'); ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= set_value('telefono'); ?>">
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('telefono') : '' ?></div>
        </div>

        <div class="col-md-8">
            <label class="form-label">Calle</label>
            <input type="text" name="calle" class="form-control" value="<?= set_value('calle'); ?>" required>
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('calle') : '' ?></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Número</label>
            <input type="text" name="numero" class="form-control" value="<?= set_value('numero'); ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Piso</label>
            <input type="text" name="piso" class="form-control" value="<?= set_value('piso'); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Depto</label>
            <input type="text" name="depto" class="form-control" value="<?= set_value('depto'); ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" value="<?= set_value('ciudad'); ?>" required>
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('ciudad') : '' ?></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia" class="form-control" value="<?= set_value('provincia'); ?>" required>
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('provincia') : '' ?></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Código Postal</label>
            <input type="text" name="codigo_postal" class="form-control" value="<?= set_value('codigo_postal'); ?>" required>
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('codigo_postal') : '' ?></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">País</label>
            <input type="text" name="pais" class="form-control" value="<?= set_value('pais', 'Argentina'); ?>" required>
            <div class="text-danger small"><?= isset($validation) ? $validation->getError('pais') : '' ?></div>
        </div>

        <div class="col-12">
            <label class="form-label">Referencias para el envío</label>
            <textarea name="referencias" rows="3" class="form-control"><?= set_value('referencias'); ?></textarea>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Guardar dirección</button>
            <a href="/" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>