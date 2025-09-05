<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Biométrica - ASG</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Configuración Biométrica</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="/biometric-settings/update" method="post">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="enable_biometric" name="enable_biometric" 
                                           <?= $biometric_enabled ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="enable_biometric">
                                        <i class="fas fa-fingerprint"></i> Habilitar autenticación biométrica
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Permite iniciar sesión con huella digital o reconocimiento facial después del primer inicio de sesión.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Configuración
                                </button>
                                <a href="<?= base_url('/perfil') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Perfil
                                </a>
                            </div>
                        </form>
                        
                        <?php if ($biometric_enabled): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Autenticación biométrica activa</strong><br>
                            La próxima vez que cierres sesión, podrás iniciar con tu huella digital o reconocimiento facial.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>