<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas | ASG</title>
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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #2c73d2;
            --secondary-color: #7209b7;
            --success-color: #00b894;
            --danger-color: #d62828;
            --warning-color: #fca311;
            --dark-color: #1a1a1a;
            --light-color: #f4f4f9;
            --text-color: #333;
            --card-bg: #ffffff;
            --border-radius: 20px;
            --box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.4s ease-in-out;
        }

        body {
            background: linear-gradient(135deg, var(--light-color) 0%, #dcdfe5 100%);
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container.main-content {
            padding: 2rem 1rem;
            animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: var(--card-bg);
            padding: 3rem;
            border: none;
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            z-index: 1;
            transform: scale(1);
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.15);
        }

        h2 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 2.5rem;
            text-align: center;
            font-size: 2.8rem;
            position: relative;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: textGradient 6s ease infinite;
        }
        
        @keyframes textGradient {
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
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 3px;
        }

        .card-title, .card-subtitle, .card-text {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .card-title i {
            color: var(--primary-color);
        }
        
        .card-subtitle i {
            color: #888;
        }
        
        .card-text i {
            color: var(--primary-color);
        }

        #estado-actual {
            font-weight: 700;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            transition: var(--transition);
            min-width: 120px;
            text-align: center;
            font-size: 1.1rem;
        }

        #estado-actual.abierta {
            color: var(--success-color);
            background-color: rgba(0, 184, 148, 0.1);
        }

        #estado-actual.cerrada {
            color: var(--danger-color);
            background-color: rgba(214, 40, 40, 0.1);
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.9rem 2rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            gap: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-success {
            background-color: var(--success-color);
            color: #fff;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: #fff;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        /* Nuevo velocímetro radial */
        .gauge-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .gauge-circle {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: conic-gradient(
                var(--success-color) 0% 30%,
                var(--warning-color) 30% 70%,
                var(--danger-color) 70% 100%
            );
            position: relative;
            transition: all 0.5s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .gauge-inner-circle {
            width: 180px;
            height: 180px;
            background-color: var(--card-bg);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .gauge-level {
            font-size: 3rem;
            font-weight: 700;
            color: var(--dark-color);
            transition: color 0.5s ease;
            position: relative;
            z-index: 2;
        }

        .gauge-label {
            font-size: 1rem;
            color: #888;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .gauge-pointer {
            position: absolute;
            width: 80px;
            height: 4px;
            background-color: var(--dark-color);
            border-radius: 2px;
            bottom: 50%;
            left: 50%;
            transform-origin: 0 2px;
            transform: rotate(-90deg);
            transition: transform 1s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1;
        }
        
        .gauge-pointer::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background-color: var(--dark-color);
            border-radius: 50%;
            top: -4px;
            right: -6px;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
        }
        
        /* Sorpresa: partículas de fondo */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
            opacity: 0.3;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: moveParticles 20s infinite linear;
        }

        @keyframes moveParticles {
            0% { transform: translate(0, 0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translate(100vw, 100vh); opacity: 0; }
        }
        
        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            :root {
                --dark-color: #f4f4f9;
                --text-color: #f4f4f9;
                --card-bg: #2a2a2a;
            }
            body {
                background: linear-gradient(135deg, #121212 0%, #1e1e1e 100%);
            }
            h2 {
                color: var(--dark-color);
            }
            h2::after {
                background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            }
            .card {
                background-color: var(--card-bg);
            }
            .card-title, .card-subtitle, .card-text {
                color: #e0e0e0;
            }
            .gauge-inner-circle {
                background-color: #1e1e1e;
            }
            .gauge-level {
                color: var(--dark-color);
            }
            .gauge-pointer {
                background-color: var(--dark-color);
            }
        }
        
        /* Media Queries */
        @media (max-width: 768px) {
            .card {
                padding: 2rem;
            }
            h2 {
                font-size: 2.2rem;
            }
            .gauge-circle {
                width: 200px;
                height: 200px;
            }
            .gauge-inner-circle {
                width: 140px;
                height: 140px;
            }
            .gauge-level {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>

<div class="particles"></div>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2>Control de Válvula de Gas</h2>
            <div class="card">
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger pulse" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        <h5 class="card-title" id="nombre-dispositivo">
                            <i class="fas fa-gas-pump"></i> Dispositivo: <?= esc($dispositivo->nombre) ?>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted" id="ubicacion-dispositivo">
                            <i class="fas fa-map-marker-alt"></i> Ubicación: <?= esc($dispositivo->ubicacion) ?>
                        </h6>
                        
                        <div class="gauge-container">
                            <div class="gauge-circle">
                                <div class="gauge-inner-circle">
                                    <div class="gauge-level" id="gasLevel">0%</div>
                                    <div class="gauge-label">Nivel de Gas</div>
                                </div>
                                <div class="gauge-pointer" id="gaugePointer"></div>
                            </div>
                        </div>

                        <p class="card-text">
                            <i class="fas fa-toggle-on"></i> Estado actual: 
                            <span id="estado-actual" class="<?= $dispositivo->estado_valvula ? 'abierta' : 'cerrada' ?>">
                                <?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?>
                            </span>
                        </p>
                        
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir" aria-label="Abrir válvula de gas">
                                <i class="fas fa-play"></i> Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar" aria-label="Cerrar válvula de gas">
                                <i class="fas fa-stop"></i> Cerrar Válvula
                            </button>
                            <button type="button" class="btn btn-primary-custom" id="btn-perfil" aria-label="Ir al perfil de usuario">
                                <i class="fas fa-cog"></i> Configuración
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> No se pudo cargar la información del dispositivo. Por favor, verifique la conexión o seleccione otro dispositivo.
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
    const gaugeLevelSpan = $('#gasLevel');
    const gaugePointer = $('#gaugePointer');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');
    const gaugeCircle = $('.gauge-circle');

    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    // Función para generar partículas
    function createParticles() {
        const particlesContainer = $('.particles');
        const count = 30;
        for (let i = 0; i < count; i++) {
            const size = Math.random() * 8 + 2;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const delay = Math.random() * 20;
            const duration = Math.random() * 15 + 10;
            const particle = $('<div>').addClass('particle').css({
                width: `${size}px`,
                height: `${size}px`,
                left: `${x}vw`,
                top: `${y}vh`,
                animationDelay: `${delay}s`,
                animationDuration: `${duration}s`
            });
            particlesContainer.append(particle);
        }
    }
    
    createParticles();

    function actualizarEstadoUI(estado) {
        estadoActualSpan.text(estado ? 'Abierta' : 'Cerrada');
        if (estado) {
            estadoActualSpan.removeClass('cerrada').addClass('abierta');
            btnAbrir.prop('disabled', true).css('opacity', 0.6);
            btnCerrar.prop('disabled', false).css('opacity', 1);
        } else {
            estadoActualSpan.removeClass('abierta').addClass('cerrada');
            btnAbrir.prop('disabled', false).css('opacity', 1);
            btnCerrar.prop('disabled', true).css('opacity', 0.6);
        }
    }

    function updateGauge(level) {
        level = Math.max(0, Math.min(100, parseFloat(level)));
        const rotation = (level / 100) * 180;
        gaugePointer.css('transform', `rotate(${rotation - 90}deg)`);
        gaugeLevelSpan.text(`${level.toFixed(1)}%`);
        
        // Efectos de color y vibración
        if (level < 30) {
            gaugeLevelSpan.css('color', 'var(--success-color)');
            gaugeCircle.removeClass('pulse');
        } else if (level < 70) {
            gaugeLevelSpan.css('color', 'var(--warning-color)');
            gaugeCircle.removeClass('pulse');
        } else {
            gaugeLevelSpan.css('color', 'var(--danger-color)');
            gaugeCircle.addClass('pulse'); // Efecto de pulso en el medidor
            
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
            backdrop: `rgba(214, 40, 40, 0.15)`,
            allowOutsideClick: false
        });
    }

    function fetchDeviceState() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            if (response.status === 'success') {
                actualizarEstadoUI(response.estado_valvula);
                updateGauge(response.nivel_gas || 0);
            } else {
                console.error('Error al obtener estado:', response.message);
                showErrorToast('Error al obtener estado del dispositivo');
            }
        }).fail(function() {
            showErrorToast('Error de conexión con el servidor');
        });
    }

    fetchDeviceState();
    
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

        const buttons = estado ? [btnAbrir, btnCerrar] : [btnCerrar, btnAbrir];
        buttons[0].html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
        
        $.post('/servo/actualizarEstado', postData)
            .done(function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: successMessage,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al actualizar el estado',
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.',
                });
            })
            .always(function() {
                buttons[0].html(estado ? '<i class="fas fa-play"></i> Abrir Válvula' : '<i class="fas fa-stop"></i> Cerrar Válvula');
                fetchDeviceState();
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