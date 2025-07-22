<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        :root {
            --primary-color: #007bff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --text-dark: #343a40;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-success: rgba(40, 167, 69, 0.3);
            --shadow-danger: rgba(220, 53, 69, 0.3);
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark); /* Color de texto base */
            line-height: 1.6; /* Mejorar legibilidad */
        }

        .container.main-content {
            margin-top: 4rem; /* Un poco más de margen superior */
            padding-bottom: 3rem;
        }

        .card {
            border-radius: 1rem; /* Bordes más redondeados */
            box-shadow: 0 0.5rem 1.5rem var(--shadow-light); /* Sombra más pronunciada pero suave */
            background-color: var(--card-bg);
            padding: 2.5rem; /* Padding uniforme */
            border: none; /* Eliminar borde por defecto de Bootstrap */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Transición para efecto hover */
        }

        .card:hover {
            transform: translateY(-5px); /* Pequeña elevación al pasar el mouse */
            box-shadow: 0 0.8rem 2rem var(--shadow-light); /* Sombra más intensa al pasar el mouse */
        }

        h1, h2 {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }

        .card-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem; /* Espacio entre el icono y el texto */
        }

        .card-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-text {
            font-size: 1.15rem; /* Ajuste ligeramente el tamaño de la fuente */
            margin-bottom: 1.8rem; /* Más espacio antes de los botones */
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #estado-actual {
            font-weight: 700;
            transition: color 0.3s ease; /* Suavizar el cambio de color */
        }

        #estado-actual.abierta {
            color: var(--success-color);
        }

        #estado-actual.cerrada {
            color: var(--danger-color);
        }

        .btn-group {
            display: flex;
            gap: 1rem; /* Espacio entre botones */
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 1.8rem; /* Ajustar padding */
            border-radius: 0.75rem; /* Bordes más redondeados para botones */
            font-size: 1.05rem; /* Ajustar tamaño de fuente */
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem; /* Espacio entre el icono y el texto del botón */
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-3px); /* Efecto de elevación más notorio */
            box-shadow: 0 0.6rem 1.5rem var(--shadow-success);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-3px);
            box-shadow: 0 0.6rem 1.5rem var(--shadow-danger);
        }

        .alert {
            border-radius: 0.75rem; /* Bordes más redondeados para alertas */
            margin-bottom: 1.5rem;
            font-size: 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 1.25rem; /* Ajustar padding de alerta */
        }

        /* Indicador visual de carga para botones */
        .btn.loading {
            position: relative;
            pointer-events: none; /* Deshabilitar clics mientras carga */
            opacity: 0.8;
        }
        .btn.loading::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 1.2em;
            height: 1.2em;
            margin-left: -0.6em;
            margin-top: -0.6em;
            border: 2px solid rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }
        .btn.loading .btn-text, .btn.loading .fas {
            visibility: hidden; /* Ocultar texto e icono mientras carga */
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Media Queries para responsividad */
        @media (max-width: 768px) {
            .container.main-content {
                margin-top: 2rem;
            }
            .card {
                padding: 1.5rem;
            }
            .card-title {
                font-size: 1.6rem;
            }
            .card-subtitle {
                font-size: 1rem;
            }
            .card-text {
                font-size: 1.05rem;
            }
            .btn-group {
                flex-direction: column;
                gap: 0.75rem;
            }
            .btn {
                width: 100%;
                font-size: 1rem;
                padding: 0.7rem 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .card {
                padding: 1rem;
            }
            .card-title {
                font-size: 1.4rem;
            }
            h1, h2 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h1>Control Inteligente de Válvulas</h1>
            <div class="card">
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert" aria-live="assertive">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        <h2 class="card-title" id="nombre-dispositivo">
                            <i class="fas fa-microchip me-2"></i>Dispositivo: <span class="fw-bold"><?= esc($dispositivo->nombre) ?></span>
                        </h2>
                        <h3 class="card-subtitle" id="ubicacion-dispositivo">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación: <span class="fw-normal"><?= esc($dispositivo->ubicacion) ?></span>
                        </h3>
                        <p class="card-text">
                            <i class="fas fa-lightbulb me-2"></i>Estado actual de la válvula: 
                            <span id="estado-actual" class="<?= $dispositivo->estado_valvula ? 'abierta' : 'cerrada' ?>" aria-live="polite">
                                <?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?>
                            </span>
                        </p>
                        
                        <div class="btn-group" role="group" aria-label="Controles de la válvula">
                            <button type="button" class="btn btn-success" id="btn-abrir" aria-label="Abrir Válvula">
                                <i class="fas fa-solid fa-valve me-2"></i><span class="btn-text">Abrir Válvula</span>
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar" aria-label="Cerrar Válvula">
                                <i class="fas fa-solid fa-valve me-2"></i><span class="btn-text">Cerrar Válvula</span>
                            </button>
                        </div>
                        <div id="status-message" class="mt-3 text-center" role="status" aria-live="polite"></div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert" aria-live="assertive">
                            <i class="fas fa-info-circle me-2"></i>No se pudo cargar la información del dispositivo. Por favor, asegúrese de que la URL sea correcta o seleccione un dispositivo de su lista.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
<?php if (isset($dispositivo) && $dispositivo !== null): ?>
$(document).ready(function() {
    const mac = '<?= esc($dispositivo->MAC) ?>'; 
    const estadoActualSpan = $('#estado-actual');
    const statusMessageDiv = $('#status-message');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');

    // Obtener el nombre y valor del token CSRF
    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>'; 
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>'; 

    function showStatusMessage(message, type = 'info') {
        statusMessageDiv.html(`<div class="alert alert-${type}">${message}</div>`);
        // Limpiar el mensaje después de 5 segundos
        setTimeout(() => statusMessageDiv.empty(), 5000);
    }

    function toggleButtons(enable) {
        btnAbrir.prop('disabled', !enable).toggleClass('loading', !enable);
        btnCerrar.prop('disabled', !enable).toggleClass('loading', !enable);
    }

    // Función para actualizar el estado mostrado en la UI
    function actualizarEstadoUI(estado) {
        estadoActualSpan.text(estado ? 'Abierta' : 'Cerrada');
        estadoActualSpan.removeClass('abierta cerrada').addClass(estado ? 'abierta' : 'cerrada');
    }
    
    // Obtener estado inicial del dispositivo al cargar la página
    function getInitialState() {
        $.ajax({
            url: '/servo/obtenerEstado/' + mac,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error al obtener estado inicial:', response.error);
                    showStatusMessage(`Error al cargar el estado inicial: ${response.error}`, 'danger');
                } else {
                    actualizarEstadoUI(response.estado_valvula);
                    showStatusMessage('Estado del dispositivo actualizado.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error de red o servidor al obtener estado inicial:', textStatus, errorThrown);
                showStatusMessage('No se pudo conectar con el servidor para obtener el estado.', 'danger');
            }
        });
    }

    getInitialState(); // Llamar al cargar la página

    // Event listener para el botón "Abrir Válvula"
    btnAbrir.click(function() {
        if (!$(this).hasClass('loading')) {
            controlarServo(mac, 1); // 1 para abrir
        }
    });
    
    // Event listener para el botón "Cerrar Válvula"
    btnCerrar.click(function() {
        if (!$(this).hasClass('loading')) {
            controlarServo(mac, 0); // 0 para cerrar
        }
    });
    
    // Función para enviar la petición al controlador para controlar el servo
    function controlarServo(mac, estado) {
        toggleButtons(false); // Deshabilitar botones y mostrar carga
        showStatusMessage(`Enviando comando para ${estado ? 'abrir' : 'cerrar'} la válvula...`, 'info');

        const postData = {
            mac: mac,
            estado: estado
        };
        
        if (csrfName && csrfHash) {
            postData[csrfName] = csrfHash;
        } else {
            console.warn('CSRF token o hash no encontrados. La petición POST podría fallar.');
        }

        $.ajax({
            url: '/servo/actualizarEstado',
            method: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error al controlar servo:', response.error);
                    showStatusMessage(`Error: ${response.error}`, 'danger');
                } else {
                    actualizarEstadoUI(response.estado);
                    showStatusMessage(`Válvula ${response.estado ? 'abierta' : 'cerrada'} exitosamente.`, 'success');
                    // Si el servidor devuelve un nuevo token CSRF, actualízalo para la siguiente petición
                    if (response.csrf_new_hash) {
                         $('meta[name="csrf-token"]').attr('content', response.csrf_new_hash);
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error de red o servidor al enviar comando:', textStatus, errorThrown);
                showStatusMessage('No se pudo conectar con el servidor para enviar el comando.', 'danger');
            },
            complete: function() {
                toggleButtons(true); // Habilitar botones de nuevo
            }
        });
    }
    
    // Actualizar el estado del dispositivo periódicamente
    setInterval(function() {
        $.ajax({
            url: '/servo/obtenerEstado/' + mac,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response.error) {
                    actualizarEstadoUI(response.estado_valvula);
                    // Opcional: showStatusMessage('Estado actualizado automáticamente.', 'secondary');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error en actualización periódica:', textStatus, errorThrown);
            }
        });
    }, 5000); // Actualizar cada 5 segundos
});
<?php endif; ?>
</script>
</body>
</html>