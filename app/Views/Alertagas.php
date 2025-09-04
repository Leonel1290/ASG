<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alerta de Gas</title>
    <link rel="manifest" href="manifest.json">
</head>
<body>
    <h1>🚨 Monitor de Detección de Gas</h1>
    <p>Sistema de alertas para fugas de gas</p>
    
    <div style="margin: 20px;">
        <button onclick="Notificaciones.enviarNotificacionPrueba()" 
                style="padding: 10px; margin: 5px; background: #007bff; color: white; border: none;">
            🔔 Probar Notificación
        </button>
        
        <button onclick="Notificaciones.alertaFugaGas()" 
                style="padding: 10px; margin: 5px; background: #dc3545; color: white; border: none;">
            🚨 Simular Alerta de Gas
        </button>
    </div>

    <!-- Incluir el archivo JavaScript -->
    <script src="app.js"></script>
</body>
</html>