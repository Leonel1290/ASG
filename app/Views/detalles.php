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
        /* ------------------------------------------------------------------ */
        /* ESTILOS MEJORADOS                         */
        /* ------------------------------------------------------------------ */
        :root {
            --primary-color: #007bff; /* Azul primario */
            --success-color: #28a745; /* Verde para abierto */
            --danger-color: #dc3545; /* Rojo para cerrado */
            --warning-color: #ffc107; /* Amarillo para cargando/procesando */
            --text-color: #333;
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --border-color: #e0e0e0;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .control-panel { 
            max-width: 420px; 
            width: 100%;
            padding: 30px; 
            border: 1px solid var(--border-color); 
            border-radius: 12px; 
            background-color: var(--card-bg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); /* Sombra más suave */
            transition: transform 0.3s ease-in-out;
        }
        
        .control-panel:hover {
            transform: translateY(-5px);
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-top: 0;
            font-size: 1.8em;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        p {
            margin: 8px 0;
            font-size: 0.95em;
        }

        .status { 
            font-weight: 600; 
            margin: 20px 0; 
            font-size: 1.5em; /* Tamaño más grande */
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            background-color: #f8f9fa;
            border: 1px dashed var(--border-color);
        }

        .status strong {
            display: block;
            font-size: 0.7em; /* "Estado Actual" más pequeño */
            font-weight: normal;
            color: #6c757d;
            margin-bottom: 5px;
        }

        /* Contenedor de botones para centrar y espaciar */
        .button-group {
            display: flex;
            justify-content: space-around;
            gap: 10px;
            margin-top: 25px;
        }

        button { 
            flex-grow: 1; /* Para que ocupen el espacio disponible */
            padding: 12px 20px; 
            margin: 0; 
            cursor: pointer; 
            border: none; 
            border-radius: 8px; 
            font-size: 1em;
            font-weight: bold;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .open { 
            background-color: var(--success-color); 
            color: white; 
        } 
        .open:hover:not(:disabled) { 
            background-color: #218838; /* Oscurecer en hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(40, 167, 69, 0.3);
        }

        .closed { 
            background-color: var(--danger-color); 
            color: white; 
        } 
        .closed:hover:not(:disabled) { 
            background-color: #c82333; /* Oscurecer en hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(220, 53, 69, 0.3);
        }
        
        button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            box-shadow: none;
            transform: none;
        }

        .note {
            margin-top: 20px;
            font-size: 0.8em;
            color: #6c757d;
            text-align: center;
        }

        /* ------------------------------------------------------------------ */
        /* FIN ESTILOS MEJORADOS                        */
        /* ------------------------------------------------------------------ */
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
        <strong>Estado Actual:</strong>
        <span id="valve-status-display">Cargando...</span>
    </div>

    <div class="button-group">
        <button id="btn-abrir" class="open" data-estado="1" disabled>Abrir Válvula</button>
        <button id="btn-cerrar" class="closed" data-estado="0" disabled>Cerrar Válvula</button>
    </div>
    
    <p class="note">Nota: Los mensajes de error/éxito aparecerán como alertas básicas del navegador (alert).</p>
</div>

<script>
    // Las variables y la funcionalidad JavaScript se mantienen intactas.

    const MAC_ADDRESS = '<?= esc($mac) ?>';
    
    // Obtener datos de CSRF del meta tag
    const CSRF_NAME = $('meta[name="csrf-name"]').attr('content');
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    const $btnAbrir = $('#btn-abrir');
    const $btnCerrar = $('#btn-cerrar');
    const $statusDisplay = $('#valve-status-display');

    // 1. Función para actualizar la interfaz de usuario (UI) según el estado (0 o 1)
    function actualizarEstadoUI(estado) {
        // Remover clases de color anteriores para asegurar una única clase
        $statusDisplay.removeClass('text-open text-closed text-unknown text-processing');
        
        if (estado === 1) {
            $statusDisplay.text('ABIERTA').css('color', 'var(--success-color)');
            $btnAbrir.prop('disabled', true);
            $btnCerrar.prop('disabled', false);
        } else if (estado === 0) {
            $statusDisplay.text('CERRADA').css('color', 'var(--danger-color)');
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
        $statusDisplay.text(processingMessage).css('color', 'var(--warning-color)');

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