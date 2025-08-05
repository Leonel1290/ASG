<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalar ASG App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Tus estilos existentes... */
        body {
            background: linear-gradient(135deg, #0D1F23, #132E35);
            font-family: 'Poppins', sans-serif;
            color: #AFB3B7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .card {
            background-color: #2D4A53;
            padding: 2.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            max-width: 28rem;
            width: 100%;
            text-align: center;
            color: #fff;
        }
        .btn {
            background-color: #698180;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.8rem 2rem;
            transition: background-color 0.3s ease-in-out;
            margin-top: 1.5rem;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #2D4A53;
        }
        .hidden {
            display: none;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon-container">
            <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="ASG Logo">
        </div>
        <h1>Instala la Aplicación ASG</h1>
        <p>
            Lleva la seguridad de tu hogar directamente a tu dispositivo. Instala nuestra aplicación para un acceso rápido y notificaciones instantáneas, ¡como una app nativa!
        </p>
        
        <div class="button-group">
            <a href="https://pwa-1s1m.onrender.com/instalar-pwa" class="btn" target="_blank">
                Descargar e Instalar App
            </a>
            <a href="https://pwa-1s1m.onrender.com" class="btn" target="_blank">
                Abrir App
            </a>
        </div>
    </div>

</body>
</html>