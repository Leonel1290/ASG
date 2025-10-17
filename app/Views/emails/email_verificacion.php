<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .header { background-color: #007bff; color: white; padding: 10px 0; text-align: center; border-radius: 4px 4px 0 0; }
        .content { padding: 20px 0; line-height: 1.6; }
        .button-container { text-align: center; margin: 20px 0; }
        .button { background-color: #28a745; color: white !important; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 0.8em; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verificación de Email ASG</h2>
        </div>
        <div class="content">
            <p>Hola **<?= esc($nombre) ?>**, </p>
            <p>Hemos recibido una solicitud para verificar tu dirección de correo electrónico y acceder a la configuración de tu perfil.</p>
            <p>Por favor, haz clic en el siguiente botón para completar el proceso de verificación:</p>

            <div class="button-container">
                <a href="<?= esc($link) ?>" class="button">Verificar Mi Email Ahora</a>
            </div>

            <p>Si no solicitaste esta verificación, puedes ignorar este correo.</p>
            <p>El enlace de verificación es: <br> <small><a href="<?= esc($link) ?>"><?= esc($link) ?></a></small></p>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> ASG. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>