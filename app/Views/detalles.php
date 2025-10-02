<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula de Gas | ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        /* Tu CSS (Lo mantengo por brevedad, asumiendo que está bien) */
        .gas-level-container {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .valve-status-open {
            color: #28a745; /* Verde */
        }
        .valve-status-closed {
            color: #dc3545; /* Rojo */
        }
    </style>
</head>
<body>
    
    <div class="container my-5">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc($error_message) ?>
            </div>
        <?php elseif ($dispositivo): ?>
            <h1 class="text-center mb-4">
                <i class="fas fa-microchip"></i>
                Detalles del Dispositivo: **<?= esc($dispositivo->nombre) ?>**
            </h1>
            <p class="text-center text-muted">MAC: <?= esc($dispositivo->MAC) ?></p>

            <div class="row justify-content-center">
                
                <div class="col-md-6 mb-4">
                    <div class="card gas-level-container p-4 text-center">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                            <h5 class="card-title">Nivel de Gas Actual</h5>
                            <p class="card-text display-4 mb-1" id="nivel-gas">--</p>
                            <p class="text-muted"><small>Última actualización: <span id="ultima-actualizacion">--</span></small></p>
                        </div>
                    </div>
                </div>

                <!-- Formulario para Abrir -->
<form method="POST" action="/valve/control">
    <input type="hidden" name="mac" value="<?= esc($dispositivo->MAC) ?>">
    <input type="hidden" name="action" value="open">
    <button type="submit">Abrir Válvula</button>
</form>

<!-- Formulario para Cerrar -->
<form method="POST" action="/valve/control">
    <input type="hidden" name="mac" value="<?= esc($dispositivo->MAC) ?>">
    <input type="hidden" name="action" value="close">
    <button type="submit">Cerrar Válvula</button>
</form>

<!-- JavaScript opcional para AJAX (sin recargar página) -->
<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const response = await fetch('/valve/control', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Error en la respuesta');
                }
                const result = await response.json();
                alert(result.message);
                // Opcional: Actualiza la UI con result.new_state
            } catch (error) {
                alert('Error: No se pudo conectar con el servidor.');
            }
        });
    });
