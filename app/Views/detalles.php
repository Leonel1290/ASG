<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas | ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- PWA Metadata -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- CSRF Protection -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #4361ee;
            --success-color: #4cc9f0;
            --danger-color: #911b21ff;
            --warning-color: #f8961e;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gray-color: #ffffffff;
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
            background-color: #ffffff;
            padding: 2.5rem;
            border: none;
            overflow: hidden;
            transition: var(--transition);
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
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

        .card-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .card-title i {
            color: var(--primary-color);
            font-size: 1.6rem;
        }

        .card-subtitle {
            color: var(--gray-color);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .card-subtitle i {
            color: var(--gray-color);
        }

        .card-text {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 1rem;
            gap: 0.75rem;
        }

        .card-text i {
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        #estado-actual {
            font-weight: 700;
            transition: var(--transition);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
        }

        #estado-actual.abierta {
            color: var(--success-color);
            background-color: rgba(76, 201, 240, 0.1);
        }

        #estado-actual.cerrada {
            color: var(--danger-color);
            background-color: rgba(247, 37, 133, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 1.25rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
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

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #3ab7d8;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(76, 201, 240, 0.3);
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c21821ff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(247, 37, 133, 0.3);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .btn-primary-custom:hover {
            background-color: #3a56d4;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        /* Estilos del Velocímetro Mejorado */
        .gauge-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .gauge {
            width: 220px;
            height: 110px;
            overflow: hidden;
            position: relative;
            background: #e0e0e0;
            border-radius: 110px 110px 0 0;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.1);
        }

        .gauge-fill {
            height: 100%;
            width: 100%;
            transform-origin: bottom center;
            transition: transform 1s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            background: linear-gradient(90deg, 
                var(--success-color) 0%, 
                var(--warning-color) 50%, 
                var(--danger-color) 100%);
            border-radius: 110px 110px 0 0;
            position: absolute;
            bottom: 0;
        }

        .gauge-cover {
            width: 160px;
            height: 80px;
            background: #ffffff;
            border-radius: 80px 80px 0 0;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--dark-color);
            font-weight: bold;
            font-size: 1.8rem;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            z-index: 1;
        }

        .gauge-label {
            margin-top: 1rem;
            text-align: center;
            font-size: 1rem;
            color: var(--gray-color);
            font-weight: 500;
        }

        .gauge-level {
            font-size: 1.8rem;
            font-weight: bold;
            transition: color 0.5s ease;
        }

        /* Efecto de pulso para alertas */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Tooltip personalizado */
        .tooltip-custom {
            position: relative;
            display: inline-block;
        }

        .tooltip-custom .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: var(--dark-color);
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
        }

        .tooltip-custom:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .container.main-content {
                margin-top: 1rem;
                padding: 0 1rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            h2 {
                font-size: 2rem;
                margin-bottom: 2rem;
            }
            
            h2::after {
                width: 60px;
                height: 3px;
                bottom: -0.75rem;
            }
            
            .card-title {
                font-size: 1.5rem;
            }
            
            .card-title i {
                font-size: 1.3rem;
            }
            
            .card-subtitle {
                font-size: 1rem;
                margin-bottom: 1.25rem;
            }
            
            .card-text {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
            }
            
            .btn-group {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn {
                width: 100%;
                padding: 0.75rem;
                font-size: 1rem;
            }
            
            .gauge {
                width: 180px;
                height: 90px;
            }
            
            .gauge-cover {
                width: 130px;
                height: 65px;
                font-size: 1.5rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #121212 0%, #1e1e1e 100%);
                color: #f0f0f0;
            }
            
            .card {
                background-color: rgba(30, 30, 30, 0.9);
                color: #f0f0f0;
            }
            
            .card-title, .card-title i {
                color: #7b9cff;
            }
            
            .card-subtitle, .card-subtitle i {
                color: #a0a0a0;
            }
            
            .gauge-cover {
                background-color: #2a2a2a;
                color: #f0f0f0;
            }
            
            .gauge {
                background-color: #3a3a3a;
            }
            
            .alert {
                background-color: #2a2a2a;
                color: #f0f0f0;
            }
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
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger pulse" role="alert">
                            <i class="fas fa-exclamation-triangle"></i><?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        <h5 class="card-title" id="nombre-dispositivo">
                            <i class="fas fa-gas-pump"></i>Dispositivo: <?= esc($dispositivo->nombre) ?>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted" id="ubicacion-dispositivo">
                            <i class="fas fa-map-marker-alt"></i>Ubicación: <?= esc($dispositivo->ubicacion) ?>
                        </h6>
                        
                        <div class="gauge-container">
                            <div class="gauge">
                                <div class="gauge-fill" id="gaugeFill"></div>
                                <div class="gauge-cover">
                                    <span id="gasLevel">0%</span>
                                </div>
                            </div>
                            <div class="gauge-label">Nivel de Gas en Ambiente</div>
                        </div>

                        <p class="card-text">
                            <i class="fas fa-toggle-on"></i>Estado actual: 
                            <span id="estado-actual" class="<?= $dispositivo->estado_valvula ? 'abierta' : 'cerrada' ?>">
                                <?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?>
                            </span>
                        </p>
                        
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir" aria-label="Abrir válvula de gas">
                                <i class="fas fa-fan"></i>Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar" aria-label="Cerrar válvula de gas">
                                <i class="fas fa-stop"></i>Cerrar Válvula
                            </button>
                            <button type="button" class="btn btn-primary-custom" id="btn-perfil" aria-label="Ir al perfil de usuario">
                                <i class="fas fa-user-cog"></i>Configuración
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i>No se pudo cargar la información del dispositivo. Por favor, verifique la conexión o seleccione otro dispositivo.
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
    const estadoActualSpan = $('#estado-actual');
    const gaugeFill = $('#gaugeFill');
    const gasLevelSpan = $('#gasLevel');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');

    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    // Efecto de carga inicial
    $('.card').css('opacity', 0).animate({opacity: 1}, 600);
    
    function actualizarEstadoUI(estado) {
        estadoActualSpan.text(estado ? 'Abierta' : 'Cerrada');
        if (estado) {
            estadoActualSpan.removeClass('cerrada').addClass('abierta');
            btnAbrir.prop('disabled', true).css('opacity', 0.7);
            btnCerrar.prop('disabled', false).css('opacity', 1);
        } else {
            estadoActualSpan.removeClass('abierta').addClass('cerrada');
            btnAbrir.prop('disabled', false).css('opacity', 1);
            btnCerrar.prop('disabled', true).css('opacity', 0.7);
        }
    }

    function updateGauge(level) {
        level = Math.max(0, Math.min(100, parseFloat(level))); 
        const rotation = (level / 100) * 180;
        gaugeFill.css('transform', `rotate(${rotation}deg)`);
        gasLevelSpan.text(`${level.toFixed(1)}%`); 

        // Actualizar colores según el nivel
        if (level < 30) {
            gasLevelSpan.css('color', 'var(--success-color)');
        } else if (level < 70) {
            gasLevelSpan.css('color', 'var(--warning-color)');
        } else {
            gasLevelSpan.css('color', 'var(--danger-color)');
            
            // Mostrar alerta si el nivel es peligroso
            if (level > 80 && !estadoActualSpan.hasClass('cerrada')) {
                showGasAlert(level);
            }
        }
    }
    
    function showGasAlert(level) {
        Swal.fire({
            title: '¡Nivel de Gas Peligroso!',
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
                showErrorToast('Error al obtener estado del dispositivo');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de conexión:', textStatus, errorThrown);
            showErrorToast('Error de conexión con el servidor');
        });
    }

    // Cargar estado inicial
    fetchDeviceState();
    
    // Eventos de botones
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1, 'Válvula abierta correctamente');
    });
    
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0, 'Válvula cerrada correctamente');
    });

    $('#btn-perfil').click(function() {
        window.location.href = '<?= base_url('perfil') ?>';
    });
    
    function controlarServo(mac, estado, successMessage) {
        const postData = {
            mac: mac,
            estado: estado
        };
        
        if (csrfName && csrfHash) {
            postData[csrfName] = csrfHash;
        }

        // Mostrar loader
        const buttons = estado ? [btnAbrir, btnCerrar] : [btnCerrar, btnAbrir];
        buttons[0].html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
        
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
                buttons[0].html(estado ? 
                    '<i class="fas fa-fan"></i>Abrir Válvula' : 
                    '<i class="fas fa-stop"></i>Cerrar Válvula').prop('disabled', estado);
                fetchDeviceState();
            });
    }
    
    // Actualizar cada 5 segundos
    const intervalId = setInterval(fetchDeviceState, 5000);
    
    // Limpiar intervalo al salir de la página
    $(window).on('beforeunload', function() {
        clearInterval(intervalId);
    });
});
<?php endif; ?>
</script>
</body>
</html>