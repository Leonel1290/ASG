<?php
$mac = $mac ?? 'Desconocida';
$nombreDispositivo = $nombreDispositivo ?? $mac; // Usar la MAC como nombre por defecto si no se pasa nombre
$ubicacionDispositivo = $ubicacionDispositivo ?? 'Desconocida';
$message = $message ?? null;

// Estas variables se actualizarán dinámicamente con JavaScript
$nivelGasActualDisplay = !empty($lecturas) && isset($lecturas[$ultimoIndice]['nivel_gas']) ? esc($lecturas[$ultimoIndice]['nivel_gas']) . ' PPM' : 'Sin datos';
$estadoValvulaDisplay = 'Cargando...'; // Estado inicial de la válvula
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
    <style>
        /* Añadimos un reset general para asegurar que no haya márgenes/paddings por defecto */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c;
            color: #cbd5e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; /* Aseguramos que el margen sea 0 */
            padding: 0; /* Aseguramos que el padding sea 0 */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
            padding: 2rem;
        }

        /* Estilo para el botón Volver */
        .btn-outline-secondary {
            color: #cbd5e0;
            border-color: #cbd5e0;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-outline-secondary:hover {
            color: #1a202c;
            background-color: #cbd5e0;
            border-color: #cbd5e0;
        }

        .page-title {
            color: #f7fafc;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        /* Estilo para la línea de detalles (MAC y Ubicación) */
        .device-details-line {
            text-align: center;
            color: #a0aec0;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.6), 0 0 40px rgba(102, 126, 234, 0.4), 0 0 60px rgba(102, 126, 234, 0.2);
        }

        .card-body {
            padding: 1.5rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }

        /* Estilos específicos para la tarjeta de Nivel de Gas Actual */
        .current-gas-level-card-simple {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 2rem;
            transition: box-shadow 0.3s ease;
            padding: 1.5rem;
            text-align: center;
        }

        .current-gas-level-card-simple:hover {
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.6), 0 0 40px rgba(102, 126, 234, 0.4), 0 0 60px rgba(102, 126, 234, 0.2);
        }

        .current-gas-level-card-simple .card-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .current-gas-level-card-simple .current-gas-level-value {
            font-size: 2rem;
            font-weight: bold;
            color: #f6e05e;
        }
        
        .current-valve-state {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 1rem;
        }
        .valve-state-open { color: #48bb78; } /* Verde para abierta */
        .valve-state-closed { color: #e53e3e; } /* Rojo para cerrada */


        /* Estilos para los nuevos botones */
        .valve-buttons {
            display: flex;
            justify-content: center;
            gap: 20px; /* Espacio entre los botones */
            margin-top: 2rem;
            flex-wrap: wrap; /* Permite que los botones se envuelvan en pantallas pequeñas */
        }

        .btn-valve {
            flex: 1; /* Permite que los botones ocupen el espacio disponible */
            max-width: 200px; /* Ancho máximo para los botones */
            padding: 15px 30px;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
            color: #fff; /* Color de texto blanco para todos los botones */
        }

        .btn-valve-open {
            background-color: #48bb78; /* Verde para abrir */
            border-color: #48bb78;
        }

        .btn-valve-close {
            background-color: #e53e3e; /* Rojo para cerrar */
            border-color: #e53e3e;
        }

        .btn-valve:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-valve:disabled {
            background-color: #6c757d; /* Gris para deshabilitado */
            border-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.75rem;
            }

            .card-title {
                font-size: 1.5rem;
            }
            .current-gas-level-card-simple .card-title {
                font-size: 1.5rem;
            }
            .current-gas-level-card-simple .current-gas-level-value {
                font-size: 1.75rem;
            }
            .valve-buttons {
                flex-direction: column; /* Apila los botones en pantallas pequeñas */
                align-items: center;
            }
            .btn-valve {
                width: 80%; /* Ocupa más ancho en móvil */
                max-width: 300px; /* Asegura que no sea demasiado ancho */
            }
        }
    </style>

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
        <p class="current-gas-level-value" id="gasLevelDisplay"><?= $nivelGasActualDisplay ?></p>
        <p class="text-sm mt-2 text-gray-600 dark:text-gray-300">
            Umbral para Abrir: <span class="font-bold text-green-500" id="openThresholdDisplay"></span> PPM |
            Umbral para Cerrar: <span class="font-bold text-red-500" id="closeThresholdDisplay"></span> PPM
    </div>

    <div class="valve-buttons">
        <button type="button" class="btn btn-valve btn-valve-open" id="openValveBtn" onclick="sendValveCommand('<?= esc($mac) ?>', 'open')" disabled>
            <i class="fas fa-door-open me-2"></i> Abrir Válvula
        </button>
        <button type="button" class="btn btn-valve btn-valve-close" id="closeValveBtn" onclick="sendValveCommand('<?= esc($mac) ?>', 'close')" disabled>
            <i class="fas fa-door-closed me-2"></i> Cerrar Válvula
        </button>
    </div>

    <div class="mt-4 p-4 text-center font-semibold rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
        <p>Estado Actual de la Válvula (ESP32): <span id="valveStateDisplay" class="current-valve-state">Cargando...</span></p>
    </div>

    <div id="valveMessage" class="alert alert-info mt-4 text-center d-none" role="alert"></div>

</div>

<script>
    // Define la URL base de tu aplicación de CodeIgniter en Render
    const API_BASE_URL = 'https://pwa-1s1m.onrender.com';
    const MAC_ADDRESS = document.getElementById('macDisplay').textContent;

    // --- Umbrales de Gas (Deben coincidir con los del controlador PHP) ---
    const OPEN_VALVE_THRESHOLD = 100; // Si el nivel de gas es MENOR a este, se permite abrir la válvula.
    const CLOSE_VALVE_THRESHOLD = 200; // Si el nivel de gas es MAYOR a este, se permite cerrar la válvula.

    // Elementos del DOM
    const gasLevelDisplay = document.getElementById('gasLevelDisplay');
    const valveStateDisplay = document.getElementById('valveStateDisplay');
    const openValveBtn = document.getElementById('openValveBtn');
    const closeValveBtn = document.getElementById('closeValveBtn');
    const valveMessageDiv = document.getElementById('valveMessage');
    const openThresholdDisplay = document.getElementById('openThresholdDisplay');
    const closeThresholdDisplay = document.getElementById('closeThresholdDisplay');

    // Mostrar umbrales en la interfaz
    openThresholdDisplay.textContent = OPEN_VALVE_THRESHOLD;
    closeThresholdDisplay.textContent = CLOSE_VALVE_THRESHOLD;

    // Función para enviar comandos a la válvula
    function sendValveCommand(mac, command) {
        valveMessageDiv.classList.add('d-none'); // Ocultar mensaje anterior
        valveMessageDiv.classList.remove('alert-success', 'alert-danger'); // Limpiar clases de estilo

        const apiUrl = `${API_BASE_URL}/api/valve_control`;

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ mac: mac, command: command })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                valveMessageDiv.textContent = data.message;
                valveMessageDiv.classList.remove('d-none');
                valveMessageDiv.classList.add('alert-success');
            } else {
                valveMessageDiv.textContent = data.message || 'Error al enviar el comando.';
                valveMessageDiv.classList.remove('d-none');
                valveMessageDiv.classList.add('alert-danger');
            }
            // Después de enviar un comando, actualizamos el estado para reflejar los cambios
            updateDeviceStatus(); 
        })
        .catch(error => {
            console.error('Error:', error);
            valveMessageDiv.textContent = 'Ocurrió un error de conexión o en el servidor.';
            valveMessageDiv.classList.remove('d-none');
            valveMessageDiv.classList.add('alert-danger');
            if (error.message) {
                valveMessageDiv.textContent = error.message;
            } else if (error.error) {
                valveMessageDiv.textContent = error.error;
            }
        });
    }

    // Función para actualizar el estado del dispositivo (nivel de gas y estado de válvula)
    async function updateDeviceStatus() {
        const apiUrl = `${API_BASE_URL}/api/get_valve_state/${MAC_ADDRESS}`;
        
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            const data = await response.json();

            if (data.status === 'success') {
                const nivelGas = data.ultimo_nivel_gas;
                const estadoValvula = data.estado_valvula;

                gasLevelDisplay.textContent = `${nivelGas} PPM`;

                // Actualizar estado visual de la válvula
                valveStateDisplay.textContent = estadoValvula === 1 ? 'ABIERTA' : 'CERRADA';
                valveStateDisplay.classList.toggle('valve-state-open', estadoValvula === 1);
                valveStateDisplay.classList.toggle('valve-state-closed', estadoValvula === 0);

                // Lógica para habilitar/deshabilitar botones
                if (nivelGas !== null) {
                    // Botón Abrir: habilitado si el gas es MENOR al umbral de apertura
                    openValveBtn.disabled = !(nivelGas < OPEN_VALVE_THRESHOLD);

                    // Botón Cerrar: habilitado si el gas es MAYOR al umbral de cierre
                    closeValveBtn.disabled = !(nivelGas > CLOSE_VALVE_THRESHOLD);
                } else {
                    // Si no hay datos de gas, deshabilitar ambos botones por seguridad
                    openValveBtn.disabled = true;
                    closeValveBtn.disabled = true;
                }

            } else {
                console.error('Error al obtener estado del dispositivo:', data.message);
                gasLevelDisplay.textContent = 'Error';
                valveStateDisplay.textContent = 'Error';
                openValveBtn.disabled = true;
                closeValveBtn.disabled = true;
            }
        } catch (error) {
            console.error('Error al obtener estado del dispositivo:', error);
            gasLevelDisplay.textContent = 'Sin conexión';
            valveStateDisplay.textContent = 'Sin conexión';
            openValveBtn.disabled = true;
            closeValveBtn.disabled = true;
        }
    }

    // Actualizar estado inicial y luego periódicamente
    updateDeviceStatus(); // Llamada inicial al cargar la página
    setInterval(updateDeviceStatus, 5000); // Actualizar cada 5 segundos

</script>

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
