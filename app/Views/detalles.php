<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula | ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #4361ee;
            --secondary-color: #48bfe3;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --border-radius: 16px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container.main-content {
            margin-top: 2rem;
            padding-bottom: 2rem;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2.5rem;
            border: none;
            overflow: hidden;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        h2 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 2.5rem;
            text-align: center;
            font-size: 2.5rem;
            position: relative;
            background: linear-gradient(to right, var(--primary-color), var(--success-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 8s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--success-color));
            border-radius: 2px;
        }

        .card-section {
            padding: 1.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .card-section:last-child {
            border-bottom: none;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .section-title i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        #status-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 50px;
            transition: var(--transition);
        }

        #status-display .status-icon {
            font-size: 1.5rem;
        }

        #status-display.abierta {
            color: var(--success-color);
            background-color: rgba(76, 201, 240, 0.1);
        }

        #status-display.cerrada {
            color: var(--danger-color);
            background-color: rgba(247, 37, 133, 0.1);
        }

        .btn-group-responsive {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            gap: 0.75rem;
            border: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(255,255,255,0.1), rgba(255,255,255,0));
            z-index: -1;
            transition: var(--transition);
            opacity: 0;
        }

        .btn:hover::after {
            opacity: 1;
        }

        .btn i {
            font-size: 1.2rem;
        }

        .btn-success { background-color: var(--success-color); color: #fff; }
        .btn-success:hover { background-color: #3ab7d8; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(76, 201, 240, 0.3); }

        .btn-danger { background-color: var(--danger-color); color: #fff; }
        .btn-danger:hover { background-color: #e51721ff; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(247, 37, 133, 0.3); }

        .btn-secondary { background-color: var(--gray-color); color: #fff; }
        .btn-secondary:hover { background-color: #5a6268; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3); }

        /* --- Estilos del nuevo velocímetro --- */
        .gauge-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .gauge-display {
            width: 250px;
            height: 125px;
            position: relative;
            overflow: hidden;
        }

        .gauge-arc {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 125px 125px 0 0;
            background: #e0e0e0;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.1);
        }

        .gauge-fill {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform-origin: bottom center;
            transition: transform 1s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            background: linear-gradient(90deg, var(--success-color), var(--warning-color), var(--danger-color));
            border-radius: 125px 125px 0 0;
            transform: rotate(0deg); /* Estado inicial */
        }
        
        .gauge-pointer {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform-origin: bottom center;
            transition: transform 1s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            width: 4px;
            height: 120px;
            background-color: var(--dark-color);
            border-radius: 2px;
            z-index: 2;
            transform: translateX(-50%) rotate(0deg); /* Estado inicial */
        }

        .gauge-center-circle {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 20px;
            height: 20px;
            background-color: var(--dark-color);
            border-radius: 50%;
            transform: translateX(-50%);
            z-index: 3;
        }

        .gauge-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            transition: color 0.5s ease;
            z-index: 4;
        }

        .gauge-label {
            margin-top: 1rem;
            text-align: center;
            font-size: 1rem;
            color: var(--gray-color);
            font-weight: 500;
        }
        
        /* --- Efectos de alerta --- */
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }

        /* --- Media Queries --- */
        @media (max-width: 768px) {
            .container.main-content {
                padding: 0 1rem;
            }
            .card {
                padding: 1.5rem;
            }
            h2 {
                font-size: 2rem;
            }
            .gauge-display {
                width: 180px;
                height: 90px;
            }
            .gauge-arc, .gauge-fill {
                width: 180px;
                height: 90px;
                border-radius: 90px 90px 0 0;
            }
            .gauge-pointer {
                height: 80px;
            }
            .gauge-center-circle {
                width: 16px;
                height: 16px;
            }
            .gauge-value {
                font-size: 2rem;
            }
            .btn {
                min-width: unset;
                width: 100%;
            }
        }

        /* --- Dark mode support --- */
        @media (prefers-color-scheme: dark) {
            body { background: linear-gradient(135deg, #121212 0%, #1e1e1e 100%); color: #f0f0f0; }
            .card { background-color: rgba(30, 30, 30, 0.9); color: #f0f0f0; }
            h2 { -webkit-text-fill-color: #f0f0f0; }
            .section-title, .section-title i { color: #7b9cff; }
            .gauge-arc { background-color: #3a3a3a; }
            .gauge-value { color: #f0f0f0; }
            .gauge-center-circle, .gauge-pointer { background-color: #f0f0f0; }
        }
    </style>
</head>
<body>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2>Control de Válvula de Gas</h2>
            <div class="card">
                <div class="card-body">
                    <?php if (isset($dispositivo) && $dispositivo !== null): ?>
                        
                        <div class="card-section text-center">
                            <p class="section-title justify-content-center">
                                <i class="fas fa-gas-pump"></i><span><?= esc($dispositivo->nombre) ?></span>
                            </p>
                            <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-2"></i><?= esc($dispositivo->ubicacion) ?></p>
                        </div>
                        
                        <div class="card-section text-center">
                            <p class="section-title justify-content-center"><i class="fas fa-tachometer-alt"></i>Nivel de Gas en Ambiente</p>
                            <div class="gauge-container">
                                <div class="gauge-display">
                                    <div class="gauge-arc"></div>
                                    <div class="gauge-fill" id="gaugeFill"></div>
                                    <div class="gauge-pointer" id="gaugePointer"></div>
                                    <span class="gauge-value" id="gasLevel">0%</span>
                                    <div class="gauge-center-circle"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-section text-center">
                            <p class="section-title justify-content-center"><i class="fas fa-cogs"></i>Control de Válvula</p>
                            <div id="status-display" class="<?= $dispositivo->estado_valvula ? 'abierta' : 'cerrada' ?>">
                                <i class="status-icon fas fa-toggle-on"></i>
                                <span id="estado-actual"><?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?></span>
                            </div>
                            <div class="btn-group-responsive mt-4">
                                <button type="button" class="btn btn-success" id="btn-abrir" aria-label="Abrir válvula de gas">
                                    <i class="fas fa-fan"></i>Abrir Válvula
                                </button>
                                <button type="button" class="btn btn-danger" id="btn-cerrar" aria-label="Cerrar válvula de gas">
                                    <i class="fas fa-stop"></i>Cerrar Válvula
                                </button>
                            </div>
                        </div>

                        <div class="card-section text-center">
                            <div class="btn-group-responsive">
                                <button type="button" class="btn btn-secondary" id="btn-perfil" aria-label="Ir al perfil de usuario">
                                    <i class="fas fa-user-cog"></i>Configuración
                                </button>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>No se pudo cargar la información del dispositivo. Por favor, verifique la conexión o seleccione otro dispositivo.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (isset($dispositivo) && $dispositivo !== null): ?>
$(document).ready(function() {
    const mac = '<?= esc($dispositivo->MAC) ?>'; 
    const statusDisplay = $('#status-display');
    const estadoActualSpan = $('#estado-actual');
    const gaugeFill = $('#gaugeFill');
    const gaugePointer = $('#gaugePointer');
    const gasLevelSpan = $('#gasLevel');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');

    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    $('.card').css('opacity', 0).animate({opacity: 1}, 600);
    
    function actualizarEstadoUI(estado) {
        estadoActualSpan.text(estado ? 'Abierta' : 'Cerrada');
        statusDisplay.removeClass('abierta cerrada').addClass(estado ? 'abierta' : 'cerrada');
        
        btnAbrir.prop('disabled', estado).css('opacity', estado ? 0.7 : 1);
        btnCerrar.prop('disabled', !estado).css('opacity', !estado ? 0.7 : 1);
    }

    function updateGauge(level) {
        level = Math.max(0, Math.min(100, parseFloat(level))); 
        const rotation = (level / 100) * 180;
        
        gaugeFill.css('transform', `rotate(${rotation}deg)`);
        gaugePointer.css('transform', `translateX(-50%) rotate(${rotation}deg)`);
        
        gasLevelSpan.text(`${level.toFixed(1)}%`); 

        let textColor;
        if (level < 30) {
            textColor = 'var(--success-color)';
        } else if (level < 70) {
            textColor = 'var(--warning-color)';
        } else {
            textColor = 'var(--danger-color)';
            if (level > 80 && statusDisplay.hasClass('abierta')) {
                showGasAlert(level);
            }
        }
        gasLevelSpan.css('color', textColor);
    }
    
    function showGasAlert(level) {
        Swal.fire({
            title: '⚠️ Nivel de Gas Peligroso',
            text: `El nivel de gas ha alcanzado ${level.toFixed(1)}%. Se recomienda cerrar la válvula inmediatamente.`,
            icon: 'error',
            confirmButtonText: 'Entendido',
            confirmButtonColor: 'var(--danger-color)',
            backdrop: `rgba(247, 37, 133, 0.15)`,
            allowOutsideClick: false
        });
    }
    
    function fetchDeviceState() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            console.log('Estado actual:', response);
            if (response.status === 'success') {
                actualizarEstadoUI(response.estado_valvula);
                if (response.nivel_gas !== undefined && response.nivel_gas !== null) {
                    updateGauge(response.nivel_gas);
                } else {
                    console.warn('Nivel de gas no definido. Estableciendo a 0.');
                    updateGauge(0);
                }
            } else {
                console.error('Error al obtener estado:', response.message);
                // Muestra un toast en lugar de un cartel estático
                showErrorToast('Error al obtener estado del dispositivo');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de conexión:', textStatus, errorThrown);
            // Muestra un toast en caso de fallo de conexión
            showErrorToast('Error de conexión con el servidor');
        });
    }
    
    fetchDeviceState();
    
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1, 'Válvula abierta correctamente', btnAbrir, '<i class="fas fa-fan"></i> Abrir Válvula');
    });
    
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0, 'Válvula cerrada correctamente', btnCerrar, '<i class="fas fa-stop"></i> Cerrar Válvula');
    });

    $('#btn-perfil').click(function() {
        window.location.href = '<?= base_url('perfil') ?>';
    });
    
    function controlarServo(mac, estado, successMessage, button, originalText) {
        const postData = { mac: mac, estado: estado };
        if (csrfName && csrfHash) { postData[csrfName] = csrfHash; }

        button.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
        
        $.post('/servo/actualizarEstado', postData)
            .done(function(response) {
                console.log('Respuesta del servidor:', response);
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                    showSuccessToast(successMessage);
                } else {
                    showErrorToast(response.message || 'Error al actualizar el estado');
                }
            })
            .fail(function(jqXHR) {
                console.error('Error en la petición:', jqXHR.responseText);
                showErrorToast('Error de conexión con el servidor');
            })
            .always(function() {
                button.html(originalText).prop('disabled', estado === 1);
                fetchDeviceState();
            });
    }
    
    function showSuccessToast(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function showErrorToast(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
    
    const intervalId = setInterval(fetchDeviceState, 5000);
    
    $(window).on('beforeunload', function() {
        clearInterval(intervalId);
    });
});
<?php endif; ?>
</script>
</body>
</html>