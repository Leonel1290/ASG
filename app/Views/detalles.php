<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas</title>

    <!-- CSRF Meta Tags -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        :root {
            --primary-color: #007bff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #6c757d;
            --text-color: #333;
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --border-color: #e0e0e0;
        }
        body { 
            font-family: 'Segoe UI', sans-serif; 
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); 
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
        p { margin: 8px 0; font-size: 0.95em; }
        .status { 
            font-weight: 600; 
            margin: 20px 0; 
            font-size: 1.5em; 
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            background-color: #f8f9fa;
            border: 1px dashed var(--border-color);
        }
        .status strong {
            display: block;
            font-size: 0.7em; 
            font-weight: normal;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .button-group { display: flex; justify-content: space-between; gap: 10px; margin-top: 25px; }
        button { padding: 12px 20px; cursor: pointer; border: none; border-radius: 8px; font-size: 1em; font-weight: bold; transition: all 0.2s ease; }
        #btn-volver { flex-grow: 0; background-color: var(--info-color); color: white; }
        #btn-abrir, #btn-cerrar { flex-grow: 1; }
        .open { background-color: var(--success-color); color: white; } 
        .closed { background-color: var(--danger-color); color: white; } 
        button:disabled { cursor: not-allowed; opacity: 0.6; }
        .note { margin-top: 20px; font-size: 0.8em; color: #6c757d; text-align: center; }
    </style>
</head>
<body>

<?php 
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
        <button id="btn-volver">Volver</button> 
        <button id="btn-abrir" class="open" data-estado="0" disabled>Abrir Válvula</button>
        <button id="btn-cerrar" class="closed" data-estado="1" disabled>Cerrar Válvula</button>
    </div>
    
    <p class="note">El estado se actualiza automáticamente.</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const MAC_ADDRESS = '<?= esc($mac) ?>';
        const CSRF_NAME = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const btnAbrir = document.getElementById('btn-abrir');
        const btnCerrar = document.getElementById('btn-cerrar');
        const btnVolver = document.getElementById('btn-volver');
        const statusDisplay = document.getElementById('valve-status-display');

        // 1. Actualiza la UI según el estado (0 para ABIERTA, 1 para CERRADA)
        function actualizarEstadoUI(estado) {
            statusDisplay.style.color = 'inherit'; // Reset color
            if (estado === 0) {
                statusDisplay.textContent = 'ABIERTA';
                statusDisplay.style.color = 'var(--success-color)';
                btnAbrir.disabled = true;
                btnCerrar.disabled = false;
            } else if (estado === 1) {
                statusDisplay.textContent = 'CERRADA';
                statusDisplay.style.color = 'var(--danger-color)';
                btnAbrir.disabled = false;
                btnCerrar.disabled = true;
            } else {
                statusDisplay.textContent = 'ESTADO DESCONOCIDO';
                btnAbrir.disabled = true;
                btnCerrar.disabled = true;
            }
        }

        // 2. Envía el comando para cambiar el estado de la válvula
        async function controlValve(nuevoEstado) {
            const estadoInt = parseInt(nuevoEstado, 10);
            statusDisplay.textContent = (estadoInt === 0) ? 'Abriendo...' : 'Cerrando...';
            statusDisplay.style.color = 'var(--warning-color)';
            btnAbrir.disabled = true;
            btnCerrar.disabled = true;

            const formData = new FormData();
            formData.append(CSRF_NAME, CSRF_TOKEN);
            formData.append('mac', MAC_ADDRESS);
            formData.append('estado', estadoInt);

            try {
                const response = await fetch('<?= base_url('servo/actualizarEstado') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    actualizarEstadoUI(data.nuevo_estado);
                } else {
                    throw new Error(data.message || 'Error en la respuesta del servidor');
                }
            } catch (error) {
                console.error('Error en controlValve:', error);
                statusDisplay.textContent = 'Error al cambiar estado';
                statusDisplay.style.color = 'red';
                // Re-habilita los botones para reintentar
                fetchDeviceState(); 
            }
        }

        // 3. Obtiene el estado actual de la válvula desde el servidor
        async function fetchDeviceState() {
            try {
                const response = await fetch('<?= base_url('servo/obtenerEstado/') ?>' + MAC_ADDRESS);
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    actualizarEstadoUI(data.estado);
                } else {
                    throw new Error(data.message || 'Respuesta no válida del servidor');
                }
            } catch (error) {
                console.error('Error en fetchDeviceState:', error);
                statusDisplay.textContent = 'Error de conexión o 500';
                statusDisplay.style.color = 'red';
                btnAbrir.disabled = true;
                btnCerrar.disabled = true;
            }
        }

        // Asignación de eventos
        btnAbrir.addEventListener('click', () => controlValve(0));
        btnCerrar.addEventListener('click', () => controlValve(1));
        btnVolver.addEventListener('click', () => window.history.back());

        // Carga inicial y actualización periódica
        fetchDeviceState();
        setInterval(fetchDeviceState, 5000);
    });
</script>

</body>
</html>
