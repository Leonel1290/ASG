<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Enlace al archivo CSS externo (si lo tienes) -->
    <!-- <link rel="stylesheet" href="<?= base_url('css/detalle_dispositivo.css'); ?>"> -->

    <!-- Configuración PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

    <style>
        /* Estilos generales para el cuerpo de la página */
        body {
            background-color: #f8f9fa; /* Fondo gris claro */
            font-family: 'Inter', sans-serif; /* Fuente moderna */
        }
        /* Contenedor principal de la página */
        .container.main-content {
            margin-top: 50px;
            padding-bottom: 50px;
        }
        /* Estilo para las tarjetas (cards) */
        .card {
            border-radius: 15px; /* Bordes redondeados */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Sombra suave */
            background-color: #ffffff;
            padding: 30px; /* Más padding interno */
        }
        /* Estilo para el título principal */
        h2 {
            color: #343a40; /* Color oscuro */
            font-weight: 700; /* Negrita */
            margin-bottom: 30px;
            text-align: center;
        }
        /* Estilo para el título de la tarjeta (nombre del dispositivo) */
        .card-title {
            color: #007bff; /* Color primario de Bootstrap */
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        /* Estilo para el subtítulo de la tarjeta (ubicación) */
        .card-subtitle {
            color: #6c757d; /* Gris para subtítulos */
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        /* Estilo para el texto general de la tarjeta */
        .card-text {
            font-size: 1.2rem;
            margin-bottom: 25px;
        }
        /* Estilo para el span que muestra el estado actual de la válvula */
        #estado-actual {
            font-weight: 700;
        }
        /* Clases para colorear el estado de la válvula */
        #estado-actual.abierta {
            color: #28a745; /* Verde para "Abierta" */
        }
        #estado-actual.cerrada {
            color: #dc3545; /* Rojo para "Cerrada" */
        }
        /* Grupo de botones */
        .btn-group {
            display: flex; /* Para que los botones estén uno al lado del otro */
            gap: 15px; /* Espacio entre botones */
            justify-content: center; /* Centrar los botones */
            margin-top: 30px;
        }
        /* Estilo general para los botones */
        .btn {
            padding: 12px 25px;
            border-radius: 10px; /* Botones más redondeados */
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease; /* Transición suave en hover */
        }
        /* Estilo específico para el botón de éxito (verde) */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        /* Efecto hover para el botón de éxito */
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px); /* Efecto de elevación */
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3); /* Sombra al pasar el mouse */
        }
        /* Estilo específico para el botón de peligro (rojo) */
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        /* Efecto hover para el botón de peligro */
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px); /* Efecto de elevación */
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); /* Sombra al pasar el mouse */
        }
        /* Estilo para los mensajes de alerta */
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 1rem;
            text-align: center;
        }
        /* Media Queries para responsividad en pantallas pequeñas (max-width: 768px) */
        @media (max-width: 768px) {
            .card {
                padding: 20px;
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
                flex-direction: column; /* Botones apilados */
                gap: 10px;
            }
            .btn {
                width: 100%; /* Botones de ancho completo */
            }
        }
    </style>
</head>
<body>

<div class="container main-content mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2>Control de Válvula</h2>
            <div class="card">
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        {{-- Si hay un mensaje de error desde el controlador, mostrarlo --}}
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        {{-- Si hay un objeto $dispositivo válido, mostrar su información --}}
                        <h5 class="card-title" id="nombre-dispositivo">
                            <i class="fas fa-microchip me-2"></i>Dispositivo: <?= esc($dispositivo->nombre) ?>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted" id="ubicacion-dispositivo">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación: <?= esc($dispositivo->ubicacion) ?>
                        </h6>
                        <p class="card-text">
                            <i class="fas fa-lightbulb me-2"></i>Estado actual: 
                            <span id="estado-actual" class="<?= $dispositivo->estado_valvula ? 'abierta' : 'cerrada' ?>">
                                <?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?>
                            </span>
                        </p>
                        
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir">
                                <i class="fas fa-solid fa-valve me-2"></i>Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar">
                                <i class="fas fa-solid fa-valve me-2"></i>Cerrar Válvula
                            </button>
                        </div>
                    <?php else: ?>
                        {{-- Caso por defecto si no hay dispositivo y no se estableció un error_message específico --}}
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>No se pudo cargar la información del dispositivo. Por favor, asegúrese de que la URL sea correcta o seleccione un dispositivo de su lista.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir jQuery antes de tu script personalizado -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
// Solo ejecutar el script si el dispositivo está definido, de lo contrario, no hay MAC para usar
<?php if (isset($dispositivo) && $dispositivo !== null): ?>
$(document).ready(function() {
    // Escapar la MAC para seguridad en JS
    const mac = '<?= esc($dispositivo->MAC) ?>'; 
    const estadoActualSpan = $('#estado-actual');

    // Función para actualizar el estado mostrado en la UI
    function actualizarEstadoUI(estado) {
        estadoActualSpan.text(estado ? 'Abierta' : 'Cerrada');
        // Añadir/quitar clases para cambiar el color del texto
        if (estado) {
            estadoActualSpan.removeClass('cerrada').addClass('abierta');
        } else {
            estadoActualSpan.removeClass('abierta').addClass('cerrada');
        }
    }
    
    // Obtener estado inicial del dispositivo al cargar la página
    $.get('/servo/obtenerEstado/' + mac, function(response) {
        if (response.error) {
            console.error('Error al obtener estado inicial:', response.error);
        } else {
            actualizarEstadoUI(response.estado_valvula);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Error de red o servidor al obtener estado inicial:', textStatus, errorThrown);
    });
    
    // Event listener para el botón "Abrir Válvula"
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1); // 1 para abrir
    });
    
    // Event listener para el botón "Cerrar Válvula"
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0); // 0 para cerrar
    });
    
    // Función para enviar la petición al controlador para controlar el servo
    function controlarServo(mac, estado) {
        $.post('/servo/actualizarEstado', {
            mac: mac,
            estado: estado
            // Si usas CSRF en CodeIgniter, necesitarías enviar el token también
            // '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.error) {
                console.error('Error al controlar servo:', response.error);
            } else {
                actualizarEstadoUI(response.estado);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error de red o servidor al enviar comando:', textStatus, errorThrown);
        });
    }
    
    // Actualizar el estado del dispositivo periódicamente
    setInterval(function() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            if (!response.error) {
                actualizarEstadoUI(response.estado_valvula);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error en actualización periódica:', textStatus, errorThrown);
        });
    }, 5000); // Actualizar cada 5 segundos
});
<?php endif; ?>
</script>
</body>
</html>
