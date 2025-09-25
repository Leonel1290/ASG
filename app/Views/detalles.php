<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Válvula | ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --success-color: #00b894;
            --danger-color: #d62828;
            --background-color: #f0f2f5;
            --card-background: #ffffff;
            --text-color: #2c2c2c;
            --border-radius: 20px;
        }
        body {
            background: linear-gradient(135deg, #e4e9f0 0%, #d5d7de 100%);
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color);
        }
        .card { 
            border-radius: var(--border-radius); 
            background-color: var(--card-background); 
            padding: 2rem; 
            border: none; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        h2.main-title { 
            font-weight: 700; 
            text-align: center; 
            margin-bottom: 2rem; 
        }
        .device-info { 
            text-align: center; 
            margin-bottom: 2rem; 
        }
        .valve-status { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 12px; 
            margin-bottom: 2rem; 
        }
        .status-led { 
            width: 15px; 
            height: 15px; 
            border-radius: 50%; 
            transition: background-color 0.5s ease; 
        }
        .status-led.abierta { 
            background-color: var(--success-color); 
            box-shadow: 0 0 10px var(--success-color); 
        }
        .status-led.cerrada { 
            background-color: var(--danger-color); 
            box-shadow: 0 0 10px var(--danger-color); 
        }
        .btn-group-actions { 
            display: flex; 
            flex-direction: column; 
            gap: 1rem; 
        }
        .btn { 
            padding: 1rem; 
            border-radius: 10px; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            border: none; 
        }
        .btn-success { background-color: var(--success-color); color: #fff; }
        .btn-danger { background-color: var(--danger-color); color: #fff; }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="main-title">Control de Válvula</h2>
            <div class="card">
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?= esc($error_message) ?>
                        </div>
                    <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                        <div class="device-info">
                            <div class="name"><strong><?= esc($dispositivo->nombre) ?></strong></div>
                            <div class="location"><?= esc($dispositivo->ubicacion) ?></div>
                        </div>

                        <div class="valve-status">
                            <div class="valve-status-text">Estado: <span id="valveStatusText">Cargando...</span></div>
                            <div class="status-led" id="statusLed"></div>
                        </div>
                        
                        <div class="btn-group-actions" role="group">
                            <button type="button" class="btn btn-success" id="btn-abrir">
                                <i class="fas fa-play"></i> Abrir Válvula
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-cerrar">
                                <i class="fas fa-stop"></i> Cerrar Válvula
                            </button>
                            <a href="<?= base_url('perfil') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Perfil
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> No se pudo cargar el dispositivo.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (isset($dispositivo) && $dispositivo !== null): ?>
$(document).ready(function() {
    const mac = '<?= esc($dispositivo->MAC) ?>';
    const statusLed = $('#statusLed');
    const btnAbrir = $('#btn-abrir');
    const btnCerrar = $('#btn-cerrar');
    const valveStatusText = $('#valveStatusText');

    const csrfName = $('meta[name="csrf-name"]').attr('content');
    const csrfHash = $('meta[name="csrf-token"]').attr('content');

    // Estado inicial
    actualizarEstadoUI(false);
    obtenerEstadoValvula();

    // Función para actualizar la interfaz según el estado
    function actualizarEstadoUI(estado) {
        if (estado) {
            statusLed.removeClass('cerrada').addClass('abierta');
            valveStatusText.text('Abierta');
            btnAbrir.prop('disabled', true);
            btnCerrar.prop('disabled', false);
        } else {
            statusLed.removeClass('abierta').addClass('cerrada');
            valveStatusText.text('Cerrada');
            btnAbrir.prop('disabled', false);
            btnCerrar.prop('disabled', true);
        }
    }

    // Función para obtener el estado actual de la válvula - CORREGIDA
    function obtenerEstadoValvula() {
        // Codificar la MAC para URL (reemplazar : por %3A)
        const macEncoded = encodeURIComponent(mac);
        
        $.ajax({
            url: '<?= base_url() ?>' + '/servo/obtenerEstado/' + macEncoded,
            method: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado_valvula);
                } else {
                    console.error('Error en respuesta:', response.message);
                    mostrarError('Error al obtener estado: ' + (response.message || 'Error desconocido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en petición:', status, error);
                if (status === 'timeout') {
                    mostrarError('Timeout al conectar con el servidor');
                } else if (xhr.status === 404) {
                    mostrarError('Dispositivo no encontrado');
                } else if (xhr.status === 500) {
                    mostrarError('Error interno del servidor');
                } else {
                    mostrarError('Error de conexión: ' + (error || 'Desconocido'));
                }
            }
        });
    }

    // Función para controlar la válvula - CORREGIDA
    function controlarValvula(accion, estado, mensaje) {
        const boton = accion === 'abrir' ? btnAbrir : btnCerrar;
        const textoOriginal = boton.html();
        
        boton.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
        
        const datos = {
            mac: mac,
            estado: estado
        };
        
        // Agregar CSRF token
        if (csrfName && csrfHash) {
            datos[csrfName] = csrfHash;
        }
        
        $.ajax({
            url: '<?= base_url() ?>' + '/servo/actualizarEstado',
            method: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                if (response.status === 'success') {
                    actualizarEstadoUI(response.estado);
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: mensaje,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al actualizar la válvula'
                    });
                }
            },
            error: function(xhr, status, error) {
                let mensajeError = 'Error de conexión';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensajeError = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    mensajeError = 'Timeout al conectar con el servidor';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensajeError
                });
            },
            complete: function() {
                boton.html(textoOriginal);
                // Reintentar obtener estado después de 1 segundo
                setTimeout(obtenerEstadoValvula, 1000);
            }
        });
    }

    // Función para mostrar errores no críticos
    function mostrarError(mensaje) {
        console.error(mensaje);
        // No mostrar alertas para errores de obtención de estado (solo log)
    }

    // Event listeners para los botones
    btnAbrir.click(function() {
        Swal.fire({
            title: '¿Abrir válvula?',
            text: '¿Está seguro de que desea abrir la válvula?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00b894',
            cancelButtonColor: '#d62828',
            confirmButtonText: 'Sí, abrir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                controlarValvula('abrir', 1, 'Válvula abierta correctamente');
            }
        });
    });
    
    btnCerrar.click(function() {
        Swal.fire({
            title: '¿Cerrar válvula?',
            text: '¿Está seguro de que desea cerrar la válvula?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d62828',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                controlarValvula('cerrar', 0, 'Válvula cerrada correctamente');
            }
        });
    });

    // Actualizar estado cada 10 segundos
    setInterval(obtenerEstadoValvula, 10000);
});
<?php endif; ?>
</script>
</body>
</html>