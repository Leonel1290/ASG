<?= $this->extend('perfil/perfil_layout') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i> Historial de Alertas de Gas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($alertas)): ?>
                <div class="alert alert-info">No se han registrado alertas de gas por encima del umbral.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Dispositivo</th>
                                <th>MAC</th>
                                <th>Nivel de Gas</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alertas as $alerta): ?>
                                <tr>
                                    <td><?= esc($alerta->nombre_dispositivo) ?></td>
                                    <td><?= esc($alerta->MAC) ?></td>
                                    <td><span class="badge bg-danger"><?= esc(round($alerta->nivel_gas)) ?></span></td>
                                    <td><?= esc($alerta->fecha) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
