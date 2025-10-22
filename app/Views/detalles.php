<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas</title>

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
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }
        .info {
            margin-bottom: 15px;
            text-align: left;
            font-size: 0.9em;
        }
        .status-box {
            background-color: var(--bg-color);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }
        .status-box p {
            margin: 0;
            font-weight: bold;
            font-size: 1.1em;
        }
        .button-group button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            width: calc(33% - 10px);
            min-width: 100px;
        }
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
            margin-top: 20px;
            font-size: 0.8em;
            color: var(--info-color);
        }
    </style>
</head>
<body>

<div class="control-panel">
    <h2>Control Básico de Válvula</h2>

    <?php if (isset($dispositivo) && $dispositivo): ?>
<div class="info">
    <p>Dispositivo: <strong><?= esc($dispositivo->nombre) ?></strong></p>
    <p>MAC: <strong><?= esc($dispositivo->MAC) ?></strong></p>
</div>
<?php elseif (isset($error_message)): ?>
<div class="alert-error">
    <p><?= esc($error_message) ?></p>
</div>
<?php else: ?>
<div class="alert-error">
    <p>Error: Datos del dispositivo no disponibles.</p>
</div>
<?php endif; ?>

    <div class="status-box">
        <p>Estado Actual:</p>
        <p id="status-display">Cargando...</p>
    </div>

    <div class="button-group">
        <button id="btn-volver">Volver</button>
        <button id="btn-abrir">Abrir Válvula</button>
        <button id="btn-cerrar">Cerrar Válvula</button>
    </div>
    
    <p class="footer-text">El estado se actualiza automáticamente.</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // **IMPORTANTE**: Asegúrate que esta MAC sea la que se pasa desde el controlador PHP
        const MAC_ADDRESS = '<?= esc($dispositivo->MAC ?? '') ?>'; 
        const API_KEY = "SUPER_SECRET_API_MLUS"; // Clave del ESP32/API
        
        const statusDisplay = document.getElementById('status-display');
        const btnAbrir = document.getElementById('btn-abrir');
        const btnCerrar = document.getElementById('btn-cerrar');
        const btnVolver = document.getElementById('btn-volver');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfName = document.querySelector('meta[name="csrf-name"]').content;

        // Función auxiliar para actualizar la UI
        function actualizarEstadoUI(estado) {
            if (estado === 1) {
                statusDisplay.textContent = 'Válvula ABIERTA';
                statusDisplay.style.color = 'var(--success-color)';
                btnAbrir.disabled = true;
                btnCerrar.disabled = false;
            } else if (estado === 0) {
                statusDisplay.textContent = 'Válvula CERRADA';
                statusDisplay.style.color = 'var(--danger-color)';
                btnAbrir.disabled = false;
                btnCerrar.disabled = true;
            }
        }

        // 1. Envía la orden de abrir/cerrar.
        async function controlValve(estado) {
            btnAbrir.disabled = true;
            btnCerrar.disabled = true;
            statusDisplay.textContent = 'Enviando orden...';
            statusDisplay.style.color = 'var(--info-color)';

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
                    actualizarEstadoUI(data.nuevo_estado);
                } else {
                    throw new Error(data.message || 'Error desconocido al cambiar estado');
                }
            } catch (error) {
                console.error('Error al controlar la válvula:', error);
                statusDisplay.textContent = 'Fallo al cambiar estado: ' + (error.message || 'Error de red.');
                statusDisplay.style.color = 'red';
                // En caso de fallo, re-consulta el estado real por si acaso
                fetchDeviceState(); 
            }
        }

        // 2. Obtiene el estado actual de la válvula (Solución del error 404)
        async function fetchDeviceState() {
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
                        // -1: No encontrado, -2: Key inválida, -3: Error DB, -4: Parámetros faltantes
                        throw new Error('Error de API. Código: ' + estado);
                    } else {
                         throw new Error('Respuesta inválida.');
                    }
                } else {
                    // Si llega aquí, es un Error HTTP (404, 500)
                    throw new Error('Error de conexión HTTP: ' + response.status);
                }

            } catch (error) {
                console.error('Error en fetchDeviceState:', error);
                statusDisplay.textContent = 'Error de conexión o 500';
                statusDisplay.style.color = 'red';
                btnAbrir.disabled = true;
                btnCerrar.disabled = true;
            }
        }

        // Asignación de eventos: Cambiamos a controlValve(estado)
        btnAbrir.addEventListener('click', () => controlValve(1)); 
        btnCerrar.addEventListener('click', () => controlValve(0)); 
        btnVolver.addEventListener('click', () => window.history.back());

        // Carga inicial y actualización periódica
        fetchDeviceState();
        setInterval(fetchDeviceState, 5000);
    });
</script>

</body>
</html>