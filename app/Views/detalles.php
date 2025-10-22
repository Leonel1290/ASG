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
        /* Variables de color adaptadas al Dark Mode */
        :root {
            --primary-color: #4CAF50; /* Verde brillante para 'Abierta' */
            --danger-color: #F44336; /* Rojo brillante para 'Cerrada' */
            --info-color: #616161; /* Gris oscuro para volver */
            --text-color-light: #f4f7f6; /* Texto claro */
            --text-color-dark: #b0b0b0; /* Texto secundario gris */
            --bg-color: #121212; /* Fondo muy oscuro */
            --card-bg: #1e1e1e; /* Fondo de panel ligeramente más claro */
        }
        body { 
            font-family: 'Roboto', sans-serif; 
            background-color: var(--bg-color);
            color: var(--text-color-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .control-panel { 
            background-color: var(--card-bg);
            border-radius: 20px; /* Bordes más pronunciados */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5); /* Sombra intensa */
            padding: 30px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: var(--text-color-light);
            font-size: 1.8em;
            margin-bottom: 30px;
            text-transform: uppercase;
            font-weight: 700;
        }
        .info {
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.1em;
            color: var(--text-color-light);
        }
        .info p { margin: 5px 0; }
        .info .nombre { font-size: 1.2em; font-weight: 500; }
        .info .mac { font-size: 0.85em; color: var(--text-color-dark); }
        .alert-error {
            padding: 15px;
            background-color: #3f191a; /* Fondo oscuro de alerta */
            border-left: 5px solid var(--danger-color);
            color: var(--danger-color);
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: left;
            font-weight: bold;
        }

        /* Indicador de Porcentaje/Estado */
        .status-percentage-container {
            margin: 40px 0;
        }
        .percentage {
            font-size: 4em; /* Grande como en la imagen */
            font-weight: 700;
            color: var(--text-color-light);
            transition: color 0.5s ease;
        }
        .status-label {
            margin-top: 10px;
            font-size: 0.9em;
            color: var(--text-color-dark);
        }
        #status-indicator-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 8px;
            transition: background-color 0.5s ease;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
        }

        /* Botones */
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        .button-group button {
            padding: 15px 20px;
            border: none;
            border-radius: 10px; /* Bordes redondeados */
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            width: 100%;
            font-size: 1.1em;
        }
        .button-group button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .button-group button:active:not(:disabled) {
            transform: translateY(0);
        }
        .button-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        #btn-abrir {
            background-color: #00A36A; /* Verde para Abrir */
            color: white;
        }
        #btn-cerrar {
            background-color: #CC0000; /* Rojo para Cerrar */
            color: white;
        }
        #btn-volver {
            background-color: var(--info-color);
            color: var(--text-color-light);
            font-size: 1em;
        }

        .icon {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="control-panel">
    <h2>Control de Válvula</h2>

    <?php if (isset($dispositivo) && $dispositivo): ?>
<div class="info">
    <p class="nombre"><?= esc($dispositivo->nombre) ?></p>
    <p class="mac">MAC: <?= esc($dispositivo->MAC) ?></p>
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

    <div class="status-percentage-container">
        <p id="percentage-display" class="percentage">Cargando...</p>
        <p class="status-label">Estado actual: <span id="status-indicator-dot"></span></p>
    </div>

    <div class="button-group">
        <button id="btn-abrir"><i class="fas fa-play icon"></i> Abrir Válvula</button>
        <button id="btn-cerrar"><i class="fas fa-stop icon"></i> Cerrar Válvula</button>
        <button id="btn-volver"><i class="fas fa-arrow-left icon"></i> Volver al Perfil</button>
    </div>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // **PARÁMETROS ESENCIALES MANTENIDOS**
        const MAC_ADDRESS = '<?= esc($dispositivo->MAC ?? '') ?>'; 
        const API_KEY = "SUPER_SECRET_API_MLUS"; // Clave del ESP32/API
        
        const percentageDisplay = document.getElementById('percentage-display');
        const statusIndicatorDot = document.getElementById('status-indicator-dot');
        const btnAbrir = document.getElementById('btn-abrir');
        const btnCerrar = document.getElementById('btn-cerrar');
        const btnVolver = document.getElementById('btn-volver');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfName = document.querySelector('meta[name="csrf-name"]').content;
        let isControlInProcess = false;

        // **FUNCIÓN ESENCIAL MANTENIDA con adaptación visual**
        function actualizarEstadoUI(estado) {
            // Limpiar clases de color
            percentageDisplay.style.color = 'var(--text-color-light)';

            if (estado === 1) {
                // Válvula ABIERTA (100% de flujo)
                percentageDisplay.textContent = '100%';
                percentageDisplay.style.color = 'var(--primary-color)';
                statusIndicatorDot.style.backgroundColor = 'var(--primary-color)';
                btnAbrir.disabled = true;
                btnCerrar.disabled = false;
            } else if (estado === 0) {
                // Válvula CERRADA (0% de flujo)
                percentageDisplay.textContent = '0%';
                percentageDisplay.style.color = 'var(--danger-color)';
                statusIndicatorDot.style.backgroundColor = 'var(--danger-color)';
                btnAbrir.disabled = false;
                btnCerrar.disabled = true;
            } else {
                 // Estado de Error/Cargando (neutro)
                percentageDisplay.textContent = '...';
                percentageDisplay.style.color = 'var(--text-color-dark)';
                statusIndicatorDot.style.backgroundColor = 'var(--info-color)';
                btnAbrir.disabled = true;
                btnCerrar.disabled = true;
            }
        }

        // Función para mostrar el estado de "Enviando Orden"
        function showSendingStatus() {
            percentageDisplay.textContent = 'Enviando...';
            percentageDisplay.style.color = 'var(--text-color-dark)';
            statusIndicatorDot.style.backgroundColor = 'var(--info-color)';
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
            formData.append('estado', estado); 
            formData.append(csrfName, csrfToken);

            try {
                // RUTA ESENCIAL MANTENIDA
                const response = await fetch('<?= base_url('valve/actualizarEstado') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    actualizarEstadoUI(data.nuevo_estado);
                } else {
                    throw new Error(data.message || 'Error desconocido al cambiar estado');
                }
            } catch (error) {
                console.error('Error al controlar la válvula:', error);
                percentageDisplay.textContent = 'FALLO';
                percentageDisplay.style.color = 'red';
                statusIndicatorDot.style.backgroundColor = 'red';
                fetchDeviceState(); // Re-consulta el estado real (esencial)
            } finally {
                isControlInProcess = false;
            }
        }

        // 2. Obtiene el estado actual de la válvula (Funcionalidad esencial mantenida)
        async function fetchDeviceState() {
            if (isControlInProcess) return;

            // URL ESENCIAL MANTENIDA
            const url = '<?= base_url('api/valve_status?mac=') ?>' + MAC_ADDRESS + '&api_key=' + API_KEY; 

            try {
                const response = await fetch(url);
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
                    throw new Error('Error de conexión HTTP: ' + response.status);
                }

            } catch (error) {
                console.error('Error en fetchDeviceState:', error);
                percentageDisplay.textContent = 'ERROR';
                percentageDisplay.style.color = 'red';
                statusIndicatorDot.style.backgroundColor = 'red';
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