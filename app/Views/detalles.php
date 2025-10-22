<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        :root {
            --primary-color: #007bff; /* Azul vibrante */
            --success-color: #28a745; /* Verde para abierta */
            --danger-color: #dc3545; /* Rojo para cerrada */
            --warning-color: #ffc107;
            --info-color: #6c757d; /* Gris para info/neutro */
            --text-color: #333;
            --bg-color: #f0f2f5; /* Fondo más suave */
            --card-bg: #ffffff;
            --border-color: #e6e6e6;
        }
        body { 
            font-family: 'Roboto', 'Arial', sans-serif; 
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
            background-color: var(--card-bg);
            border: none; /* Eliminamos el borde simple */
            border-radius: 12px; /* Bordes más suaves */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); /* Sombra más pronunciada */
            padding: 40px;
            text-align: center;
            width: 100%;
            max-width: 450px; /* Panel un poco más ancho */
            transition: transform 0.3s ease-in-out;
        }
        h2 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8em;
            font-weight: 500;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f7f9fc;
            border-left: 5px solid var(--primary-color);
            border-radius: 4px;
            text-align: left;
            font-size: 0.95em;
        }
        .info p { margin: 5px 0; }

        /* **NUEVO**: Estilo para el estado de la válvula */
        .status-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            transition: all 0.5s ease;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .status-box p {
            margin: 5px 0;
            font-weight: 700;
            font-size: 1.4em;
            text-transform: uppercase;
        }

        /* Indicadores visuales */
        .status-display-text {
            display: inline-block;
            margin-left: 10px;
        }
        .status-icon {
            font-size: 1.2em;
            margin-right: 10px;
        }
        .status-loading {
            color: var(--info-color);
        }
        .status-open {
            background-color: #e6f6e9; /* Fondo verde claro */
            color: var(--success-color);
        }
        .status-closed {
            background-color: #fbe6e8; /* Fondo rojo claro */
            color: var(--danger-color);
        }
        .status-error {
             background-color: #fff0f0;
            color: red;
        }
        
        /* **NUEVO**: Grupo de botones más dinámico */
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .button-group button {
            padding: 12px 15px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s, box-shadow 0.3s;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1em;
        }
        .button-group button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .button-group button:active:not(:disabled) {
            transform: translateY(0);
        }
        .button-group button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Colores de botones */
        #btn-volver {
            background-color: var(--info-color);
            color: white;
        }
        #btn-abrir {
            background-color: var(--success-color);
            color: white;
        }
        #btn-cerrar {
            background-color: var(--danger-color);
            color: white;
        }

        .footer-text {
            margin-top: 25px;
            font-size: 0.85em;
            color: var(--info-color);
        }
        .alert-error {
            padding: 10px;
            background-color: #fbe6e8;
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: left;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="control-panel">
    <i class="fas fa-gas-pump" style="font-size: 2em; color: var(--primary-color); margin-bottom: 10px;"></i>
    <h2>Control de Válvula de Gas</h2>

    <?php if (isset($dispositivo) && $dispositivo): ?>
<div class="info">
    <p><i class="fas fa-microchip"></i> Dispositivo: <strong><?= esc($dispositivo->nombre) ?></strong></p>
    <p><i class="fas fa-network-wired"></i> MAC: <strong><?= esc($dispositivo->MAC) ?></strong></p>
</div>
<?php elseif (isset($error_message)): ?>
<div class="alert-error">
    <p><i class="fas fa-exclamation-triangle"></i> <?= esc($error_message) ?></p>
</div>
<?php else: ?>
<div class="alert-error">
    <p><i class="fas fa-exclamation-triangle"></i> Error: Datos del dispositivo no disponibles.</p>
</div>
<?php endif; ?>

    <div id="valve-status-box" class="status-box status-loading">
        <p>Estado Actual:</p>
        <p id="status-display">
            <i class="fas fa-spinner fa-spin status-icon"></i>
            <span class="status-display-text">Cargando...</span>
        </p>
    </div>

    <div class="button-group">
        <button id="btn-volver"><i class="fas fa-arrow-left"></i> Volver</button>
        <button id="btn-abrir"><i class="fas fa-door-open"></i> Abrir</button>
        <button id="btn-cerrar"><i class="fas fa-door-closed"></i> Cerrar</button>
    </div>
    
    <p class="footer-text">El estado se actualiza automáticamente cada 5 segundos.</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // **IMPORTANTE**: Asegúrate que esta MAC sea la que se pasa desde el controlador PHP
        const MAC_ADDRESS = '<?= esc($dispositivo->MAC ?? '') ?>'; 
        const API_KEY = "SUPER_SECRET_API_MLUS"; // Clave del ESP32/API
        
        const statusBox = document.getElementById('valve-status-box');
        const statusDisplay = document.getElementById('status-display');
        const btnAbrir = document.getElementById('btn-abrir');
        const btnCerrar = document.getElementById('btn-cerrar');
        const btnVolver = document.getElementById('btn-volver');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfName = document.querySelector('meta[name="csrf-name"]').content;
        let isControlInProcess = false; // Bandera para evitar clics múltiples

        // Función auxiliar para actualizar la UI (Mantiene la funcionalidad original)
        function actualizarEstadoUI(estado) {
            // Reinicia las clases de estado
            statusBox.classList.remove('status-open', 'status-closed', 'status-error', 'status-loading');
            statusDisplay.innerHTML = ''; // Limpia el contenido

            if (estado === 1) {
                statusDisplay.innerHTML = '<i class="fas fa-check-circle status-icon"></i><span class="status-display-text">Válvula ABIERTA</span>';
                statusBox.classList.add('status-open');
                btnAbrir.disabled = true;
                btnCerrar.disabled = false;
            } else if (estado === 0) {
                statusDisplay.innerHTML = '<i class="fas fa-times-circle status-icon"></i><span class="status-display-text">Válvula CERRADA</span>';
                statusBox.classList.add('status-closed');
                btnAbrir.disabled = false;
                btnCerrar.disabled = true;
            }
        }

        // Función para mostrar el estado de "Enviando Orden"
        function showSendingStatus() {
            statusBox.classList.remove('status-open', 'status-closed', 'status-error');
            statusBox.classList.add('status-loading');
            statusDisplay.innerHTML = '<i class="fas fa-sync-alt fa-spin status-icon"></i><span class="status-display-text">Enviando orden...</span>';
            btnAbrir.disabled = true;
            btnCerrar.disabled = true;
        }

        // 1. Envía la orden de abrir/cerrar. (Funcionalidad esencial mantenida)
        async function controlValve(estado) {
            if (isControlInProcess) return;
            isControlInProcess = true;

            showSendingStatus();

            const formData = new FormData();
            formData.append('mac', MAC_ADDRESS);
            // La DB tiene 1=Abierta, 0=Cerrada
            formData.append('estado', estado); 
            formData.append(csrfName, csrfToken);

            try {
                // RUTA CORREGIDA: Apunta al ValveController::actualizarEstado
                const response = await fetch('<?= base_url('valve/actualizarEstado') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    // Actualizamos inmediatamente con el estado de la DB (aunque el ESP32 no haya confirmado)
                    actualizarEstadoUI(data.nuevo_estado);
                } else {
                    throw new Error(data.message || 'Error desconocido al cambiar estado');
                }
            } catch (error) {
                console.error('Error al controlar la válvula:', error);
                statusBox.classList.remove('status-loading');
                statusBox.classList.add('status-error');
                statusDisplay.innerHTML = '<i class="fas fa-exclamation-circle status-icon"></i><span class="status-display-text">Fallo: ' + (error.message || 'Error de red.') + '</span>';
                
                // En caso de fallo, re-consulta el estado real (esencial)
                fetchDeviceState(); 
            } finally {
                isControlInProcess = false;
            }
        }

        // 2. Obtiene el estado actual de la válvula (Funcionalidad esencial mantenida)
        async function fetchDeviceState() {
            if (isControlInProcess) return; // No consultar si se está enviando una orden

            if (!statusBox.classList.contains('status-error')) {
                // Muestra un estado de "Consultando" si no hay error previo
                statusBox.classList.remove('status-open', 'status-closed');
                statusBox.classList.add('status-loading');
                statusDisplay.innerHTML = '<i class="fas fa-sync-alt fa-spin status-icon"></i><span class="status-display-text">Consultando estado...</span>';
            }
            

            // URL CRÍTICA: Ahora usa la ruta /api/valve_status, que apunta a ValveController::obtenerEstadoSimple()
            const url = '<?= base_url('api/valve_status?mac=') ?>' + MAC_ADDRESS + '&api_key=' + API_KEY; 

            try {
                const response = await fetch(url);
                
                // response.text() es CLAVE: Espera un string simple ("1", "0", "-1", etc.)
                const estadoTexto = await response.text(); 
                
                if (response.ok) {
                    const estado = parseInt(estadoTexto.trim());
                    
                    if (estado === 0 || estado === 1) {
                        actualizarEstadoUI(estado);
                    } else if (estado < 0) {
                        throw new Error('Error de API. Código: ' + estado);
                    } else {
                        throw new Error('Respuesta inválida.');
                    }
                } else {
                    // Error HTTP (404, 500)
                    throw new Error('Error de conexión HTTP: ' + response.status);
                }

            } catch (error) {
                console.error('Error en fetchDeviceState:', error);
                statusBox.classList.remove('status-loading', 'status-open', 'status-closed');
                statusBox.classList.add('status-error');
                statusDisplay.innerHTML = '<i class="fas fa-exclamation-circle status-icon"></i><span class="status-display-text">Error: No se pudo obtener el estado.</span>';
                btnAbrir.disabled = true;
                btnCerrar.disabled = true;
            }
        }

        // Asignación de eventos: Mantenida
        btnAbrir.addEventListener('click', () => controlValve(1)); 
        btnCerrar.addEventListener('click', () => controlValve(0)); 
        btnVolver.addEventListener('click', () => window.history.back());

        // Carga inicial y actualización periódica (Mantenida)
        fetchDeviceState();
        setInterval(fetchDeviceState, 5000);
    });
</script>

</body>
</html>