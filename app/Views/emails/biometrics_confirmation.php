<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Autenticación Biométrica</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { background: #333; color: white; padding: 10px; text-align: center; }
        .alert { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ASG - Confirmación Biométrica</h1>
        </div>
        
        <div class="content">
            <h2>Hola,</h2>
            <p>Se ha habilitado la autenticación biométrica para tu cuenta de ASG.</p>
            
            <div class="alert">
                <strong>📅 Fecha de activación:</strong> <?= $fecha ?><br>
                <strong>📱 Dispositivo:</strong> <?= $dispositivo ?><br>
                <strong>🔑 Token de seguridad:</strong> <?= substr($token, 0, 10) ?>...<?= substr($token, -10) ?>
            </div>
            
            <p>Ahora podrás iniciar sesión usando:</p>
            <ul>
                <li>✅ Huella digital</li>
                <li>✅ Reconocimiento facial</li>
                <li>✅ Patrón de desbloqueo (dependiendo de tu dispositivo)</li>
            </ul>
            
            <p><strong>¿No reconoces esta actividad?</strong><br>
            Si no fuiste tú quien activó esta función, por favor:</p>
            <ol>
                <li>Desactiva inmediatamente la autenticación biométrica desde tu perfil</li>
                <li>Cambia tu contraseña</li>
                <li>Contacta con nuestro soporte técnico</li>
            </ol>
        </div>
        
        <div class="footer">
            <p>© <?= date('Y') ?> ASG - Against Safe Gas. Todos los derechos reservados.</p>
            <p>Este es un mensaje automático, por favor no respondas a este email.</p>
        </div>
    </div>
</body>
</html>