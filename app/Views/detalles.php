<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Configuración PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Meta tags para CSRF token. -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container.main-content {
            margin-top: 20px;
            padding-bottom: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            background-color: #ffffff;
            padding: 30px;
            border: none;
            overflow: hidden; /* Para asegurar que el velocímetro no se desborde */
        }
        h2 {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.2rem;
            position: relative;
        }
        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background-color: #007bff;
            border-radius: 2px;
        }
        .card-title {
            color: #007bff;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .card-title i {
            margin-right: 10px;
            color: #0056b3;
        }
        .card-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .card-subtitle i {
            margin-right: 10px;
            color: #5a6268;
        }
        .card-text {
            font-size: 1.2rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center; /* Centrar el texto del estado de la válvula */
            text-align: center;
        }
        .card-text i {
            margin-right: 10px;
            color: #6f42c1; /* Un color distintivo para el estado */
        }
        #estado-actual {
            font-weight: 700;
        }
        #estado-actual.abierta {
            color: #28a745;
        }
        #estado-actual.cerrada {
            color: #dc3545;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn i {
            margin-right: 8px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .alert i {
            margin-right: 8px;
        }

        /* Estilos del Velocímetro */
        .gauge-container {
            width: 100%;
            display: flex;
            flex-direction: column; /* Para alinear el velocímetro y su etiqueta */
            align-items: center;
            margin-bottom: 30px;
        }
        .gauge {
            width: 180px;
            height: 90px;
            overflow: hidden;
            position: relative;
            background: #e0e0e0;
            border-radius: 90px 90px 0 0;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }
        .gauge-fill {
            height: 100%;
            width: 100%;
            transform-origin: bottom center;
            transition: transform 0.6s ease-out;
            background: linear-gradient(to right, #28a745 0%, #ffc107 50%, #dc3545 100%);
            border-radius: 90px 90px 0 0;
            position: absolute;
            bottom: 0;
        }
        .gauge-cover {
            width: 140px;
            height: 70px;
            background: #ffffff;
            border-radius: 70px 70px 0 0;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #343a40;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 -5px 10px rgba(0,0,0,0.1);
            z-index: 1; /* Asegura que la cubierta esté sobre el relleno */
        }
        .gauge-label {
            margin-top: 10px; /* Espacio entre el velocímetro y su etiqueta */
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .gauge-level {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .card {
                padding: 20px;
            }
            h2 {
                font-size: 1.8rem;
            }
            .card-title {
                font-size: 1.5rem;
            }
            .card-subtitle {
                font-size: 1rem;
            }
            .card-text {
                font-size: 1.1rem;
            }
            .btn-group {
                flex-direction: column;
                gap: 10px;
            }
            .btn {
                width: 100%;
            }
            .gauge {
                width: 150px;
                height: 75px;
            }
            .gauge-cover {
                width: 110px;
                height: 55px;
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
                        
                        <!-- Velocímetro de Nivel de Gas -->
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
        // Asegurarse de que el nivel esté entre 0 y 100
        level = Math.max(0, Math.min(100, level)); 
        const rotation = (level / 100) * 180;
        gaugeFill.css('transform', `rotate(${rotation}deg)`);
        gasLevelSpan.text(`${level.toFixed(1)}%`); // Mostrar un decimal para mayor precisión

        if (level < 30) {
            gasLevelSpan.css('color', '#28a745'); // Verde (Seguro)
        } else if (level < 70) {
            gasLevelSpan.css('color', '#ffc107'); // Amarillo (Precaución)
        } else {
            gasLevelSpan.css('color', '#dc3545'); // Rojo (Peligro)
        }
    }
    
    // Función para obtener el estado del dispositivo (válvula y gas)
    function fetchDeviceState() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            console.log('Respuesta del backend (obtenerEstado):', response); // Para depuración
            if (response.status === 'success') { // Usar el campo 'status' de la respuesta
                actualizarEstadoUI(response.estado_valvula);
                if (response.nivel_gas !== undefined && response.nivel_gas !== null) {
                    updateGauge(response.nivel_gas);
                } else {
                    console.warn('Nivel de gas no definido o nulo en la respuesta.');
                    updateGauge(0); // Opcional: poner a 0 si no hay datos
                }
            } else {
                console.error('Error al obtener estado del dispositivo:', response.message);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de red o servidor al obtener estado:', textStatus, errorThrown);
            console.error('Detalles del error:', jqXHR.responseText); // Mostrar respuesta del servidor
        });
    }

    // Obtener estado inicial del dispositivo al cargar la página
    fetchDeviceState();
    
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1);
    });
    
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0);
    });
    
    function controlarServo(mac, estado) {
        const postData = {
            mac: mac,
            estado: estado
        };
        // Añadir el token CSRF dinámicamente
        if (csrfName && csrfHash) {
            postData[csrfName] = csrfHash;
        } else {
            console.warn('CSRF token o hash no encontrados. La petición POST podría fallar.');
        }

        $.post('/servo/actualizarEstado', postData, function(response) {
            console.log('Respuesta del backend (actualizarEstado):', response); // Para depuración
            if (response.status === 'success') { // Usar el campo 'status' de la respuesta
                actualizarEstadoUI(response.estado);
                // Después de controlar el servo, volvemos a obtener el estado para asegurar la sincronización
                fetchDeviceState(); 
            } else {
                console.error('Error al controlar servo:', response.message);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de red o servidor al enviar comando:', textStatus, errorThrown);
            console.error('Detalles del error:', jqXHR.responseText); // Mostrar respuesta del servidor
        });
    }
    
    // Actualizar el estado del dispositivo y el nivel de gas periódicamente
    setInterval(fetchDeviceState, 5000); // Llamar a la función de obtención de estado cada 5 segundos
});
<?php endif; ?>
</script>
</body>
</html>