</script>
                        </div>
                    </div>
                </div>

            </div>

        <?php endif; ?>
    </div>

    <div aria-live="polite" aria-atomic="true" class="bg-dark position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div id="toast-container"></div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        
        // **CORRECCIÓN CLAVE:** Almacenamos la MAC correcta del dispositivo que se está viendo.
        // PHP imprime la MAC solo una vez al cargar la página.
        const currentMac = '<?= $dispositivo->MAC ?? "" ?>';
        
        // --- Funciones de Utilidad ---
        function showToast(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;
            const toastContainer = $('#toast-container');
            toastContainer.append(toastHtml);
            const toastEl = toastContainer.children().last();
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            // Eliminar el toast del DOM después de que se oculte
            toastEl.on('hidden.bs.toast', function () {
                $(this).remove();
            });
        }

        function showSuccessToast(message) {
            showToast(message, 'success');
        }

        function showErrorToast(message) {
            showToast(message, 'danger');
        }

        function actualizarEstadoUI(estado) {
            const $icono = $('#icono-valvula');
            const $texto = $('#estado-valvula-texto');
            const $btnCerrar = $('#btn-cerrar');
            const $btnAbrir = $('#btn-abrir');

            // 1 es Abierta, 0 es Cerrada (según tu descripción inicial)
            if (estado === 1) {
                $icono.removeClass('fa-door-closed valve-status-closed').addClass('fa-door-open valve-status-open');
                $texto.text('Abierta').removeClass('valve-status-closed').addClass('valve-status-open');
                $btnCerrar.prop('disabled', false);
                $btnAbrir.prop('disabled', true);
            } else {
                $icono.removeClass('fa-door-open valve-status-open').addClass('fa-door-closed valve-status-closed');
                $texto.text('Cerrada').removeClass('valve-status-open').addClass('valve-status-closed');
                $btnCerrar.prop('disabled', true);
                $btnAbrir.prop('disabled', false);
            }
        }
        
        // --- Lógica de la Válvula ---

        /**
         * Obtiene el estado actual de la válvula y el nivel de gas.
         */
        function fetchDeviceState() {
            if (!currentMac) return; // No hacer nada si no hay MAC
            
            $.get(`/servo/obtenerEstado/${currentMac}`)
                .done(function(response) {
                    if (response.status === 'success') {
                        // Actualizar UI de la válvula
                        actualizarEstadoUI(response.estado_valvula);
                        
                        // Actualizar UI de nivel de gas
                        $('#nivel-gas').text(response.nivel_gas + ' PPM'); // Asumo que son Partes Por Millón
                        
                        // Formatear fecha (si existe)
                        if (response.ultima_actualizacion) {
                            const date = new Date(response.ultima_actualizacion);
                            $('#ultima-actualizacion').text(date.toLocaleString());
                        } else {
                            $('#ultima-actualizacion').text('--');
                        }
                    } else {
                        console.error('Error al obtener estado:', response.message);
                    }
                })
                .fail(function(jqXHR) {
                    console.error('Error de conexión al obtener estado:', jqXHR.responseText);
                });
        }

        /**
         * Actualiza el estado de la válvula en el servidor.
         * @param {number} estado El nuevo estado (1 para abrir, 0 para cerrar).
         * @param {string} mac La dirección MAC del dispositivo a controlar.
         */
        function actualizarEstadoValvula(estado, mac) {
            if (!mac) return;

            const btnCerrar = $('#btn-cerrar');
            const btnAbrir = $('#btn-abrir');
            const successMessage = (estado === 1) ? 'Válvula abierta correctamente' : 'Válvula cerrada correctamente';

            const csrfName = $('meta[name="csrf-name"]').attr('content');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            
            // **CORRECCIÓN CLAVE:** La MAC se recibe como argumento 'mac'
            const postData = {
                [csrfName]: csrfHash,
                mac: mac, // Usamos el argumento 'mac'
                estado: estado
            };

            // Determinar qué botón deshabilitar y qué loader mostrar
            const targetButton = (estado === 1) ? btnAbrir : btnCerrar;
            const otherButton = (estado === 1) ? btnCerrar : btnAbrir;

            targetButton.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            otherButton.prop('disabled', true); // Deshabilita el otro botón mientras procesa
            
            $.post('/servo/actualizarEstado', postData)
                .done(function(response) {
                    if (response.status === 'success') {
                        actualizarEstadoUI(response.estado ? 1 : 0); // La respuesta debe ser 1 o 0
                        showSuccessToast(successMessage);
                    } else {
                        showErrorToast(response.message || 'Error al actualizar el estado');
                    }
                })
                .fail(function(jqXHR) {
                    showErrorToast('Error de conexión con el servidor o error interno: ' + jqXHR.statusText);
                })
                .always(function() {
                    // Restaurar textos y re-habilitar según el nuevo estado (se hace en fetchDeviceState)
                    targetButton.html(estado ? 
                        '<i class="fas fa-fan"></i> Abrir Válvula' : 
                        '<i class="fas fa-stop"></i> Cerrar Válvula');
                    
                    // Llama a fetchDeviceState para asegurar el estado final correcto y habilitar/deshabilitar
                    fetchDeviceState();
                });
        }

        // --- Eventos de Clic ---

        // **CORRECCIÓN APLICADA AQUÍ:** Pasamos la variable 'currentMac'
        $('#btn-cerrar').on('click', function() {
            actualizarEstadoValvula(0, currentMac); // 0 para Cerrar
        });

        $('#btn-abrir').on('click', function() {
            actualizarEstadoValvula(1, currentMac); // 1 para Abrir
        });


        // --- Inicialización ---

        // Cargar el estado la primera vez que se carga la página
        fetchDeviceState();
        
        // Actualizar cada 5 segundos
        const intervalId = setInterval(fetchDeviceState, 5000);
        
        // Limpiar intervalo al salir de la página
        $(window).on('beforeunload', function() {
            clearInterval(intervalId);
        });
    });
    </script>
</body>
</html>