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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        :root {
            --primary-color: #2c73d2;
            --success-color: #00b894;
            --danger-color: #d62828;
            --warning-color: #fca311;
            --background-color: #f0f2f5;
            --card-background: #ffffff;
            --text-color: #2c2c2c;
            --text-secondary: #6c757d;
            --border-radius: 20px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #e4e9f0 0%, #d5d7de 100%);
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color);
            padding: 20px;
        }

        .container.main-content {
            padding: 2rem 1rem;
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            width: 100%;
            max-width: 600px;
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: var(--card-background);
            padding: 2.5rem;
            border: none;
            transition: var(--transition);
            width: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        h2.main-title {
            font-weight: 700;
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: var(--text-color);
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .device-info {
            text-align: center;
            margin-bottom: 2.5rem;
            padding: 1rem;
            background: rgba(44, 115, 210, 0.05);
            border-radius: 15px;
        }

        .device-info .name {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .device-info .location {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .gas-level-container {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .gas-level-bubble {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 
                inset 0 0 20px rgba(0, 0, 0, 0.1),
                0 10px 30px rgba(0, 0, 0, 0.15);
            border: 8px solid #fff;
            transition: var(--transition);
        }

        .bubble-fill {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, var(--success-color), var(--warning-color), var(--danger-color));
            border-bottom-left-radius: 180px;
            border-bottom-right-radius: 180px;
            transition: height 1s ease-in-out, background-color 0.5s ease;
            height: 0%;
        }

        .gas-level-value {
            font-size: 2.8rem;
            font-weight: 700;
            position: relative;
            z-index: 2;
            background: linear-gradient(135deg, var(--primary-color), #2c73d2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gas-level-label {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .valve-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 2.5rem;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .valve-status-text {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .status-led {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #ccc;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            transition: all 0.5s ease;
            position: relative;
        }

        .status-led.abierta {
            background-color: var(--success-color);
            box-shadow: 
                0 0 15px var(--success-color),
                0 0 30px var(--success-color);
            animation: pulse 2s infinite;
        }

        .status-led.cerrada {
            background-color: var(--danger-color);
            box-shadow: 
                0 0 15px var(--danger-color),
                0 0 30px var(--danger-color);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .btn-group-actions {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            justify-content: center;
        }

        .btn {
            padding: 1.3rem;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #00a085);
            color: #fff;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c82333);
            color: #fff;
        }

        .btn-secondary-custom {
            background: linear-gradient(135deg, var(--text-secondary), #5a6268);
            color: #fff;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        }

        .alert {
            border-radius: 15px;
            border: none;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Modo oscuro */
        @media (prefers-color-scheme: dark) {
            :root {
                --background-color: #121212;
                --card-background: #1e1e1e;
                --text-color: #f5f5f5;
                --text-secondary: #aaa;
            }
            
            body {
                background: linear-gradient(135deg, #1e1e1e 0%, #121212 100%);
            }
            
            .card {
                background: var(--card-background);
                box-shadow: 0 10px 30px rgba(255, 255, 255, 0.03);
            }
            
            .gas-level-bubble {
                background: linear-gradient(135deg, #2a2a2a 0%, #1e1e1e 100%);
                border: 8px solid #2a2a2a;
            }
            
            .valve-status {
                background: rgba(255, 255, 255, 0.05);
            }
            
            .btn {
                box-shadow: 0 6px 20px rgba(255, 255, 255, 0.05);
            }
            
            .device-info {
                background: rgba(44, 115, 210, 0.1);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container.main-content {
                padding: 1rem 0.5rem;
            }
            
            .card {
                padding: 2rem 1.5rem;
            }
            
            h2.main-title {
                font-size: 1.8rem;
            }
            
            .gas-level-bubble {
                width: 150px;
                height: 150px;
            }
            
            .gas-level-value {
                font-size: 2.2rem;
            }
            
            .btn {
                padding: 1.1rem;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .gas-level-bubble {
                width: 130px;
                height: 130px;
            }
            
            .gas-level-value {
                font-size: 1.8rem;
            }
            
            .valve-status-text {
                font-size: 1.1rem;
            }
            
            .btn {
                padding: 1rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="main-title">Control de Válvula</h2>
            <div class="card">
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        <div class="device-info">
                            <div class="name">Dispositivo: <?= esc($dispositivo->nombre) ?></div>
                            <div class="location">Ubicación: <?= esc($dispositivo->ubicacion) ?></div>
                            <div class="mac-address" style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                MAC: <?= esc($dispositivo->MAC) ?>
                            </div>
                        </div>
                        
                        <div class="gas-level-container">
                            <div class="gas-level-bubble">
                                <div class="bubble-fill" id="gasLevelFill"></div>
                                <div class="gas-level-value" id="gasLevel">0%</div>
                            </div>
                            <div class="gas-level-label">Nivel de Gas Detectado</div>
                        </div>

                        <div class="valve-status">
                            <div class="valve-status-text">Estado actual:</div>
                            <div class="status-led" id="statusLed"></div>
                            <div class="valve-status-text" id="statusText">Cargando...</div>
                        </div>
                        
                        <div class="btn-group-actions" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir" aria-label="Abrir válvula de gas">
                                <i class="fas fa-play"></i> Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar" aria-label="Cerrar válvula de gas">
                                <i class="fas fa-stop"></i> Cerrar Válvula
                            </button>
                            <button type="button" class="btn btn-secondary-custom" id="btn-perfil" aria-label="Volver al perfil de usuario">
                                <i class="fas fa-arrow-left"></i> Volver al Perfil
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> No se pudo cargar el dispositivo.
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
    const statusLed = $('#statusLed');
    const statusText = $('#statusText');
    const gasLevelSpan = $('#gasLevel');
    const gasLevelFill = $('#gasLevelFill');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');

    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    // Estado inicial
    let estadoActual = null;
    let ultimoNivelGas = 0;

    function actualizarEstadoUI(estado) {
        estadoActual = estado;
        
        if (estado) {
            statusLed.removeClass('cerrada').addClass('abierta');
            statusText.text('VÁLVULA ABIERTA');
            btnAbrir.prop('disabled', true).css('opacity', 0.6);
            btnCerrar.prop('disabled', false).css('opacity', 1);
        } else {
            statusLed.removeClass('abierta').addClass('cerrada');
            statusText.text('VÁLVULA CERRADA');
            btnAbrir.prop('disabled', false).css('opacity', 1);
            btnCerrar.prop('disabled', true).css('opacity', 0.6);
        }
    }

    function updateGauge(level) {
        level = Math.max(0, Math.min(100, parseFloat(level)));
        ultimoNivelGas = level;
        
        const height = (level / 100) * 100;
        gasLevelFill.css('height', `${height}%`);
        gasLevelSpan.text(`${level.toFixed(1)}%`);
        
        // Cambiar colores según el nivel
        if (level < 30) {
            gasLevelFill.css('background', 'linear-gradient(to top, var(--success-color), #00b894)');
        } else if (level < 70) {
            gasLevelFill.css('background', 'linear-gradient(to top, var(--warning-color), #fca311)');
        } else {
            gasLevelFill.css('background', 'linear-gradient(to top, var(--danger-color), #d62828)');
            
            // Alerta de gas peligroso si la válvula está abierta
            if (estadoActual && level > 80) {
                showGasAlert(level);
            }
        }
    }

    function fetchDeviceState() {
        $.ajax({
            url: '/servo/obtenerEstado/' + encodeURIComponent(mac),
            method: 'GET',
            timeout: 10000,
            success: function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado_valvula);
                    updateGauge(response.nivel_gas || 0);
                } else {
                    console.error('Error al obtener estado:', response.message);
                    showError('Error al cargar estado del dispositivo');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error de conexión con el servidor:', error);
                if (status !== 'abort') {
                    showError('Error de conexión con el servidor');
                }
            }
        });
    }

    function showGasAlert(level) {
        if (estadoActual) { // Solo alerta si la válvula está abierta
            Swal.fire({
                title: '¡ALERTA DE GAS!',
                text: `Nivel crítico: ${level.toFixed(1)}%. Se recomienda cerrar la válvula inmediatamente.`,
                icon: 'error',
                confirmButtonText: 'Cerrar Válvula',
                confirmButtonColor: 'var(--danger-color)',
                showCancelButton: true,
                cancelButtonText: 'Ignorar',
                backdrop: `rgba(214, 40, 40, 0.2)`,
                allowOutsideClick: false,
                customClass: {
                    popup: 'sweetalert-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    controlarServo(mac, 0, 'Válvula cerrada por seguridad');
                }
            });
        }
    }

    function showError(message) {
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error',
            confirmButtonText: 'Reintentar',
            confirmButtonColor: 'var(--danger-color)'
        }).then(() => {
            fetchDeviceState();
        });
    }

    function showSuccess(message) {
        Swal.fire({
            title: '¡Éxito!',
            text: message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    // Event handlers
    btnAbrir.click(function() {
        controlarServo(mac, 1, 'Válvula abierta exitosamente');
    });
    
    btnCerrar.click(function() {
        controlarServo(mac, 0, 'Válvula cerrada exitosamente');
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

        const button = estado ? btnAbrir : btnCerrar;
        const originalHtml = button.html();
        
        button.html('<div class="loading-spinner"></div> Procesando...').prop('disabled', true);
        
        $.ajax({
            url: '/servo/actualizarEstado',
            method: 'POST',
            data: postData,
            timeout: 15000,
            success: function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                    showSuccess(successMessage);
                    
                    // El ESP32 será notificado automáticamente por el ServoController
                    console.log('Estado actualizado, ESP32 será notificado');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al actualizar el estado'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor. Verifica tu conexión.'
                });
            },
            complete: function() {
                button.html(originalHtml).prop('disabled', false);
                fetchDeviceState(); // Actualizar estado después de la operación
            }
        });
    }
    
    // Inicializar
    fetchDeviceState();
    
    // Actualizar cada 3 segundos
    const intervalId = setInterval(fetchDeviceState, 3000);
    
    // Limpiar intervalo al salir de la página
    $(window).on('beforeunload', function() {
        clearInterval(intervalId);
    });

    // Manejar visibilidad de la página (pausar actualizaciones cuando no está visible)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(intervalId);
        } else {
            fetchDeviceState();
            intervalId = setInterval(fetchDeviceState, 3000);
        }
    });
});
<?php else: ?>
$(document).ready(function() {
    $('#btn-perfil').click(function() {
        window.location.href = '<?= base_url('perfil') ?>';
    });
});
<?php endif; ?>
</script>
</body>
</html>