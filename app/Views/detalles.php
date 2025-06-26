<?php
// Estas variables ($dispositivo, $error_message) son pasadas desde el controlador.
// No es necesario inicializarlas aquí con el operador ??, ya que el controlador
// se encarga de que estén presentes o que la lógica de la vista las maneje si faltan.
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

<div class="container mt-4">
    <h2>Control de Válvula</h2>
    <div class="card">
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                {{-- Si hay un mensaje de error desde el controlador, mostrarlo --}}
                <div class="alert alert-danger" role="alert">
                    <?= $error_message ?>
                </div>
            <?php elseif (isset($dispositivo) && $dispositivo !== null): ?>
                {{-- Si hay un dispositivo válido, mostrar su información --}}
                <h5 class="card-title" id="nombre-dispositivo">Dispositivo: <?= $dispositivo->nombre ?></h5>
                <h6 class="card-subtitle mb-2 text-muted" id="ubicacion-dispositivo">Ubicación: <?= $dispositivo->ubicacion ?></h6>
                {{-- Acceso a estado_valvula como propiedad de objeto --}}
                <p class="card-text">Estado actual: <span id="estado-actual"><?= $dispositivo->estado_valvula ? 'Abierta' : 'Cerrada' ?></span></p>
                
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" id="btn-abrir">Abrir Válvula</button>
                    <button type="button" class="btn btn-danger" id="btn-cerrar">Cerrar Válvula</button>
                </div>
            <?php else: ?>
                {{-- Caso por defecto si no hay dispositivo y no se estableció un error_message específico --}}
                <p>No se pudo cargar la información del dispositivo. Por favor, asegúrese de que la URL sea correcta o seleccione un dispositivo de su lista.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Incluir jQuery antes de tu script personalizado -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
// Solo ejecutar el script si el dispositivo está definido, de lo contrario, no hay MAC para usar
<?php if (isset($dispositivo) && $dispositivo !== null): ?>
$(document).ready(function() {
    const mac = '<?= $dispositivo->MAC ?>'; // Acceso a MAC como propiedad de objeto
    
    // Función para actualizar el estado mostrado en la UI
    function actualizarEstado(estado) {
        $('#estado-actual').text(estado ? 'Abierta' : 'Cerrada');
    }
    
    // Obtener estado inicial del dispositivo al cargar la página
    $.get('/servo/obtenerEstado/' + mac, function(response) {
        if (response.error) {
            // Considera usar un modal o un div para mostrar errores en lugar de alert()
            alert(response.error); 
        } else {
            actualizarEstado(response.estado_valvula);
        }
    }).fail(function() {
        alert('Error al obtener el estado del dispositivo');
    });
    
    // Event listener para el botón "Abrir Válvula"
    $('#btn-abrir').click(function() {
        controlarServo(mac, 1); // 1 para abrir
    });
    
    // Event listener para el botón "Cerrar Válvula"
    $('#btn-cerrar').click(function() {
        controlarServo(mac, 0); // 0 para cerrar
    });
    
    // Función para enviar la petición al controlador para controlar el servo
    function controlarServo(mac, estado) {
        $.post('/servo/actualizarEstado', {
            mac: mac,
            estado: estado
        }, function(response) {
            if (response.error) {
                // Considera usar un modal o un div para mostrar errores en lugar de alert()
                alert(response.error);
            } else {
                actualizarEstado(estado);
                // Aquí podrías agregar notificación de éxito si lo deseas
            }
        }).fail(function() {
            alert('Error al enviar el comando al dispositivo');
        });
    }
    
    // Opcional: Actualizar el estado del dispositivo periódicamente
    // Esto asegura que la UI refleje el estado más reciente del dispositivo
    setInterval(function() {
        $.get('/servo/obtenerEstado/' + mac, function(response) {
            if (!response.error) {
                actualizarEstado(response.estado_valvula);
            }
        });
    }, 5000); // Actualizar cada 5 segundos
});
<?php endif; ?>
</script>
</body>
</html>
