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

<div class="container my-5">
    <a href="<?= base_url('/perfil') ?>" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al Perfil
    </a>

    <h1 class="page-title"><i class="fas fa-microchip me-2"></i> Detalle del Dispositivo: <span class="text-primary"><?= esc($nombreDispositivo) ?></span></h1>
    <p class="device-details-line">
        MAC: <span id="macDisplay"><?= esc($mac) ?></span> | Ubicación: <?= esc($ubicacionDispositivo) ?>
    </p>

    <div class="current-gas-level-card-simple">
        <h3 class="card-title"><i class="fas fa-gas-pump me-2"></i>Nivel de Gas Actual</h3>
        <p class="current-gas-level-value" id="gasLevelDisplay">Cargando...</p> <!-- Inicializado con "Cargando..." -->
        <p class="text-sm mt-2 text-gray-600 dark:text-gray-300">
            Umbral para Alarma (ESP32): <span class="font-bold text-green-500" id="openThresholdDisplay"></span> PPM
        </p>
    </div>

    <div class="valve-buttons">
        <button type="button" class="btn btn-valve btn-valve-open" id="openValveBtn" onclick="sendValveCommand('<?= esc($mac) ?>', 'open')">
            <i class="fas fa-door-open me-2"></i> Abrir Válvula
        </button>
        <button type="button" class="btn btn-valve btn-valve-close" id="closeValveBtn" onclick="sendValveCommand('<?= esc($mac) ?>', 'close')">
            <i class="fas fa-door-closed me-2"></i> Cerrar Válvula
        </button>
    </div>

    <div class="mt-4 p-4 text-center font-semibold rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
        <p>Estado Actual de la Válvula (ESP32): <span id="valveStateDisplay" class="current-valve-state">Cargando...</span></p>
    </div>

    <div id="valveMessage" class="alert alert-info mt-4 text-center d-none" role="alert"></div>

</div>

<!-- Pasa la URL base y la MAC a JavaScript como variables globales para que el script externo las use -->
<script>
    const API_BASE_URL = '<?= base_url(); ?>';
    const MAC_ADDRESS = '<?= esc($mac); ?>';
    // Define el umbral de alarma para el frontend si la ESP32 lo usa localmente
    const GAS_ALARM_THRESHOLD_FRONTEND = 100; // Ajusta este valor si es diferente al de tu ESP32

    document.getElementById('openThresholdDisplay').textContent = GAS_ALARM_THRESHOLD_FRONTEND;

    /**
     * Envía un comando para abrir o cerrar la válvula al backend.
     * @param {string} macAddress La dirección MAC del dispositivo.
     * @param {string} command 'open' para abrir, 'close' para cerrar.
     */
    async function sendValveCommand(macAddress, command) {
        let state;
        if (command === 'open') {
            state = 1;
        } else if (command === 'close') {
            state = 0;
        } else {
            console.error("Comando de válvula inválido:", command);
            return;
        }

        const valveMessageDiv = document.getElementById('valveMessage');
        valveMessageDiv.classList.add('d-none'); // Ocultar mensaje anterior

        try {
            const response = await fetch(`${API_BASE_URL}web/controlServo`, { // <--- RUTA ACTUALIZADA
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `MAC=${macAddress}&state=${state}`
            });
            const data = await response.json();

            if (data.status === 'success') {
                valveMessageDiv.textContent = `Comando enviado: Válvula ${command === 'open' ? 'Abierta' : 'Cerrada'}.`;
                valveMessageDiv.classList.remove('d-none', 'alert-danger');
                valveMessageDiv.classList.add('alert-success');
                // Después de enviar el comando, actualiza el estado mostrado en la UI
                getServoStateFromWeb(macAddress);
            } else {
                valveMessageDiv.textContent = `Error al enviar comando: ${data.message || 'Error desconocido'}.`;
                valveMessageDiv.classList.remove('d-none', 'alert-success');
                valveMessageDiv.classList.add('alert-danger');
            }
        } catch (error) {
            console.error('Error al comunicarse con la API de control de servo:', error);
            valveMessageDiv.textContent = 'Error de conexión con el servidor. Intenta de nuevo.';
            valveMessageDiv.classList.remove('d-none', 'alert-success');
            valveMessageDiv.classList.add('alert-danger');
        }
    }

    /**
     * Consulta el estado actual del servo desde el backend y actualiza la UI.
     * @param {string} macAddress La dirección MAC del dispositivo.
     */
    async function getServoStateFromWeb(macAddress) {
        const valveStateDisplay = document.getElementById('valveStateDisplay');
        valveStateDisplay.textContent = 'Actualizando...';

        try {
            const response = await fetch(`${API_BASE_URL}web/getServoState/${macAddress}`); // <--- RUTA ACTUALIZADA
            const data = await response.json();

            if (data.status === 'success' && typeof data.estado_servo !== 'undefined') {
                const currentState = (data.estado_servo === 1) ? 'Abierta' : 'Cerrada';
                valveStateDisplay.textContent = currentState;
                valveStateDisplay.className = 'current-valve-state ' + (data.estado_servo === 1 ? 'text-success' : 'text-danger');
            } else {
                valveStateDisplay.textContent = 'Error al cargar estado';
                console.error("Error al obtener estado del servo desde la web:", data);
            }
        } catch (error) {
            valveStateDisplay.textContent = 'Error de red';
            console.error('Error al comunicarse con la API para obtener estado del servo:', error);
        }
    }

    // Llama a la función para obtener el estado del servo cuando la página carga.
    // También puedes configurar un intervalo para que se actualice periódicamente.
    window.addEventListener('load', () => {
        getServoStateFromWeb(MAC_ADDRESS);
        // Opcional: Actualizar el estado cada 5 segundos
        // setInterval(() => getServoStateFromWeb(MAC_ADDRESS), 5000);
    });

</script>

<!-- Enlace al archivo JavaScript externo -->
<script src="<?= base_url('js/detalle_dispositivo.js'); ?>"></script>

<!-- Registro del Service Worker -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                .then(registration => {
                    console.log('ServiceWorker registrado con éxito:', registration.scope);
                })
                .catch(error => {
                    console.log('Fallo el registro de ServiceWorker:', error);
                });
        });
    }
</script>

</body>
</html>
