<?php
// Estas variables se pasan desde el controlador
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Usar la MAC como nombre por defecto si no se pasa nombre
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$message = $message ?? null; // Mensaje general de la vista, no directamente usado por JS de API

// Estas variables se actualizarán dinámicamente con JavaScript.
// No necesitamos calcular el último índice de $lecturas aquí en PHP.
// El nivel de gas y el estado de la válvula serán llenados por JavaScript.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Dispositivo</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="<?= base_url('css/detalle_dispositivo.css'); ?>">

    <!-- Configuración PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>

<div class="container mt-4">
    <h2>Control de Válvula</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title" id="nombre-dispositivo">Dispositivo: <?= $dispositivo['nombre'] ?></h5>
            <h6 class="card-subtitle mb-2 text-muted" id="ubicacion-dispositivo">Ubicación: <?= $dispositivo['ubicacion'] ?></h6>
            <p class="card-text">Estado actual: <span id="estado-actual"><?= $dispositivo['estado_valvula'] ? 'Abierta' : 'Cerrada' ?></span></p>
            
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success" id="btn-abrir">Abrir Válvula</button>
                <button type="button" class="btn btn-danger" id="btn-cerrar">Cerrar Válvula</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const mac = '<?= $dispositivo["MAC"] ?>';
    
    // Función para actualizar el estado mostrado
    function actualizarEstado(estado) {
        $('#estado-actual').text(estado ? 'Abierta' : 'Cerrada');
    }
    
    // Obtener estado inicial
    $.get('/servo/obtenerEstado/' + mac, function(response) {
        if (response.error) {
            alert(response.error);
        } else {
            actualizarEstado(response.estado_valvula);
        }
    }).fail(function() {
        alert('Error al obtener el estado del dispositivo');
    });
    
    // Control de botones
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1);
    });
    
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0);
    });
    
    // Función para controlar el servo
    function controlarServo(mac, estado) {
        $.post('/servo/actualizarEstado', {
            mac: mac,
            estado: estado
        }, function(response) {
            if (response.error) {
                alert(response.error);
            } else {
                actualizarEstado(estado);
                // Aquí podrías agregar notificación de éxito
            }
        }).fail(function() {
            alert('Error al enviar el comando al dispositivo');
        });
    }
    
    // Opcional: Actualizar estado periódicamente
    setInterval(function() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            if (!response.error) {
                actualizarEstado(response.estado_valvula);
            }
        });
    }, 5000); // Actualizar cada 5 segundos
});
</script>