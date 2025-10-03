<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas Básico</title>

    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Estilos CSS mínimos */
        body { font-family: Arial, sans-serif; padding: 20px; }
        .control-panel { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 3px; }
        .open { background-color: #28a745; color: white; } /* Verde */
        .closed { background-color: #dc3545; color: white; } /* Rojo */
        .status { font-weight: bold; margin-bottom: 15px; font-size: 1.2em; }
    </style>
</head>
<body>

<?php 
    // Aseguramos que las variables existan para evitar errores de PHP
    $mac = $dispositivo->MAC ?? 'MAC_NO_DISPONIBLE'; 
    $nombre = $dispositivo->nombre ?? 'Dispositivo Desconocido'; 
?>

<div class="control-panel">
    <h1>Control Básico de Válvula</h1>
    <p>Dispositivo: <strong><?= esc($nombre) ?></strong></p>
    <p>MAC: <strong><?= esc($mac) ?></strong></p>
    
    <div class="status">
        Estado Actual: <span id="valve-status-display">Cargando...</span>
    </div>

    <div>
        <button id="btn-abrir" class="open" data-estado="1" disabled>Abrir Válvula</button>
        <button id="btn-cerrar" class="closed" data-estado="0" disabled>Cerrar Válvula</button>
    </div>
    
    <p>Nota: Los mensajes de error/éxito aparecerán como alertas básicas del navegador (alert).</p>
</div>

<script>
    const MAC_ADDRESS = '<?= esc($mac) ?>';
    
    // Obtener datos de CSRF del meta tag
    const CSRF_NAME = $('meta[name="csrf-name"]').attr('content');
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    const $btnAbrir = $('#btn-abrir');
    const $btnCerrar = $('#btn-cerrar');
    const $statusDisplay = $('#valve-status-display');

    // 1. Función para actualizar la interfaz de usuario (UI) según el estado (0 o 1)
    function actualizarEstadoUI(estado) {
        if (estado === 1) {
            $statusDisplay.text('ABIERTA').css('color', 'green');
            $btnAbrir.prop('disabled', true);
            $btnCerrar.prop('disabled', false);
        } else if (estado === 0) {
            $statusDisplay.text('CERRADA').css('color', 'red');
            $btnAbrir.prop('disabled', false);
            $btnCerrar.prop('disabled', true);
        } else {
             $statusDisplay.text('ESTADO DESCONOCIDO').css('color', 'gray');
             $btnAbrir.prop('disabled', true);
             $btnCerrar.prop('disabled', true);
        }
    }

    // 2. Función principal para enviar el comando de control al servidor
    function controlValve(estado) {
        const estadoInt = parseInt(estado, 10);
        const processingMessage = (estadoInt === 1) ? 'Abriendo...' : 'Cerrando...';
        const successMessage = (estadoInt === 1) ? '¡Válvula Abierta!' : '¡Válvula Cerrada!';
        
        // Deshabilitar botones y mostrar estado de procesamiento
        $btnAbrir.prop('disabled', true);
        $btnCerrar.prop('disabled', true);
        $statusDisplay.text(processingMessage).css('color', 'orange');

        const postData = {
            [CSRF_NAME]: CSRF_TOKEN,
            mac: MAC_ADDRESS,
            estado: estadoInt
        };

        // Petición POST al endpoint del controlador: /servo/actualizarEstado
        $.post('/servo/actualizarEstado', postData)
            .done(function(response) {
                console.log('Respuesta del servidor al control:', response);
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                    alert(successMessage); // Reemplazo de showSuccessToast
                } else {
                    alert('Error: ' + (response.message || 'Error al actualizar el estado')); // Reemplazo de showErrorToast
                }
            })
            .fail(function(jqXHR) {
                console.error('Error en la petición POST:', jqXHR.responseText);
                alert('Error de conexión con el servidor (código ' + jqXHR.status + ')'); // Reemplazo de showErrorToast
            })
            .always(function() {
                // Forzar una consulta de estado después de la acción
                fetchDeviceState();
            });
    }

    // 3. Función para obtener el estado actual de la válvula desde el servidor
    function fetchDeviceState() {
        // Petición GET al endpoint del controlador: /servo/obtenerEstado/(MAC)
        $.get(`/servo/obtenerEstado/${MAC_ADDRESS}`)
            .done(function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                } else {
                    $statusDisplay.text(response.message || 'Error al obtener estado').css('color', 'gray');
                    $btnAbrir.prop('disabled', true);
                    $btnCerrar.prop('disabled', true);
                }
            })
            .fail(function(jqXHR) {
                console.error('Error al obtener estado:', jqXHR.responseText);
                $statusDisplay.text('Error de conexión o 500').css('color', 'red');
                $btnAbrir.prop('disabled', true);
                $btnCerrar.prop('disabled', true);
            });
    }

    // 4. Inicialización y Event Listeners
    $(document).ready(function() {
        // Asignar el controlador a los botones
        $btnAbrir.on('click', function() { controlValve(1); });
        $btnCerrar.on('click', function() { controlValve(0); });

        // Iniciar la consulta del estado
        fetchDeviceState();

        // Actualizar el estado cada 5 segundos
        setInterval(fetchDeviceState, 5000);
    });
</script>

</body>
</html>