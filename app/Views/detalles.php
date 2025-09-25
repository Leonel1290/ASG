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
            --offline-color: #95a5a6;
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
        }
        .container.main-content { padding: 2rem 1rem; animation: fadeIn 0.8s ease-in-out; position: relative; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .card { border-radius: var(--border-radius); box-shadow: var(--box-shadow); background-color: var(--card-background); padding: 3.5rem; border: none; transition: var(--transition); }
        h2.main-title { font-weight: 700; text-align: center; font-size: 2rem; margin-bottom: 2rem; color: var(--text-color); }
        .device-info { text-align: center; margin-bottom: 2rem; }
        .device-info .name { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-color); }
        .device-info .location { font-size: 1rem; color: var(--text-secondary); font-weight: 400; }
        .device-status { text-align: center; margin-bottom: 1rem; }
        .device-status .status { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 500; }
        .status.online { background-color: rgba(0, 184, 148, 0.1); color: var(--success-color); }
        .status.offline { background-color: rgba(149, 165, 166, 0.1); color: var(--offline-color); }
        
        /* Estilos para el velocímetro */
        .gauge-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 3rem;
        }
        .gauge {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            position: relative;
            background: #f0f0f0;
            overflow: hidden;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .gauge.offline {
            opacity: 0.6;
            filter: grayscale(70%);
        }
        .gauge-fill {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, var(--success-color), var(--warning-color), var(--danger-color));
            transition: height 1s ease-in-out;
            transform-origin: center bottom;
        }
        .gauge-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            background: var(--card-background);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }
        .gas-level-value {
            font-size: 2.5rem;
            font-weight: 700;
            z-index: 3;
            transition: color 0.5s ease;
        }
        .gauge-markers {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .gauge-marker {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform-origin: bottom center;
            height: 10px;
            width: 2px;
            background: rgba(0, 0, 0, 0.3);
        }
        .gauge-marker.major {
            height: 15px;
            width: 3px;
            background: rgba(0, 0, 0, 0.5);
        }
        .gauge-label {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
        }
        
        .valve-status { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 2rem; }
        .valve-status-text { font-size: 1.2rem; font-weight: 600; color: var(--text-color); }
        .status-led { width: 15px; height: 15px; border-radius: 50%; background-color: #ccc; box-shadow: 0 0 5px rgba(0,0,0,0.2); transition: background-color 0.5s ease; }
        .status-led.abierta { background-color: var(--success-color); box-shadow: 0 0 10px var(--success-color), 0 0 20px var(--success-color); }
        .status-led.cerrada { background-color: var(--danger-color); box-shadow: 0 0 10px var(--danger-color), 0 0 20px var(--danger-color); }
        .status-led.offline { background-color: var(--offline-color); box-shadow: 0 0 10px var(--offline-color), 0 0 20px var(--offline-color); }
        
        .btn-group-actions { display: flex; flex-direction: column; gap: 1.2rem; justify-content: center; }
        .btn { padding: 1.2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: var(--transition); display: flex; align-items: center; justify-content: center; gap: 10px; border: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .btn-success { background-color: var(--success-color); color: #fff; }
        .btn-danger { background-color: var(--danger-color); color: #fff; }
        .btn-secondary-custom { background-color: var(--text-secondary); color: #fff; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .connection-status {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        @media (prefers-color-scheme: dark) {
            :root { 
                --background-color: #121212; 
                --card-background: #1e1e1e; 
                --text-color: #f5f5f5; 
                --text-secondary: #aaa; 
                --offline-color: #7f8c8d;
            }
            body { background: linear-gradient(135deg, #1e1e1e 0%, #121212 100%); }
            .card { box-shadow: 0 10px 30px rgba(255, 255, 255, 0.03); }
            .btn { box-shadow: 0 4px 15px rgba(255, 255, 255, 0.05); }
            .btn-secondary-custom { background-color: #555; }
            .gauge { background: #2a2a2a; }
            .gauge-center { background: var(--card-background); }
        }
    </style>
</head>
<body>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
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
                        </div>
                        
                        <div class="device-status">
                            <div class="status offline" id="deviceStatus">
                                <i class="fas fa-plug"></i> <span id="statusText">Desconectado</span>
                            </div>
                        </div>
                        
                        <!-- Velocímetro -->
                        <div class="gauge-container">
                            <div class="gauge offline" id="gauge">
                                <div class="gauge-fill" id="gasLevelFill"></div>
                                <div class="gauge-markers" id="gaugeMarkers"></div>
                                <div class="gauge-center">
                                    <div class="gas-level-value" id="gasLevel">--%</div>
                                </div>
                            </div>
                            <div class="gauge-label">Nivel de Gas</div>
                        </div>

                        <div class="valve-status">
                            <div class="valve-status-text">Estado actual:</div>
                            <div class="status-led offline" id="statusLed"></div>
                        </div>
                        
                        <div class="btn-group-actions" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir" aria-label="Abrir válvula de gas" disabled>
                                <i class="fas fa-play"></i> Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar" aria-label="Cerrar válvula de gas" disabled>
                                <i class="fas fa-stop"></i> Cerrar Válvula
                            </button>
                            <button type="button" class="btn btn-secondary-custom" id="btn-perfil" aria-label="Volver al perfil de usuario">
                                <i class="fas fa-arrow-left"></i> Volver al Perfil
                            </button>
                        </div>
                        
                        <div class="connection-status" id="connectionStatus">
                            <i class="fas fa-sync-alt fa-spin"></i> Intentando conectar con el dispositivo...
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
    const macUrl = encodeURIComponent(mac);
    const statusLed = $('#statusLed');
    const gasLevelSpan = $('#gasLevel');
    const gasLevelFill = $('#gasLevelFill');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');
    const gaugeMarkers = $('#gaugeMarkers');
    const gauge = $('#gauge');
    const deviceStatus = $('#deviceStatus');
    const statusText = $('#statusText');
    const connectionStatus = $('#connectionStatus');
    
    let isOnline = false;
    let retryCount = 0;
    const maxRetries = 10;

    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    // Crear marcadores para el velocímetro
    function createGaugeMarkers() {
        gaugeMarkers.empty();
        for (let i = 0; i <= 10; i++) {
            const angle = (i * 18) - 90;
            const marker = $('<div>').addClass(i % 5 === 0 ? 'gauge-marker major' : 'gauge-marker');
            marker.css({
                transform: `rotate(${angle}deg) translateX(-50%)`
            });
            gaugeMarkers.append(marker);
        }
    }
    
    createGaugeMarkers();

    function setDeviceOnline(online) {
        isOnline = online;
        if (online) {
            deviceStatus.removeClass('offline').addClass('online');
            statusText.html('<i class="fas fa-wifi"></i> Conectado');
            gauge.removeClass('offline');
            statusLed.removeClass('offline');
            connectionStatus.html('<i class="fas fa-wifi"></i> Conectado al dispositivo');
            retryCount = 0;
            
            // Habilitar botones
            btnAbrir.prop('disabled', false);
            btnCerrar.prop('disabled', false);
        } else {
            deviceStatus.removeClass('online').addClass('offline');
            statusText.html('<i class="fas fa-plug"></i> Desconectado');
            gauge.addClass('offline');
            statusLed.addClass('offline');
            
            // Deshabilitar botones
            btnAbrir.prop('disabled', true);
            btnCerrar.prop('disabled', true);
            
            if (retryCount < maxRetries) {
                connectionStatus.html(`<i class="fas fa-sync-alt fa-spin"></i> Intentando reconectar... (${retryCount + 1}/${maxRetries})`);
            } else {
                connectionStatus.html('<i class="fas fa-exclamation-triangle"></i> Dispositivo no disponible. Verifica la conexión.');
            }
        }
    }

    function actualizarEstadoUI(estado) {
        if (estado) {
            statusLed.removeClass('cerrada').addClass('abierta');
            btnAbrir.prop('disabled', true).css('opacity', 0.6);
            btnCerrar.prop('disabled', false).css('opacity', 1);
        } else {
            statusLed.removeClass('abierta').addClass('cerrada');
            btnAbrir.prop('disabled', false).css('opacity', 1);
            btnCerrar.prop('disabled', true).css('opacity', 0.6);
        }
    }

    function updateGauge(level) {
        level = Math.max(0, Math.min(100, parseFloat(level)));
        const height = (level / 100) * 100;
        gasLevelFill.css('height', `${height}%`);
        gasLevelSpan.text(`${level.toFixed(1)}%`);
        
        if (level < 30) {
            gasLevelSpan.css('color', 'var(--success-color)');
        } else if (level < 70) {
            gasLevelSpan.css('color', 'var(--warning-color)');
        } else {
            gasLevelSpan.css('color', 'var(--danger-color)');
            if (level > 80 && !statusLed.hasClass('cerrada')) {
                showGasAlert(level);
            }
        }
    }

    function fetchDeviceState() {
        $.ajax({
            url: '<?= base_url() ?>' + 'lecturas/obtenerUltimaLectura/' + macUrl,
            method: 'GET',
            dataType: 'json',
            timeout: 8000,
            success: function(response) {
                if (response.status === 'success') {
                    setDeviceOnline(true);
                    actualizarEstadoUI(response.estado_valvula); 
                    updateGauge(response.nivel_gas || 0);
                } else {
                    setDeviceOnline(false);
                    console.warn('Dispositivo sin datos:', response.message);
                }
            },
            error: function(xhr, status, error) {
                setDeviceOnline(false);
                retryCount++;
                
                if (retryCount >= maxRetries) {
                    console.warn('Dispositivo offline después de múltiples intentos');
                    clearInterval(intervalId);
                }
            }
        });
    }

    function showGasAlert(level) {
        Swal.fire({
            title: '¡Nivel de Gas Peligroso!',
            text: `El nivel de gas ha alcanzado ${level.toFixed(1)}%. Se recomienda cerrar la válvula.`,
            icon: 'error',
            confirmButtonText: 'Entendido',
            confirmButtonColor: 'var(--danger-color)',
            backdrop: `rgba(214, 40, 40, 0.15)`,
            allowOutsideClick: false
        });
    }

    $('#btn-abrir').click(function() {
        controlarServo(mac, 1, 'Válvula abierta');
    });
    
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0, 'Válvula cerrada');
    });

    $('#btn-perfil').click(function() {
        window.location.href = '<?= base_url('perfil') ?>';
    });
    
    function controlarServo(mac, estado, successMessage) {
        if (!isOnline) {
            Swal.fire({
                icon: 'warning',
                title: 'Dispositivo offline',
                text: 'No se puede controlar la válvula mientras el dispositivo esté desconectado.',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        const postData = {
            mac: mac,
            estado: estado
        };
        if (csrfName && csrfHash) {
            postData[csrfName] = csrfHash;
        }
        
        const buttons = estado ? [btnAbrir, btnCerrar] : [btnCerrar, btnAbrir];
        buttons[0].html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
        
        $.post('<?= base_url() ?>' + 'servo/actualizarEstado', postData)
            .done(function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                    Swal.fire({ icon: 'success', title: '¡Éxito!', text: successMessage, timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error al actualizar' });
                }
            })
            .fail(function() {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo comunicar con el servidor.' });
            })
            .always(function() {
                buttons[0].html(estado ? '<i class="fas fa-play"></i> Abrir Válvula' : '<i class="fas fa-stop"></i> Cerrar Válvula');
                fetchDeviceState();
            });
    }
    
    // Inicializar con estado offline
    setDeviceOnline(false);
    updateGauge(0);
    gasLevelSpan.text('--%');
    gasLevelSpan.css('color', 'var(--offline-color)');
    
    // Intentar conectar
    fetchDeviceState();
    const intervalId = setInterval(fetchDeviceState, 5000);
    $(window).on('beforeunload', function() { clearInterval(intervalId); });
});
<?php endif; ?>
</script>
</body>
</html>