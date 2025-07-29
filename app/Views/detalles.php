<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo</title>
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            /* Degradado de fondo de gris claro a gris oscuro */
            background: linear-gradient(to bottom, #f0f0f0, #cccccc); 
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #343a40; /* Color de texto principal */
        }
        .container.main-content {
            margin-top: 20px;
            padding-bottom: 20px;
        }
        .card {
            border-radius: 20px; /* Bordes más redondeados */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); /* Sombra más pronunciada */
            background-color: #ffffff;
            padding: 30px;
            border: none;
            overflow: hidden;
            transition: transform 0.3s ease-in-out; /* Transición para el hover */
        }
        .card:hover {
            transform: translateY(-5px); /* Efecto hover */
        }
        h2 {
            color: #343a40; /* Color de título oscuro para contrastar con el fondo gris */
            font-weight: 700;
            margin-bottom: 40px; /* Más espacio inferior */
            text-align: center;
            font-size: 2.5rem; /* Título más grande */
            position: relative;
            text-shadow: none; /* Quitamos la sombra de texto */
        }
        h2::after {
            content: '';
            position: absolute;
            bottom: -15px; /* Más abajo */
            left: 50%;
            transform: translateX(-50%);
            width: 80px; /* Más ancho */
            height: 5px; /* Más grueso */
            background-color: #6c757d; /* Línea gris para combinar con el fondo */
            border-radius: 3px;
        }
        .card-title {
            color: #007bff;
            font-size: 2rem; /* Título de tarjeta más grande */
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center; /* Centrar título de la tarjeta */
        }
        .card-title i {
            margin-right: 12px; /* Más espacio para el ícono */
            color: #0056b3;
            font-size: 1.8rem; /* Ícono más grande */
        }
        .card-subtitle {
            color: #6c757d;
            font-size: 1.2rem; /* Subtítulo más grande */
            margin-bottom: 25px; /* Más espacio inferior */
            display: flex;
            align-items: center;
            justify-content: center; /* Centrar subtítulo de la tarjeta */
        }
        .card-subtitle i {
            margin-right: 12px;
            color: #5a6268;
            font-size: 1.1rem;
        }
        .card-text {
            font-size: 1.3rem; /* Texto más grande */
            margin-bottom: 30px; /* Más espacio inferior */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 15px; /* Añadir padding horizontal */
        }
        .card-text i {
            margin-right: 10px;
            color: #6f42c1;
            font-size: 1.4rem;
        }
        #estado-actual {
            font-weight: 700;
            transition: color 0.3s ease-in-out; /* Transición de color para el estado */
        }
        #estado-actual.abierta {
            color: #28a745;
        }
        #estado-actual.cerrada {
            color: #dc3545;
        }
        .btn-group {
            display: flex;
            gap: 20px; /* Más espacio entre botones */
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap; /* Permite que los botones se envuelvan en pantallas pequeñas */
        }
        .btn {
            padding: 15px 30px; /* Más padding para botones */
            border-radius: 30px; /* Botones más redondeados */
            font-size: 1.2rem; /* Fuente más grande */
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 180px; /* Ancho mínimo para mantener uniformidad */
        }
        .btn i {
            margin-right: 10px; /* Más espacio para el ícono */
            font-size: 1.3rem;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-3px); /* Efecto 3D al pasar el mouse */
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }
        .btn-primary-custom { /* Nuevo estilo para el botón de perfil */
            background-color: #007bff;
            border-color: #007bff;
            color: #ffffff;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
            border-color: #004085;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4);
        }
        .alert {
            border-radius: 15px; /* Bordes más redondeados */
            margin-bottom: 25px;
            font-size: 1.1rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Estilos del Velocímetro Mejorado */
        .gauge-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 40px; /* Más espacio inferior */
            position: relative;
        }
        .gauge {
            width: 220px; /* Velocímetro más grande */
            height: 110px;
            overflow: hidden;
            position: relative;
            background: #e0e0e0;
            border-radius: 110px 110px 0 0;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.15); /* Sombra interna más suave */
        }
        .gauge-fill {
            height: 100%;
            width: 100%;
            transform-origin: bottom center;
            transition: transform 0.8s ease-out; /* Transición más suave */
            background: linear-gradient(to right, #28a745 0%, #ffc107 50%, #dc3545 100%);
            border-radius: 110px 110px 0 0;
            position: absolute;
            bottom: 0;
        }
        .gauge-cover {
            width: 180px; /* Cubierta más grande */
            height: 90px;
            background: #ffffff;
            border-radius: 90px 90px 0 0;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #343a40;
            font-weight: bold;
            font-size: 1.8rem; /* Número de nivel más grande */
            box-shadow: 0 -8px 20px rgba(0,0,0,0.15); /* Sombra de la cubierta más pronunciada */
            z-index: 1;
        }
        .gauge-label {
            margin-top: 15px; /* Más espacio */
            text-align: center;
            font-size: 1rem; /* Etiqueta más legible */
            color: #6c757d;
        }
        .gauge-level {
            font-size: 1.8rem; /* Se ajusta con .gauge-cover font-size */
            font-weight: bold;
            color: #007bff; /* Color inicial, será sobrescrito por JS */
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .card {
                padding: 25px;
            }
            h2 {
                font-size: 2rem;
                margin-bottom: 30px;
            }
            h2::after {
                width: 70px;
                height: 4px;
                bottom: -10px;
            }
            .card-title {
                font-size: 1.7rem;
            }
            .card-title i {
                font-size: 1.5rem;
            }
            .card-subtitle {
                font-size: 1.1rem;
                margin-bottom: 20px;
            }
            .card-subtitle i {
                font-size: 1rem;
            }
            .card-text {
                font-size: 1.2rem;
                margin-bottom: 25px;
            }
            .card-text i {
                font-size: 1.3rem;
            }
            .btn-group {
                flex-direction: column;
                gap: 15px;
            }
            .btn {
                width: 100%;
                padding: 12px 25px;
                font-size: 1.1rem;
            }
            .btn i {
                font-size: 1.2rem;
            }
            .gauge {
                width: 180px;
                height: 90px;
            }
            .gauge-cover {
                width: 140px;
                height: 70px;
                font-size: 1.5rem;
            }
            .gauge-label {
                margin-top: 10px;
                font-size: 0.9rem;
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
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i><?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        <h5 class="card-title" id="nombre-dispositivo">
                            <i class="fas fa-microchip"></i>Dispositivo: <?= esc($dispositivo->nombre) ?>
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
                            <i class="fas fa-toggle-on"></i>Estado actual de la válvula: 
                            <span id="estado-actual" class="<?= $dispositivo->estado_valvula ? 'abierta' : 'cerrada' ?>">
                                <?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?>
                            </span>
                        </p>
                        
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir">
                                <i class="fas fa-valve"></i>Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar">
                                <i class="fas fa-valve-slash"></i>Cerrar Válvula
                            </button>
                            <button type="button" class="btn btn-primary-custom" id="btn-perfil">
                                <i class="fas fa-user-circle"></i>Ir al Perfil
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i>No se pudo cargar la información del dispositivo. Por favor, asegúrese de que la URL sea correcta o seleccione un dispositivo de su lista.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
<?php if (isset($dispositivo) && $dispositivo !== null): ?>
$(document).ready(function() {
    const mac = '<?= esc($dispositivo->MAC) ?>'; 
    const estadoActualSpan = $('#estado-actual');
    const gaugeFill = $('#gaugeFill');
    const gasLevelSpan = $('#gasLevel');

    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    function actualizarEstadoUI(estado) {
        estadoActualSpan.text(estado ? 'Abierta' : 'Cerrada');
        if (estado) {
            estadoActualSpan.removeClass('cerrada').addClass('abierta');
        } else {
            estadoActualSpan.removeClass('abierta').addClass('cerrada');
        }
    }

    function updateGauge(level) {
        level = Math.max(0, Math.min(100, level)); 
        const rotation = (level / 100) * 180;
        gaugeFill.css('transform', `rotate(${rotation}deg)`);
        gasLevelSpan.text(`${level.toFixed(1)}%`); 

        // Colores del texto del nivel de gas dentro del velocímetro
        if (level < 30) {
            gasLevelSpan.css('color', '#28a745'); // Verde (Seguro)
        } else if (level < 70) {
            gasLevelSpan.css('color', '#ffc107'); // Amarillo (Precaución)
        } else {
            gasLevelSpan.css('color', '#dc3545'); // Rojo (Peligro)
        }
    }
    
    function fetchDeviceState() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            console.log('Respuesta del backend (obtenerEstado):', response);
            if (response.status === 'success') {
                actualizarEstadoUI(response.estado_valvula);
                if (response.nivel_gas !== undefined && response.nivel_gas !== null) {
                    updateGauge(response.nivel_gas);
                } else {
                    console.warn('Nivel de gas no definido o nulo en la respuesta. Estableciendo a 0.');
                    updateGauge(0);
                }
            } else {
                console.error('Error al obtener estado del dispositivo:', response.message);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de red o servidor al obtener estado:', textStatus, errorThrown);
            console.error('Detalles del error:', jqXHR.responseText);
        });
    }

    fetchDeviceState();
    
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1);
    });
    
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0);
    });

    // Evento click para el nuevo botón de perfil
    $('#btn-perfil').click(function() {
        window.location.href = '<?= base_url('perfil') ?>'; // Redirige a la ruta del perfil
    });
    
    function controlarServo(mac, estado) {
        const postData = {
            mac: mac,
            estado: estado
        };
        if (csrfName && csrfHash) {
            postData[csrfName] = csrfHash;
        } else {
            console.warn('CSRF token o hash no encontrados. La petición POST podría fallar.');
        }

        $.post('/servo/actualizarEstado', postData, function(response) {
            console.log('Respuesta del backend (actualizarEstado):', response);
            if (response.status === 'success') {
                actualizarEstadoUI(response.estado);
                fetchDeviceState(); 
            } else {
                console.error('Error al controlar servo:', response.message);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de red o servidor al enviar comando:', textStatus, errorThrown);
            console.error('Detalles del error:', jqXHR.responseText);
        });
    }
    
    setInterval(fetchDeviceState, 5000);
});
<?php endif; ?>
</script>
</body>
</html>